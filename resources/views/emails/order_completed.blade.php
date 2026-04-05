<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cảm ơn bạn đã trải nghiệm - Holomia VR</title>
</head>
<body style="background-color: #0d0d0d; color: #ffffff; font-family: Arial, sans-serif; margin: 0; padding: 20px;">

    <div style="max-width: 600px; margin: 0 auto; background-color: #1a1d20; border: 1px solid #2a2d30; border-radius: 12px; overflow: hidden;">

        <div style="background: linear-gradient(135deg, #198754 0%, #0f5132 100%); padding: 30px 20px; text-align: center;">
            <div style="font-size: 48px; margin-bottom: 10px;">🎉</div>
            <h1 style="color: #ffffff; margin: 0; font-size: 22px; text-transform: uppercase; letter-spacing: 2px;">Holomia VR</h1>
            <p style="color: #a3cfbb; margin: 8px 0 0 0; font-size: 16px; font-weight: bold;">Cảm ơn bạn đã trải nghiệm!</p>
        </div>

        <div style="padding: 30px 25px;">
            <p style="font-size: 16px; margin-bottom: 5px;">Xin chào <strong style="color: #0dcaf0;">{{ $order->customer_name }}</strong>,</p>
            <p style="color: #aaaaaa; line-height: 1.7; margin-top: 8px;">
                Đơn vé <strong style="color: #ffffff;">{{ $order->id }}</strong> của bạn đã được đánh dấu
                <span style="color: #198754; font-weight: bold;">✅ Hoàn thành</span>.
                Hy vọng bạn đã có những giây phút trải nghiệm thực tế ảo thật tuyệt vời tại Holomia VR!
            </p>

            <div style="background-color: #212529; border: 1px solid #2a2d30; border-radius: 10px; padding: 20px; margin: 25px 0;">
                <h3 style="margin: 0 0 15px 0; color: #0dcaf0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                    📋 Chi tiết đơn hàng
                </h3>
                <table style="width: 100%; border-collapse: collapse; color: #ffffff;">
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding: 10px 0; color: #888888; font-size: 14px;">Mã đơn hàng</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #ffc107;">{{ $order->id }}</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding: 10px 0; color: #888888; font-size: 14px;">Ngày trải nghiệm</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: bold;">
                            {{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}
                        </td>
                    </tr>
                    @if($order->slot)
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding: 10px 0; color: #888888; font-size: 14px;">Khung giờ</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: bold;">
                            {{ \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') }}
                            – {{ \Carbon\Carbon::parse($order->slot->end_time)->format('H:i') }}
                        </td>
                    </tr>
                    @endif
                    @if($order->location)
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding: 10px 0; color: #888888; font-size: 14px;">Cơ sở</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $order->location->name }}</td>
                    </tr>
                    @endif
                    <tr style="border-bottom: 1px solid #333;">
                        <td style="padding: 10px 0; color: #888888; font-size: 14px;">Số lượng vé</td>
                        <td style="padding: 10px 0; text-align: right;">{{ $order->quantity ?? '—' }} vé</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 0; color: #888888; font-size: 14px;">Tổng thanh toán</td>
                        <td style="padding: 10px 0; text-align: right; font-weight: bold; color: #0dcaf0; font-size: 18px;">
                            {{ number_format($order->total_amount) }} VNĐ
                        </td>
                    </tr>
                </table>
            </div>

            @if($order->details && $order->details->count() > 0)
            <div style="background-color: #212529; border: 1px solid #2a2d30; border-radius: 10px; padding: 20px; margin: 20px 0;">
                <h3 style="margin: 0 0 15px 0; color: #0dcaf0; font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">
                    🎮 Trò chơi đã trải nghiệm
                </h3>
                @foreach($order->details as $item)
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed #333;">
                    <span style="color: #cccccc;">🕹️ {{ $item->ticket->name ?? 'Vé VR' }}</span>
                    <span style="color: #ffc107; font-weight: bold;">x{{ $item->quantity }}</span>
                </div>
                @endforeach
            </div>
            @endif

            <div style="background: linear-gradient(135deg, #0d2137 0%, #0a1929 100%); border: 1px solid #0dcaf0; border-radius: 10px; padding: 20px; margin: 25px 0; text-align: center;">
                <p style="margin: 0 0 8px 0; font-size: 15px; color: #ffffff;">⭐ Bạn thấy trải nghiệm như thế nào?</p>
                <p style="margin: 0 0 15px 0; color: #888888; font-size: 13px;">Đánh giá của bạn giúp chúng tôi cải thiện dịch vụ.</p>
                <a href="{{ url('/tro-choi/' . ($order->details->first()->ticket_id ?? '')) }}"
                   style="display: inline-block; background-color: #0dcaf0; color: #000000; text-decoration: none; font-weight: bold; padding: 10px 25px; border-radius: 25px; font-size: 14px;">
                    ✍️ Viết đánh giá ngay
                </a>
            </div>

            <div style="text-align: center; margin-top: 20px;">
                <a href="{{ url('/danh-sach-ve') }}"
                   style="display: inline-block; background-color: #198754; color: #ffffff; text-decoration: none; font-weight: bold; padding: 10px 25px; border-radius: 25px; font-size: 14px;">
                    🎮 Khám phá trò chơi mới
                </a>
            </div>
        </div>

        <div style="background-color: #111111; padding: 20px; text-align: center; font-size: 12px; color: #555555;">
            <p style="margin: 0 0 5px 0;">© {{ date('Y') }} Holomia VR. All rights reserved.</p>
            <p style="margin: 0; color: #444444;">Đây là email tự động, vui lòng không trả lời email này.</p>
        </div>
    </div>

</body>
</html>