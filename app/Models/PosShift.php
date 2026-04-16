<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosShift extends Model
{
    protected $fillable = [
        'user_id', 'location_id', 'opening_cash',
        'closing_cash', 'cash_difference', 'opened_at',
        'closed_at', 'status', 'closing_note',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function location() { return $this->belongsTo(Location::class); }
    public function orders() { return $this->hasMany(Order::class, 'pos_shift_id'); }

    // Tổng doanh thu tiền mặt trong ca
    public function cashRevenue()
    {
        return $this->orders()
            ->where('payment_method', 'cash')
            ->where('status', 'paid')
            ->sum('total_amount');
    }

    // Tính lệch tiền
    public function calculateDifference()
    {
        $expected = $this->opening_cash + $this->cashRevenue();
        return $this->closing_cash - $expected;
    }
}