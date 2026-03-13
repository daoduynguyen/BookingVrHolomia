<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\Ticket;
use App\Models\TimeSlot;
use App\Services\QrTicketService;
use App\Mail\BookingConfirmedMail;
use Carbon\Carbon;

class CheckoutController extends Controller
{
    // 1. Hiển thị Form nhập thông tin
    public function bookingForm($id)
    {
        $ticket = Ticket::findOrFail($id);

        $totalWeek    = $ticket->price;
        $totalWeekend = ($ticket->price_weekend > 0) ? $ticket->price_weekend : $ticket->price;

        return view('checkout.index', [
            'totalWeek'    => $totalWeek,
            'totalWeekend' => $totalWeekend,
            'ticket_id'    => $id,
            'ticket'       => $ticket,
        ]);
    }

    // 2. Lưu thông tin vào Giỏ hàng -> Chuyển sang trang Giỏ hàng để check mã giảm giá
    public function confirmToCart(Request $request)
    {
        $request->validate([
            'booking_date'   => 'required|date|after_or_equal:today',
            'customer_name'  => 'required',
            'customer_phone' => 'required',
            'ticket_id'      => 'required|exists:tickets,id',
            'quantity'       => 'required|integer|min:1|max:50',
            'slot_id'        => 'nullable|exists:time_slots,id',
        ]);

        $ticket    = Ticket::findOrFail($request->ticket_id);
        $date      = Carbon::parse($request->booking_date);
        
        // Tính giá ngày thường / cuối tuần
        $price = $ticket->price;
        if ($date->isWeekend() && $ticket->price_weekend > 0) {
            $price = $ticket->price_weekend;
        }

        // Kiểm tra khung giờ còn chỗ không
        if ($request->slot_id) {
            $slot = TimeSlot::findOrFail($request->slot_id);
            if (!$slot->isBookable()) {
                return back()->with('error', 'Khung giờ này đã đầy! Vui lòng chọn giờ khác.')->withInput();
            }
        }

        // 🔥 GIẢI QUYẾT VẤN ĐỀ 3: TÁCH GIỎ HÀNG DỰA TRÊN KHUNG GIỜ
        // Thêm slot_id vào khóa bảo mật. Khác giờ = Tách dòng mới. Trùng giờ = Cộng dồn số lượng.
        $cartKey  = $ticket->id . '_' . $date->format('Ymd') . '_' . ($request->slot_id ?? 'none');
        $quantity = $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        // 🔥 GIẢI QUYẾT VẤN ĐỀ 2: XỬ LÝ MÃ GIẢM GIÁ CHO TỪNG VÉ
        $couponCode = $request->input('applied_coupon'); // Lấy mã khách đã nhập ở trang trước
        $discountAmount = 0;

        // Hàm ẩn để tính tiền giảm giá cho 1 vé
        $calculateDiscount = function($subTotal, $code) {
            if (!$code) return 0;
            $coupon = \App\Models\Coupon::where('code', $code)->first();
            if (!$coupon) return 0;
            
            $discount = $coupon->type == 'percent' ? ($subTotal * $coupon->value) / 100 : $coupon->value;
            return $discount > $subTotal ? $subTotal : $discount; // Không giảm lố tiền
        };

        if (isset($cart[$cartKey])) {
            // NẾU TRÙNG MỌI THỨ -> CỘNG DỒN SỐ LƯỢNG VÀ TÍNH LẠI TIỀN GIẢM
            $cart[$cartKey]['quantity'] += $quantity;
            
            // Lấy mã giảm giá (Ưu tiên mã mới nhập, không có thì lấy mã cũ)
            $currentCoupon = $couponCode ? $couponCode : ($cart[$cartKey]['coupon_code'] ?? null);
            $newSubTotal = $price * $cart[$cartKey]['quantity'];
            
            $cart[$cartKey]['coupon_code'] = $currentCoupon;
            $cart[$cartKey]['discount'] = $calculateDiscount($newSubTotal, $currentCoupon);

        } else {
            // NẾU LÀ VÉ MỚI (Khác Game, Khác Ngày hoặc Khác Giờ) -> TẠO DÒNG MỚI
            $cart[$cartKey] = [
                "id"           => $ticket->id,
                "name"         => $ticket->name,
                "quantity"     => $quantity,
                "price"        => $price,
                "image"        => $ticket->image ?? $ticket->image_url,
                "booking_date" => $request->booking_date,
                "slot_id"      => $request->slot_id, 
                "coupon_code"  => $couponCode, // Lưu riêng tên mã
                "discount"     => $calculateDiscount($price * $quantity, $couponCode), // Lưu số tiền giảm
                "customer_info" => $request->only([
                    'customer_name', 'customer_phone',
                    'payment_method', 'shipping_address', 'note',
                ]),
            ];
        }

        // Đẩy lại vào Session
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Đã lưu thông tin vào giỏ hàng!');
    }

