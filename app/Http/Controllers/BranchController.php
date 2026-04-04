<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\Order;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ContactController;
class BranchController extends Controller
{
    // ==================== HELPER ====================
    private function getLocation($subdomain)
    {
        return Location::where('slug', $subdomain)->firstOrFail();
    }

    // ==================== TRANG CHỦ ====================
    public function index($subdomain)
    {
        $location = $this->getLocation($subdomain);
        $tickets = Ticket::whereHas('locations', function ($q) use ($location) {
            $q->where('locations.id', $location->id);
        })->where('status', 'active')->get();

        return view('branch.index', compact('location', 'tickets', 'subdomain'));
    }

    // ==================== CHI TIẾT VÉ ====================
    public function detail($subdomain, $id)
    {
        $location = $this->getLocation($subdomain);
        $ticket = Ticket::findOrFail($id);
        return view('branch.detail', compact('location', 'ticket', 'subdomain'));
    }

    // ==================== ĐĂNG NHẬP ====================
    public function showLogin($subdomain)
    {
        $location = $this->getLocation($subdomain);
        return view('branch.login', compact('location', 'subdomain'));
    }

    public function login(Request $request, $subdomain)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('branch.profile', ['subdomain' => $subdomain]);
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])->withInput();
    }

    // ==================== ĐĂNG KÝ ====================
    public function showRegister($subdomain)
    {
        $location = $this->getLocation($subdomain);
        return view('branch.register', compact('location', 'subdomain'));
    }

    public function register(Request $request, $subdomain)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.unique' => 'Email này đã được đăng ký',
            'password.min' => 'Mật khẩu phải từ 6 ký tự',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        Auth::login($user);
        return redirect()->route('branch.home', ['subdomain' => $subdomain])
            ->with('success', 'Đăng ký thành công! Chào mừng bạn.');
    }

    // ==================== ĐĂNG XUẤT ====================
    public function logout(Request $request, $subdomain)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('branch.home', ['subdomain' => $subdomain]);
    }

    // ==================== ĐẶT VÉ FORM ====================
    public function bookingForm($subdomain, $id)
    {
        $location = $this->getLocation($subdomain);
        $ticket = Ticket::findOrFail($id);
        $selectedType = request('selected_ticket_type');

        // Lấy danh sách slot
        $today = Carbon::today()->format('Y-m-d');
        $slots = TimeSlot::where('ticket_id', $ticket->id)
            ->where('date', '>=', $today)
            ->where('status', 'open')
            ->orderBy('date')->orderBy('start_time')
            ->get()
            ->groupBy('date');

        // Lấy phụ phí cuối tuần
        $surchargeStr = \App\Models\Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? '0';
        $surchargeRate = (int) $surchargeStr;

        return view('branch.booking', compact('location', 'ticket', 'subdomain', 'selectedType', 'slots', 'surchargeRate'));
    }

    // ==================== THÊM GIỎ HÀNG ====================
    public function addToCart(Request $request, $subdomain)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'customer_name' => 'required|string',
            'customer_phone' => 'required|string',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);
        $location = $this->getLocation($subdomain);

        $cart = session()->get('branch_cart_' . $subdomain, []);
        $cartKey = $ticket->id . '_' . $request->booking_date . '_' . ($request->slot_id ?? 'noslot');

        $types = $request->input('ticket_types', []);
        $totalQty = array_sum($types);
        if ($totalQty == 0)
            $totalQty = 1;

        // Kiểm tra phụ phí cuối tuần
        $date = Carbon::parse($request->booking_date);
        $surchargeStr = \App\Models\Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? '0';
        $surchargeRate = (int) $surchargeStr / 100;
        $isWeekend = $date->isWeekend();

        // 1. TRƯỜNG HỢP CÓ NHIỀU LOẠI VÉ (Người lớn, Trẻ em, VIP...)
        if ($ticket->ticket_types && is_array($ticket->ticket_types) && count($ticket->ticket_types) > 0 && is_array($types)) {
            $added = false;
            foreach ($types as $typeName => $qty) {
                $qty = (int) $qty;
                if ($qty <= 0)
                    continue;

                // Tìm giá gốc của loại vé này
                $baseUnitPrice = $ticket->price;
                foreach ($ticket->ticket_types as $tt) {
                    if ($tt['name'] === $typeName) {
                        $baseUnitPrice = (int) $tt['price'];
                        break;
                    }
                }

                // Tính giá cuối (có surcharge nếu cuối tuần)
                $finalUnitPrice = $baseUnitPrice;
                if ($isWeekend) {
                    $finalUnitPrice += $baseUnitPrice * $surchargeRate;
                }

                $cartKey = $ticket->id . '_' . $date->format('Ymd') . '_' . ($request->slot_id ?? 'noslot') . '_' . $typeName;

                $cart[$cartKey] = [
                    'id' => $ticket->id,
                    'name' => $ticket->name . ' - ' . $typeName,
                    'ticket_type' => $typeName,
                    'image' => $ticket->image ?? $ticket->image_url,
                    'quantity' => $qty,
                    'price' => $finalUnitPrice,
                    'booking_date' => $request->booking_date,
                    'slot_id' => $request->slot_id,
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'customer_email' => $request->customer_email ?? (Auth::user()->email ?? ''),
                    'payment_method' => $request->input('payment_method', 'cod'),
                    'note' => $request->input('note', ''),
                ];
                $added = true;
            }

            if (!$added) {
                return back()->with('error', 'Vui lòng chọn ít nhất 1 loại vé.')->withInput();
            }
        }
        // 2. TRƯỜNG HỢP VÉ THÔNG THƯỜNG (KHÔNG PHÂN LOẠI)
        else {
            $qty = $request->input('quantity') ? (int) $request->input('quantity') : 1;

            $finalUnitPrice = $ticket->price;
            if ($isWeekend) {
                $finalUnitPrice += $ticket->price * $surchargeRate;
            }

            $cartKey = $ticket->id . '_' . $date->format('Ymd') . '_' . ($request->slot_id ?? 'noslot') . '_base';

            $cart[$cartKey] = [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'ticket_type' => null,
                'image' => $ticket->image ?? $ticket->image_url,
                'quantity' => $qty,
                'price' => $finalUnitPrice,
                'booking_date' => $request->booking_date,
                'slot_id' => $request->slot_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email ?? (Auth::user()->email ?? ''),
                'payment_method' => $request->input('payment_method', 'cod'),
                'note' => $request->input('note', ''),
            ];
        }

        session()->put('branch_cart_' . $subdomain, $cart);
        return redirect()->route('branch.cart', ['subdomain' => $subdomain])
            ->with('success', 'Đã thêm vé vào giỏ hàng!');
    }

    // ==================== THÊM NHANH VÀO GIỎ ====================
    public function quickAddToCart($subdomain, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $cart = session()->get('branch_cart_' . $subdomain, []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                'id' => $ticket->id,
                'name' => $ticket->name,
                'ticket_type' => null,
                'image' => $ticket->image ?? $ticket->image_url,
                'quantity' => 1,
                'price' => $ticket->price,
                'booking_date' => \Carbon\Carbon::tomorrow()->format('Y-m-d'),
                'customer_name' => Auth::user()->name ?? 'Khách hàng',
                'customer_phone' => Auth::user()->phone ?? '',
            ];
        }

        session()->put('branch_cart_' . $subdomain, $cart);
        return redirect()->route('branch.cart', ['subdomain' => $subdomain])
            ->with('success', 'Đã thêm vé vào giỏ hàng!');
    }

    // ==================== GIỎ HÀNG ====================
    public function cart($subdomain)
    {
        $location = $this->getLocation($subdomain);
        $cart = session()->get('branch_cart_' . $subdomain, []);
        $total = 0;
        foreach ($cart as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
        return view('branch.cart', compact('location', 'subdomain', 'cart', 'total'));
    }

    public function removeCart(Request $request, $subdomain, $id)
    {
        $cartKey = 'branch_cart_' . $subdomain;
        $cart = session()->get($cartKey, []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put($cartKey, $cart);
        }
        return redirect()->route('branch.cart', ['subdomain' => $subdomain])
            ->with('success', 'Đã xóa vé khỏi giỏ hàng.');
    }

    public function updateCart(Request $request, $subdomain)
    {
        $cartKey = 'branch_cart_' . $subdomain;
        $cart = session()->get($cartKey, []);
        $id = $request->id;
        $quantity = $request->quantity;

        if ($id && $quantity && isset($cart[$id])) {
            $cart[$id]['quantity'] = $quantity;
            session()->put($cartKey, $cart);
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }

    public function emptyCart(Request $request, $subdomain)
    {
        session()->forget('branch_cart_' . $subdomain);
        if ($request->query('redirect') === 'home') {
            return redirect()->route('branch.home', ['subdomain' => $subdomain])
                ->with('error', 'Lệnh đặt chỗ hết hạn, chỗ đã được phục hồi giữ chỗ.');
        }
        return redirect()->route('branch.cart', ['subdomain' => $subdomain])
            ->with('success', 'Đã làm trống giỏ hàng.');
    }

    // ==================== THANH TOÁN ====================
    public function checkout(Request $request, $subdomain)
    {
        $cart = session()->get('branch_cart_' . $subdomain, []);
        if (empty($cart)) {
            return redirect()->route('branch.cart', ['subdomain' => $subdomain])
                ->with('error', 'Giỏ hàng trống!');
        }

        $location = $this->getLocation($subdomain);
        $user = Auth::user();
        $firstItem = reset($cart);

        // Tính tổng đúng: price * quantity (không áp phụ phí cuối tuần cho trang cơ sở)
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
        $paymentMethod = $request->input('payment_method', 'cod');

        // Tạo đơn hàng
        $order = Order::create([
           'user_id' => Auth::id() ?? null,
            'customer_name' => $firstItem['customer_name'],
            'customer_phone' => $firstItem['customer_phone'],
            'customer_email' => $firstItem['customer_email'] ?? ($user->email ?? ''),
            'shipping_address' => $location->address ?? 'Tại quầy',
            'total_amount' => $totalAmount,
            'payment_method' => $paymentMethod,
            'status' => $paymentMethod === 'wallet' ? 'paid' : 'pending',
            'booking_date' => $firstItem['booking_date'],
            'slot_id' => $firstItem['slot_id'] ?? null,
            'quantity' => array_sum(array_column($cart, 'quantity')),
            'location_id' => $location->id,
        ]);

        // Tạo chi tiết đơn
        foreach ($cart as $item) {
            \App\Models\OrderItem::create([
                'order_id' => $order->id,
                'ticket_id' => $item['id'],
                'ticket_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Cập nhật slot
        if ($order->slot_id) {
            $slot = TimeSlot::find($order->slot_id);
            if ($slot)
                $slot->incrementBooked($order->quantity);
        }

        // Trừ ví nếu thanh toán bằng ví
        if ($paymentMethod === 'wallet' && $user) {
            if ($user->balance < $totalAmount) {
                $order->delete();
                return back()->with('error', 'Số dư ví không đủ!');
            }
            $user->decrement('balance', $totalAmount);
        }

        // Xóa giỏ hàng
        session()->forget('branch_cart_' . $subdomain);

        // 2. CHUYỂN KHOẢN / QR → Nhảy sang trang thanh toán QR
        if ($paymentMethod === 'banking') {
            return redirect()->route('branch.payment.banking', ['subdomain' => $subdomain, 'id' => $order->id]);
        }

        // 3. VÍ HOLOMIA + COD → Thông báo thành công
        return redirect()->route('branch.home', ['subdomain' => $subdomain])
            ->with('success', 'Đặt vé thành công! Mã đơn hàng của bạn là DH' . $order->id);
    }

    // ==================== PROFILE ====================
    public function profile($subdomain)
    {
        if (!Auth::check()) {
            return redirect()->route('branch.login', ['subdomain' => $subdomain]);
        }

        $location = $this->getLocation($subdomain);
        $user = Auth::user();
        $wishlist = $user->favorites()->get();
        $orders = Order::where('user_id', $user->id)
            ->where('location_id', $location->id)
            ->with(['details.ticket', 'slot'])
            ->orderByDesc('created_at')
            ->get();

        // Lấy coupons còn hạn (bảng coupons không có cột status)
        $coupons = \App\Models\Coupon::where('expiry_date', '>', now())
            ->where('quantity', '>', 0)
            ->get();

        return view('branch.profile', compact('location', 'subdomain', 'user', 'orders', 'coupons', 'wishlist'));
    }

    public function updateProfile(Request $request, $subdomain)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user->update($request->only(['name', 'phone', 'birthday', 'gender', 'address']));

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function updateAvatar(Request $request, $subdomain)
    {
        $request->validate(['avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048']);
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $avatarName = $user->id . '_avatar_' . time() . '.' . $request->avatar->extension();
            $request->avatar->move(public_path('avatars'), $avatarName);
            $user->avatar = asset('avatars/' . $avatarName);
            $user->save();
        }

        return back()->with('success', 'Đổi ảnh đại diện thành công!');
    }

    public function orderDetail($subdomain, $orderId)
    {
        $order = Order::with(['details.ticket', 'location', 'slot'])->findOrFail($orderId);
        $html = view('partials.order_detail_content', compact('order'))->render();
        return response()->json(['html' => $html]);
    }

    public function getSlots(Request $request, $subdomain)
    {
        $ticketId = $request->ticket_id;
        $date = $request->date;

        if (!$ticketId || !$date) {
            return response()->json([]);
        }

        $query = TimeSlot::where('ticket_id', $ticketId)
            ->where('date', $date)
            ->where('status', 'open')
            ->whereColumn('booked_count', '<', 'capacity');

        // Nếu là ngày hiện tại, chỉ lấy khung giờ tương lai
        $targetDate = Carbon::parse($date);
        if ($targetDate->isToday()) {
            $query->where('start_time', '>', Carbon::now()->format('H:i:s'));
        }

        $slots = $query->orderBy('start_time', 'asc')->get();
        return response()->json($slots);
    }

    // ==================== THANH TOÁN BANKING ====================
    public function bankingPaymentPage($subdomain, $id)
    {
        $location = $this->getLocation($subdomain);
        $order = Order::with(['details.ticket', 'location', 'slot'])->findOrFail($id);

        // Bảo mật: chỉ khách của đơn mới xem được (hoặc guest có session)
        if ($order->user_id) {
            if ($order->user_id !== Auth::id())
                abort(403);
        } else {
            // Giả định logic session guest tương tự CheckoutController
            if (!session('guest_order_' . $order->id))
                abort(403);
        }

        $bankBin = \App\Models\Setting::where('key', 'bank_bin')->value('value') ?? '970436';
        $bankAccount = \App\Models\Setting::where('key', 'bank_account')->value('value') ?? '';
        $bankName = \App\Models\Setting::where('key', 'bank_name')->value('value') ?? 'Ngân hàng';
        $bankOwner = \App\Models\Setting::where('key', 'bank_owner')->value('value') ?? 'HOLOMIA VR';

        $refCode = 'DH' . $order->id;
        $amount = $order->total_amount;

        $qrUrl = "https://img.vietqr.io/image/{$bankBin}-{$bankAccount}-compact2.png"
            . "?amount={$amount}&addInfo=" . urlencode($refCode)
            . "&accountName=" . urlencode($bankOwner);

        return view('branch.banking_payment', compact(
            'location',
            'subdomain',
            'order',
            'bankName',
            'bankAccount',
            'bankOwner',
            'refCode',
            'amount',
            'qrUrl'
        ));
    }

    public function about($subdomain)
    {
        $location = $this->getLocation($subdomain);
        $tickets = Ticket::whereHas('locations', function ($q) use ($location) {
            $q->where('locations.id', $location->id);
        })->where('status', 'active')->get();
        return view('branch.about', compact('location', 'subdomain', 'tickets'));
    }
    public function contact($subdomain)
    {
        $location = $this->getLocation($subdomain);
        return view('branch.contact', compact('location', 'subdomain'));
    }

    public function contactSend(Request $request, $subdomain)
    {
        $location = $this->getLocation($subdomain);
        return app(ContactController::class)->send($request);
    }

    public function wishlist($subdomain)
    {
        $location = $this->getLocation($subdomain);
        $wishlist = auth()->user()->wishlists()->get();
        return redirect()->route('branch.profile', ['subdomain' => $subdomain]);
    }

    public function settings($subdomain)
    {
        $location = $this->getLocation($subdomain);
        return redirect()->route('branch.profile', ['subdomain' => $subdomain]);
    }
    public function shop($subdomain)
    {
        $location = $this->getLocation($subdomain);
        $tickets = Ticket::whereHas('locations', function ($q) use ($location) {
            $q->where('locations.id', $location->id);
        })->where('status', 'active')->get();
        return view('branch.shop', compact('location', 'tickets', 'subdomain'));
    }
}