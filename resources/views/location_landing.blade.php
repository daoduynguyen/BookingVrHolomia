@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $location->name }} – Holomia VR</title>
    <meta name="description" content="{{ $location->description ?? 'Trải nghiệm VR đỉnh cao tại ' . $location->name }}">

    {{-- OG tags cho chia sẻ Facebook --}}
    <meta property="og:title" content="{{ $location->name }} – Holomia VR">
    <meta property="og:description" content="{{ $location->description ?? 'Đặt vé ngay tại ' . $location->name }}">
    @if($location->banner_image)
    <meta property="og:image" content="{{ $location->banner_image }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family={{ $langConfig['font'] }}&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">

    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }

        /* ── HERO ── */
        .landing-hero {
            position: relative;
            height: 520px;
            background:
                linear-gradient(to bottom, rgba(0,0,0,.45) 0%, rgba(13,27,78,.75) 100%),
                url('{{ $location->banner_image ?? "https://images.unsplash.com/photo-1593508512255-86ab42a8e620?q=80&w=2000" }}')
                center/cover no-repeat;
            display: flex;
            align-items: flex-end;
            padding-bottom: 48px;
        }

        .hero-brand {
            font-family: 'Orbitron', monospace;
            font-size: clamp(1.8rem, 5vw, 3rem);
            font-weight: 900;
            color: #fff;
            letter-spacing: 3px;
            text-shadow: 0 0 30px rgba(56,189,248,.6);
        }

        .hero-brand span { color: #38bdf8; }

        /* ── INFO STRIP ── */
        .info-strip {
            background: linear-gradient(90deg, #0d1b3e, #0a2a6e);
            color: #fff;
            padding: 18px 0;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .9rem;
        }

        .info-item i { color: #38bdf8; font-size: 1.2rem; flex-shrink: 0; }

        /* ── GAMES SECTION ── */
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0d1b3e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .card-game {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,.1);
            transition: transform .25s, box-shadow .25s;
        }

        .card-game:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,.16);
        }

        .card-game img { height: 200px; object-fit: cover; width: 100%; }

        /* ── CTA BANNER ── */
        .cta-banner {
            background: linear-gradient(135deg, #0055c4, #38bdf8);
            border-radius: 20px;
            padding: 48px 32px;
            text-align: center;
            color: #fff;
        }

        .btn-cta {
            background: #fff;
            color: #0055c4;
            font-weight: 700;
            border: none;
            border-radius: 50px;
            padding: 14px 40px;
            font-size: 1rem;
            transition: all .2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,.2);
            color: #0055c4;
        }

        /* ── MAP ── */
        .map-wrap iframe {
            border-radius: 16px;
            border: none;
            width: 100%;
            height: 320px;
        }

        /* ── NAVBAR MINIMAL ── */
        .landing-nav {
            background: transparent;
            position: absolute;
            top: 0; left: 0; right: 0;
            z-index: 100;
            padding: 20px 0;
        }

        .landing-nav .brand {
            font-family: 'Orbitron', monospace;
            font-weight: 900;
            color: #fff;
            font-size: 1.3rem;
            letter-spacing: 2px;
            text-decoration: none;
        }

        .landing-nav .brand span { color: #38bdf8; }

        .nav-btn {
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.3);
            color: #fff;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        }

        .nav-btn:hover { background: rgba(255,255,255,.25); color: #fff; }
        .nav-btn.primary { background: #38bdf8; border-color: #38bdf8; color: #0d1b3e; }
        .nav-btn.primary:hover { background: #0ea5e9; }
    </style>
</head>
<body class="bg-light">

{{-- ═══════════════════════════════════════
     HERO + NAV
════════════════════════════════════════ --}}
<div class="position-relative">
    {{-- Navbar nổi trên hero --}}
    <nav class="landing-nav">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="{{ route('home') }}" class="brand">HOLOMIA <span>VR</span></a>
            <div class="d-flex gap-2">
                <a href="{{ route('location.all') }}" class="nav-btn">
                    <i class="bi bi-geo-alt me-1"></i>Hệ thống cơ sở
                </a>
                @auth
                    <a href="{{ route('profile.index') }}" class="nav-btn">
                        <i class="bi bi-person-circle me-1"></i>Tài khoản
                    </a>
                @else
                    <a href="{{ route('login') }}" class="nav-btn primary">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <div class="landing-hero">
        <div class="container">
            <p class="text-info mb-1" style="font-size:.85rem; font-weight:600; letter-spacing:2px; text-transform:uppercase;">
                <i class="bi bi-geo-alt-fill me-1"></i>{{ $location->address }}
            </p>
            <div class="hero-brand mb-3">{{ $location->name }}<br><span>VR Arena</span></div>
            <div class="d-flex flex-wrap gap-3">
                <a href="{{ route('ticket.shop') }}" class="btn btn-info fw-bold px-4 py-2 rounded-pill shadow">
                    <i class="bi bi-controller me-2"></i>Xem tất cả trò chơi
                </a>
                <a href="#games" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">
                    <i class="bi bi-arrow-down-circle me-2"></i>Khám phá
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     INFO STRIP
════════════════════════════════════════ --}}
<div class="info-strip">
    <div class="container">
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="info-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>{{ $location->address }}</span>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="info-item">
                    <i class="bi bi-telephone-fill"></i>
                    <a href="tel:{{ $location->hotline }}" class="text-white text-decoration-none">
                        {{ $location->hotline }}
                    </a>
                </div>
            </div>
            @if($location->opening_hours)
            <div class="col-12 col-md-3">
                <div class="info-item">
                    <i class="bi bi-clock-fill"></i>
                    <span>{{ $location->opening_hours }}</span>
                </div>
            </div>
            @endif
            @if($location->facebook_url)
            <div class="col-12 col-md-2">
                <div class="info-item">
                    <i class="bi bi-facebook"></i>
                    <a href="{{ $location->facebook_url }}" target="_blank"
                       class="text-white text-decoration-none">Fanpage</a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
     MÔ TẢ CƠ SỞ
════════════════════════════════════════ --}}
@if($location->description)
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 text-center">
            <h2 class="section-title mb-3">
                <i class="bi bi-stars text-info me-2"></i>Về cơ sở
            </h2>
            <p class="text-muted fs-5 lh-lg">{{ $location->description }}</p>
        </div>
    </div>
