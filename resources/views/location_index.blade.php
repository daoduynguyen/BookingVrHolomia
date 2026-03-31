@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hệ thống cơ sở – Holomia VR</title>
    <meta name="description" content="Danh sách các cơ sở VR Arena Holomia trên toàn quốc. Tìm cơ sở gần bạn và đặt vé ngay.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family={{ $langConfig['font'] }}&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }

        .page-hero {
            background: linear-gradient(135deg, #0d1b3e 0%, #0a2a6e 60%, #1565c0 100%);
            padding: 80px 0 60px;
            color: #fff;
            text-align: center;
        }

        .page-hero h1 {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            letter-spacing: 3px;
        }

        .loc-card {
            border: none;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.1);
            transition: transform .25s, box-shadow .25s;
            height: 100%;
        }

        .loc-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 40px rgba(0,0,0,.16);
        }

        .loc-card-banner {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .loc-card-banner-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #0d1b3e, #0a2a6e);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .badge-count {
            background: rgba(56,189,248,.15);
            color: #0ea5e9;
            border-radius: 50px;
            padding: 4px 12px;
            font-size: .8rem;
            font-weight: 600;
        }

        .info-row {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            color: #6b7280;
            font-size: .88rem;
            line-height: 1.4;
        }

        .info-row i { color: #0055c4; flex-shrink: 0; margin-top: 2px; }
    </style>
</head>
<body class="bg-light">

@include('partials.navbar')

{{-- Hero --}}
<div class="page-hero">
    <div class="container">
        <p class="text-info mb-2" style="font-size:.8rem; font-weight:600; letter-spacing:3px; text-transform:uppercase;">
            HỆ THỐNG CƠ SỞ
        </p>
        <h1 class="mb-3">HOLOMIA <span style="color:#38bdf8;">VR</span></h1>
        <p class="text-light opacity-75 fs-5 mb-0">
            {{ $locations->count() }} cơ sở trên toàn quốc – Chọn cơ sở gần bạn nhất
        </p>
    </div>
</div>

{{-- Danh sách cơ sở --}}
<div class="container py-5">
    @if($locations->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-geo-alt display-4 opacity-25"></i>
            <p class="mt-3">Chưa có cơ sở nào được thiết lập.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($locations as $location)
            <div class="col-md-6 col-lg-4">
                <div class="loc-card card">
                    {{-- Banner --}}
                    @if($location->banner_image)
                        <img src="{{ $location->banner_image }}" alt="{{ $location->name }}"
                             class="loc-card-banner">
                    @else
                        <div class="loc-card-banner-placeholder">
                            <i class="bi bi-building" style="font-size:3rem; color:rgba(255,255,255,.3);"></i>
                        </div>
                    @endif

                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="fw-bold mb-0 text-dark" style="font-size:1.1rem;">{{ $location->name }}</h5>
                            <span class="badge-count">
                                <i class="bi bi-controller me-1"></i>{{ $location->tickets_count }} game
                            </span>
                        </div>

                        <div class="d-flex flex-column gap-2 mb-4">
                            <div class="info-row">
                                <i class="bi bi-geo-alt-fill"></i>
                                <span>{{ $location->address }}</span>
                            </div>
                            <div class="info-row">
                                <i class="bi bi-telephone-fill"></i>
                                <a href="tel:{{ $location->hotline }}" class="text-decoration-none" style="color:#6b7280;">
                                    {{ $location->hotline }}
                                </a>
                            </div>
                            @if($location->opening_hours)
                            <div class="info-row">
                                <i class="bi bi-clock-fill"></i>
                                <span>{{ $location->opening_hours }}</span>
                            </div>
                            @endif
                        </div>

                        @if($location->description)
                            <p class="text-muted small mb-4 lh-lg" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                {{ $location->description }}
                            </p>
                        @endif

                        <div class="mt-auto d-flex gap-2">
                            <a href="{{ route('location.landing', $location->slug) }}"
                               class="btn btn-primary fw-bold rounded-pill flex-grow-1">
                                <i class="bi bi-arrow-right-circle me-1"></i>Xem chi nhánh
                            </a>
                            @if($location->facebook_url)
                            <a href="{{ $location->facebook_url }}" target="_blank"
                               class="btn btn-outline-primary rounded-pill px-3" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
