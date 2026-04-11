<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phản hồi từ Holomia VR</title>
</head>
<body style="margin:0;padding:0;background-color:#f0f2f5;font-family:'Segoe UI',sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0">
<tr><td align="center" style="padding:40px 20px;">
<table width="620" cellpadding="0" cellspacing="0" style="max-width:620px;width:100%;">

  <!-- HEADER -->
  <tr><td style="background:linear-gradient(135deg,#ff6b35,#f7931e);border-radius:16px 16px 0 0;padding:36px 40px;text-align:center;">
    <div style="font-size:36px;margin-bottom:10px;">🥽</div>
    <div style="color:#fff;font-size:24px;font-weight:700;letter-spacing:1px;">HOLOMIA VR</div>
    <div style="color:rgba(255,255,255,0.85);font-size:11px;letter-spacing:3px;text-transform:uppercase;margin-top:4px;">Thực tế ảo tiên phong</div>
  </td></tr>

  <!-- BODY -->
  <tr><td style="background:#ffffff;padding:40px;">
    <p style="font-size:20px;font-weight:700;color:#1a1a2e;margin:0 0 12px;">Xin chào {{ $customerName }},</p>
    <p style="font-size:15px;color:#666;line-height:1.7;margin:0 0 28px;">Cảm ơn bạn đã liên hệ với <strong>Holomia VR</strong>. Chúng tôi đã nhận được tin nhắn của bạn và xin phản hồi như sau:</p>

    <!-- Reply box -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
    <tr><td style="background:#fff8f5;border:1px solid #ffe0cc;border-left:4px solid #ff6b35;border-radius:0 12px 12px 0;padding:24px 28px;">
      <div style="font-size:11px;font-weight:700;color:#ff6b35;letter-spacing:2px;text-transform:uppercase;margin-bottom:12px;">💬 Phản hồi từ chúng tôi</div>
      <div style="font-size:15px;color:#2d2d2d;line-height:1.8;">{!! nl2br(e($replyContent)) !!}</div>
    </td></tr>
    </table>
    
    {{-- Booking / Ticket summary (optional) --}}
    @if(isset($booking) || isset($ticketName))
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;border-collapse:collapse;">
      <tr>
        <td style="padding:18px 0 8px;font-size:13px;color:#999;font-weight:700;">🎟️ Thông tin vé & đặt chỗ</td>
      </tr>
      <tr>
        <td style="background:#fafafa;border:1px solid #f0f0f0;border-radius:10px;padding:18px;">
          <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;width:55%;"><strong>Tên vé / Trò chơi</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ $ticketName ?? ($booking->ticket_name ?? '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Ngày chơi</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ isset($booking->play_date) ? \Carbon\Carbon::parse($booking->play_date)->format('d/m/Y') : ($playDate ?? '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Khung giờ</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ $playTime ?? ($booking->play_time ?? '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Số lượng</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ $quantity ?? ($booking->quantity ?? 1) }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Giá / Tổng</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ isset($booking->total_price) ? number_format($booking->total_price,0,',','.') . '₫' : (isset($price) ? number_format($price,0,',','.') . '₫' : '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Mã đơn</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ $orderId ?? ($booking->order_id ?? '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Ngày đặt</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ isset($booking->created_at) ? \Carbon\Carbon::parse($booking->created_at)->format('d/m/Y H:i') : (isset($bookingDate) ? $bookingDate : '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Hình thức thanh toán</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ $paymentMethod ?? ($booking->payment_method ?? '—') }}</td>
            </tr>
            <tr>
              <td style="padding:6px 0;font-size:14px;color:#333;"><strong>Địa điểm</strong></td>
              <td style="padding:6px 0;font-size:14px;color:#333;">{{ $location ?? ($booking->location_name ?? 'Holomia Aeon Mall') }}</td>
            </tr>
            @if(isset($booking->qr_url) || isset($qrUrl))
            <tr>
              <td style="padding:12px 0 0;font-size:13px;color:#333;" colspan="2">
                <div style="padding-top:8px;border-top:1px dashed #eee;margin-top:8px;">
                  <a href="{{ $qrUrl ?? $booking->qr_url }}" style="display:inline-block;padding:10px 14px;background:#ff6b35;color:#fff;border-radius:8px;text-decoration:none;font-weight:700;font-size:13px;">Xem QR / Vé của bạn</a>
                </div>
              </td>
            </tr>
            @endif
          </table>
        </td>
      </tr>
    </table>
    @endif
    <p style="font-size:14px;color:#888;line-height:1.7;margin:0 0 28px;">Nếu bạn có thêm thắc mắc, đừng ngần ngại liên hệ lại với chúng tôi qua các kênh bên dưới.</p>

    <!-- Divider -->
    <hr style="border:none;border-top:1px solid #f0f0f0;margin:0 0 28px;">

    <!-- Contact info -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:28px;">
    <tr>
      <td style="padding:8px 0;font-size:13px;color:#555;">📍 <strong>Địa chỉ:</strong> Aeon Mall Hà Đông & Royal City, Hà Nội</td>
    </tr>
    <tr>
      <td style="padding:8px 0;font-size:13px;color:#555;">📞 <strong>Hotline:</strong> 024.9999.7777</td>
    </tr>
    <tr>
      <td style="padding:8px 0;font-size:13px;color:#555;">🌐 <strong>Website:</strong> <a href="https://holomia.shop" style="color:#ff6b35;text-decoration:none;">holomia.shop</a></td>
    </tr>
    </table>

    <!-- Signature -->
    <table cellpadding="0" cellspacing="0">
    <tr>
      <td style="padding-right:16px;vertical-align:middle;">
        <div style="width:48px;height:48px;background:linear-gradient(135deg,#ff6b35,#f7931e);border-radius:12px;text-align:center;line-height:48px;font-size:22px;">🥽</div>
      </td>
      <td style="vertical-align:middle;">
        <div style="font-size:15px;font-weight:700;color:#1a1a2e;">Đội ngũ Holomia VR</div>
        <div style="font-size:12px;color:#999;margin-top:2px;">Trải nghiệm thực tế ảo đỉnh cao</div>
      </td>
    </tr>
    </table>
  </td></tr>

  <!-- FOOTER -->
  <tr><td style="background:#1a1a2e;border-radius:0 0 16px 16px;padding:24px 40px;text-align:center;">
    <p style="color:rgba(255,255,255,0.5);font-size:12px;margin:0;">© 2026 Holomia VR. Email này được gửi tự động, vui lòng không reply trực tiếp.</p>
    <p style="margin:8px 0 0;"><a href="https://holomia.shop" style="color:#ff6b35;font-size:12px;text-decoration:none;">holomia.shop</a></p>
  </td></tr>

</table>
</td></tr>
</table>
</body>
</html>