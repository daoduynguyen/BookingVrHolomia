@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('home.page_title') }}</title>
    <meta name="description" content="{{ __('home.meta_description') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    {{-- Font động theo ngôn ngữ --}}
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
    </style>
</head>
<body class="bg-light text-dark">

    @include('partials.navbar')

    <div class="container-fluid p-0 mb-5 position-relative" style="height: 600px; background: url('https://images.unsplash.com/photo-1622979135225-d2ba269cf1ac?q=80&w=2000&auto=format&fit=crop') center right/cover no-repeat;">
        
        <!-- Background overlay: Lớp phủ xám sáng bên trái, mờ dần sang phải -->
        <!-- Thêm lưới hạt không gian Particles.js -->
        <div id="particles-js" class="position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;"></div>
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(90deg, #e9ecef 0%, rgba(233, 236, 239, 0.9) 35%, rgba(233, 236, 239, 0) 100%); z-index: 1; pointer-events: none;"></div>

        <div class="row g-0 align-items-center h-100 position-relative" style="z-index: 2; pointer-events: none;">
            <!-- Cột trái: Chữ -->
            <div class="col-lg-6 position-relative text-dark d-flex align-items-center" style="height: 100%; pointer-events: auto;">
                <div class="px-5 w-100" style="padding-left: 8% !important;">
                    <div class="mb-3 animate-bounce" data-aos="zoom-in" data-aos-duration="1000">
                        <i class="bi bi-headset-vr display-4 text-primary"></i>
                    </div>

                    <h1 class="display-3 fw-bold text-uppercase mb-3" style="letter-spacing: 3px; color: #212529;" data-aos="fade-right" data-aos-duration="1000">
                        Holomia <br><span class="text-primary">VR World</span>
                    </h1>

                    <p class="lead fs-4 mb-5 text-secondary" style="max-width: 500px; font-weight: 500;" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="150">
                        {{ __('home.hero_tagline') }}
                    </p>

                    <a href="#featured-games" class="btn btn-primary rounded-pill px-4 py-3 fw-bold text-white border-0" style="box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
                        <i class="bi bi-controller me-2"></i> {{ __('home.explore_now') }}
                    </a>
                </div>
            </div>

            <!-- Cột phải: Để trống để lộ ảnh nền rực rỡ -->
            <div class="col-lg-6"></div>
        </div>
    </div>

    @include('partials.search_bar')

    <div id="featured-games"></div>

    <div class="container pb-5" id="list-games">

        @if(session('success'))
    <div style="position:fixed; top:80px; right:20px; z-index:9999; min-width:320px; max-width:400px;"
         class="alert alert-success alert-dismissible fade show shadow">
        <i class="bi bi-check-circle-fill me-2"></i>
        <strong>{{ __('home.success') }}</strong> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

        <div class="text-center mb-5" data-aos="fade-up">
            <h2 class="text-info fw-bold text-uppercase display-6">
                <i class="bi bi-controller"></i> {{ __('home.featured_games') }}
            </h2>
            <div style="width: 60px; height: 3px; background-color: #0dcaf0; margin: 10px auto;"></div>
        </div>

        <div class="row g-4">
            @if($tickets->isEmpty())
                <div class="col-12 text-center text-muted py-5">
                    <h3>{{ __('home.no_tickets') }}</h3>
                </div>
            @else
                @foreach($tickets as $index => $ticket)
                    <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
                        <div class="card h-100 card-game overflow-hidden rounded-4 border-0 {{ $ticket->status == 'maintenance' ? 'card-maintenance' : '' }}">

                            <div class="position-relative">
                                <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}"
                                     class="card-img-top"
                                     alt="{{ $ticket->name }}"
                                     style="height: 240px; object-fit: cover;">

                                <div class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow rounded-pill px-3 py-2">
                                    <i class="bi bi-star-fill text-dark"></i> {{ $ticket->avg_rating }}
                                </div>

                                @if($ticket->status == 'maintenance')
                                    <div class="maintenance-overlay">{{ __('home.maintenance') }}</div>
                                @endif
                            </div>

                            <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ $ticket->category->name }}</span>
                                    <small class="text-muted fw-bold" style="font-size: 0.8rem;">
                                        <i class="bi bi-person-check-fill text-secondary"></i>
                                        {{ number_format($ticket->play_count) }} {{ __('home.plays') }}
                                    </small>
                                </div>

                                <h5 class="card-title fw-bold mb-2 text-truncate" title="{{ $ticket->name }}" style="font-size: 1.2rem;">
                                    {{ $ticket->name }}
                                </h5>

                                <p class="small text-muted mb-4 fw-medium">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ $ticket->locations->pluck('name')->join(', ') ?: __('home.multiple_branches') }}
                                </p>

                                <div class="mt-auto pt-3 border-top border-light mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted fw-medium">{{ __('home.price_from') }}</span>
                                        <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                                    </div>
                                </div>

                                @if($ticket->status == 'maintenance')
                                    <button class="btn btn-secondary w-100 fw-bold py-2 text-uppercase shadow-sm" disabled>{{ __('home.maintenance') ?? 'Bảo trì' }}</button>
                                @else
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('booking.form', $ticket->id) }}" class="btn btn-primary flex-grow-1 fw-bold py-2 text-uppercase shadow-sm d-flex align-items-center justify-content-center">
                                            {{ __('home.book_now') }}
                                        </a>
                                        <a href="{{ route('cart.add', $ticket->id) }}" class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm d-flex align-items-center justify-content-center" title="{{ __('home.add_to_cart') ?? 'Thêm vào giỏ' }}">
                                            <i class="bi bi-cart-plus fs-5"></i>
                                        </a>
                                        <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm d-flex align-items-center justify-content-center" title="Xem chi tiết">
                                            <i class="bi bi-eye fs-5"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
                    "color": { "value": "#1e88e5" }, // Màu xanh primary holomia
                    "shape": { "type": "circle" },
                    "opacity": { "value": 0.5, "random": false },
                    "size": { "value": 3, "random": true },
                    "line_linked": { "enable": true, "distance": 150, "color": "#1e88e5", "opacity": 0.4, "width": 1 },
                    "move": { "enable": true, "speed": 4, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
                },
                "interactivity": {
                    "detect_on": "window", // Phát hiện rê chuột trên toàn bộ banner
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
    @if(session('cod_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ __("profile.order_success") ?? "Đặt vé thành công!" }} 🎉',
            html: `
                <div class="text-start small">
                    <div class="alert alert-warning border-0 rounded-3 mb-3 py-2" style="font-size:0.85rem;">
                        <i class="bi bi-shop me-2"></i>
                        <strong>{{ __("profile.pay_at_counter") ?? "Thanh toán tại quầy" }}</strong><br>
                        {{ __("profile.pay_at_counter_simple") ?? "Vui lòng đến quầy để thanh toán và nhận vé." }}
                    </div>
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                        <span class="text-muted">{{ __("profile.order_id_label") ?? "Mã đơn:" }}</span>
                        <span class="fw-bold text-dark">#{{ session('order_id') }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ __("profile.total_label") ?? "Tổng tiền:" }}</span>
                        <span class="fw-bold text-success">{{ number_format(session('total_amount')) }}đ</span>
                    </div>
                </div>
            `,
            background: '#fff',
            color: '#1f2937',
            confirmButtonText: '{{ __("profile.understood") ?? "Đã hiểu" }}',
            confirmButtonColor: '#0dcaf0',
            width: '420px',
        });
    </script>
    @endif
</body>
</html>
