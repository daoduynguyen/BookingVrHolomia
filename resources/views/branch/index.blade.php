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
        }
    </style>
</head>

<body class="bg-light text-dark">

    @include('branch.partials.navbar')

    <div class="hero-section container-fluid p-0 mb-5">
        <div class="hero-overlay"></div>
        <div class="row g-0 align-items-center h-100 position-relative" style="z-index: 2;">
            <div class="col-lg-6 px-5" style="padding-left: 8% !important;">
                <div class="mb-3">
                    <i class="bi bi-geo-alt-fill display-4 text-primary"></i>
                </div>
                <h1 class="display-3 fw-bold text-uppercase mb-3" style="letter-spacing: 2px;">
                    Holomia <br><span class="text-primary">{{ $location->name }}</span>
                </h1>
                <p class="lead fs-4 mb-5 text-secondary fw-medium">
                    Trải nghiệm thực tế ảo đỉnh cao tại trung tâm {{ $location->name }}. Đặt vé ngay hôm nay để nhận ưu
                    đãi!
                </p>
                <a href="#list-games" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-lg">
                    KHÁM PHÁ NGAY <i class="bi bi-arrow-down-circle ms-2"></i>
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
                <i class="bi bi-controller me-2"></i> Trò chơi tại cơ sở
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

                            <button
                                class="btn btn-light rounded-circle position-absolute top-0 start-0 m-3 shadow-sm btn-wishlist"
                                data-id="{{ $ticket->id }}" title="Thêm vào yêu thích">
                                @if(Auth::check() && Auth::user()->favorites->contains($ticket->id))
                                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                                @else
                                    <i class="bi bi-heart text-danger fs-5"></i>
                                @endif
                            </button>

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
                                        {{ number_format($ticket->play_count) }} lượt
                                    </small>
                                </div>
                                
                                <h5 class="card-title fw-bold mb-3 text-truncate">{{ $ticket->name }}</h5>

                                <div class="mt-auto pt-3 border-top border-light mb-4 d-flex justify-content-between align-items-center">
                                    <span class="small text-muted fw-medium">{{ __('home.price_from') }}</span>
                                    <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                                </div>

                                @if($ticket->status == 'maintenance')
                                    <button class="btn btn-secondary w-100 fw-bold py-2 text-uppercase" disabled>BẢO TRÌ</button>
                                @else
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('branch.booking.form', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" class="btn btn-primary flex-grow-1 fw-bold py-2 text-uppercase shadow-sm d-flex align-items-center justify-content-center">
                                            ĐẶT VÉ NGAY
                                        </a>
                                        <a href="{{ route('branch.cart.add.quick', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm d-flex align-items-center justify-content-center" title="Thêm vào giỏ">
                                            <i class="bi bi-cart-plus fs-5"></i>
                                        </a>
                                        <a href="{{ route('branch.detail', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm d-flex align-items-center justify-content-center" title="Xem chi tiết">
                                            <i class="bi bi-eye fs-5"></i>
                                        </a>
                                    </div>
                                @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center text-muted py-5">
                    <h3>Hiện tại chưa có trò chơi nào tại cơ sở này.</h3>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         BẢN ĐỒ
    ════════════════════════════════════════ --}}
    @if($location->maps_url)
    <div class="container pb-5">
        <h2 class="text-dark fw-bold text-uppercase d-flex align-items-center mb-4">
            <i class="bi bi-map-fill text-primary me-3 fs-3"></i> Tìm đường đến chúng tôi
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
</body>

</html>