</div>
@endif

{{-- ═══════════════════════════════════════
     GAME NỔI BẬT
════════════════════════════════════════ --}}
<div class="container py-5" id="games">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-1">
                <i class="bi bi-controller text-primary me-2"></i>Trò chơi tại đây
            </h2>
            <p class="text-muted small mb-0">{{ $tickets->count() }} trò chơi hiện có tại {{ $location->name }}</p>
        </div>
        <a href="{{ route('ticket.shop') }}" class="btn btn-outline-primary fw-bold rounded-pill px-4">
            Xem tất cả <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>

    @if($tickets->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-controller display-4 opacity-25"></i>
            <p class="mt-3">Chưa có trò chơi tại cơ sở này.</p>
        </div>
    @else
        <div class="row g-4">
            @foreach($tickets as $ticket)
            <div class="col-md-6 col-lg-4">
                <div class="card card-game h-100">
                    <div class="position-relative">
                        <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}"
                             alt="{{ $ticket->name }}">
                        <span class="position-absolute top-0 end-0 m-2 badge bg-warning text-dark rounded-pill px-2 py-1">
                            <i class="bi bi-star-fill"></i> {{ $ticket->avg_rating }}
                        </span>
                    </div>
                    <div class="card-body d-flex flex-column p-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary mb-2 align-self-start px-2">
                            {{ $ticket->category->name }}
                        </span>
                        <h5 class="fw-bold mb-2 text-truncate">{{ $ticket->name }}</h5>
                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                            <div class="d-flex gap-1">
                                <a href="{{ route('ticket.show', $ticket->id) }}"
                                   class="btn btn-primary btn-sm fw-bold rounded-pill px-3">Đặt ngay</a>
                                <a href="{{ route('cart.add', $ticket->id) }}"
                                   class="btn btn-outline-primary btn-sm rounded-pill px-2" title="Thêm vào giỏ">
                                    <i class="bi bi-cart-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════
     CTA BANNER
════════════════════════════════════════ --}}
<div class="container pb-5">
    <div class="cta-banner">
        <h3 class="fw-bold mb-2">Sẵn sàng trải nghiệm VR?</h3>
        <p class="mb-4 opacity-75">Đặt vé ngay hôm nay — nhận ngay ưu đãi thành viên đặc biệt!</p>
        <a href="{{ route('ticket.shop') }}" class="btn-cta">
            <i class="bi bi-lightning-charge-fill me-2"></i>Đặt vé ngay
        </a>
        @if($location->hotline)
        <p class="mt-4 mb-0 opacity-75 small">
            Hoặc gọi cho chúng tôi: <a href="tel:{{ $location->hotline }}"
                class="text-white fw-bold text-decoration-none">{{ $location->hotline }}</a>
        </p>
        @endif
    </div>
</div>

{{-- ═══════════════════════════════════════
     BẢN ĐỒ
════════════════════════════════════════ --}}
@if($location->maps_url)
<div class="container pb-5">
    <h2 class="section-title mb-3">
        <i class="bi bi-map text-primary me-2"></i>Tìm đường đến chúng tôi
    </h2>
    <div class="map-wrap">
        {!! $location->maps_url !!}
    </div>
</div>
@endif

{{-- Footer --}}
@include('partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
