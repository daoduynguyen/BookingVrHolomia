<?php

namespace App\Models;

use Carbon\Carbon;
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
        $hasCapacity = $this->status === 'open' && ($this->capacity - $this->booked_count) >= $quantity;

        // Bổ sung kiểm tra thời gian thực tế
        $targetDate = \Carbon\Carbon::parse($this->date)->startOfDay();
        $today = \Carbon\Carbon::today();

        $isFuture = true;
        if ($targetDate->lt($today)) {
            $isFuture = false; // Ngày quá khứ
        }
        elseif ($targetDate->equalTo($today)) {
            // Cùng ngày hôm nay -> Giờ bắt đầu phải lớn hơn giờ hiện tại
            if ($this->start_time <= \Carbon\Carbon::now()->format('H:i:s')) {
                $isFuture = false;
            }
        }

        return $hasCapacity && $isFuture;
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
        return $this->hasMany(Order::class , 'slot_id');
    }
}