@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt vé thành công - {{ $location->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        :root { --primary: {{ $location->color ?? '#0d6efd' }}; }

        .success-icon-ring {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 0 0 12px rgba(16, 185, 129, 0.12);
            animation: pulse-ring 2s infinite;
        }
        @keyframes pulse-ring {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.35); }
            70% { box-shadow: 0 0 0 20px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }

        .order-info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
        }
        .order-info-row:last-child { border-bottom: none; }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 1.5rem;
        }
        .step-num {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
    </style>
</head>

<body class="bg-light text-dark">
    @include('branch.partials.navbar')

    <div class="container py-5" style="max-width: 780px;">

        {{-- SUCCESS CARD --}}
        <div class="card border-0 shadow rounded-4 overflow-hidden mb-4">
            {{-- Header gradient --}}
            <div class="py-5 text-white text-center" style="background: linear-gradient(135deg, #0d1b3e 0%, #0a2a6e 55%, {{ $location->color ?? '#1565c0' }} 100%);">
                <div class="success-icon-ring">
                    <i class="bi bi-check-lg text-success" style="font-size: 3rem;"></i>
                </div>
                <h2 class="fw-bold mb-2">🎉 Đặt vé thành công!</h2>
                <p class="text-white-50 mb-0">Cảm ơn bạn đã tin tưởng <strong>{{ $location->name }}</strong></p>
            </div>

            <div class="card-body p-4 p-lg-5">

                {{-- ORDER SUMMARY --}}
                @if(isset($order))
                <div class="mb-4 p-4 bg-light rounded-4 border border-light">
                    <h6 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; color: var(--primary);">
                        <i class="bi bi-receipt me-2"></i>Thông tin đơn hàng
                    </h6>
                    <div class="order-info-row">
                        <span class="text-muted">Mã đơn</span>
                        <strong class="text-primary">{{ $order->id }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="text-muted">Khách hàng</span>
                        <strong>{{ $order->customer_name }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="text-muted">Ngày chơi</span>
                        <strong>{{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</strong>
                    </div>
                    @if($order->slot)
                    <div class="order-info-row">
                        <span class="text-muted">Khung giờ</span>
                        <strong>{{ substr($order->slot->start_time, 0, 5) }} - {{ substr($order->slot->end_time, 0, 5) }}</strong>
                    </div>
                    @endif
                    <div class="order-info-row">
                        <span class="text-muted">Cơ sở</span>
                        <strong>{{ $location->name }}</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="text-muted">Tổng thanh toán</span>
                        <strong class="fs-5" style="color: var(--primary);">{{ number_format($order->total_amount) }}đ</strong>
                    </div>
                    <div class="order-info-row">
                        <span class="text-muted">Phương thức thanh toán</span>
                        <span class="badge bg-success bg-opacity-75">
                            @if($order->payment_method == 'wallet') Ví Holomia
                            @elseif($order->payment_method == 'banking') Chuyển khoản
                            @else Tại quầy @endif
                        </span>
                    </div>
                </div>
                @else
                {{-- fallback khi không truyền $order --}}
                <div class="mb-4 p-4 bg-success bg-opacity-10 border border-success border-opacity-25 rounded-4 text-center">
                    <i class="bi bi-envelope-check-fill text-success fs-2 mb-2 d-block"></i>
                    <p class="mb-0 text-dark">Email xác nhận đã được gửi đến hộp thư của bạn. Vui lòng kiểm tra email để xem chi tiết đơn hàng.</p>
                </div>
                @endif

                {{-- HƯỚNG DẪN TIẾP THEO --}}
                <div class="mb-4">
                    <h6 class="fw-bold text-uppercase mb-3" style="letter-spacing: 1px; color: var(--primary);">
                        <i class="bi bi-list-check me-2"></i>Các bước tiếp theo
                    </h6>
                    <div class="step-item">
                        <div class="step-num">1</div>
                        <div>
                            <p class="fw-semibold mb-1">Kiểm tra email xác nhận</p>
                            <p class="text-muted small mb-0">Vé điện tử và mã QR sẽ được gửi tới email của bạn trong vài phút.</p>
                        </div>
                    </div>
                    <div class="step-item">
                        <div class="step-num">2</div>
                        <div>
                            <p class="fw-semibold mb-1">Đến đúng giờ đã chọn</p>
                            <p class="text-muted small mb-0">{{ $location->address ?? 'Xem địa chỉ trong email xác nhận' }}. Vui lòng đến trước 10 phút để check-in.</p>
                        </div>
                    </div>
                    <div class="step-item mb-0">
                        <div class="step-num">3</div>
                        <div>
                            <p class="fw-semibold mb-1">Xuất trình mã QR</p>
                            <p class="text-muted small mb-0">Cho nhân viên quét mã QR trong email hoặc trong mục lịch sử đặt vé của tài khoản.</p>
                        </div>
                    </div>
                </div>

                {{-- CTA BUTTONS --}}
                <div class="d-flex flex-wrap gap-3 justify-content-center pt-3 border-top border-light">
                    <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-primary fw-bold px-5 py-3 rounded-pill shadow-sm">
                        <i class="bi bi-controller me-2"></i>Đặt thêm vé
                    </a>
                    @auth
                    <a href="{{ route('branch.profile', ['subdomain' => $subdomain]) }}" class="btn btn-outline-secondary fw-bold px-5 py-3 rounded-pill">
                        <i class="bi bi-clock-history me-2"></i>Lịch sử đặt vé
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- BANNER LIÊN HỆ --}}
        <div class="p-4 rounded-4 text-center" style="background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <p class="mb-2 text-primary fw-semibold">Cần hỗ trợ? Liên hệ ngay với {{ $location->name }}:</p>
            <div class="d-flex justify-content-center flex-wrap gap-3">
                @if($location->hotline)
                <a href="tel:{{ $location->hotline }}" class="btn btn-primary btn-sm rounded-pill fw-bold px-4">
                    <i class="bi bi-telephone-fill me-1"></i> {{ $location->hotline }}
                </a>
                @endif
                <a href="{{ route('branch.contact', ['subdomain' => $subdomain]) }}" class="btn btn-outline-primary btn-sm rounded-pill fw-bold px-4">
                    <i class="bi bi-envelope me-1"></i>Gửi tin nhắn
                </a>
            </div>
        </div>

    </div>

    @include('branch.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
