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
        $query = Ticket::whereHas('locations', function ($q) use ($location) {
            $q->where('locations.id', $location->id);
        });

        if (request()->has('search') && request()->search != '') {
            $query->where('name', 'like', '%' . request()->search . '%');
        }

        // Safety: Ensure location has total_devices >= 20
        if (!$location->total_devices || $location->total_devices <= 0) {
            $location->update(['total_devices' => 20]);
        }

        // Trigger maintenance sync BEFORE loading tickets so eager-loaded
        // timeSlots reflect corrected status values (not stale 'full').
        $firstSlot = \App\Models\TimeSlot::where('location_id', $location->id)
            ->whereDate('date', today())
            ->first();
        if ($firstSlot) {
            $firstSlot->syncGlobalOverlappingStatus();
        }

        $tickets = $query->with(['timeSlots' => function ($q) use ($location) {
            $q->where('location_id', $location->id)
              ->whereDate('date', today())
              ->orderBy('start_time');
        }])->paginate(9)->withQueryString();

        $totalDevicesConfig = $location->total_devices ?? 20;

        // Thiết bị VR
        $devices = \App\Models\PosDevice::where('location_id', $location->id)
            ->orderBy('id')
            ->get();

        if ($devices->count() < $totalDevicesConfig) {
            $currentCount = $devices->count();
            for ($i = $currentCount + 1; $i <= $totalDevicesConfig; $i++) {
                \App\Models\PosDevice::create([
                    'location_id' => $location->id,
                    'name'        => 'Kính ' . str_pad($i, 2, '0', STR_PAD_LEFT),
                    'status'      => 'available'
                ]);
            }
            $devices = \App\Models\PosDevice::where('location_id', $location->id)
                ->orderBy('id')
                ->get();
        }

        $availabilities = \App\Models\TimeSlot::getTrueAvailabilitiesForDate($location->id, today()->format('Y-m-d'));

        return view('pos.dashboard', compact('location', 'subdomain', 'activeShift', 'tickets', 'devices', 'availabilities'));
    }

    // -------------------------------------------------------
    // Chi tiết slot: khách đang chơi + đếm ngược
    // -------------------------------------------------------
    public function slotDetail(string $subdomain, int $slotId)
    {
        $location = $this->getLocation($subdomain);
        $slot     = TimeSlot::query()->where('id', $slotId)
            ->where('location_id', $location->id)
            ->with('ticket')
            ->firstOrFail();

        $deadline = \Carbon\Carbon::parse($slot->date . ' ' . $slot->start_time)->addMinutes(15);
        $now = now();

        $orders = Order::where('slot_id', $slotId)
            ->where('location_id', $location->id)
            ->whereIn('status', ['paid', 'cancelled', 'refunded', 'expired'])
            ->with(['user', 'device'])
            ->orderByRaw('CASE WHEN checkin_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('checkin_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($order) use ($slot, $deadline, $now) {
                $endTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time);
                $playingRemain = max(0, $now->diffInSeconds($endTime, false));
                $waitingRemain = max(0, $now->diffInSeconds($deadline, false));

                $order->device_name = $order->device->name ?? ($order->device_id ? 'Máy #' . $order->device_id : 'Chưa gán máy');
                $order->state = match ($order->status) {
                    'cancelled' => 'cancelled',
                    'refunded' => 'refunded',
                    'expired' => 'expired',
                    default => $order->checkin_at ? 'playing' : ($waitingRemain <= 0 ? 'expired' : 'waiting'),
                };
                $order->deadline_seconds = $waitingRemain;
                $order->remain_seconds = $playingRemain;
                $order->deadline_at = $deadline->copy();
                return $order;
            });

        $playingOrders = $orders->where('state', 'playing')->values();
        $waitingOrders = $orders->where('state', 'waiting')->values();
        $expiredOrders = $orders->where('state', 'expired')->values();
        $cancelledOrders = $orders->where('state', 'cancelled')->values();
        $refundedOrders = $orders->where('state', 'refunded')->values();

        $activeShift = $this->getActiveShift($location->id);

        return view('pos.slot-detail', compact(
            'location', 'subdomain', 'slot', 'orders', 'playingOrders', 'waitingOrders', 'expiredOrders', 'cancelledOrders', 'refundedOrders', 'activeShift'
        ));
    }

    // -------------------------------------------------------
    // API: trạng thái slot (dùng cho polling JS)
    // -------------------------------------------------------
    public function slotStatus(string $subdomain, int $slotId)
    {
        $location = $this->getLocation($subdomain);
        $slot     = TimeSlot::query()->where('id', $slotId)
            ->where('location_id', $location->id)
            ->firstOrFail();

        $deadline = \Carbon\Carbon::parse($slot->date . ' ' . $slot->start_time)->addMinutes(15);
        $now = now();

        $allOrders = Order::where('slot_id', $slotId)
            ->where('location_id', $location->id)
            ->whereIn('status', ['paid', 'cancelled', 'refunded', 'expired'])
            ->with('device')
            ->get()
            ->map(function ($order) use ($slot, $deadline, $now) {
                $endTime = \Carbon\Carbon::parse($slot->date . ' ' . $slot->end_time);
                $playingRemain = max(0, $now->diffInSeconds($endTime, false));
                $waitingRemain = max(0, $now->diffInSeconds($deadline, false));
                $state = match ($order->status) {
                    'cancelled' => 'cancelled',
                    'refunded' => 'refunded',
                    'expired' => 'expired',
                    default => $order->checkin_at ? 'playing' : ($waitingRemain <= 0 ? 'expired' : 'waiting'),
                };
                return [
                    'id' => $order->id,
                    'state' => $state,
                    'device_name' => $order->device->name ?? ($order->device_id ? 'Máy #' . $order->device_id : 'Máy chưa gán'),
                    'customer_name' => $order->customer_name,
                    'checkin_at' => $order->checkin_at,
                    'remain_seconds' => (int) $playingRemain,
                    'deadline_seconds' => (int) $waitingRemain,
                ];
            });

        $activeOrders = $allOrders->where('state', 'playing')->values();
        $summary = [
            'playing' => $allOrders->where('state', 'playing')->count(),
            'waiting' => $allOrders->where('state', 'waiting')->count(),
            'expired' => $allOrders->where('state', 'expired')->count(),
            'cancelled' => $allOrders->where('state', 'cancelled')->count(),
            'refunded' => $allOrders->where('state', 'refunded')->count(),
        ];

        return response()->json([
            'slot_id'       => $slot->id,
            'available'     => $slot->capacity - $slot->booked_count,
            'active_orders' => $activeOrders,
            'summary'       => $summary,
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

        $availabilities = \App\Models\TimeSlot::getTrueAvailabilitiesForDate($location->id, $slot->date);
        $available = $availabilities[$slot->id] ?? 0;

        if (!$slot->isBookable() || $available <= 0) {
            return back()->with('error', 'Slot này đã hết thiết bị trống hoặc đã qua giờ báo danh.');
        }

        return view('pos.sale.form', compact('location', 'subdomain', 'slot', 'activeShift', 'available'));
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
                'shipping_address'=> '',
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
        $request->validate([
            'qr_token' => 'nullable|string',
            'order_id' => 'nullable|exists:orders,id',
        ]);

        $location = $this->getLocation($subdomain);

        $result = DB::transaction(function () use ($location, $request) {
            $query = Order::where('location_id', $location->id)
                ->where('status', 'paid')
                ->lockForUpdate()
                ->with(['slot.ticket', 'user', 'device']);

            if ($request->filled('order_id')) {
                $query->where('id', $request->order_id);
            } else {
                $query->where('qr_token', $request->qr_token);
            }

            $order = $query
                ->where('location_id', $location->id)
                ->first();

            if (!$order) {
                return ['success' => false, 'message' => 'Mã QR không hợp lệ hoặc không thuộc chi nhánh này.'];
            }

            if ($order->qr_used) {
                return ['success' => false, 'message' => 'Mã QR này đã được sử dụng rồi.'];
            }

            if (!$order->slot) {
                return ['success' => false, 'message' => 'Không tìm thấy thông tin slot.'];
            }

            $deadline = \Carbon\Carbon::parse($order->slot->date . ' ' . $order->slot->start_time)->addMinutes(15);
            if (!$order->checkin_at && now()->greaterThan($deadline)) {
                $order->update(['status' => 'expired']);
                return ['success' => false, 'message' => 'Đơn này đã hết hạn check-in.'];
            }

            $device = $order->device;
            if (!$device) {
                $device = PosDevice::where('location_id', $location->id)
                    ->where('status', 'available')
                    ->orderBy('id')
                    ->lockForUpdate()
                    ->first();

                if (!$device) {
                    return ['success' => false, 'message' => 'Không còn thiết bị trống để gán cho khách này.'];
                }

                $device->update(['status' => 'in_use']);
                $order->device_id = $device->id;
            }

            $order->update([
                'qr_used'       => true,
                'checkin_at'    => now(),
                'device_id'     => $order->device_id,
            ]);

            return [
                'success'        => true,
                'order_id'       => $order->id,
                'customer_name'  => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'ticket_name'    => $order->slot->ticket->name ?? '',
                'slot_time'      => \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($order->slot->end_time)->format('H:i'),
                'slot_date'      => \Carbon\Carbon::parse($order->slot->date)->format('d/m/Y'),
                'quantity'       => $order->quantity ?? 1,
                'slot_id'        => $order->slot_id,
                'device_name'    => $device->name ?? 'Máy',
            ];
        });

        return response()->json($result);
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
            if ($order->device_id) {
                $device = PosDevice::find($order->device_id);
                if ($device) {
                    $device->update(['status' => 'available']);
                }
            }

            // Hoàn lại slot
            if ($order->slot_id) {
                $slot = TimeSlot::find($order->slot_id);
                if ($slot) {
                    $slot->decrementBooked($order->quantity ?? 1);
                }
            }

            $order->update([
                'status' => 'cancelled',
                'notes'  => 'Huỷ bởi ' . Auth::user()->name . ': ' . $request->cancel_reason,
            ]);
        });

        return back()->with('success', 'Đã huỷ vé thành công.');
    }

    // -------------------------------------------------------
    // Cài đặt giao diện POS
    // -------------------------------------------------------
    public function settings(string $subdomain)
    {
        $location    = $this->getLocation($subdomain);
        $activeShift = $this->getActiveShift($location->id);
        
        $uiSettings = auth()->user()->ui_settings ?? [];

        return view('pos.settings', compact('location', 'subdomain', 'activeShift', 'uiSettings'));
    }

    public function updateSettings(Request $request, string $subdomain)
    {
        $request->validate([
            'pos_theme'         => ['required', 'in:dark,light'],
            'pos_primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ]);

        $user = auth()->user();
        $uiSettings = $user->ui_settings ?? [];
        
        // Cập nhật cài đặt mới vào mảng hiện tại
        $uiSettings['pos_theme']         = $request->pos_theme;
        $uiSettings['pos_primary_color'] = $request->pos_primary_color;
        $uiSettings['pos_show_past_slots'] = $request->has('pos_show_past_slots');

        $user->update([
            'ui_settings' => $uiSettings
        ]);

        return back()->with('success', 'Đã lưu cài đặt giao diện thành công.');
    }
}