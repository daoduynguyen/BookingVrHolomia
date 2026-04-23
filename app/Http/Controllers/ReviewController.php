<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Ticket;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'order_item_id' => 'nullable|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $ticketId = $request->ticket_id;
        $orderItemId = $request->order_item_id;

        // If order_item_id provided, validate it belongs to user
        if ($orderItemId) {
            $orderItem = OrderItem::where('id', $orderItemId)
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->whereIn('status', ['paid', 'expired']);
                })->first();

            if (!$orderItem) {
                return response()->json(['success' => false, 'message' => 'Bạn không thể đánh giá sản phẩm này.'], 403);
            }

            // Check if the user already reviewed this exact order item
            if (Review::where('order_item_id', $orderItemId)->exists()) {
                 return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'], 400);
            }
        } else {
            // If no order_item_id, check that user has purchased this ticket before
            $hasPurchased = OrderItem::where('ticket_id', $ticketId)
                ->whereHas('order', function ($query) use ($user) {
                    $query->where('user_id', $user->id)
                          ->whereIn('status', ['paid', 'expired']);
                })->exists();

            if (!$hasPurchased) {
                return response()->json(['success' => false, 'message' => 'Bạn cần mua vé này trước khi đánh giá.'], 403);
            }

            // Check if user already reviewed this ticket
            if (Review::where('user_id', $user->id)
                       ->where('ticket_id', $ticketId)
                       ->whereNull('order_item_id')
                       ->exists()) {
                return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá vé này rồi.'], 400);
            }
        }

        // Create the review
        Review::create([
            'user_id' => $user->id,
            'ticket_id' => $ticketId,
            'order_item_id' => $orderItemId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Calculate and update the average rating for the ticket
        $ticket = Ticket::find($ticketId);
        $avgRating = $ticket->reviews()->avg('rating');
        $ticket->avg_rating = round($avgRating, 1);
        $ticket->save();

        return response()->json(['success' => true, 'message' => 'Đánh giá của bạn đã được ghi nhận.']);
    }

    public function show($ticketId)
    {
        $ticket = Ticket::findOrFail($ticketId);
        
        // Get all reviews for this ticket, sorted by newest first
        $reviews = $ticket->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate rating distribution
        $allReviews = $ticket->reviews()->get();
        $ratingDistribution = [
            5 => $allReviews->where('rating', 5)->count(),
            4 => $allReviews->where('rating', 4)->count(),
            3 => $allReviews->where('rating', 3)->count(),
            2 => $allReviews->where('rating', 2)->count(),
            1 => $allReviews->where('rating', 1)->count(),
        ];

        $totalReviews = $allReviews->count();

        return view('reviews.show', compact('ticket', 'reviews', 'ratingDistribution', 'totalReviews'));
    }
}
