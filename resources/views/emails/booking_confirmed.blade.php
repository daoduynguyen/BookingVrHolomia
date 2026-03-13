<div style="font-family: Arial; padding: 20px; border: 1px solid #ddd;">
    <h2 style="color: #0dcaf0;">Cảm ơn bạn đã đặt vé!</h2>
    <p>Chào <strong>{{ $order->customer_name }}</strong>,</p>
    <p>Đơn hàng <strong>#{{ $order->id }}</strong> đã được xác nhận.</p>
    <p>Ngày chơi: {{ $order->booking_date }}</p>
    
    <div style="text-align: center; margin: 20px 0;">
        <p>Mã QR Check-in của bạn:</p>
        <img src="{{ $qr->qr_code }}" width="200">
        <p><code>{{ $qr->code_string }}</code></p>
    </div>
</div>