    // 3. Thanh toán cuối cùng (Gọi từ trang Giỏ hàng) -> Tạo đơn + QR + Mail
    public function finalPayment()
    {
        $cart = session('cart');
        
        if (!$cart) return redirect()->route('ticket.shop')->with('error', 'Giỏ hàng trống!');

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

            // Thông tin khách (Lấy từ item đầu tiên trong giỏ)
            $firstItem = reset($cart);
            $info      = $firstItem['customer_info'] ?? [];
            $user      = Auth::user();

            $slotId = $firstItem['slot_id'] ?? null;
            $slot   = $slotId ? TimeSlot::find($slotId) : null;
            
            // Tính tổng số lượng vé trong đơn
            $totalQty = array_sum(array_column($cart, 'quantity'));

            if ($slot && !$slot->isBookable()) {
                DB::rollBack();
                return back()->with('error', 'Khung giờ vừa hết chỗ! Vui lòng chọn giờ khác.');
            }

            // 2. TẠO ĐƠN HÀNG MỚI VÀO DATABASE
            $order = Order::create([
                'user_id'          => Auth::id(),
                'customer_name'    => $info['customer_name'] ?? ($user->name ?? 'Khách'),
                'customer_phone'   => $info['customer_phone'] ?? ($user->phone ?? ''),
                'shipping_address' => $info['shipping_address'] ?? ($user->address ?? 'Tại quầy'),
                'total_amount'     => $finalTotal, // Đã trừ tiền
                'discount_amount'  => $discountAmount, // Tổng số tiền đã được giảm
                'coupon_code'      => $couponCodeString, // Lưu chuỗi các mã đã dùng
                'payment_method'   => $info['payment_method'] ?? 'cod',
                'status'           => 'pending',
                'note'             => $info['note'] ?? '',
                'booking_date'     => $firstItem['booking_date'] ?? Carbon::tomorrow()->format('Y-m-d'),
                'slot_id'          => $slotId,
                'quantity'         => $totalQty,
            ]);

            // 3. TẠO CHI TIẾT ĐƠN HÀNG (OrderItem)
            foreach ($cart as $details) {
                OrderItem::create([
                    'order_id'    => $order->id,
                    'ticket_id'   => $details['id'],
                    'ticket_name' => $details['name'],
                    'quantity'    => $details['quantity'],
                    'price'       => $details['price'],
                ]);
            }

            // 4. TRỪ SỐ LƯỢNG CHỖ TRỐNG CỦA KHUNG GIỜ
            if ($order->slot_id) {
                $slot = TimeSlot::find($order->slot_id);
                if ($slot) {
                    $slot->increment('booked_count', $order->quantity);

                    if ($slot->booked_count >= $slot->capacity) {
                        $slot->update(['status' => 'full']);
                    }
                }
            }

            // Tạo QR Code
            $qr = app(QrTicketService::class)->generate($order);

            // Gửi Email
            if ($user?->email) {
                try {
                    Mail::to($user->email)->send(new BookingConfirmedMail($order->load('slot.ticket', 'slot.location'), $qr));
                } catch (\Exception $mailErr) {
                    \Log::warning('Lỗi gửi mail: ' . $mailErr->getMessage());
                }
            }

            // Dọn dẹp Giỏ hàng
            session()->forget(['cart', 'coupon', 'coupon_code']);
            DB::commit();

            // Chuyển hướng sang trang Profile/Lịch sử
            return redirect()->route('profile.index')->with([
                'payment_success' => true,
                'order_id'        => $order->id,
                'total_amount'    => $order->total_amount,
                'method'          => $order->payment_method,
                'qr_code'         => $qr->qr_code,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // Các hàm phụ trợ
    public function checkCoupon(Request $request)
    {
        $code = $request->code;
        $totalAmount = $request->total_amount;
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) return response()->json(['status' => 'error', 'message' => 'Mã giảm giá không tồn tại!']);
        if ($coupon->expiration_date && Carbon::now()->gt($coupon->expiration_date)) return response()->json(['status' => 'error', 'message' => 'Mã này đã hết hạn!']);
        if ($coupon->quantity <= 0) return response()->json(['status' => 'error', 'message' => 'Mã này đã hết lượt sử dụng!']);

        $discount = $coupon->type == 'percent' ? ($totalAmount * $coupon->value) / 100 : $coupon->value;
        if ($discount > $totalAmount) $discount = $totalAmount;

        session()->put('coupon', ['code' => $coupon->code, 'discount' => $discount, 'type' => $coupon->type, 'value' => $coupon->value]);
        return response()->json(['status' => 'success', 'message' => 'Áp dụng mã thành công!', 'discount' => $discount, 'new_total' => $totalAmount - $discount]);
    }

    public function removeCoupon()
    {
        session()->forget('coupon');
        return back()->with('success', 'Đã gỡ mã giảm giá thành công!');
    }

   public function getSlots(Request $request)
{
    // Validate dữ liệu đầu vào
    if (!$request->ticket_id || !$request->date) {
        return response()->json([]);
    }

    $slots = \App\Models\TimeSlot::where('ticket_id', $request->ticket_id)
        ->where('date', $request->date)
        ->where('status', 'open') // Chỉ lấy slot đang mở
        ->whereColumn('booked_count', '<', 'capacity') // Chỉ lấy slot còn chỗ
        ->orderBy('start_time', 'asc')
        ->get()
        ->map(function($slot) {
            return [
                'id' => $slot->id,
                'label' => \Carbon\Carbon::parse($slot->start_time)->format('H:i') . ' - ' . \Carbon\Carbon::parse($slot->end_time)->format('H:i'),
                'available' => $slot->capacity - $slot->booked_count
            ];
        });

    return response()->json($slots);
}
}