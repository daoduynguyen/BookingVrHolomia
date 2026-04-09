@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $location->name }} - Holomia VR World</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        :root { var(--primary: {{ $location->color ?? '#0d6efd' }}); }
        .hero-section {
            height: 600px;
            background: url('https://images.unsplash.com/photo-1592478411213-6153e4ebc07d?q=80&w=2000&auto=format&fit=crop') center center/cover no-repeat;
            position: relative;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            start: 0;
            w: 100%;
            h: 100%;
            background: linear-gradient(90deg, rgba(233, 236, 239, 0.95) 0%, rgba(233, 236, 239, 0.8) 40%, rgba(233, 236, 239, 0) 100%);
            z-index: 1;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        .card-game {
            position: relative;
            transition: 0.3s;
            background: #fff;
        }
        .card-game:hover::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 1rem;
            padding: 2px;
            background: radial-gradient(800px circle at var(--mouse-x) var(--mouse-y), {{ $location->color ?? '#0d6efd' }}, transparent 40%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }
    </style>
</head>

<body class="bg-light text-dark">

    @include('branch.partials.navbar')

    <div class="hero-section container-fluid p-0 mb-5">
        <div id="particles-js" class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;"></div>
        <div class="hero-overlay"></div>
        <div class="row g-0 align-items-center h-100 position-relative" style="z-index: 2; pointer-events: none;">
            <div class="col-lg-6 px-5" style="padding-left: 8% !important; pointer-events: auto;">
                <div class="mb-3">
                    <i class="bi bi-geo-alt-fill display-4 text-primary"></i>
                </div>
                <h1 class="display-3 fw-bold text-uppercase mb-3" style="letter-spacing: 2px;">
                    Holomia <br><span class="text-primary">{{ $location->name }}</span>
                </h1>
                <p class="lead fs-4 mb-5 text-secondary fw-medium">
                    {{ __('branch.hero_desc', ['name' => $location->name]) }}
                </p>
                <a href="#list-games" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg">
                    {{ __('branch.explore_now') }} <i class="bi bi-arrow-down-circle ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="container pb-5" id="list-games">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-5">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="text-center mb-5">
            <h2 class="text-dark fw-bold text-uppercase display-6">
                <i class="bi bi-controller me-2"></i> {{ __('branch.games_at_branch') }}
            </h2>
            <div style="width: 60px; height: 3px; background-color: var(--primary); margin: 10px auto;"></div>
        </div>

        <div class="row g-4">
            @forelse($tickets as $ticket)
                <div class="col-md-4">
                    <div class="card h-100 card-game overflow-hidden rounded-4 border-0 shadow-sm">
                        <div class="position-relative">
                            <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}"
                                class="card-img-top" alt="{{ $ticket->name }}" style="height: 240px; object-fit: cover;">

                            <button class="btn btn-light rounded-circle position-absolute top-0 start-0 m-3 shadow-sm btn-wishlist"
                                data-id="{{ $ticket->id }}" title="{{ __('branch.add_to_wishlist') }}">
                                @if(Auth::check() && Auth::user()->favorites->contains($ticket->id))
                                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                                @else
                                    <i class="bi bi-heart text-danger fs-5"></i>
                                @endif
                            </button>

                            <div class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow rounded-pill px-3 py-2">
                                <i class="bi bi-star-fill text-dark"></i> {{ $ticket->avg_rating }}
                            </div>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ $ticket->category->name }}</span>
                                    <small class="text-muted fw-bold" style="font-size: 0.8rem;">
                                        <i class="bi bi-person-check-fill text-secondary"></i>
                                        {{ number_format($ticket->play_count) }} {{ __('branch.plays') }}
                                    </small>
                                </div>
                                
                                <h5 class="card-title fw-bold mb-3 text-truncate">{{ $ticket->name }}</h5>

                                <div class="mt-auto pt-3 border-top border-light mb-4 d-flex justify-content-between align-items-center">
                                    <span class="small text-muted fw-medium">{{ __('home.price_from') }}</span>
                                    <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                                </div>

                                @if($ticket->status == 'maintenance')
                                    <button class="btn btn-secondary w-100 fw-bold py-2 text-uppercase" disabled>{{ __('branch.maintenance') }}</button>
                                @else
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('branch.booking.form', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" class="btn btn-primary flex-grow-1 fw-bold py-2 text-uppercase shadow-sm d-flex align-items-center justify-content-center">
                                            {{ __('branch.book_now') }}
                                        </a>
                                        <a href="{{ route('branch.cart.add.quick', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm d-flex align-items-center justify-content-center" title="{{ __('branch.add_to_cart') }}">
                                            <i class="bi bi-cart-plus fs-5"></i>
                                        </a>
                                        <a href="{{ route('branch.detail', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm d-flex align-items-center justify-content-center" title="{{ __('branch.view_detail') }}">
                                            <i class="bi bi-eye fs-5"></i>
                                        </a>
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <h3>{{ __('branch.no_games') }}</h3>
                </div>
            @endforelse
        </div>
    </div>

    @if($location->maps_url)
    <div class="container pb-5">
        <h2 class="text-dark fw-bold text-uppercase d-flex align-items-center mb-4">
            <i class="bi bi-map-fill text-primary me-3 fs-3"></i> {{ __('branch.find_us') }}
        </h2>
        <div class="shadow-sm rounded-4 overflow-hidden" style="border: 4px solid #fff;">
            <div style="width: 100%; height: 350px;">
                {!! str_replace('<iframe ', '<iframe style="width: 100%; height: 100%; border: 0;" ', $location->maps_url) !!}
            </div>
        </div>
    </div>
    @endif

    @include('branch.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>
    <script>
        AOS.init({ once: true, duration: 800, offset: 50 });

        // Cấu hình Particles.js
        if(document.getElementById('particles-js')) {
            particlesJS('particles-js', {
                "particles": {
                    "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                    "color": { "value": "{{ $location->color ?? '#0d6efd' }}" }, // Màu chi nhánh
                    "shape": { "type": "circle" },
                    "opacity": { "value": 0.5, "random": false },
                    "size": { "value": 3, "random": true },
                    "line_linked": { "enable": true, "distance": 150, "color": "{{ $location->color ?? '#0d6efd' }}", "opacity": 0.4, "width": 1 },
                    "move": { "enable": true, "speed": 4, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
                },
                "interactivity": {
                    "detect_on": "window",
                    "events": {
                        "onhover": { "enable": true, "mode": "grab" },
                        "onclick": { "enable": true, "mode": "push" },
                        "resize": true
                    },
                    "modes": {
                        "grab": { "distance": 200, "line_linked": { "opacity": 1 } },
                        "push": { "particles_nb": 4 }
                    }
                },
                "retina_detect": true
            });
        }

        // Script tọa độ ánh sáng Card Glow
        document.querySelectorAll('.card-game').forEach(card => {
            card.addEventListener('mousemove', e => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                card.style.setProperty('--mouse-x', `${x}px`);
                card.style.setProperty('--mouse-y', `${y}px`);
            });
        });

        // Script Hiệu ứng Bắn Game vào Giỏ Hàng
        document.querySelectorAll('a[href*="cart/add"], a[href*="them-gio-hang"]').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if(this.classList.contains('processing')) return;
                this.classList.add('processing');
                e.preventDefault();
                const href = this.getAttribute('href');
                
                const card = this.closest('.card-game');
                if(card) {
                    const img = card.querySelector('.card-img-top');
                    const cartIcon = document.querySelector('.bi-cart3, .cart-icon-hover');
                    
                    if(img && cartIcon) {
                        const imgRect = img.getBoundingClientRect();
                        const clone = img.cloneNode(true);
                        clone.style.width = imgRect.width + 'px';
                        clone.style.height = imgRect.height + 'px';
                        clone.style.position = 'fixed';
                        clone.style.top = imgRect.top + 'px';
                        clone.style.left = imgRect.left + 'px';
                        clone.style.zIndex = '9999';
                        clone.style.transition = 'all 0.8s cubic-bezier(0.25, 1, 0.5, 1)';
                        clone.style.borderRadius = '12px';
                        clone.style.opacity = '0.9';
                        document.body.appendChild(clone);
                        
                        const cartRect = cartIcon.getBoundingClientRect();
                        
                        setTimeout(() => {
                            clone.style.top = (cartRect.top + 5) + 'px';
                            clone.style.left = (cartRect.left + 5) + 'px';
                            clone.style.width = '30px';
                            clone.style.height = '30px';
                            clone.style.opacity = '0';
                        }, 20);
                        
                        setTimeout(() => {
                            clone.remove();
                            if(cartIcon.parentElement) {
                                cartIcon.parentElement.classList.add('cart-shake');
                                setTimeout(() => cartIcon.parentElement.classList.remove('cart-shake'), 400);
                            }
                            window.location.href = href;
                        }, 800);
                        return;
                    }
                }
                window.location.href = href;
            });
        });
    </script>
    <script>
        $(document).on('click', '.btn-wishlist', function () {
            @if(!Auth::check())
                window.location.href = "{{ route('branch.login', ['subdomain' => $subdomain]) }}";
                return;
            @endif
            let btn = $(this);
            let icon = btn.find('i');
            let id = btn.data('id');
            $.ajax({
                url: '{{ url("/wishlist/toggle") }}/' + id,
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                beforeSend: function () { btn.prop('disabled', true).css('opacity', '0.6'); },
                complete: function () { btn.prop('disabled', false).css('opacity', '1'); },
                success: function (response) {
                    if (response.is_favorited) {
                        icon.removeClass('bi-heart').addClass('bi-heart-fill');
                    } else {
                        icon.removeClass('bi-heart-fill').addClass('bi-heart');
                    }
                }
            });
        });
    </script>
    @if(session('cod_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ __('branch.booking_success') }} 🎉',
            html: `
                <div class="text-start small">
                    <div class="alert alert-warning border-0 rounded-3 mb-3 py-2" style="font-size:0.85rem;">
                        <i class="bi bi-shop me-2"></i>
                        <strong>{{ __('branch.pay_at_counter') }}</strong><br>
                        {{ __('branch.pay_at_counter_desc') }}
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                        <span class="text-muted">{{ __('branch.order_code') }}</span>
                        <span class="fw-bold text-dark">#{{ session('order_id') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ __('branch.total_amount') }}</span>
                        <span class="fw-bold text-success">{{ number_format(session('total_amount')) }}đ</span>
                    </div>
                </div>
            `,
            background: '#fff',
            color: '#1f2937',
            confirmButtonText: '{{ __('branch.got_it') }}',
            confirmButtonColor: '#0dcaf0',
            width: '420px',
        });
    </script>
    @endif
</body>
</html>