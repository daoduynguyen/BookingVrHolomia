<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosDevice extends Model
{
    protected $fillable = [
        'location_id', 'name', 'status',
        'battery_percent', 'error_note',
    ];

    public function location() { return $this->belongsTo(Location::class); }

    // Gắn với order đang chạy
    public function activeOrder()
    {
        return Order::where('device_id', $this->id)
            ->whereNotNull('checkin_at')
            ->where('status', 'paid')
            ->first();
    }
}
