<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id', 
        'location_id', 
        'date', 
        'start_time', 
        'end_time',
        'capacity', 
        'booked_count', 
        'status'
    ];

    // ==========================================
    // CÁC HÀM XỬ LÝ LOGIC (Dùng trong Controller)
    // ==========================================

    // 1. Kiểm tra xem khung giờ này còn đủ chỗ trống không?
    public function isBookable($quantity = 1)
    {
        return $this->status === 'open' && ($this->capacity - $this->booked_count) >= $quantity;
    }

    // 2. Tăng số lượng đã đặt (và tự động khóa nếu đầy)
    public function incrementBooked($quantity)
    {
        $this->booked_count += $quantity;
        
        // Nếu số người đặt đã bằng hoặc vượt quá sức chứa -> Đổi trạng thái thành Full (Hết chỗ)
        if ($this->booked_count >= $this->capacity) {
            $this->status = 'full';
        }
        
        $this->save();
    }

    // ==========================================
    // CÁC HÀM LIÊN KẾT BẢNG (Relationships)
    // ==========================================

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'slot_id');
    }
}