<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cửa hàng vé - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
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
                <h1 class="display-4 fw-bold text-uppercase mb-2 text-shadow" style="letter-spacing: 2px;">
                    <i class="bi bi-shop text-info"></i> KHO VÉ TOÀN TẬP
                </h1>
                <p class="lead text-light opacity-75">Hiện có tổng cộng {{ $tickets->total() }} trò chơi trong hệ thống</p>
            </div>
        </div>
    </div>
    
    <!-- THANH TÌM KIẾM NỔI -->
    @include('partials.search_bar')

    <div class="container pb-5">

        <div class="row g-4">
            @foreach($tickets as $ticket)
                <div class="col-md-4">
                    <div
                        class="card h-100 card-game overflow-hidden rounded-4 border-0 {{ $ticket->status == 'maintenance' ? 'card-maintenance' : '' }}">

                        @if($ticket->status == 'maintenance')
                            <div class="badge-maintenance">BẢO TRÌ</div>
                        @endif

                        <div class="position-relative">
                            <img src="{{ $ticket->image_url }}" class="card-img-top"
                                style="height: 200px; object-fit: cover;">

                            {{-- Badge danh mục --}}
                            <span class="position-absolute top-0 start-0 m-3 badge bg-primary bg-opacity-90 px-3 py-2 rounded-pill shadow-sm">
                                {{ $ticket->category->name }}
                            </span>

                            {{-- NÚT THẢ TIM  --}}
                            <button
                                class="btn btn-light rounded-circle position-absolute top-0 end-0 m-3 shadow-sm btn-wishlist btn-toggle-wishlist-global"
                                data-id="{{ $ticket->id }}" title="Thêm vào yêu thích">
                                @if(Auth::check() && Auth::user()->favorites->contains($ticket->id))
                                    <i class="bi bi-heart-fill text-danger fs-5"></i>
                                @else
                                    <i class="bi bi-heart text-danger fs-5"></i>
                                @endif
                            </button>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title fw-bold text-truncate mb-3 align-items-center" style="font-size: 1.25rem;">{{ $ticket->name }}</h5>

                            <div class="d-flex justify-content-between small text-muted mb-4 fw-medium">
                                <span><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $ticket->location->name }}</span>
                                <span class="bg-primary bg-opacity-10 text-primary px-2 py-1 rounded-pill"><i class="bi bi-star-fill"></i> {{ $ticket->avg_rating }}</span>
                            </div>

                            <div
                                class="mt-auto d-flex justify-content-between align-items-center bg-light p-3 rounded-4 shadow-sm border border-light">
                                <div>
                                    <span class="d-block small text-muted fw-bold mb-1" style="font-size: 0.75rem;">GIÁ TỪ</span>
                                    <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                                </div>

                                @if($ticket->status == 'maintenance')
                                    <button class="btn btn-secondary rounded-pill px-4 fw-bold shadow-sm" disabled>Tạm đóng</button>
                                @else
                                    {{-- TRỎ ĐẾN ROUTE booking.form --}}
                                    <a href="{{ route('booking.form', $ticket->id) }}"
                                        class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm d-flex align-items-center">
                                        <i class="bi bi-ticket-perforated me-2"></i> ĐẶT VÉ
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $tickets->links('pagination::bootstrap-5') }}
        </div>

    </div>

    <footer class="bg-black text-center py-4 border-top border-secondary mt-5 text-muted">
        <p class="mb-0">© 2026 Holomia VR System</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- [SCRIPT XỬ LÝ AJAX THẢ TIM] --}}
    <script>
        $(document).ready(function () {
            $('.btn-toggle-wishlist-global').click(function (e) {
                e.preventDefault();

                // Kiểm tra xem đã đăng nhập chưa (Nếu chưa thì báo lỗi)
                @if(!Auth::check())
                    Swal.fire({
                        icon: 'warning',
                        title: 'Chưa đăng nhập',
                        text: 'Vui lòng đăng nhập để lưu danh sách yêu thích!',
                        confirmButtonText: 'Đăng nhập ngay',
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
                            Toast.fire({ icon: 'success', title: 'Đã thêm vào yêu thích' });
                        } else {
                            // Đổi về tim rỗng
                            icon.removeClass('bi-heart-fill').addClass('bi-heart');
                        }
                    },
                    error: function () {
                        alert('Có lỗi xảy ra, vui lòng thử lại!');
                    }
                });
            });
        });
    </script>
</body>

</html>