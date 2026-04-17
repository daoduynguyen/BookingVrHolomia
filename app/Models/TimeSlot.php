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

    // 1. Kiểm tra xem khung giờ này còn đủ chỗ trống không? (Bao gồm giới hạn game và giới hạn chi nhánh)
    public function isBookable($quantity = 1)
    {
        $hasCapacity = true;
        
        // 1.1 Khóa nếu slot đã bị tắt/full
        if ($this->status !== 'open') {
            $hasCapacity = false;
        }

        // 1.2 Kiểm tra sức chứa riêng của trò chơi này
        if (($this->capacity - $this->booked_count) < $quantity) {
            $hasCapacity = false;
        }

        // 1.3 Kiểm tra tổng tài nguyên thiết bị của TẤT CẢ các trò chơi tại chi nhánh cùng khung giờ
        if ($hasCapacity) {
            $totalBookedAtTime = self::where('location_id', $this->location_id)
                                    ->where('date', $this->date)
                                    ->where('start_time', $this->start_time)
                                    ->sum('booked_count');
                                    
            $location = \App\Models\Location::find($this->location_id);
            $maxDevices = $location ? ($location->total_devices ?? 20) : 20;

            if (($totalBookedAtTime + $quantity) > $maxDevices) {
                $hasCapacity = false;
            }
        }

        // 2. Kiểm tra thời gian thực tế (Không đặt slot quá khứ)
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

    // 2. Tăng số lượng đã đặt (và tự động đồng bộ trạng thái full cho toàn bộ slot cùng khung giờ nếu cần)
    public function incrementBooked($quantity)
    {
        $this->booked_count += $quantity;

        // Nếu số người đặt đã bằng hoặc vượt quá sức chứa MẶC ĐỊNH của trò chơi -> Cập nhật thành full
        if ($this->booked_count >= $this->capacity) {
            $this->status = 'full';
        }
        $this->save();

        // KIỂM TRA ĐỒNG BỘ: Tính tổng số máy đã sử dụng trong cùng khung giờ tại chi nhánh
        $totalBookedAtTime = self::where('location_id', $this->location_id)
                                ->where('date', $this->date)
                                ->where('start_time', $this->start_time)
                                ->sum('booked_count');
                                
        $location = \App\Models\Location::find($this->location_id);
        $maxDevices = $location ? ($location->total_devices ?? 20) : 20;

        // Nếu tổng thiết bị sử dụng >= giới hạn chi nhánh -> Đánh dấu TẤT CẢ slot khác đang open thành full
        if ($totalBookedAtTime >= $maxDevices) {
            self::where('location_id', $this->location_id)
                ->where('date', $this->date)
                ->where('start_time', $this->start_time)
                ->where('status', 'open')
                ->update(['status' => 'full']);
        }
    }

    // 3. Giảm số lượng đặt (và mở lại trạng thái cho các trò chơi khác nếu khung giờ được giải phóng)
    public function decrementBooked($quantity)
    {
        $this->booked_count = max(0, $this->booked_count - $quantity);
        $this->save();

        // Bản thân trò chơi này nếu rớt dưới ngưỡng capacity cá nhân thì đổi về open
        if ($this->status === 'full' && $this->booked_count < $this->capacity) {
            $this->status = 'open';
            $this->save();
        }

        // Đồng bộ kiểm tra mở lại chéo các slot ở cùng khung giờ
        $totalBookedAtTime = self::where('location_id', $this->location_id)
                                ->where('date', $this->date)
                                ->where('start_time', $this->start_time)
                                ->sum('booked_count');
                                
        $location = \App\Models\Location::find($this->location_id);
        $maxDevices = $location ? ($location->total_devices ?? 20) : 20;

        if ($totalBookedAtTime < $maxDevices) {
            $slotsAtTime = self::where('location_id', $this->location_id)
                               ->where('date', $this->date)
                               ->where('start_time', $this->start_time)
                               ->where('status', 'full')
                               ->get();

            foreach ($slotsAtTime as $slot) {
                // Chỉ mở lại các slot còn sức chứa riêng
                if ($slot->booked_count < $slot->capacity) {
                    $slot->status = 'open';
                    $slot->save();
                }
            }
        }
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