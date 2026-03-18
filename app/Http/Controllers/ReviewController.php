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
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $ticketId = $request->ticket_id;
        $orderItemId = $request->order_item_id;

        // Check if the order item belongs to the user and is in a valid state to be reviewed
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
}
