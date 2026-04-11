<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé Điện Tử Holomia VR</title>
</head>
<body style="margin:0;padding:0;background-color:#0d0d1a;font-family:'Segoe UI',sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center" style="padding:40px 20px;">
<table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;">

  <!-- HEADER -->
  <tr><td style="background:linear-gradient(135deg,#ff6b35,#f7931e);border-radius:16px 16px 0 0;padding:36px 40px;text-align:center;">
    <div style="font-size:36px;margin-bottom:10px;">🥽</div>
    <div style="color:#fff;font-size:24px;font-weight:700;letter-spacing:1px;">HOLOMIA VR</div>
    <div style="color:rgba(255,255,255,0.85);font-size:11px;letter-spacing:3px;text-transform:uppercase;margin-top:4px;">Xác nhận đặt vé thành công</div>
  </td></tr>

  <!-- BODY -->
  <tr><td style="background:#1a1d2e;padding:40px;">

    <p style="font-size:18px;font-weight:700;color:#ffffff;margin:0 0 10px;">Xin chào {{ $order->customer_name }},</p>
    <p style="font-size:14px;color:#aaa;line-height:1.7;margin:0 0 32px;">Cảm ơn bạn đã lựa chọn trải nghiệm thực tế ảo tại <strong style="color:#ff6b35;">Holomia VR</strong>. Dưới đây là vé điện tử (E-Ticket) của bạn.</p>

    <!-- QR Box -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
    <tr><td style="background:#12152a;border:2px dashed #ff6b35;border-radius:16px;padding:32px;text-align:center;">
      <div style="font-size:11px;font-weight:700;color:#ff6b35;letter-spacing:3px;text-transform:uppercase;margin-bottom:20px;">🎟️ Mã Check-in tại quầy</div>
        @php
          // Prefer token-based QR (persistent) if available, else fallback to signed URL
          $qrImageSrc = null;
          if (!empty($order->qr_token)) {
            $tokenUrl = route('ticket.scan.token', ['token' => $order->qr_token]);
            $qrImageSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($tokenUrl);
          } else {
            $signed = URL::temporarySignedRoute('ticket.scan', now()->addHours(72), ['id' => $order->id]);
            $qrImageSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($signed);
          }
        @endphp
        <img src="{{ $qrImageSrc }}"
           alt="QR Code Check-in"
           style="border:6px solid #ffffff;border-radius:12px;display:block;margin:0 auto 16px;">
      <div style="font-size:28px;font-weight:700;color:#ff6b35;letter-spacing:4px;">{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</div>
      <div style="font-size:12px;color:#666;margin-top:6px;">Mã đơn hàng</div>
    </td></tr>
    </table>

    <!-- Order Info -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
      <tr><td colspan="2" style="padding-bottom:16px;">
        <div style="font-size:11px;font-weight:700;color:#ff6b35;letter-spacing:2px;text-transform:uppercase;">📋 Thông tin đơn hàng</div>
      </td></tr>

      <tr style="border-bottom:1px solid #2a2d3e;">
        <td style="padding:12px 0;font-size:13px;color:#888;">Cơ sở trải nghiệm</td>
        <td style="padding:12px 0;font-size:13px;color:#ffc107;font-weight:600;text-align:right;">{{ $order->location->name ?? 'Holomia VR' }}</td>
      </tr>

      @if($order->booking_date)
      <tr style="border-bottom:1px solid #2a2d3e;">
        <td style="padding:12px 0;font-size:13px;color:#888;">Ngày trải nghiệm</td>
        <td style="padding:12px 0;font-size:13px;color:#fff;font-weight:600;text-align:right;">{{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</td>
      </tr>
      @endif

      <tr style="border-bottom:1px solid #2a2d3e;">
        <td style="padding:12px 0;font-size:13px;color:#888;">Số lượng vé</td>
        <td style="padding:12px 0;font-size:13px;color:#fff;font-weight:600;text-align:right;">{{ $order->quantity ?? 'N/A' }} vé</td>
      </tr>

      <tr style="border-bottom:1px solid #2a2d3e;">
        <td style="padding:12px 0;font-size:13px;color:#888;">Phương thức thanh toán</td>
        <td style="padding:12px 0;font-size:13px;color:#fff;text-align:right;">
          @php
            $methodLabel = match($order->payment_method) {
              'cod'     => '💵 Tiền mặt tại quầy',
              'banking' => '🏦 Chuyển khoản',
              'wallet'  => '👛 Ví Holomia',
              default   => strtoupper($order->payment_method),
            };
          @endphp
          {{ $methodLabel }}
        </td>
      </tr>

      <tr>
        <td style="padding:16px 0 8px;font-size:15px;color:#aaa;font-weight:600;">Tổng thanh toán</td>
        <td style="padding:16px 0 8px;font-size:20px;color:#ff6b35;font-weight:700;text-align:right;">{{ number_format($order->total_amount) }} VNĐ</td>
      </tr>
    </table>

    <!-- Warning -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
    <tr><td style="background:#1f1015;border:1px solid #ff4444;border-radius:10px;padding:16px 20px;">
      <p style="font-size:13px;color:#ff6b6b;margin:0;line-height:1.6;">⚠️ <strong>Lưu ý:</strong> Vui lòng giữ mã QR này bảo mật và xuất trình cho nhân viên khi đến trải nghiệm. Mã chỉ có giá trị sử dụng 1 lần.</p>
    </td></tr>
    </table>

    <!-- Contact -->
    <table width="100%" cellpadding="0" cellspacing="0">
      <tr>
        <td style="padding:6px 0;font-size:13px;color:#777;">📞 Hotline: <span style="color:#fff;">024.9999.7777</span></td>
      </tr>
      <tr>
        <td style="padding:6px 0;font-size:13px;color:#777;">🌐 Website: <a href="https://holomia.shop" style="color:#ff6b35;text-decoration:none;">holomia.shop</a></td>
      </tr>
    </table>

  </td></tr>

  <!-- FOOTER -->
  <tr><td style="background:#0a0a14;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
    <p style="color:rgba(255,255,255,0.3);font-size:12px;margin:0;">© {{ date('Y') }} Holomia VR. All rights reserved.</p>
    <p style="margin:8px 0 0;"><a href="https://holomia.shop" style="color:#ff6b35;font-size:12px;text-decoration:none;">holomia.shop</a></p>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>