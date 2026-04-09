@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('branch.shop_title') }} - {{ $location->name }} | Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body {
            font-family:
                {{ $langConfig['font_family'] }}
            ;
        }

        :root {
            --primary:
                {{ $location->color ?? '#0d6efd' }}
            ;
        }

        .shop-header {
            background: linear-gradient(135deg, #0d1b2a 0%, #1a2f4a 100%);
            padding: 60px 0 50px;
        }

        .search-box {
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            padding: 24px 28px;
            backdrop-filter: blur(10px);
        }

        .search-box .input-group-text {
            background: white;
            border-right: none;
            border-radius: 10px 0 0 10px;
            padding: 12px 16px;
        }

        .search-box .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
            padding: 12px 16px;
        }

        .search-box .form-control:focus {
            box-shadow: none;
            border-color: #dee2e6;
        }

        .card-game {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .card-game:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12) !important;
        }

        #noResult {
            display: none;
        }
    </style>
</head>

<body class="bg-light text-dark">

    @include('branch.partials.navbar')

    {{-- HEADER --}}
    <div class="shop-header">
        <div class="container">
            <div class="text-center mb-4">
                <span class="badge rounded-pill px-3 py-2 mb-3"
                    style="background: rgba(255,255,255,0.1); color: rgba(255,255,255,0.7); font-size: 0.8rem; letter-spacing: 2px;">
                    <i class="bi bi-geo-alt-fill me-1" style="color: var(--primary);"></i>
                    {{ strtoupper($location->name) }}
                </span>
                <h1 class="text-white fw-bold display-5 mb-1">
                    <i class="bi bi-controller me-2" style="color: var(--primary);"></i>
                    {{ __('branch.shop_header') }}
                </h1>
                <p class="text-white-50 mb-0">
                    {{ __('branch.shop_available') }} <strong class="text-white">{{ $tickets->count() }}</strong> {{ __('branch.shop_games_here') }}
                </p>
            </div>

            {{-- THANH TÌM KIẾM --}}
            <div class="row justify-content-center mt-4">
                <div class="col-lg-7">
                    <div class="search-box">
                        <label class="text-white-50 small fw-semibold mb-2 d-block" style="letter-spacing: 1px;">
                            {{ __('branch.shop_search_label') }}
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search text-primary"></i>
                            </span>
                            <input type="text" id="searchInput" class="form-control" placeholder="{{ __('branch.shop_search_ph') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH VÉ --}}
    <div class="container py-5">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <p class="text-muted mb-0" id="resultCount">
                {{ __('branch.shop_showing') }} <strong>{{ $tickets->count() }}</strong> {{ __('branch.shop_games') }}
            </p>
        </div>

        <div class="row g-4" id="ticketList">
            @forelse($tickets as $ticket)
                <div class="col-md-4 ticket-card">
                    <div class="card h-100 card-game overflow-hidden rounded-4 border-0 shadow-sm">
                        <div class="position-relative">
                            <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}"
                                class="card-img-top" alt="{{ $ticket->name }}" style="height: 240px; object-fit: cover;">

                            {{-- NÚT TIM YÊU THÍCH --}}
                            <button
                                class="btn btn-light rounded-circle position-absolute top-0 start-0 m-3 shadow-sm btn-wishlist"
                                data-id="{{ $ticket->id }}" title="{{ __('branch.add_to_wishlist') }}">
                                @if(Auth::check() && Auth::user()->favorites->contains($ticket->id))
                                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                                @else
                                    <i class="bi bi-heart text-danger fs-5"></i>
                                @endif
                            </button>

                            {{-- RATING --}}
                            <div
                                class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow rounded-pill px-3 py-2">
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
                            <h5 class="card-title fw-bold mb-2 ticket-name">{{ $ticket->name }}</h5>
                            <p class="small text-muted mb-3 fw-medium">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                {{ $location->name }}
                            </p>

                            <div
                                class="mt-auto pt-3 border-top border-light mb-4 d-flex justify-content-between align-items-center">
                                <span class="small text-muted fw-medium">{{ __('branch.shop_price_from') }}</span>
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
                    <i class="bi bi-controller display-1 opacity-25"></i>
                    <h4 class="mt-3">{{ __('branch.no_games') }}</h4>
                </div>
            @endforelse
        </div>

        {{-- KHÔNG TÌM THẤY --}}
        <div id="noResult" class="text-center py-5">
            <i class="bi bi-search display-1 text-muted opacity-25"></i>
            <h4 class="mt-3 text-muted">{{ __('branch.shop_no_result') }}</h4>
            <p class="text-muted">{{ __('branch.shop_try_again') }}</p>
        </div>

    </div>

    @include('branch.partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // ===== TÌM KIẾM =====
        const searchInput = document.getElementById('searchInput');
        const cards = document.querySelectorAll('.ticket-card');
        const noResult = document.getElementById('noResult');
        const resultCount = document.getElementById('resultCount');

        searchInput.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            let visible = 0;
            cards.forEach(function (card) {
                const name = card.querySelector('.ticket-name').textContent.toLowerCase();
                if (name.includes(q)) {
                    card.style.display = '';
                    visible++;
                } else {
                    card.style.display = 'none';
                }
            });
            noResult.style.display = visible === 0 ? 'block' : 'none';
            resultCount.innerHTML = `{{ __('branch.shop_showing') }} <strong>${visible}</strong> {{ __('branch.shop_games') }}`;
        });

        // ===== YÊU THÍCH =====
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
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#212529', color: '#fff' })
                            .fire({ icon: 'success', title: '{{ __('branch.added_wishlist') }}' });
                    } else {
                        icon.removeClass('bi-heart-fill').addClass('bi-heart');
                        Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, background: '#212529', color: '#fff' })
                            .fire({ icon: 'info', title: '{{ __('branch.removed_wishlist') }}' });
                    }
                }
            });
        });
    </script>
</body>

</html>