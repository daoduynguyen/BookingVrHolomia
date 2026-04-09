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

    public function applyCoupon(Request $request, $subdomain)
    {
        $code = strtoupper(trim($request->coupon_code));
        $coupon = \App\Models\Coupon::where('code', $code)
            ->where(function ($q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now()); })
            ->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Mã không hợp lệ hoặc đã hết hạn.']);
        }

        session()->put('applied_coupon_code_' . $subdomain, $code);
        session()->put('applied_coupon_id_' . $subdomain, $coupon->id);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng thành công! Giảm ' . ($coupon->type === 'percent' ? $coupon->value . '%' : number_format($coupon->value) . 'đ'),
            'type' => $coupon->type,
            'value' => $coupon->value,
        ]);
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
            'password' => 'required|min:8|confirmed',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.unique' => 'Email này đã được đăng ký',
            'password.min' => 'Mật khẩu phải từ 8 ký tự',
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

        $oldCartData = null;
        $replaceId = request('replace_cart_id');
        if ($replaceId) {
            $cart = session()->get('branch_cart_' . $subdomain, []);
            $oldCartData = $cart[$replaceId] ?? null;
        }

        return view('branch.booking', compact('location', 'ticket', 'subdomain', 'selectedType', 'slots', 'surchargeRate', 'oldCartData', 'replaceId'));
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

        // Nếu đang sửa item cũ (từ giỏ hàng), xóa item cũ đi
        if ($request->input('replace_cart_id')) {
            unset($cart[$request->input('replace_cart_id')]);
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

        try {
            $message = "🚨 *CÓ ĐƠN ĐẶT VÉ MỚI (CHI NHÁNH)!* 🚨\n\n"
                . "📍 *Cơ sở:* " . $location->name . "\n"
                . "👤 *Khách hàng:* " . $order->customer_name . "\n"
                . "📞 *SĐT:* " . $order->customer_phone . "\n"
                . "🕹️ *Số lượng vé:* " . $order->quantity . " vé\n"
                . "💰 *Tổng tiền:* " . number_format($order->total_amount) . " VNĐ\n"
                . "💳 *Trạng thái:* " . ($order->status == 'paid' ? '✅ Đã thanh toán' : '⏳ Chờ thanh toán');

            \App\Jobs\SendTelegramNotification::dispatchSync($message);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Lỗi gửi Telegram (Branch): " . $e->getMessage());
        }

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

        // Tạo QR Code Vé
        $qr = app(\App\Services\QrTicketService::class)->generate($order);

        // LOGIC CỘNG ĐIỂM, PLAY_COUNT VÀ GỬI MAIL (Trừ Banking)
        if ($paymentMethod !== 'banking') {
            foreach ($cart as $item) {
                Ticket::where('id', $item['id'])
                    ->increment('play_count', $item['quantity']);
            }

            if ($user) {
                \App\Services\LoyaltyService::addPoints($user, $order);
            }

            $validEmail = $order->customer_email ?? ($user->email ?? null);
            if ($validEmail) {
                try {
                    \Illuminate\Support\Facades\Mail::to($validEmail)->send(new \App\Mail\BookingConfirmedMail($order));
                } catch (\Exception $mailErr) {
                    \Illuminate\Support\Facades\Log::warning('Lỗi gửi mail: ' . $mailErr->getMessage());
                }
            }
        }

        // 2. CHUYỂN KHOẢN / QR → Nhảy sang trang thanh toán QR
        if ($paymentMethod === 'banking') {
            if (!Auth::check()) {
                session(['guest_order_' . $order->id => true]);
            }
            return redirect()->route('branch.payment.banking', ['subdomain' => $subdomain, 'id' => $order->id]);
        }

        // 3. VÍ HOLOMIA + COD → Thông báo thành công
        if ($paymentMethod === 'cod') {
            if (Auth::check()) {
                return redirect()->route('branch.home', ['subdomain' => $subdomain])
                    ->with('success', __('messages.booking_success') ?? 'Đặt vé thành công!');
            }
            return redirect()->route('branch.profile', ['subdomain' => $subdomain])
                ->with([
                    'cod_success' => true,
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount
                ]);
        }

        // Wallet
        return redirect()->route('branch.profile', ['subdomain' => $subdomain])
            ->with([
                'payment_success' => true,
                'order_id' => $order->id,
                'total_amount' => $order->total_amount
            ]);
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
        $order = Order::with(['orderItems.ticket', 'orderItems.review', 'location', 'slot'])->findOrFail($orderId);
        $html = view('profile.order_invoice', compact('order'))->render();
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

        $settings = \App\Models\Setting::whereIn('key', ['bank_bin', 'bank_account', 'bank_name', 'bank_owner'])->pluck('value', 'key');
        $bankBin = !empty($settings['bank_bin']) ? $settings['bank_bin'] : '970418';
        $bankAccount = !empty($settings['bank_account']) ? $settings['bank_account'] : '8860075445';
        $bankName = !empty($settings['bank_name']) ? $settings['bank_name'] : 'BIDV';
        $bankOwner = !empty($settings['bank_owner']) ? $settings['bank_owner'] : 'HOLOMIA VR';

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
        $user = Auth::user();
        return view('branch.settings', compact('location', 'subdomain', 'user'));
    }

    public function updateUISettings(Request $request, $subdomain)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,auto',
            'font' => 'required|string|max:60',
            'font_size' => 'required|integer|min:12|max:32',
            'primary_color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        Auth::user()->update([
            'ui_settings' => array_merge(Auth::user()->ui_settings ?? [], [
                'theme' => $request->theme,
                'font' => $request->font,
                'font_size' => (int) $request->font_size,
                'primary_color' => $request->primary_color,
            ])
        ]);

        return back()->with('success', 'Đã lưu cài đặt giao diện!');
    }

    public function updatePassword(Request $request, $subdomain)
    {
        $user = Auth::user();
        if ($user->provider_name) {
            return back()->withErrors(['current_password' => 'Tài khoản của bạn sử dụng đăng nhập qua mạng xã hội, không thể đổi mật khẩu tại đây.']);
        }

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        if (Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);
        return back()->with('success', 'Đổi mật khẩu thành công!');
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