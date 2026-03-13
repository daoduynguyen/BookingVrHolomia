<?php

namespace App\Services;

use App\Models\Order;

class QrTicketService
{
    public function generate(Order $order)
    {
        // Tạo nội dung mã QR chứa ID đơn hàng và mã bảo mật ngẫu nhiên
        $qrData = 'HOLOMIA-REF-' . $order->id . '-' . strtoupper(bin2hex(random_bytes(4)));

        // Sử dụng API tạo QR miễn phí (không cần cài thư viện nặng máy)
        $qrImageUrl = "https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=" . urlencode($qrData);

        return (object) [
            'qr_code'     => $qrImageUrl,
            'code_string' => $qrData
        ];
    }
}