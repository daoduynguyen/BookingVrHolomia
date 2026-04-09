<!DOCTYPE html>
@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('shop.page_title') }}</title>
    <meta name="description" content="{{ __('shop.meta_description') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style> body { font-family: {{ $langConfig['font_family'] }}; } </style>
    {{-- Thêm SweetAlert2 để thông báo đẹp --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        /* CSS cho nút tim */
        .btn-wishlist {
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            transition: all 0.2s;
        }

        .btn-wishlist:hover {
            transform: scale(1.1);
            background: white;
        }
    </style>
</head>

<body class="bg-light text-dark">

    @include('partials.navbar')

    {{-- Mini Hero Banner --}}
    <div class="container-fluid p-0 mb-5">
        <div class="position-relative d-flex align-items-center justify-content-center"
            style="height: 300px; margin-bottom: 60px;
                    background: url('https://images.unsplash.com/photo-1593508512255-86ab42a8e620?q=80&w=2000&auto=format&fit=crop') center/bottom no-repeat fixed; background-size: cover;">

            <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50"></div>

            <div class="container position-relative text-center text-white" style="z-index: 1;">
                <h1 class="display-4 fw-bold text-uppercase mb-2 text-shadow" style="letter-spacing: 2px;" data-aos="fade-down" data-aos-duration="1000">
                    <i class="bi bi-shop text-info"></i> {{ __('shop.shop_title') }}
                </h1>
                <p class="lead text-light opacity-75" data-aos="fade-up" data-aos-delay="200">{{ __('shop.total_games', ['count' => $tickets->total()]) }}</p>
            </div>
        </div>
    </div>

    <!-- THANH TÌM KIẾM NỔI -->
    @include('partials.search_bar')

    <div class="container pb-5">

        <div class="row g-4">
            @foreach($tickets as $index => $ticket)
                <div class="col-md-4" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 100 }}">
                    <div
                        class="card h-100 card-game overflow-hidden rounded-4 border-0 {{ $ticket->status == 'maintenance' ? 'card-maintenance' : '' }}">

                        <div class="position-relative">
                            <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}"
                                class="card-img-top" alt="{{ $ticket->name }}" style="height: 240px; object-fit: cover;">

                            {{-- Tem BẢO TRÌ nằm chéo đè lên ảnh --}}
                            @if($ticket->status == 'maintenance')
                                <div class="maintenance-overlay">🔧 BẢO TRÌ</div>
                            @endif

                            {{-- Badge rating góc trên phải --}}
                            <div
                                class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow rounded-pill px-3 py-2">
                                <i class="bi bi-star-fill text-dark"></i> {{ $ticket->avg_rating }}
                            </div>

                            {{-- NÚT THẢ TIM góc trên trái --}}
                            <button
                                class="btn btn-light rounded-circle position-absolute top-0 start-0 m-3 shadow-sm btn-wishlist btn-toggle-wishlist-global"
                                data-id="{{ $ticket->id }}" title="Thêm vào yêu thích">
                                @if(Auth::check() && Auth::user()->favorites->contains($ticket->id))
                                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                                @else
                                    <i class="bi bi-heart text-danger fs-5"></i>
                                @endif
                            </button>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            {{-- Danh mục + lượt chơi --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ $ticket->category->name }}</span>
                                <small class="text-muted fw-bold" style="font-size: 0.8rem;">
                                    <i class="bi bi-person-check-fill text-secondary"></i>
                                    {{ number_format($ticket->play_count) }} {{ __('shop.views') ?? 'lượt' }}
                                </small>
                            </div>

                            <h5 class="card-title fw-bold mb-2 text-truncate" title="{{ $ticket->name }}"
                                style="font-size: 1.2rem;">
                                {{ $ticket->name }}
                            </h5>

                            <p class="small text-muted mb-4 fw-medium">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                {{ $ticket->locations->pluck('name')->join(', ') ?: __('shop.multiple_branches') }}
                            </p>

                            <div class="mt-auto pt-3 border-top border-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span
                                        class="small text-muted fw-medium">{{ __('shop.price_from') ?? 'Giá vé từ:' }}</span>
                                    <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                                </div>
                            </div>

                            @if($ticket->status == 'maintenance')
                                <button class="btn btn-secondary w-100 fw-bold py-2 text-uppercase shadow-sm mt-3"
                                    disabled>{{ __('shop.maintenance') }}</button>
                            @else
                                <div class="d-flex gap-2 mt-3">
                                    <a href="{{ route('ticket.show', $ticket->id) }}"
                                        class="btn btn-primary flex-grow-1 fw-bold py-2 text-uppercase shadow-sm">
                                        {{ __('shop.book_now') }}
                                    </a>
                                    <a href="{{ route('cart.add', $ticket->id) }}"
                                        class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm"
                                        title="{{ __('shop.add_to_cart') ?? 'Thêm vào giỏ' }}">
                                        <i class="bi bi-cart-plus fs-5"></i>
                                    </a>
                                    <a href="{{ route('ticket.show', $ticket->id) }}"
    class="btn btn-outline-primary fw-bold py-2 px-3 shadow-sm"
    title="Xem chi tiết">
    <i class="bi bi-eye fs-5"></i>
