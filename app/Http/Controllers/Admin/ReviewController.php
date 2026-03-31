<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Ticket;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = Review::with(['user', 'ticket'])
            ->orderBy('created_at', 'desc');

        // Lọc theo số sao
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Lọc theo vé
        if ($request->filled('ticket_id')) {
            $query->where('ticket_id', $request->ticket_id);
        }

        $reviews = $query->paginate(20);
        $tickets = Ticket::select('id', 'name')->orderBy('name')->get();

        return view('admin.reviews.index', compact('reviews', 'tickets'));
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $ticketId = $review->ticket_id;
        $review->delete();

        // Cập nhật lại avg_rating
        $ticket = Ticket::find($ticketId);
        if ($ticket) {
            $ticket->avg_rating = round($ticket->reviews()->avg('rating') ?? 0, 1);
            $ticket->save();
        }

        return back()->with('success', 'Đã xóa đánh giá.');
    }
}