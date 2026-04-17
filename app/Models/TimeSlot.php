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

        // 1.3 Kiểm tra tổng tài nguyên thiết bị của TẤT CẢ các trò chơi tại chi nhánh có GIAO NHAU về mặt thời gian
        if ($hasCapacity) {
            $location = \App\Models\Location::find($this->location_id);
            $maxDevices = $location ? ($location->total_devices ?: 20) : 20;

            $peakConcurrent = $this->getPeakConcurrentBookings($quantity);
            if ($peakConcurrent > $maxDevices) {
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

    // 2. Tăng số lượng đã đặt (và tự động đồng bộ trạng thái full cho toàn bộ slot có giao nhau nếu cần)
    public function incrementBooked($quantity)
    {
        $this->booked_count += $quantity;
        if ($this->booked_count >= $this->capacity) {
            $this->status = 'full';
        }
        $this->save();

        $this->syncGlobalOverlappingStatus();
    }

    // 3. Giảm số lượng đặt (và mở lại trạng thái cho các trò chơi khác nếu khung giờ được giải phóng)
    public function decrementBooked($quantity)
    {
        $this->booked_count = max(0, $this->booked_count - $quantity);
        $this->save();

        $this->syncGlobalOverlappingStatus();
    }

    // Tính số lượng thiết bị sử dụng đồng thời tối đa trong khung giờ của Slot này
    private function getPeakConcurrentBookings($additionalQuantity = 0)
    {
        $overlappingSlots = self::where('location_id', $this->location_id)
            ->where('date', $this->date)
            ->where('start_time', '<', $this->end_time) // Xét các khoảng thời gian bị cắt chéo
            ->where('end_time', '>', $this->start_time)
            ->where('id', '!=', $this->id)
            ->get();

        $events = [];
        
        $totalCurrent = $this->booked_count + $additionalQuantity;
        if ($totalCurrent > 0) {
            $events[] = ['time' => $this->start_time, 'change' => $totalCurrent];
            $events[] = ['time' => $this->end_time, 'change' => -$totalCurrent];
        }

        foreach ($overlappingSlots as $os) {
            if ($os->booked_count > 0) {
                $events[] = ['time' => $os->start_time, 'change' => $os->booked_count];
                $events[] = ['time' => $os->end_time, 'change' => -$os->booked_count];
            }
        }

        // Sắp xếp các sự kiện về thời gian, nếu trùng giờ thì xử lý trả máy (-) trước nhận máy (+)
        usort($events, function($a, $b) {
            if ($a['time'] === $b['time']) {
                return $a['change'] <=> $b['change']; 
            }
            return $a['time'] <=> $b['time'];
        });

        $maxConcurrent = 0;
        $currentConcurrent = 0;
        foreach ($events as $event) {
            $currentConcurrent += $event['change'];
            if ($currentConcurrent > $maxConcurrent) {
                $maxConcurrent = $currentConcurrent;
            }
        }

        return $maxConcurrent;
    }

    // Đồng bộ trạng thái 'full' cho TẤT CẢ các slot trong ngày có thể bị kẹt máy do giao giờ
    public function syncGlobalOverlappingStatus()
    {
        // Để tổng quát, lấy mọi slot trong ngày
        $allSlots = self::where('location_id', $this->location_id)
            ->where('date', $this->date)
            ->get();
            
        $location = \App\Models\Location::find($this->location_id);
        $maxDevices = $location ? ($location->total_devices ?: 20) : 20;

        // Tạo base events cho tất cả các máy đã được đặt thật trên tất cả các khung giờ
        $baseEvents = [];
        foreach ($allSlots as $os) {
            if ($os->booked_count > 0) {
                $baseEvents[] = ['time' => $os->start_time, 'change' => $os->booked_count];
                $baseEvents[] = ['time' => $os->end_time, 'change' => -$os->booked_count];
            }
        }
        
        // Thử +1 vé cho mỗi slot xem có vi phạm giới hạn tổng máy không
        /** @var \App\Models\TimeSlot $testSlot */
        foreach ($allSlots as $testSlot) {
            $testEvents = $baseEvents;
            $testEvents[] = ['time' => $testSlot->start_time, 'change' => 1];
            $testEvents[] = ['time' => $testSlot->end_time, 'change' => -1];
            
            usort($testEvents, function($a, $b) {
                if ($a['time'] === $b['time']) {
                    return $a['change'] <=> $b['change']; 
                }
                return $a['time'] <=> $b['time'];
            });

            $maxConcurrent = 0;
            $currentConcurrent = 0;
            $exceeds = false;
            foreach ($testEvents as $event) {
                $currentConcurrent += $event['change'];
                if ($currentConcurrent > $maxConcurrent) {
                    $maxConcurrent = $currentConcurrent;
                }
                if ($maxConcurrent > $maxDevices) {
                    $exceeds = true;
                    break;
                }
            }
            
            // Một slot được gọi là FULL nếu nó đạt sức chứa giới hạn riêng HOẶC nếu thêm 1 người nữa thì nó vi phạm tổng capacity chung
            $shouldBeFull = $exceeds || ($testSlot->booked_count >= $testSlot->capacity);
            
            if ($shouldBeFull && $testSlot->status === 'open') {
                $testSlot->status = 'full';
                $testSlot->save();
            } elseif (!$shouldBeFull && $testSlot->status === 'full') {
                $testSlot->status = 'open';
                $testSlot->save();
            }
        }
    }

    // ==========================================
    // TÍNH TOÁN HIỂN THỊ KHÔNG N+1 (In-Memory)
    // ==========================================
    
    public static function getTrueAvailabilitiesForDate($locationId, $date)
    {
        $slots = self::where('location_id', $locationId)
                     ->where('date', $date)
                     ->get();

        $location = \App\Models\Location::find($locationId);
        $maxDevices = $location ? ($location->total_devices ?: 20) : 20;

        $availabilities = [];

        foreach ($slots as $slot) {
            $slotPeak = self::calculatePeakForSlotInMemory($slot, $slots);
            $locationAvailable = max(0, $maxDevices - $slotPeak);
            $slotAvailable = max(0, $slot->capacity - $slot->booked_count);
            $availabilities[$slot->id] = min($slotAvailable, $locationAvailable);
        }
        return $availabilities;
    }

    private static function calculatePeakForSlotInMemory($targetSlot, $allSlots)
    {
        $events = [];
        $totalCurrent = $targetSlot->booked_count;
        if ($totalCurrent > 0) {
            $events[] = ['time' => $targetSlot->start_time, 'change' => $totalCurrent];
            $events[] = ['time' => $targetSlot->end_time, 'change' => -$totalCurrent];
        }

        foreach ($allSlots as $os) {
            if ($os->id === $targetSlot->id) continue;
            if ($os->booked_count > 0) {
                // Kiểm tra có giao nhau về thời gian không
                if ($os->start_time < $targetSlot->end_time && $os->end_time > $targetSlot->start_time) {
                    $events[] = ['time' => $os->start_time, 'change' => $os->booked_count];
                    $events[] = ['time' => $os->end_time, 'change' => -$os->booked_count];
                }
            }
        }

        usort($events, function($a, $b) {
            if ($a['time'] === $b['time']) {
                return $a['change'] <=> $b['change']; 
            }
            return $a['time'] <=> $b['time'];
        });

        $maxConcurrent = 0;
        $currentConcurrent = 0;
        foreach ($events as $event) {
            $currentConcurrent += $event['change'];
            if ($currentConcurrent > $maxConcurrent) {
                $maxConcurrent = $currentConcurrent;
            }
        }

        return $maxConcurrent;
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