<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'ticket_id','ticket_name', 'quantity', 'price'];

    // 1. Quan hệ ngược về Order
    public function order() {
        return $this->belongsTo(Order::class);
    }

    // 2. Quan hệ với Ticket 
    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    // Quan hệ: 1 OrderItem có thể có 1 Đánh giá
    public function review() {
        return $this->hasOne(Review::class);
    }
}