</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>

    </div>

    @include('partials.footer')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 800, offset: 50 });

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
    </script>

    {{-- [SCRIPT XỬ LÝ AJAX THẢ TIM] --}}
    <script>
        $(document).ready(function () {
            $('.btn-toggle-wishlist-global').click(function (e) {
                e.preventDefault();

                // Kiểm tra xem đã đăng nhập chưa (Nếu chưa thì báo lỗi)
                @if(!Auth::check())
                    Swal.fire({
                        icon: 'warning',
                        title: '{{ __("shop.not_logged_in") ?? "Chưa đăng nhập" }}',
                        text: '{{ __("shop.login_to_save_favorite") ?? "Vui lòng đăng nhập để lưu danh sách yêu thích!" }}',
                        confirmButtonText: '{{ __("shop.login_now") ?? "Đăng nhập ngay" }}',
                        confirmButtonColor: '#0dcaf0',
                        background: '#212529',
                        color: '#fff'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                @endif

                let btn = $(this);
                let icon = btn.find('i');
                let id = btn.data('id');

                $.ajax({
                    url: '/wishlist/toggle/' + id,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },

                    beforeSend: function () {
                        btn.prop('disabled', true).css('opacity', '0.6');
                    },
                    complete: function () {
                        btn.prop('disabled', false).css('opacity', '1');
                    },

                    success: function (response) {
                        if (response.is_favorited) {
                            // Đổi thành tim đỏ
                            icon.removeClass('bi-heart').addClass('bi-heart-fill');

                            // Hiệu ứng Toast thông báo
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                background: '#212529',
                                color: '#fff'
                            });
                            Toast.fire({ icon: 'success', title: '{{ __("shop.added_to_favorite") ?? "Đã thêm vào yêu thích" }}' });
                        } else {
                            // Đổi về tim rỗng
                            icon.removeClass('bi-heart-fill').addClass('bi-heart');

                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000,
                                background: '#212529',
                                color: '#fff'
                            });
                            Toast.fire({ icon: 'info', title: '{{ __("shop.removed_from_favorite") ?? "Đã xóa khỏi yêu thích" }}' });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 401 || xhr.status === 419) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Phiên đăng nhập hết hạn',
                                text: 'Vui lòng đăng nhập lại.',
                                confirmButtonText: 'Đăng nhập',
                                confirmButtonColor: '#0dcaf0',
                                background: '#212529',
                                color: '#fff'
                            }).then((r) => { if (r.isConfirmed) window.location.href = "{{ route('login') }}"; });
                        } else {
                            alert('Có lỗi xảy ra, vui lòng thử lại!');
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>