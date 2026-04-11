<?php

namespace App\Services;

use App\Models\Order;

class QrTicketService
{
    public function generate(Order $order)
    {
        // Tạo token QR (dùng làm fallback token-based URL)
        $token = 'HOLOMIA-TKN-' . strtoupper(bin2hex(random_bytes(8)));

        // Lưu token vào đơn hàng (nếu cột tồn tại)
        try {
            $order->qr_token = $token;
            $order->save();
        } catch (\Throwable $e) {
            // Nếu không thể lưu (cột chưa tồn tại), tiếp tục nhưng không dừng flow
        }

        // Tạo URL quét bằng token (public, fallback)
        try {
            $scanUrl = \Illuminate\Support\Facades\URL::route('ticket.scan.token', ['token' => $token], true);
        } catch (\Throwable $e) {
            // Fallback to a simple token path if route generation fails
            $scanUrl = url('/scan-ticket/token/' . $token);
        }

        // Sử dụng API tạo QR miễn phí (không cần cài thư viện nặng máy)
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($scanUrl);

        return (object) [
            'qr_code'     => $qrImageUrl,
            'code_string' => $token,
            'scan_url'    => $scanUrl,
        ];
    }
}