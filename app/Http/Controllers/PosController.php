<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\PosDevice;
use App\Models\PosShift;
use App\Models\TimeSlot;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    // Lấy location từ subdomain
    private function getLocation(string $subdomain): Location
    {
        return Location::where('slug', $subdomain)->firstOrFail();
    }

    // Lấy ca đang mở (hoặc null)
    private function getActiveShift(int $locationId): ?PosShift
    {
        return PosShift::where('location_id', $locationId)
            ->where('status', 'open')
            ->first();
    }

    // -------------------------------------------------------
    // Dashboard POS: hiển thị vé + slot hôm nay
    // -------------------------------------------------------
    public function dashboard(string $subdomain)
    {
        $location    = $this->getLocation($subdomain);
        $activeShift = $this->getActiveShift($location->id);

        // Nếu chưa mở ca → chuyển sang trang mở ca
        if (!$activeShift) {
            return redirect()->route('pos.shift.open.form', $subdomain)
                ->with('info', 'Vui lòng mở ca trước khi bắt đầu bán vé.');
        }

        // Vé thuộc chi nhánh
        $tickets = Ticket::whereHas('locations', function ($q) use ($location) {
            $q->where('locations.id', $location->id);
        })->with(['timeSlots' => function ($q) {
            $q->whereDate('date', today())
              ->orderBy('start_time');
        }])->get();

        // Thiết bị VR
        $devices = PosDevice::where('location_id', $location->id)
            ->orderBy('name')
            ->get();

        return view('pos.dashboard', compact('location', 'subdomain', 'activeShift', 'tickets', 'devices'));
    }

    // -------------------------------------------------------
    // Chi tiết slot: khách đang chơi + đếm ngược
    // -------------------------------------------------------
    public function slotDetail(string $subdomain, int $slotId)
    {
        $location = $this->getLocation($subdomain);
        $slot     = TimeSlot::where('id', $slotId)
            ->where('location_id', $location->id)
            ->with('ticket')
            ->firstOrFail();

        // Các đơn đã check-in (đang chơi)
        $activeOrders = Order::where('slot_id', $slotId)
            ->whereNotNull('checkin_at')
            ->where('status', 'paid')
            ->with('user')
            ->get()
            ->map(function ($order) use ($slot) {
                $checkinAt      = \Carbon\Carbon::parse($order->checkin_at);
                $endTime        = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time);
                $remainSeconds  = max(0, now()->diffInSeconds($endTime, false));
                $order->remain_seconds = (int) $remainSeconds;
                return $order;
            });

        // Các đơn đã thanh toán nhưng chưa check-in
        $waitingOrders = Order::where('slot_id', $slotId)
            ->whereNull('checkin_at')
            ->where('status', 'paid')
            ->with('user')
            ->get();

        $activeShift = $this->getActiveShift($location->id);

        return view('pos.slot-detail', compact(
            'location', 'subdomain', 'slot', 'activeOrders', 'waitingOrders', 'activeShift'
        ));
    }

    // -------------------------------------------------------
    // API: trạng thái slot (dùng cho polling JS)
    // -------------------------------------------------------
    public function slotStatus(string $subdomain, int $slotId)
    {
        $location = $this->getLocation($subdomain);
        $slot     = TimeSlot::where('id', $slotId)
            ->where('location_id', $location->id)
            ->firstOrFail();

        $activeOrders = Order::where('slot_id', $slotId)
            ->whereNotNull('checkin_at')
            ->where('status', 'paid')
            ->get()
            ->map(function ($order) use ($slot) {
                $endTime       = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time);
                $remainSeconds = max(0, now()->diffInSeconds($endTime, false));
                return [
                    'id'             => $order->id,
                    'customer_name'  => $order->customer_name,
                    'checkin_at'     => $order->checkin_at,
                    'remain_seconds' => (int) $remainSeconds,
                ];
            });

        return response()->json([
            'slot_id'       => $slot->id,
            'available'     => $slot->capacity - $slot->booked_count,
            'active_orders' => $activeOrders,
        ]);
    }

    // -------------------------------------------------------
    // Form bán vé nhanh
    // -------------------------------------------------------
    public function saleForm(string $subdomain, int $slotId)
    {
        $location    = $this->getLocation($subdomain);
        $activeShift = $this->getActiveShift($location->id);

        if (!$activeShift) {
            return redirect()->route('pos.shift.open.form', $subdomain)
                ->with('error', 'Vui lòng mở ca trước khi bán vé.');
        }

        $slot = TimeSlot::where('id', $slotId)
            ->where('location_id', $location->id)
            ->with('ticket')
            ->firstOrFail();

        if (!$slot->isBookable()) {
            return back()->with('error', 'Slot này đã hết chỗ hoặc đã qua giờ.');
        }

        return view('pos.sale.form', compact('location', 'subdomain', 'slot', 'activeShift'));
    }


    // -------------------------------------------------------
    // Lưu đơn bán vé tại quầy
    // -------------------------------------------------------
    public function storeSale(Request $request, string $subdomain)
    {
        $location = $this->getLocation($subdomain);

        $request->validate([
            'slot_id'        => 'required|exists:time_slots,id',
            'payment_method' => 'required|in:cash,card,transfer',
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'user_id'        => 'nullable|exists:users,id',
            'quantity'       => 'required|integer|min:1|max:10',
            'use_points'     => 'nullable|boolean',
        ]);

        $slot = TimeSlot::where('id', $request->slot_id)
            ->where('location_id', $location->id)
            ->with('ticket')
            ->firstOrFail();

        if (!$slot->isBookable($request->quantity)) {
            return back()->with('error', 'Slot này không còn đủ chỗ trống!');
        }

        $activeShift = $this->getActiveShift($location->id);
        if (!$activeShift) {
            return back()->with('error', 'Vui lòng mở ca trước khi bán vé.');
        }

        $order = null;
        $discountAmount = 0;
        $pointsToDeduct = 0;

        // Xử lý tích điểm
        if ($request->user_id && $request->use_points) {
            $user = User::find($request->user_id);
            if ($user && $user->loyalty_points > 0) {
                $pointsToDeduct = $user->loyalty_points;
                $discountAmount = $pointsToDeduct * 1000; // 1 điểm = 1.000đ
                
                // Không để giảm giá vượt quá tổng tiền
                $maxDiscount = $slot->ticket->price * $request->quantity;
                if ($discountAmount > $maxDiscount) {
                    $discountAmount = $maxDiscount;
                    $pointsToDeduct = ceil($discountAmount / 1000);
                }
            }
        }

        DB::transaction(function () use ($request, $slot, $activeShift, $location, $discountAmount, $pointsToDeduct, &$order) {
            $totalAmount = ($slot->ticket->price * $request->quantity) - $discountAmount;
            
            $order = Order::create([
                'user_id'         => $request->user_id,
                'customer_name'   => $request->customer_name,
                'customer_phone'  => $request->customer_phone,
                'customer_email'  => $request->customer_email,
                'slot_id'         => $slot->id,
                'location_id'     => $location->id,
                'total_amount'    => max(0, $totalAmount),
                'discount_amount' => $discountAmount,
                'quantity'        => $request->quantity,
                'payment_method'  => $request->payment_method,
                'status'          => 'paid',
                'is_pos_sale'     => true,
                'pos_shift_id'    => $activeShift->id,
                'qr_token'        => Str::random(40),
                'qr_used'         => false,
            ]);

            $slot->incrementBooked($request->quantity);

            // Trừ điểm user
            if ($pointsToDeduct > 0) {
                User::where('id', $request->user_id)->decrement('loyalty_points', $pointsToDeduct);
            }
            
            // Tích điểm mới cho khách (nếu có user_id)
            if ($request->user_id) {
                $newPoints = floor($order->total_amount / 100000); // 100k = 1 điểm
                if ($newPoints > 0) {
                    User::where('id', $request->user_id)->increment('loyalty_points', $newPoints);
                }
            }
        });

        return redirect()->route('pos.sale.success', [$subdomain, $order->id]);
    }

    // -------------------------------------------------------
    // Trang thành công sau bán vé (hiển thị bill + in)
    // -------------------------------------------------------
    public function saleSuccess(string $subdomain, int $orderId)
    {
        $location = $this->getLocation($subdomain);
        $order    = Order::where('id', $orderId)
            ->where('location_id', $location->id)
            ->with(['slot.ticket', 'user'])
            ->firstOrFail();

        return view('pos.sale.success', compact('location', 'subdomain', 'order'));
    }

    // -------------------------------------------------------
    // Check-in bằng QR code
    // -------------------------------------------------------
    public function checkinForm(string $subdomain)
    {
        $location    = $this->getLocation($subdomain);
        $activeShift = $this->getActiveShift($location->id);

        return view('pos.checkin', compact('location', 'subdomain', 'activeShift'));
    }

    public function checkinProcess(Request $request, string $subdomain)
    {
        $request->validate(['qr_token' => 'required|string']);

        $location = $this->getLocation($subdomain);

        $order = Order::where('qr_token', $request->qr_token)
            ->where('location_id', $location->id)
            ->where('status', 'paid')
            ->with(['slot.ticket', 'user'])
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Mã QR không hợp lệ hoặc không thuộc chi nhánh này.']);
        }

        if ($order->qr_used) {
            return response()->json(['success' => false, 'message' => 'Mã QR này đã được sử dụng rồi.']);
        }

        if (!$order->slot) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thông tin slot.']);
        }

        // Cập nhật trạng thái check-in
        $order->update([
            'qr_used'       => true,
            'checked_in_at' => now(), // Corrected column name if necessary, check Model
        ]);

        return response()->json([
            'success'        => true,
            'order_id'       => $order->id,
            'customer_name'  => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'ticket_name'    => $order->slot->ticket->name ?? '',
            'slot_time'      => \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($order->slot->end_time)->format('H:i'),
            'slot_date'      => \Carbon\Carbon::parse($order->slot->date)->format('d/m/Y'),
            'quantity'       => $order->quantity ?? 1,
            'slot_id'        => $order->slot_id,
        ]);
    }

    public function findCustomer(Request $request, string $subdomain)
    {
        $phone = $request->phone;
        $user = User::where('phone', $phone)
            ->where('role', 'customer')
            ->first();

        if ($user) {
            return response()->json([
                'found'  => true,
                'id'     => $user->id,
                'name'   => $user->name,
                'points' => $user->loyalty_points ?? 0,
            ]);
        }

        return response()->json(['found' => false]);
    }

    // -------------------------------------------------------
    // Quản lý thiết bị VR
    // -------------------------------------------------------
    public function devices(string $subdomain)
    {
        $location = $this->getLocation($subdomain);
        $devices  = PosDevice::where('location_id', $location->id)
            ->orderBy('name')
            ->get();

        return view('pos.devices', compact('location', 'subdomain', 'devices'));
    }

    public function updateDeviceStatus(Request $request, string $subdomain, int $deviceId)
    {
        $request->validate([
            'status'     => 'required|in:available,in_use,cleaning,charging,error',
            'error_note' => 'nullable|string|max:500',
        ]);

        $location = $this->getLocation($subdomain);
        $device   = PosDevice::where('id', $deviceId)
            ->where('location_id', $location->id)
            ->firstOrFail();

        $device->update([
            'status'     => $request->status,
            'error_note' => $request->status === 'error' ? $request->error_note : null,
        ]);

        return response()->json(['success' => true, 'status' => $device->status]);
    }

    // -------------------------------------------------------
    // Lịch sử giao dịch trong ca
    // -------------------------------------------------------
    public function history(string $subdomain)
    {
        $location    = $this->getLocation($subdomain);
        $activeShift = $this->getActiveShift($location->id);

        $orders = Order::where('location_id', $location->id)
            ->where('is_pos_sale', true)
            ->when($activeShift, fn($q) => $q->where('pos_shift_id', $activeShift->id))
            ->with(['slot.ticket', 'user'])
            ->latest()
            ->paginate(20);

        return view('pos.history', compact('location', 'subdomain', 'activeShift', 'orders'));
    }

    // -------------------------------------------------------
    // Huỷ vé (yêu cầu quyền branch_admin hoặc admin)
    // -------------------------------------------------------
    public function cancelOrder(Request $request, string $subdomain, int $orderId)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'branch_admin'])) {
            return back()->with('error', 'Chỉ quản lý chi nhánh mới được phép huỷ vé.');
        }

        $location = $this->getLocation($subdomain);
        $order    = Order::where('id', $orderId)
            ->where('location_id', $location->id)
            ->firstOrFail();

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã bị huỷ trước đó.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($order, $request) {
            // Hoàn lại slot
            if ($order->slot_id) {
                TimeSlot::where('id', $order->slot_id)
                    ->decrement('booked_count', $order->quantity ?? 1);
            }

            $order->update([
                'status' => 'cancelled',
                'notes'  => 'Huỷ bởi ' . Auth::user()->name . ': ' . $request->cancel_reason,
            ]);
        });

        return back()->with('success', 'Đã huỷ vé thành công.');
    }
}