<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé Điện Tử Holomia VR</title>
</head>
<body style="background-color: #121212; color: #ffffff; font-family: 'Be Vietnam Pro', sans-serif; margin: 0; padding: 20px;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #1a1d20; border: 1px solid #333; border-radius: 10px; overflow: hidden;">
        
        <div style="background-color: #0dcaf0; padding: 20px; text-align: center;">
            <h1 style="color: #000000; margin: 0; font-size: 24px; text-transform: uppercase; letter-spacing: 2px;">Holomia VR</h1>
            <p style="color: #000000; margin: 5px 0 0 0; font-weight: bold;">Xác nhận đặt vé thành công!</p>
        </div>

        <div style="padding: 30px 20px;">
            <p style="font-size: 16px;">Xin chào <strong>{{ $order->customer_name }}</strong>,</p>
            <p style="color: #cccccc; line-height: 1.5;">Cảm ơn bạn đã lựa chọn trải nghiệm thực tế ảo tại Holomia VR. Dưới đây là vé điện tử (E-Ticket) của bạn.</p>

            <div style="background-color: #212529; border: 1px dashed #0dcaf0; border-radius: 8px; text-align: center; padding: 20px; margin: 25px 0;">
                <p style="margin: 0 0 10px 0; color: #888888; font-size: 14px;">MÃ CHECK-IN TẠI QUẦY</p>
                
               <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('ticket.scan', $order->id)) }}" alt="QR Code Check-in" style="border: 5px solid #ffffff; border-radius: 5px;">
                
                <h3 style="color: #0dcaf0; margin: 15px 0 0 0; font-size: 20px;">{{ $order->id }}</h3>
            </div>

            <table style="width: 100%; border-collapse: collapse; margin-top: 20px; color: #ffffff;">
                <p><strong>Cơ sở trải nghiệm:</strong> <span style="color: #ffc107;">{{ $order->location->name ?? 'Holomia VR' }}</span></p>
                <tr style="border-bottom: 1px solid #333;">
                    <td style="padding: 10px 0; color: #888888;">Tổng số lượng vé:</td>
                    <td style="padding: 10px 0; text-align: right; font-weight: bold;">{{ $order->quantity ?? 'N/A' }} vé</td>
                </tr>
                <tr style="border-bottom: 1px solid #333;">
                    <td style="padding: 10px 0; color: #888888;">Tổng thanh toán:</td>
                    <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #0dcaf0;">{{ number_format($order->total_amount) }} VNĐ</td>
                </tr>
                <tr>
                    <td style="padding: 10px 0; color: #888888;">Phương thức:</td>
                    <td style="padding: 10px 0; text-align: right; text-transform: uppercase;">{{ $order->payment_method == 'cod' ? 'Tiền mặt' : 'Chuyển khoản' }}</td>
                </tr>
            </table>

            <p style="color: #ff4444; font-size: 13px; text-align: center; margin-top: 30px; font-style: italic;">
                *Vui lòng giữ mã QR này bảo mật và xuất trình cho nhân viên khi đến trải nghiệm.
            </p>
        </div>

        <div style="background-color: #111111; padding: 15px; text-align: center; font-size: 12px; color: #666666;">
            &copy; {{ date('Y') }} Holomia VR. All rights reserved.
        </div>
    </div>

</body>
</html>