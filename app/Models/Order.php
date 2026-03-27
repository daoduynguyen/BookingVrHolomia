<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_name', 'customer_phone', 'customer_email', 
        'total_amount', 'status', 'payment_method', 'notes','shipping_address','booking_date','slot_id',
        'quantity','location_id','coupon_code', 'discount_amount',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function details() {
        
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    protected $guarded = [];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function slot()
    {
        // Đơn hàng này thuộc về một Khung giờ (TimeSlot)
        return $this->belongsTo(\App\Models\TimeSlot::class, 'slot_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}