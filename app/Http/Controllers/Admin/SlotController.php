<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TimeSlot;
use App\Models\Ticket;
use Carbon\Carbon;

class SlotController extends Controller
{
    public function index(Request $request)
    {
        // 1. Mặc định hiển thị lịch của ngày hôm nay (Hoặc ngày Admin chọn)
        $selectedDate = $request->input('date', Carbon::today()->format('Y-m-d'));

        // 2. Lấy danh sách tất cả các trò chơi/vé để làm bộ lọc
        $tickets = Ticket::all();

        // 3. Lấy toàn bộ Khung giờ của ngày được chọn, nhóm theo từng Trò chơi
        $slots = TimeSlot::with('ticket')
            ->where('date', $selectedDate)
            ->orderBy('start_time')
            ->get()
            ->groupBy('ticket_id');

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

        // 2. Lấy location_id từ Ticket
        $ticket = Ticket::find($request->ticket_id);
        $locationId = $ticket->location_id ?? 1;

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

        foreach ($period as $dateObj) {
            $currentDate = $dateObj->format('Y-m-d'); // Lấy ngày hiện tại trong vòng lặp

            foreach ($tickets as $ticket) {
                $locationId = $ticket->location_id ?? 1;

                // Tạo từ 08:00 đến 21:00
                for ($i = 8; $i <= 21; $i++) {
                    $startTime = sprintf('%02d:00:00', $i);
                    $endTime = sprintf('%02d:00:00', $i + 1);

                    $slot = TimeSlot::firstOrCreate([
                        'ticket_id'  => $ticket->id,
                        'date'       => $currentDate, // Dùng ngày trong vòng lặp
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
                }
            }
        }

        if ($count > 0) {
            $startStr = Carbon::parse($request->start_date)->format('d/m/Y');
            $endStr = Carbon::parse($request->end_date)->format('d/m/Y');
            return back()->with('success', "Tuyệt vời! Đã tạo thành công {$count} ca chơi từ ngày {$startStr} đến {$endStr}.");
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