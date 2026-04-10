<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\WalletTransaction;
use App\Models\Order;
use App\Models\Setting;
use App\Models\User;

class WalletController extends Controller
{
    // =============================================
    // TRANG NẠP VÍ (Khách xem QR + nội dung CK)
    // =============================================
    public function topupPage(Request $request)
    {
        $user = Auth::user();
        $amount = (int) $request->input('amount', 100000);

        // Mã nội dung chuyển khoản duy nhất: NAP + user_id + timestamp
        $refCode = 'NAP' . $user->id . time();

        // Lưu pending transaction
        $tx = WalletTransaction::create([
            'user_id' => $user->id,
            'type' => 'topup',
            'amount' => $amount,
            'balance_before' => $user->balance,
            'balance_after' => $user->balance,
            'description' => 'Nạp tiền vào ví Holomia',
            'reference_code' => $refCode,
            'status' => 'pending',
        ]);

        // Thông tin ngân hàng từ settings
        $settings = Setting::whereIn('key', ['bank_bin', 'bank_account', 'bank_name', 'bank_owner'])->pluck('value', 'key');
        $bankBin = $settings['bank_bin'] ?? '970436';
        $bankAccount = $settings['bank_account'] ?? '1234567890';
        $bankName = $settings['bank_name'] ?? 'Vietcombank';
        $bankOwner = $settings['bank_owner'] ?? 'HOLOMIA VR';

        // QR VietQR chuẩn
        $qrUrl = "https://img.vietqr.io/image/{$bankBin}-{$bankAccount}-compact2.png"
            . "?amount={$amount}&addInfo=" . urlencode($refCode)
            . "&accountName=" . urlencode($bankOwner);

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->latest()->take(10)->get();

        return view('wallet.topup', compact(
            'user',
            'amount',
            'refCode',
            'qrUrl',
            'bankName',
            'bankAccount',
            'bankOwner',
            'tx',
            'transactions'
        ));
    }

    // =============================================
    // SEPAY WEBHOOK — Nhận thông báo chuyển tiền
    // =============================================
    public function sepayWebhook(Request $request)
    {
        Log::info('Sepay Webhook:', $request->all());

        // Xác thực token từ Sepay
        $token = Setting::where('key', 'sepay_api_token')->value('value');
        $authHeader = $request->header('Authorization');

        if ($token && $authHeader !== 'Apikey ' . $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $content = $request->input('content', '');       // Nội dung CK
        $amount = (float) $request->input('transferAmount', 0);
        $transferType = $request->input('transferType', ''); // in/out

        if ($transferType !== 'in' || $amount <= 0) {
            return response()->json(['success' => false, 'message' => 'Ignored']);
        }

        // ---- TRƯỜNG HỢP 1: Nạp ví (nội dung chứa NAP<user_id>) ----
        if (preg_match('/NAP(\d+)/i', $content, $m)) {
            $this->handleTopup($content, $amount);
        }

        // ---- TRƯỜNG HỢP 2: Thanh toán đơn hàng (nội dung chứa DH<order_id>) ----
        if (preg_match('/DH(\d+)/i', $content, $m)) {
            $this->handleOrderPayment((int) $m[1], $amount);
        }

        return response()->json(['success' => true]);
    }

    // Xử lý nạp ví
    private function handleTopup(string $content, float $amount): void
    {
        $tx = null;
        if (preg_match('/(NAP\d+)/i', $content, $m)) {
            $refCode = strtoupper($m[1]);
            // Tìm pending transaction theo reference_code
            $tx = WalletTransaction::where('reference_code', 'LIKE', '%' . $refCode . '%')
                ->where('type', 'topup')
                ->where('status', 'pending')
                ->first();
        }

        if (!$tx) {
            Log::warning('Sepay topup: Không tìm thấy transaction cho nội dung: ' . $content);
            return;
        }

        DB::transaction(function () use ($tx, $amount) {
            $user = User::lockForUpdate()->find($tx->user_id);
            if (!$user)
                return;

            $before = (float) $user->balance;
            $user->increment('balance', $amount);
            $after = $before + $amount;

            $tx->update([
                'status' => 'completed',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
            ]);

            Log::info("Nạp ví thành công: User #{$user->id} +{$amount}đ → Số dư: {$after}đ");
        });
    }

    // Xử lý thanh toán đơn hàng qua chuyển khoản
    private function handleOrderPayment(int $orderId, float $amount): void
    {
        $order = Order::find($orderId);

        if (!$order) {
            Log::warning("Sepay: Không tìm thấy đơn #{$orderId}");
            return;
        }

        if ($order->status !== 'pending') {
            Log::info("Sepay: Đơn #{$orderId} đã xử lý rồi, bỏ qua");
            return;
        }

        // Kiểm tra số tiền chuyển >= tổng đơn (cho phép sai lệch 1000đ)
        if ($amount < ($order->total_amount - 1000)) {
            Log::warning("Sepay: Đơn #{$orderId} thiếu tiền. Cần {$order->total_amount}, nhận {$amount}");
            return;
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'paid']);

            // Cộng play_count cho từng vé trong đơn
            foreach ($order->orderItems as $item) {
                \App\Models\Ticket::where('id', $item->ticket_id)
                    ->increment('play_count', $item->quantity);
            }

            // Cộng điểm và xét thăng hạng
            if ($order->user_id) {
                $user = User::find($order->user_id);
                if ($user) {
                    \App\Services\LoyaltyService::addPoints($user, $order);
                }
            }


            // ✅ Lấy email: ưu tiên customer_email (bao gồm cả guest), fallback về user
            $recipientEmail = $order->customer_email
                ?? ($order->user ? $order->user->email : null);

            if ($recipientEmail) {
                try {
                    \Illuminate\Support\Facades\Mail::to($recipientEmail)
                        ->send(new \App\Mail\BookingConfirmedMail($order));
                } catch (\Exception $e) {
                    Log::warning('Sepay: Lỗi gửi mail xác nhận: ' . $e->getMessage());
                }
            }


            Log::info("Đơn #{$order->id} đã được xác nhận thanh toán qua Sepay");
        });
    }

    // =============================================
    // AJAX — Kiểm tra trạng thái nạp ví (polling)
    // =============================================
    public function checkTopupStatus(Request $request)
    {
        $refCode = $request->input('ref_code');
        $tx = WalletTransaction::where('reference_code', $refCode)
            ->where('user_id', Auth::id())
            ->first();

        if (!$tx) {
            return response()->json(['status' => 'not_found']);
        }

        return response()->json([
            'status' => $tx->status,
            'amount' => $tx->amount,
            'balance' => Auth::user()->fresh()->balance,
        ]);
    }
}