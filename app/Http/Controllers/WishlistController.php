<?php
namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $favorites = $user->favorites()->with('category', 'locations')->paginate(12);
        return view('wishlist.index', compact('favorites'));
    }

    public function toggle($id)
    {
        $user = Auth::user();
        $result = $user->favorites()->toggle($id);

        $attached = count($result['attached']) > 0;

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'wishlisted' => $attached,
                'message' => $attached ? 'Đã thêm vào yêu thích!' : 'Đã xóa khỏi yêu thích!',
            ]);
        }

        return back()->with('success', $attached ? 'Đã thêm vào yêu thích!' : 'Đã xóa khỏi yêu thích!');
    }
}