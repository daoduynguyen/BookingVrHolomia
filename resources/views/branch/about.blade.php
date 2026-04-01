@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu - {{ $location->name }}</title>
    <meta name="description" content="Tìm hiểu về {{ $location->name }} - trung tâm VR hàng đầu tại Việt Nam.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        :root { --primary: {{ $location->color ?? '#0d6efd' }}; }
    </style>
</head>

<body class="bg-light text-dark">

    @include('branch.partials.navbar')

    {{-- HERO --}}
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #0d1b3e 0%, #0a2a6e 55%, {{ $location->color ?? '#1565c0' }} 100%);">
        <div class="container py-3">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3" style="width: 50px; height: 2px; background: {{ $location->color ?? '#38bdf8' }};"></div>
                        <span class="fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 3px; color: {{ $location->color ?? '#38bdf8' }};">Giới thiệu</span>
                    </div>
                    <h1 class="display-4 fw-bold mb-3">
                        Holomia <br><span style="color: {{ $location->color ?? '#38bdf8' }};">{{ $location->name }}</span>
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        {{ $location->description ?? 'Trải nghiệm thực tế ảo đỉnh cao tại trung tâm ' . $location->name . '. Chúng tôi mang đến những cuộc phiêu lưu VR không giới hạn.' }}
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-primary fw-bold px-4 py-2 rounded-pill">
                            <i class="bi bi-controller me-2"></i>Khám phá trò chơi
                        </a>
                        <a href="{{ route('branch.contact', ['subdomain' => $subdomain]) }}" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">
                            <i class="bi bi-telephone me-2"></i>Liên hệ
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://holomia.com/images/contents/17194557751_16576174651-homejpgjpg.jpg"
                        class="img-fluid rounded-4 shadow-lg" alt="{{ $location->name }}" style="max-height: 360px; object-fit: cover; width: 100%;">
                </div>
            </div>
        </div>
    </div>

    {{-- THỐNG KÊ --}}
    <div class="container py-5">
        <div class="row g-4 text-center mb-5">
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">50+</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Trò chơi VR</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">10k+</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Lượt khách</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">4.9/5</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Đánh giá</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">{{ $location->opening_hours ? '7/7' : '7/7' }}</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Ngày mở cửa</small>
                </div>
            </div>
        </div>

        {{-- TẦM NHÌN & SỨ MỆNH --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-eye fs-2 me-3" style="color: var(--primary);"></i>
                        <h4 class="fw-bold text-dark mb-0">Tầm nhìn</h4>
                    </div>
                    <p class="text-muted mb-0">
                        Trở thành điểm đến VR hàng đầu khu vực, mang lại những trải nghiệm thực tế ảo đỉnh cao và khó quên nhất cho mọi lứa tuổi.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-rocket-takeoff fs-2 me-3" style="color: var(--primary);"></i>
                        <h4 class="fw-bold text-dark mb-0">Sứ mệnh</h4>
                    </div>
                    <p class="text-muted mb-0">
                        Cung cấp dịch vụ VR chất lượng cao với thiết bị hiện đại, đội ngũ nhiệt tình và không gian trải nghiệm thoải mái, an toàn cho tất cả khách hàng.
                    </p>
                </div>
            </div>
        </div>

        {{-- THÔNG TIN CHI NHÁNH --}}
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="p-4 p-lg-5 rounded-4 text-white" style="background: linear-gradient(135deg, #0d1b3e 0%, #0a2a6e 60%, {{ $location->color ?? '#1565c0' }} 100%);">
                    <h3 class="fw-bold mb-4">
                        <i class="bi bi-geo-alt-fill me-2" style="color: {{ $location->color ?? '#38bdf8' }};"></i>
                        Thông tin cơ sở {{ $location->name }}
                    </h3>
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-geo-alt-fill fs-4" style="color: {{ $location->color ?? '#f87171' }};"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Địa chỉ</h6>
                                    <p class="text-white-50 mb-0 small">{{ $location->address ?? 'Đang cập nhật' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-telephone-fill fs-4" style="color: {{ $location->color ?? '#38bdf8' }};"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Hotline</h6>
                                    <p class="text-white-50 mb-0 small">
                                        <a href="tel:{{ $location->hotline }}" class="text-white-50 text-decoration-none">{{ $location->hotline ?? 'Đang cập nhật' }}</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-start gap-3">
                                <i class="bi bi-clock-fill fs-4" style="color: {{ $location->color ?? '#38bdf8' }};"></i>
                                <div>
                                    <h6 class="fw-bold mb-1">Giờ mở cửa</h6>
                                    <p class="text-white-50 mb-0 small">{{ $location->opening_hours ?? '9:00 - 22:00 hàng ngày' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA --}}
        <div class="text-center py-4">
            <h3 class="fw-bold mb-3">Sẵn sàng trải nghiệm?</h3>
            <p class="text-muted mb-4">Đặt vé ngay hôm nay để không bỏ lỡ những trò chơi VR hấp dẫn nhất!</p>
            <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-controller me-2"></i>Đặt vé ngay
            </a>
        </div>
    </div>

    @include('branch.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
