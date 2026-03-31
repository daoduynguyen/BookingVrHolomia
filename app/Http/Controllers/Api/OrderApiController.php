<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\TimeSlot;
use App\Models\Coupon;
use App\Mail\BookingConfirmedMail;
use App\Jobs\SendTelegramNotification;
use App\Services\QrTicketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class OrderApiController extends Controller
{
    // ==========================================
    // DANH SÁCH ĐƠN HÀNG CỦA USER
    // ==========================================
    public function index(Request $request)
    {
        $orders = Order::with(['orderItems.ticket', 'slot', 'location'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $orders->map(fn($o) => $this->formatOrder($o)),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    // ==========================================
    // CHI TIẾT 1 ĐƠN HÀNG
    // ==========================================
    public function show(Request $request, $id)
    {
        $order = Order::with(['orderItems.ticket', 'orderItems.review', 'slot', 'location'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatOrder($order, detailed: true),
        ]);
    }

    // ==========================================
    // ĐẶT VÉ MỚI (thay thế flow web: bookingForm → confirmToCart → finalPayment)
    // Mobile gom hết vào 1 request
    // ==========================================
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'slot_id' => 'nullable|exists:time_slots,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'payment_method' => 'required|in:wallet,cod,banking',
            'note' => 'nullable|string|max:500',
            'quantity' => 'nullable|integer|min:1|max:20',
            // Nếu vé có ticket_types: [{"name":"Vé Thường","qty":2},{"name":"VIP","qty":1}]
            'ticket_types' => 'nullable|array',
            'ticket_types.*.name' => 'required_with:ticket_types|string',
            'ticket_types.*.qty' => 'required_with:ticket_types|integer|min:1',
            'coupon_code' => 'nullable|string',
        ]);

        $ticket = Ticket::findOrFail($request->ticket_id);

        // Vé không khả dụng
        if ($ticket->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Vé này hiện không khả dụng.',
            ], 422);
        }

        $date = Carbon::parse($request->booking_date);
        $user = $request->user();

        // Lấy % phụ thu cuối tuần
        $surchargeRate = (int) (\App\Models\Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? 0) / 100;

        DB::beginTransaction();
        try {
            // ── KIỂM TRA SLOT ──
            $slot = null;
            if ($request->slot_id) {
                $slot = TimeSlot::lockForUpdate()->findOrFail($request->slot_id);
                $totalQty = $request->ticket_types
                    ? array_sum(array_column($request->ticket_types, 'qty'))
                    : ($request->quantity ?? 1);

                if (!$slot->isBookable($totalQty)) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Khung giờ này đã hết chỗ.',
                    ], 422);
                }
            }

            // ── TÍNH TIỀN ──
            [$totalAmount, $discountAmount, $items, $couponCodeUsed] = $this->calculateTotal(
                ticket: $ticket,
                request: $request,
                date: $date,
                surchargeRate: $surchargeRate,
            );

            $finalTotal = $totalAmount - $discountAmount;

            // ── KIỂM TRA VÍ ──
            if ($request->payment_method === 'wallet') {
                $freshUser = \App\Models\User::lockForUpdate()->find($user->id);
                if ($freshUser->balance < $finalTotal) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Số dư ví không đủ. Vui lòng nạp thêm tiền.',
                        'balance' => (float) $freshUser->balance,
                        'required' => (float) $finalTotal,
                    ], 422);
                }
            }

            // ── LẤY LOCATION ──
            $locationId = $ticket->locations->first()?->id;

            // ── TẠO ĐƠN HÀNG ──
            $order = Order::create([
                'user_id' => $user->id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email ?? $user->email,
                'shipping_address' => $user->address ?? 'Tại quầy',
                'total_amount' => $finalTotal,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCodeUsed,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'note' => $request->note ?? '',
                'booking_date' => $request->booking_date,
                'slot_id' => $request->slot_id,
                'quantity' => array_sum(array_column($items, 'quantity')),
                'location_id' => $locationId,
            ]);

            // ── TẠO ORDER ITEMS ──
            foreach ($items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'ticket_id' => $ticket->id,
                    'ticket_name' => $ticket->name . ($item['type'] ? ' - ' . $item['type'] : ''),
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            // ── THANH TOÁN VÍ ──
            if ($request->payment_method === 'wallet') {
                $freshUser = \App\Models\User::lockForUpdate()->find($user->id);
                $balanceBefore = (float) $freshUser->balance;
                $freshUser->decrement('balance', $finalTotal);
                $order->update(['status' => 'paid']);

                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'payment',
                    'amount' => $finalTotal,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore - $finalTotal,
                    'description' => 'Thanh toán đơn #' . $order->id,
                    'reference_code' => 'DH' . $order->id,
                    'order_id' => $order->id,
                    'status' => 'completed',
                ]);
            } elseif ($request->payment_method === 'banking') {
                $order->update(['notes' => 'DH' . $order->id]);
            }

            // ── BANKING: tạm giữ slot, gửi thông tin QR ──
            if ($request->slot_id) {
                TimeSlot::lockForUpdate()->find($request->slot_id)
                        ?->incrementBooked($order->quantity);
            }

            // ── TẠO QR VÉ ──
            $qr = app(QrTicketService::class)->generate($order);

            // ── CỘNG ĐIỂM + PLAY COUNT (chỉ khi không phải banking) ──
            if ($request->payment_method !== 'banking') {
                Ticket::where('id', $ticket->id)->increment('play_count', $order->quantity);

                $pointsEarned = (int) floor($order->total_amount / 1000);
                $user->increment('points', $pointsEarned);

                // Gửi email xác nhận
                $email = $request->customer_email ?? $user->email;
                if ($email) {
                    try {
                        Mail::to($email)->send(new BookingConfirmedMail($order));
                    } catch (\Exception $e) {
                        Log::warning('API: Lỗi gửi mail xác nhận: ' . $e->getMessage());
                    }
                }
            }

            // ── TRỪ COUPON ──
            if ($couponCodeUsed) {
                Coupon::where('code', $couponCodeUsed)
                    ->where('quantity', '>', 0)
                    ->decrement('quantity');
            }

            // ── TELEGRAM ──
            try {
                SendTelegramNotification::dispatch(
                    "🚨 *ĐƠN ĐẶT VÉ MỚI (Mobile)* 🚨\n\n"
                    . "👤 *Khách:* {$order->customer_name}\n"
                    . "📞 *SĐT:* {$order->customer_phone}\n"
                    . "🕹️ *Vé:* {$ticket->name} x{$order->quantity}\n"
                    . "💰 *Tổng:* " . number_format($order->total_amount) . " VNĐ\n"
                    . "💳 *TT:* " . strtoupper($request->payment_method)
                );
            } catch (\Exception $e) {
                Log::error('API: Lỗi Telegram: ' . $e->getMessage());
            }

            DB::commit();

            // ── RESPONSE ──
            $responseData = [
                'order' => $this->formatOrder($order->fresh(['orderItems', 'slot', 'location'])),
                'qr_code' => $qr->qr_code ?? null,
            ];

            // Nếu banking: trả thêm thông tin để app hiển thị trang QR chuyển khoản
            if ($request->payment_method === 'banking') {
                $bankBin = \App\Models\Setting::where('key', 'bank_bin')->value('value') ?? '970436';
                $bankAccount = \App\Models\Setting::where('key', 'bank_account')->value('value') ?? '';
                $bankOwner = \App\Models\Setting::where('key', 'bank_owner')->value('value') ?? 'HOLOMIA VR';
                $refCode = 'DH' . $order->id;

                $responseData['banking'] = [
                    'ref_code' => $refCode,
                    'amount' => $finalTotal,
                    'bank_name' => \App\Models\Setting::where('key', 'bank_name')->value('value') ?? 'Vietcombank',
                    'bank_account' => $bankAccount,
                    'bank_owner' => $bankOwner,
                    'qr_url' => "https://img.vietqr.io/image/{$bankBin}-{$bankAccount}-compact2.png"
                        . "?amount={$finalTotal}&addInfo=" . urlencode($refCode)
                        . "&accountName=" . urlencode($bankOwner),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Đặt vé thành công!',
                'data' => $responseData,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('API OrderStore error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại.',
            ], 500);
        }
    }

    // ==========================================
    // HỦY / HOÀN TIỀN ĐƠN HÀNG
    // ==========================================
    public function refund(Request $request, $id)
    {
        $order = Order::with('slot')->findOrFail($id);
        $user = $request->user();

        // Kiểm tra quyền sở hữu
        if ($order->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền hủy đơn hàng này.',
            ], 403);
        }

        $now = Carbon::now();
        $startTime = $order->slot?->start_time ?? '00:00:00';
        $playTime = Carbon::parse($order->booking_date . ' ' . $startTime);

        // Quá 24h từ lúc đặt
        if ($now->diffInHours($order->created_at) > 24) {
            return response()->json([
                'success' => false,
                'message' => 'Đã quá 24 giờ kể từ lúc đặt, không thể hoàn vé.',
            ], 422);
        }

        // Còn dưới 48h đến giờ chơi
        if ($now->diffInHours($playTime, false) < 48) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ được hoàn vé khi còn ít nhất 48 giờ trước giờ chơi.',
            ], 422);
        }

        DB::transaction(function () use ($order, $user) {
            if ($order->payment_method !== 'cod') {
                $balanceBefore = (float) $user->fresh()->balance;
                $user->increment('balance', $order->total_amount);

                \App\Models\WalletTransaction::create([
                    'user_id' => $user->id,
                    'type' => 'refund',
                    'amount' => $order->total_amount,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceBefore + $order->total_amount,
                    'description' => 'Hoàn tiền đơn #' . $order->id,
                    'reference_code' => 'REFUND' . $order->id,
                    'order_id' => $order->id,
                    'status' => 'completed',
                ]);
            }

            // Nhả slot
            if ($order->slot_id) {
                TimeSlot::find($order->slot_id)?->decrement('booked_count', $order->quantity);
            }

            $order->update(['status' => 'refunded']);
        });

        return response()->json([
            'success' => true,
            'message' => 'Hoàn vé thành công! Tiền đã được cộng vào ví.',
            'data' => $this->formatOrder($order->fresh()),
        ]);
    }

    // ==========================================
    // KIỂM TRA TRẠNG THÁI ĐƠN (polling sau banking)
    // ==========================================
    public function checkStatus(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'order_id' => $order->id,
                'status' => $order->status,
                'paid' => $order->status === 'paid',
            ],
        ]);
    }

    // ==========================================
    // HELPER — Tính tổng tiền đơn hàng
    // ==========================================
    private function calculateTotal(Ticket $ticket, Request $request, Carbon $date, float $surchargeRate): array
    {
        $totalAmount = 0;
        $discountAmount = 0;
        $items = [];
        $couponCodeUsed = null;

        // Validate và lấy coupon
        $coupon = null;
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', $request->coupon_code)
                ->where('quantity', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', now());
                })
                ->first();
            if ($coupon) {
                $couponCodeUsed = $coupon->code;
            }
        }

        $calculateDiscount = function (float $subTotal) use ($coupon): float {
            if (!$coupon)
                return 0;
            $discount = $coupon->type === 'percent'
                ? ($subTotal * $coupon->value) / 100
                : $coupon->value;
            return min($discount, $subTotal);
        };

        // Vé có ticket_types
        if ($request->ticket_types && $ticket->ticket_types) {
            foreach ($request->ticket_types as $typeReq) {
                $typeName = $typeReq['name'];
                $qty = (int) $typeReq['qty'];
                if ($qty <= 0)
                    continue;

                // Tìm giá của loại vé
                $basePrice = $ticket->price;
                foreach ($ticket->ticket_types as $t) {
                    if ($t['name'] === $typeName) {
                        $basePrice = (int) $t['price'];
                        break;
                    }
                }

                $finalPrice = $basePrice;
                if ($date->isWeekend()) {
                    $finalPrice += $basePrice * $surchargeRate;
                }

                $subTotal = $finalPrice * $qty;
                $totalAmount += $subTotal;
                $discountAmount += $calculateDiscount($subTotal);

                $items[] = ['type' => $typeName, 'quantity' => $qty, 'price' => $finalPrice];
            }
        } else {
            // Vé thường
            $qty = max(1, (int) ($request->quantity ?? 1));
            $price = $ticket->price;
            if ($date->isWeekend()) {
                $price += $price * $surchargeRate;
            }

            $subTotal = $price * $qty;
            $totalAmount += $subTotal;
            $discountAmount += $calculateDiscount($subTotal);

            $items[] = ['type' => null, 'quantity' => $qty, 'price' => $price];
        }

        return [$totalAmount, $discountAmount, $items, $couponCodeUsed];
    }

    // ==========================================
    // HELPER — Format order trả về
    // ==========================================
    private function formatOrder(Order $order, bool $detailed = false): array
    {
        $data = [
            'id' => $order->id,
            'status' => $order->status,
            'payment_method' => $order->payment_method,
            'booking_date' => $order->booking_date,
            'total_amount' => (float) $order->total_amount,
            'discount_amount' => (float) $order->discount_amount,
            'quantity' => $order->quantity,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'note' => $order->note,
            'location' => $order->location?->name,
            'slot' => $order->slot ? [
                'id' => $order->slot->id,
                'start_time' => $order->slot->start_time,
                'end_time' => $order->slot->end_time,
            ] : null,
            'created_at' => $order->created_at->format('d/m/Y H:i'),
        ];

        if ($detailed) {
            $data['items'] = $order->orderItems->map(fn($item) => [
                'ticket_name' => $item->ticket_name,
                'quantity' => $item->quantity,
                'price' => (float) $item->price,
                'subtotal' => (float) ($item->price * $item->quantity),
                'reviewed' => $item->review !== null,
            ]);
        }

        return $data;
    }
}