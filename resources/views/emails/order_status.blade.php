<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .header { background: #2563eb; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; }
        .status-badge { display: inline-block; padding: 5px 15px; background: #f3f4f6; border-radius: 20px; font-weight: bold; color: #2563eb; }
        .footer { text-align: center; font-size: 0.8rem; color: #777; margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header"><h2>HOLOMIA VR - THÔNG BÁO ĐƠN VÉ</h2></div>
        <div style="padding: 20px;">
            <p>Chào <b>{{ $order->customer_name }}</b>,</p>
            <p>Đơn vé <b>{{ $order->id }}</b> vừa được cập nhật:</p>
            <p style="text-align:center;"><span class="status-badge">{{ $statusName }}</span></p>
            <p><b>Chi tiết:</b> {{ $messageBody }}</p>
            <hr style="border:0; border-top:1px dashed #ccc; margin:20px 0;">
            <ul>
                <li>Ngày chơi: {{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</li>
                <li>Tổng tiền: {{ number_format($order->total_amount) }} VNĐ</li>
            </ul>
        </div>
        <div class="footer">Đây là email tự động, vui lòng không trả lời.</div>
    </div>
</body>
</html>