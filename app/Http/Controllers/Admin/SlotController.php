<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSlot;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SlotController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // 1. Mặc định hiển thị lịch của ngày hôm nay (Hoặc ngày Admin chọn)
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        // Khởi tạo Query gốc
        $ticketQuery = Ticket::query();
        
        // Thêm tìm kiếm theo tên vé
        if ($request->has('search') && $request->search != '') {
            $ticketQuery->where('name', 'like', '%' . $request->search . '%');
        }

        $slotQuery = TimeSlot::with('ticket')->where('date', $selectedDate);

        // CHỐT CHẶN BẢO MẬT: ÉP THEO CƠ SỞ
        if ($user && $user->role !== 'super_admin' && $user->location_id) {
            
            // a. Ép bộ lọc Game/Vé chỉ hiện game của cơ sở này
            $ticketQuery->where('location_id', $user->location_id);
            
            // b. Ép các Khung giờ (Slot) chỉ lấy của những Game thuộc cơ sở này
            $slotQuery->whereHas('ticket', function($query) use ($user) {
                $query->where('location_id', $user->location_id);
            });
        }

        // 2. Lấy danh sách Trò chơi/vé sau khi đã qua bộ lọc
        $tickets = $ticketQuery->get();

        // 3. Lấy toàn bộ Khung giờ sau khi đã lọc, sắp xếp và nhóm theo Trò chơi
        $slots = $slotQuery->orderBy('start_time')->get()->groupBy('ticket_id');

        return view('admin.slots.index', compact('slots', 'tickets', 'selectedDate'));
    }
    // 1. Thêm 1 ca lẻ
    public function storeSingle(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'date' => 'required|date',
            'start_time' => 'required', 
            'end_time' => 'required',
            'capacity' => 'required|integer|min:1'
        ]);

        // 1. Chuyển đổi định dạng giờ (Hỗ trợ cả AM/PM thành định dạng chuẩn 24h của DB)
        $startTime = Carbon::parse($request->start_time)->format('H:i:00');
        $endTime = Carbon::parse($request->end_time)->format('H:i:00');

        // Kiểm tra logic giờ
        if (strtotime($startTime) >= strtotime($endTime)) {
            return back()->with('error', 'Giờ bắt đầu phải nhỏ hơn giờ kết thúc!');
        }

        // 2. Lấy location_id từ Ticket (eager load để tránh N+1 và null)
        $ticket = Ticket::with('locations')->find($request->ticket_id);
        $locationId = $ticket->locations->first()->id ?? null;

        if (!$locationId) {
            return back()->with('error', 'Vé này chưa được gán cơ sở. Vui lòng cập nhật vé trước!');
        }

        TimeSlot::create([
            'ticket_id'  => $request->ticket_id,
            'location_id'=> $locationId, 
            'date'       => $request->date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'capacity'   => $request->capacity,
            'status'     => 'open' // SỬA 'available' THÀNH 'open'
        ]);

        return back()->with('success', 'Đã thêm ca chơi mới thành công!');
    }

    // 2. Sửa thông tin ca
    public function update(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);
        
        // Chuyển đổi định dạng giờ
        $startTime = Carbon::parse($request->start_time)->format('H:i:00');
        $endTime = Carbon::parse($request->end_time)->format('H:i:00');

        if (strtotime($startTime) >= strtotime($endTime)) {
            return back()->with('error', 'Giờ bắt đầu phải nhỏ hơn giờ kết thúc!');
        }

        $slot->update([
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'capacity'   => $request->capacity,
        ]);

        // Cập nhật lại status theo sức chứa mới
        if ($slot->booked_count >= $slot->capacity) {
            $slot->update(['status' => 'full']);
        } elseif ($slot->status == 'full' && $slot->booked_count < $slot->capacity) {
             $slot->update(['status' => 'open']); // SỬA THÀNH 'open'
        }

        return back()->with('success', 'Đã cập nhật ca chơi!');
    }

    // 3. Xóa ca
    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);

        if ($slot->booked_count > 0) {
            return back()->with('error', 'Không thể xóa! Ca này đã có khách đặt vé.');
        }

        $slot->delete();
        return back()->with('success', 'Đã xóa ca chơi!');
    }

    // 4. Hàm tạo lịch tự động hàng loạt (Hỗ trợ nhiều ngày)
    public function generate(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date', // Ngày kết thúc phải sau hoặc bằng ngày bắt đầu
            'ticket_id'  => 'nullable|exists:tickets,id' 
        ]);

        // Lấy danh sách trò chơi
        if ($request->ticket_id) {
            $tickets = Ticket::where('id', $request->ticket_id)->get();
        } else {
            $tickets = Ticket::all();
        }

        $count = 0; 
        
        // Dùng CarbonPeriod để lặp qua mảng các ngày từ start_date đến end_date
        $period = \Carbon\CarbonPeriod::create($request->start_date, $request->end_date);

        $dailyStart = Carbon::parse($request->input('start_time', '08:00'))->format('H:i:00');
        $dailyEnd   = Carbon::parse($request->input('end_time', '21:00'))->format('H:i:00');

        foreach ($period as $dateObj) {
            $currentDate = $dateObj->format('Y-m-d'); // Lấy ngày hiện tại trong vòng lặp

            foreach ($tickets as $ticket) {
                $locationId = $ticket->locations->first()->id ?? 1;
                $baseDuration = (int)$ticket->duration > 0 ? (int)$ticket->duration : 60;

             // Làm tròn lên số tận 0 hoặc 5 TIẾP THEO (không giữ nguyên nếu đã là bội 5)
                $duration = (int)(floor(($baseDuration) / 5) + 1) * 5;
                $currentTimeStr = $dailyStart;

                while (strtotime($currentTimeStr) < strtotime($dailyEnd)) {
                    $slotStartCarbon = Carbon::parse($currentTimeStr);
                    $slotEndCarbon = (clone $slotStartCarbon)->addMinutes($duration);

                    // Không dính ca qua ngày hôm sau hoặc qua giờ kết thúc
                    if ($slotEndCarbon->format('H:i:00') > $dailyEnd || $slotEndCarbon->format('H:i:00') < $dailyStart) {
                        break;
                    }

                    $startTime = $slotStartCarbon->format('H:i:00');
                    $endTime = $slotEndCarbon->format('H:i:00');

                    $slot = TimeSlot::firstOrCreate([
                        'ticket_id'  => $ticket->id,
                        'date'       => $currentDate,
                        'start_time' => $startTime,
                    ], [
                        'location_id'=> $locationId,
                        'end_time'   => $endTime,
                        'capacity'   => 15,
                        'status'     => 'open'
                    ]);

                    if ($slot->wasRecentlyCreated) {
                        $count++;
                    }

                    $currentTimeStr = $endTime;
                }
            }
        }

        if ($count > 0) {
            $startStr = Carbon::parse($request->start_date)->format('d/m/Y');
            $endStr = Carbon::parse($request->end_date)->format('d/m/Y');
            return back()->with('success', "Tuyệt vời! Đã tạo thành công {$count} ca chơi tùy chỉnh từ ngày {$startStr} đến {$endStr}.");
        } else {
            return back()->with('error', "Khoảng thời gian này đã có sẵn lịch, không có ca mới nào được tạo thêm.");
        }
    }

    public function getCustomers($id)
    {
        // Lấy thông tin ca chơi kèm danh sách đơn hàng (orders) đã đặt ca đó
        // Lưu ý: Giả sử model TimeSlot của bạn có quan hệ 'orders' qua bảng orders
        $slot = TimeSlot::with([
            'orders' => function ($query) {
                $query->where('status', '!=', 'cancelled'); // Không lấy đơn đã hủy
            }
        ])->findOrFail($id);

        return response()->json([
            'time' => Carbon::parse($slot->start_time)->format('H:i'),
            'date' => Carbon::parse($slot->date)->format('d/m/Y'),
            'booked' => $slot->booked_count,
            'capacity' => $slot->capacity,
            'customers' => $slot->orders->map(function ($order) {
                return [
                    'order_id' => $order->id,
                    'name' => $order->customer_name,
                    'phone' => $order->customer_phone,
                    'qty' => $order->quantity, // Giả sử cột quantity trong đơn hàng
                    'status' => $order->status,
                    'payment' => $order->payment_method
                ];
            })
        ]);
    }
}