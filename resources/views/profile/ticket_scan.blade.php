<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vé {{ $order->id }} - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>

<body class="bg-light text-dark p-3 d-flex align-items-center justify-content-center" style="min-height: 100vh;">

    <div class="container" style="max-width: 450px;">
        <div class="card bg-white border border-light border-2 rounded-4 p-4 text-center shadow-lg">

            <i class="bi bi-check-circle-fill text-success mb-2" style="font-size: 4rem;"></i>
            <h3 class="text-primary fw-bold text-uppercase" style="letter-spacing: 1px;">VÉ HỢP LỆ</h3>
            <p class="text-muted mb-4">Mã tham chiếu: {{ $order->id }}</p>

            <div class="text-start bg-light p-3 rounded-3 mb-4 border border-light small">
                @if($order->location)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Cơ sở:</span>
                        <strong class="text-primary">{{ $order->location->name }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Địa chỉ:</span>
                        <strong class="text-dark text-end" style="max-width:60%">{{ $order->location->address }}</strong>
                    </div>
                @endif
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Khách hàng:</span>
                    <strong class="text-dark">{{ $order->customer_name }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Giờ chơi:</span>
                    <strong class="text-dark">
                        @php $slot = $order->slot; @endphp
                        {{ $slot ? substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5) : 'N/A' }}
                    </strong>
                </div>
                <div class="d-flex justify-content-between mb-0">
                    <span class="text-muted">Trạng thái:</span>
                    <span
                        class="badge {{ $order->status == 'paid' ? 'bg-success' : 'bg-warning text-dark' }} text-uppercase">
                        {{ $order->status }}
                    </span>
                </div>
            </div>

            <div class="text-start small mb-4">
                <h6 class="text-primary fw-bold border-bottom border-light pb-2 mb-3">CHI TIẾT TRÒ CHƠI</h6>
                @foreach($order->orderItems as $item)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-dark">{{ $item->ticket_name }}</span>
                        <strong
                            class="text-primary bg-primary bg-opacity-10 px-2 py-1 rounded">x{{ $item->quantity }}</strong>
                    </div>
                @endforeach
            </div>

            <hr class="border-light mb-3">
            <h4 class="text-primary fw-bold">{{ number_format($order->total_amount) }} VNĐ</h4>
        </div>
    </div>

</body>

</html>