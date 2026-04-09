<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Ticket;
use App\Models\TimeSlot;
use App\Services\QrTicketService;
use App\Mail\OrderStatusMail;
use App\Mail\BookingConfirmedMail;
use App\Jobs\SendTelegramNotification;
use App\Services\LoyaltyService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CheckoutController extends Controller
{
    // 1. Hiển thị Form nhập thông tin
    public function bookingForm($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Không cho đặt vé đã bị ẩn/ngừng hoạt động
        if ($ticket->status !== 'active') {
            return redirect()->route('ticket.shop')
                ->with('error', __('messages.ticket_unavailable'));
        }

        $surchargeStr = \App\Models\Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? '0';
        $surchargeRate = (int) $surchargeStr;

        $totalWeek = $ticket->price;

        return view('checkout.index', [
            'totalWeek' => $totalWeek,
            'surchargeRate' => $surchargeRate,
            'ticket_id' => $id,
            'ticket' => $ticket,
        ]);
    }

    // 2. Lưu thông tin vào Giỏ hàng -> Chuyển sang trang Giỏ hàng để check mã giảm giá
    public function confirmToCart(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'customer_name' => 'required',
            'customer_phone' => 'required',
            'ticket_id' => 'required|exists:tickets,id',
            'slot_id' => 'nullable|exists:time_slots,id',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Không cho đặt vé đã bị ẩn/ngừng hoạt động
        if ($ticket->status !== 'active') {
            return back()->with('error', __('messages.ticket_unavailable'))->withInput();
        }

        $date = Carbon::parse($request->booking_date);

        // Kiểm tra khung giờ còn chỗ không
        if ($request->slot_id) {
            $slot = TimeSlot::findOrFail($request->slot_id);
            if (!$slot->isBookable()) {
                return back()->with('error', __('messages.slot_full'))->withInput();
            }
        }

        // Lấy % phụ thu cuối tuần
        $surchargeStr = \App\Models\Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? '0';
        $surchargeRate = (int) $surchargeStr / 100;

        $cart = session()->get('cart', []);
        $couponCode = $request->input('applied_coupon');

        $calculateDiscount = function ($subTotal, $code) {
            if (!$code)
                return 0;
            $dbCoupon = Coupon::where('code', $code)->first();
            if (!$dbCoupon)
                return 0;
            $discount = $dbCoupon->type == 'percent' ? ($subTotal * $dbCoupon->value) / 100 : $dbCoupon->value;
            return $discount > $subTotal ? $subTotal : $discount; // Không giảm lố tiền
        };

        // Lấy mảng ticket_types khách gửi lên (dạng ['Tên loại' => Số lượng])
        $submittedTypes = $request->input('ticket_types'); // e.g. ['Vé Thường' => 2, 'VIP' => 1]
        $totalQuantitySubmitted = 0;

        // Nếu ticket có loại vé và khách gửi lên
        if ($ticket->ticket_types && is_array($ticket->ticket_types) && count($ticket->ticket_types) > 0 && is_array($submittedTypes)) {
            foreach ($submittedTypes as $typeName => $qty) {
                $qty = (int) $qty;
                if ($qty > 0) {
                    $totalQuantitySubmitted += $qty;
                    // Tìm giá của loại vé này
                    $basePrice = $ticket->price; // fallback
                    foreach ($ticket->ticket_types as $type) {
                        if ($type['name'] === $typeName) {
                            $basePrice = (int) $type['price'];
                            break;
                        }
                    }

                    // Tính phụ thu nếu là T7, CN
                    $finalPrice = $basePrice;
                    if ($date->isWeekend()) {
                        $finalPrice += $basePrice * $surchargeRate;
                    }

                    $cartKey = $ticket->id . '_' . $date->format('Ymd') . '_' . ($request->slot_id ?? 'none') . '_' . $typeName;

                    if (isset($cart[$cartKey])) {
                        $cart[$cartKey]['quantity'] += $qty;
                        $cart[$cartKey]['price'] = $finalPrice; // Cập nhật lại giá lỡ đổi cuối tuần
                        $currentCoupon = $couponCode ?: ($cart[$cartKey]['coupon_code'] ?? null);
                        $newSubTotal = $finalPrice * $cart[$cartKey]['quantity'];
                        $cart[$cartKey]['coupon_code'] = $currentCoupon;
                        $cart[$cartKey]['discount'] = $calculateDiscount($newSubTotal, $currentCoupon);
                    } else {
                        $cart[$cartKey] = [
                            "id" => $ticket->id,
                            "name" => $ticket->name . ' - ' . $typeName,
                            "ticket_type" => $typeName,
                            "quantity" => $qty,
                            "price" => $finalPrice,
                            "image" => $ticket->image ?? $ticket->image_url,
                            "booking_date" => $request->booking_date,
                            "slot_id" => $request->slot_id,
                            "coupon_code" => $couponCode,
                            "discount" => $calculateDiscount($finalPrice * $qty, $couponCode),
                            "customer_info" => $request->only(['customer_name', 'customer_phone', 'customer_email', 'payment_method', 'shipping_address', 'note']),
                        ];
                    }
                }
            }
        } else {
            // VÉ BÌNH THƯỜNG (KHÔNG CÓ TICKET_TYPES)
            $qty = $request->input('quantity') ? (int) $request->input('quantity') : 1;
            $totalQuantitySubmitted = $qty;

            $basePrice = $ticket->price;
            $finalPrice = $basePrice;
            if ($date->isWeekend()) {
                $finalPrice += $basePrice * $surchargeRate;
            }

            $cartKey = $ticket->id . '_' . $date->format('Ymd') . '_' . ($request->slot_id ?? 'none') . '_base';

            if (isset($cart[$cartKey])) {
                $cart[$cartKey]['quantity'] += $qty;
                $cart[$cartKey]['price'] = $finalPrice;
                $currentCoupon = $couponCode ?: ($cart[$cartKey]['coupon_code'] ?? null);
                $newSubTotal = $finalPrice * $cart[$cartKey]['quantity'];
                $cart[$cartKey]['coupon_code'] = $currentCoupon;
                $cart[$cartKey]['discount'] = $calculateDiscount($newSubTotal, $currentCoupon);
            } else {
                $cart[$cartKey] = [
                    "id" => $ticket->id,
                    "name" => $ticket->name,
                    "ticket_type" => null,
                    "quantity" => $qty,
                    "price" => $finalPrice,
                    "image" => $ticket->image ?? $ticket->image_url,
                    "booking_date" => $request->booking_date,
                    "slot_id" => $request->slot_id,
                    "coupon_code" => $couponCode,
                    "discount" => $calculateDiscount($finalPrice * $qty, $couponCode),
                    "customer_info" => $request->only(['customer_name', 'customer_phone', 'customer_email', 'payment_method', 'shipping_address', 'note']),
                ];
            }
        }

        if ($totalQuantitySubmitted <= 0) {
            return back()->with('error', __('messages.select_at_least_one'))->withInput();
        }

        if ($request->has('replace_cart_id') && isset($cart[$request->replace_cart_id])) {
            unset($cart[$request->replace_cart_id]);
        }

        session()->put('cart', $cart);

        if (!session()->has('cart_created_at')) {
            session(['cart_created_at' => time()]);
        }
        return redirect()->route('cart.index')->with('success', __('messages.saved_to_cart'));
    }

    // 3. Thanh toán cuối cùng (Gọi từ trang Giỏ hàng) -> Tạo đơn + QR + Mail
    public function finalPayment()
    {
        $cart = session('cart');

        if (!$cart)
            return redirect()->route('ticket.shop')->with('error', __('messages.cart_empty'));

        DB::beginTransaction();
        try {
            $totalAmount = 0;
            $discountAmount = 0;
            $appliedCoupons = []; // Mảng chứa các mã giảm giá đã xài

            // 1. QUÉT QUA GIỎ HÀNG ĐỂ TÍNH TỔNG TIỀN GỐC & TỔNG TIỀN GIẢM
            foreach ($cart as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $totalAmount += $subtotal;

                // Cộng dồn tiền giảm của từng vé
                $itemDiscount = $item['discount'] ?? 0;
                $discountAmount += $itemDiscount;

                // Nếu vé này có xài mã giảm giá, lưu tên mã lại
                if (!empty($item['coupon_code'])) {
                    $appliedCoupons[] = $item['coupon_code'];
                }
            }

            // Tính tiền thanh toán cuối cùng
            $finalTotal = $totalAmount - $discountAmount;

            // Xử lý tên các mã giảm giá để lưu vào CSDL (VD: "HOLOMIA10, ZOMBIE20")
            // Dùng array_unique để loại bỏ mã trùng nếu 2 vé xài chung 1 mã
            $couponCodeString = !empty($appliedCoupons) ? implode(', ', array_unique($appliedCoupons)) : null;

            // Khóa mã giảm giá để ngăn chặn việc dùng chung 1 mã quá lượt (Race Condition)
            if (!empty($appliedCoupons)) {
                $uniqueCoupons = array_unique($appliedCoupons);
                $lockedCoupons = \App\Models\Coupon::whereIn('code', $uniqueCoupons)->lockForUpdate()->get()->keyBy('code');
                foreach ($uniqueCoupons as $code) {
                    if (!isset($lockedCoupons[$code]) || $lockedCoupons[$code]->quantity <= 0) {
                        DB::rollBack();
                        return back()->with('error', __('messages.coupon_used_up') . " ($code)");
                    }
                }
            }

            // Thông tin khách (Lấy từ item đầu tiên trong giỏ)
            $firstItem = reset($cart);
            $info = $firstItem['customer_info'] ?? [];
            /** @var \App\Models\User|null $user */
            $user = Auth::user();

            $slotId = $firstItem['slot_id'] ?? null;
            $slot = $slotId ? TimeSlot::find($slotId) : null;

            // Tính tổng số lượng vé trong đơn
            $totalQty = array_sum(array_column($cart, 'quantity'));

            if ($slotId) {
                $slot = TimeSlot::lockForUpdate()->find($slotId);
                if (!$slot || !$slot->isBookable($totalQty)) {
                    DB::rollBack();
                    return back()->with('error', __('messages.slot_full'));
                }
            }

            $ticketId = $firstItem['id'] ?? $firstItem['ticket_id'] ?? null;
            $locationId = null;
            if (!empty($cart)) {
                $firstItem = reset($cart); // Lấy món đầu tiên trong giỏ

                // Quét tìm ID vé (có thể là 'id', 'ticket_id', hoặc chính là cái key của mảng $cart)
                $ticketId = $firstItem['id'] ?? $firstItem['ticket_id'] ?? key($cart);

                if ($ticketId) {
                    $ticketForLocation = \App\Models\Ticket::find($ticketId);
                    if ($ticketForLocation && $ticketForLocation->locations->count() > 0) {
                        $locationId = $ticketForLocation->locations->first()->id;
                    }
                }
            }

            // 2. TẠO ĐƠN HÀNG MỚI VÀO DATABASE
            $paymentMethod = $info['payment_method'] ?? 'cod';

            // Nếu thanh toán bằng ví: kiểm tra số dư trước
            if ($paymentMethod === 'wallet') {
                $freshUser = \App\Models\User::lockForUpdate()->find(Auth::id());
                if (!$freshUser || $freshUser->balance < $finalTotal) {
                    DB::rollBack();
                    return back()->with('error', __('messages.insufficient_balance'));
                }
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'customer_name' => $info['customer_name'] ?? ($user->name ?? __('messages.guest')),
                'customer_phone' => $info['customer_phone'] ?? ($user->phone ?? ''),
                'customer_email' => $info['customer_email'] ?? ($user->email ?? ''),
                'shipping_address' => $info['shipping_address'] ?? ($user->address ?? __('messages.at_counter')),
                'total_amount' => $finalTotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCodeString,
                'payment_method' => $paymentMethod,
                'status' => 'pending',
                'note' => $info['note'] ?? '',
                'booking_date' => $firstItem['booking_date'] ?? Carbon::tomorrow()->format('Y-m-d'),
                'slot_id' => $slotId,
                'quantity' => $totalQty,
                'location_id' => $locationId,
            ]);


            // 🚀 BẮT ĐẦU ĐOẠN CODE GỬI TELEGRAM 🚀
            try {
                // Định dạng nội dung tin nhắn (Dùng dấu * để in đậm)
                $message = "🚨 *CÓ ĐƠN ĐẶT VÉ MỚI!* 🚨\n\n"
                    . "👤 *Khách hàng:* " . $order->customer_name . "\n"
                    . "📞 *SĐT:* " . $order->customer_phone . "\n"
                    . "🕹️ *Số lượng vé:* " . $order->quantity . " vé\n"
                    . "💰 *Tổng tiền:* " . number_format($order->total_amount) . " VNĐ\n"
                    . "💳 *Trạng thái:* " . ($order->status == 'paid' ? '✅ Đã thanh toán' : '⏳ Chờ thanh toán');

                // Bắn Request lên API của Telegram thông qua Background Job
                \App\Jobs\SendTelegramNotification::dispatchSync($message);
            } catch (\Exception $e) {
                // Ghi log
                Log::error("Lỗi gửi Telegram: " . $e->getMessage());
            }
            // 🚀 KẾT THÚC ĐOẠN CODE GỬI TELEGRAM 🚀

            // XỬ LÝ THANH TOÁN THEO PHƯƠNG THỨC
            if ($paymentMethod === 'wallet') {
                // Trừ ví ngay lập tức
                $freshUser = \App\Models\User::lockForUpdate()->find(Auth::id());
                $balanceBefore = (float) $freshUser->balance;
                $freshUser->decrement('balance', $finalTotal);
                $order->update(['status' => 'paid']);

                // Ghi lịch sử ví
                \App\Models\WalletTransaction::create([
                    'user_id' => $freshUser->id,
                    'type' => 'payment',
                    'amount' => $finalTotal,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore - $finalTotal,
                    'description' => 'Thanh toán đơn #' . $order->id,
                    'reference_code' => 'DH' . $order->id,
                    'order_id' => $order->id,
                    'status' => 'completed',
                ]);
            } elseif ($paymentMethod === 'banking') {
                // Lưu mã tham chiếu để Sepay đối chiếu
                $order->update(['notes' => 'DH' . $order->id]);
            }
            // COD: giữ nguyên status = pending, Admin duyệt tại quầy

            // 3. TẠO CHI TIẾT ĐƠN HÀNG
            foreach ($cart as $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_id' => $details['id'],
                    'ticket_name' => $details['name'],
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
            }

            // 4. TRỪ SỐ LƯỢNG CHỖ TRỐNG CỦA KHUNG GIỜ (slot đã được lockForUpdate bên trên)
            if ($order->slot_id) {
                $slot = TimeSlot::lockForUpdate()->find($order->slot_id);
                if ($slot) {
                    $slot->incrementBooked($order->quantity);
                }
            }

            // Tạo QR Code
            $qr = app(QrTicketService::class)->generate($order);

            // LOGIC CỘNG ĐIỂM, PLAY_COUNT VÀ GỬI MAIL
            // CHỈ áp dụng ngay cho COD và Wallet.
            // Banking: Sepay Webhook sẽ xử lý sau khi xác nhận tiền vào — tránh cộng 2 lần.
            if ($paymentMethod !== 'banking') {
                // Cộng play_count cho từng vé trong đơn
                foreach ($cart as $details) {
                    Ticket::where('id', $details['id'])
                        ->increment('play_count', $details['quantity']);
                }

                // Cộng điểm và xét thăng hạng qua LoyaltyService
                if ($user) {
                    LoyaltyService::addPoints($user, $order);
                }

                // Gửi email vé điện tử
                $validEmail = $order->customer_email ?? ($user->email ?? null);
                if ($validEmail) {
                    try {
                        Mail::to($validEmail)->send(new BookingConfirmedMail($order));
                    } catch (\Exception $mailErr) {
                        Log::warning('Lỗi gửi mail: ' . $mailErr->getMessage());
                    }
                }
            }

            // Trừ số lượng coupon đã dùng (trừ từng mã riêng lẻ, không dùng chuỗi ghép)
            if (!empty($appliedCoupons)) {
                foreach (array_unique($appliedCoupons) as $usedCode) {
                    Coupon::where('code', $usedCode)
                        ->where('quantity', '>', 0)
                        ->decrement('quantity');
                }
            }

            // Dọn dẹp Giỏ hàng
            session()->forget(['cart', 'coupon', 'coupon_code', 'cart_created_at']);
            DB::commit();

            // =============================================
            // REDIRECT THEO TỪNG PHƯƠNG THỨC THANH TOÁN
            // =============================================

            // Đánh dấu quyền truy cập xem thông tin banking order ngay trên session cho Guest
            if (!Auth::check()) {
                session(['guest_order_' . $order->id => true]);
            }

            // 1. THANH TOÁN TẠI QUẦY (COD)
            if ($paymentMethod === 'cod') {
                if (!Auth::check()) {
                    return redirect()->route('home')->with([
                        'cod_success' => true,
                        'order_id' => $order->id,
                        'total_amount' => $order->total_amount,
                    ]);
                }
                return redirect()->route('profile.index')->with([
                    'cod_success' => true,
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount,
                ]);
            }

            // 2. CHUYỂN KHOẢN / QR → Nhảy sang trang thanh toán QR
            if ($paymentMethod === 'banking') {
                return redirect()->route('payment.banking', $order->id);
            }

            // 3. VÍ HOLOMIA → Thông báo thành công
            return redirect()->route('profile.index')->with([
                'payment_success' => true,
                'order_id' => $order->id,
                'total_amount' => $order->total_amount,
                'method' => 'wallet',
                'qr_code' => $qr->qr_code ?? '',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('finalPayment error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', __('messages.payment_error') ?? 'Đã có lỗi xảy ra trong quá trình thanh toán, vui lòng thử lại!');
        }
    }

    // Các hàm phụ trợ
    // Trang QR thanh toán chuyển khoản
    public function bankingPaymentPage($id)
    {
        $order = \App\Models\Order::with(['orderItems.ticket', 'location', 'slot'])->findOrFail($id);

        // Bảo mật: chỉ khách của đơn mới xem được
        if ($order->user_id) {
            if ($order->user_id !== Auth::id())
                abort(403);
        } else {
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

        return view('checkout.banking_payment', compact(
            'order',
            'bankName',
            'bankAccount',
            'bankOwner',
            'refCode',
            'amount',
            'qrUrl'
        ));
    }

    public function checkCoupon(Request $request)
    {
        $code = $request->code;
        $totalAmount = $request->total_amount;
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon)
            return response()->json(['status' => 'error', 'message' => __('messages.coupon_not_found')]);
        if ($coupon->expiry_date && Carbon::now()->gt($coupon->expiry_date))
            return response()->json(['status' => 'error', 'message' => __('messages.coupon_expired')]);
        if ($coupon->quantity <= 0)
            return response()->json(['status' => 'error', 'message' => __('messages.coupon_used_up')]);

        $discount = $coupon->type == 'percent' ? ($totalAmount * $coupon->value) / 100 : $coupon->value;
        if ($discount > $totalAmount)
            $discount = $totalAmount;

        session()->put('coupon', ['code' => $coupon->code, 'discount' => $discount, 'type' => $coupon->type, 'value' => $coupon->value]);
        return response()->json(['status' => 'success', 'message' => __('messages.coupon_applied'), 'discount' => $discount, 'new_total' => $totalAmount - $discount]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', __('messages.coupon_removed'));
    }

    public function getSlots(Request $request)
    {
        // Validate dữ liệu đầu vào
        if (!$request->ticket_id || !$request->date) {
            return response()->json([]);
        }

        $query = TimeSlot::where('ticket_id', $request->ticket_id)
            ->where('date', $request->date)
            ->where('status', 'open') // Chỉ lấy slot đang mở
            ->whereColumn('booked_count', '<', 'capacity'); // Chỉ lấy slot còn chỗ

        // NGUYÊN TẮC QUAN TRỌNG: Nếu là mốc xem trong hôm nay, 
        // chỉ hiển thị những khung giờ bắt đầu sau thời điểm hiện tại.
        $targetDate = Carbon::parse($request->date);
        if ($targetDate->isToday()) {
            $query->where('start_time', '>', Carbon::now()->format('H:i:s'));
        }

        $slots = $query->orderBy('start_time', 'asc')
            ->get()
            ->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'label' => Carbon::parse($slot->start_time)->format('H:i') . ' - ' . Carbon::parse($slot->end_time)->format('H:i'),
                    'available' => $slot->capacity - $slot->booked_count
                ];
            });

        return response()->json($slots);
    }
    public function refundOrder($id)
    {
        /** @var \App\Models\Order $order */
        $order = Order::with('slot')->findOrFail($id);
        /** @var \App\Models\User $user */
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để thực hiện hoàn tiền.');
        }

        if ($order->user_id !== $user->id) {
            abort(403, 'Bạn không có quyền hoàn tiền đơn hàng này.');
        }

        // 1. Kiểm tra thời gian
        $now = Carbon::now();
        $createdAt = $order->created_at;

        // SỬA LỖI: Dùng booking_date thay vì play_date. Xử lý trường hợp không có slot.
        $startTime = $order->slot ? $order->slot->start_time : '00:00:00';
        $playTime = Carbon::parse($order->booking_date . ' ' . $startTime);

        $diffCreation = $now->diffInHours($createdAt);
        $diffPlay = $now->diffInHours($playTime, false); // false để lấy số âm nếu đã qua giờ chơi

        // KIỂM TRA ĐIỀU KIỆN
        // Nếu quá 24h từ lúc đặt -> Lỗi
        if ($diffCreation > 24) {
            return back()->with('error', __('messages.refund_time_limit'));
        }

        // Nếu khoảng cách đến giờ chơi < 48h (hoặc đã quá giờ chơi) -> Lỗi
        if ($diffPlay < 48) {
            return back()->with('error', __('messages.refund_play_limit'));
        }

        // 2. Thực hiện hoàn tiền và Nhả slot
        DB::transaction(function () use ($order, $user) {
            // Chỉ cộng tiền vào ví nếu thanh toán không phải bằng tiền mặt
            if ($order->payment_method !== 'cod') {
                $balanceBefore = (float) $user->fresh()->balance;
                $user->increment('balance', $order->total_amount);

                // Ghi lịch sử ví cho khoản hoàn tiền
                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'refund',
                    'amount' => $order->total_amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + $order->total_amount,
                    'description' => 'Hoàn tiền đơn #' . $order->id,
                    'reference_code' => 'HD' . $order->id,
                    'order_id' => $order->id,
                    'status' => 'completed',
                ]);
            }

            //  LOGIC TRỪ ĐIỂM KHI HOÀN VÉ
            $shouldDeductPoints = in_array($order->payment_method, ['cod', 'wallet']) || $order->status === 'paid';
            if ($shouldDeductPoints) {
                $pointsToDeduct = floor($order->total_amount / LoyaltyService::POINTS_PER_VND);

                // Trừ điểm nhưng không để bị âm điểm
                $user->points = max(0, $user->points - $pointsToDeduct);
                $user->tier = LoyaltyService::calculateTier($user->points);
                $user->save();
            }
            // Đổi trạng thái đơn hàng
            $order->status = 'refunded';
            $order->save();

            // 🚀 ĐOẠN CODE TELEGRAM BÁO HỦY VÉ (Ở HÀM refundOrder) 🚀
            try {
                $message = "⚠️ *CÓ KHÁCH HÀNG HOÀN/HỦY VÉ!* ⚠️\n\n"
                    . "👤 *Khách hàng:* " . $order->customer_name . "\n"
                    . "📞 *SĐT:* " . $order->customer_phone . "\n"
                    . "🕹️ *Số vé trả lại:* " . $order->quantity . " vé\n"
                    . "💰 *Tiền đã hoàn:* " . number_format($order->total_amount) . " VNĐ\n"
                    . "🔄 *Trạng thái:* Đã nhả lại Slot trống";

                \App\Jobs\SendTelegramNotification::dispatchSync($message);
            } catch (\Exception $e) {
                Log::error("Lỗi gửi Telegram: " . $e->getMessage());
            }

            if ($order->user && $order->user->email) {
                try {
                    $statusName = 'Đã Hoàn Trả';
                    $messageBody = 'Yêu cầu hoàn vé của bạn đã được xử lý thành công. Số tiền ' . number_format($order->total_amount) . 'đ đã được cộng lại vào Ví của bạn. Các khung giờ chơi đã được hủy bỏ.';
                    Mail::to($order->user->email)
                        ->send(new OrderStatusMail($order, $statusName, $messageBody));
                } catch (\Exception $mailErr) {
                    Log::warning('Lỗi gửi mail hoàn vé: ' . $mailErr->getMessage());
                }
            }

            // 3. LOGIC NHẢ SLOT TRỐNG CHO KHÁCH KHÁC ĐẶT LẠI
            if ($order->slot_id) {
                $slot = TimeSlot::find($order->slot_id);
                if ($slot) {
                    // Trừ đi số lượng khách đã hủy (Hạ booked_count xuống)
                    $slot->decrement('booked_count', $order->quantity);

                    // Đảm bảo không bị âm và cập nhật lại trạng thái open nếu trước đó bị full
                    if ($slot->booked_count < $slot->capacity) {
                        $slot->update(['status' => 'open']);
                    }
                }
            }
        });

        return redirect()->route('profile.index')->with('success', __('messages.refund_success'));
    }


    // Các hàm cho Route API & Thành công
    public function checkOrderStatus($id)
    {
        $order = Order::find($id);
        if (!$order || $order->user_id !== auth()->id()) {
            return response()->json(['status' => 'not_found']);
        }
        return response()->json(['status' => $order->status]);
    }

    public function orderStatus($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => 'not_found']);
        }
        $isOwner = auth()->check() && $order->user_id === auth()->id();
        $isGuest = !$order->user_id && session('guest_order_' . $id);
        if (!$isOwner && !$isGuest) {
            return response()->json(['status' => 'not_found']);
        }
        return response()->json(['status' => $order->status]);
    }

    public function bookingSuccess()
    {
        return view('checkout.success');
    }
}