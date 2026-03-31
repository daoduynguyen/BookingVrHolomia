<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::with(['category', 'locations'])
            ->where('status', 'active')
            ->when(
                $request->location_id,
                fn($q) =>
                $q->whereHas('locations', fn($l) => $l->where('locations.id', $request->location_id))
            )
            ->when(
                $request->search,
                fn($q) =>
                $q->where('name', 'like', "%{$request->search}%")
            )
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $tickets,
        ]);
    }

    public function show($id)
    {
        $ticket = Ticket::with(['category', 'locations', 'reviews.user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ticket,
        ]);
    }
}