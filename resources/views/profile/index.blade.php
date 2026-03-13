<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hồ sơ của tôi - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* CSS nội bộ để tinh chỉnh giao diện Profile */
        .profile-card {
            background-color: rgba(33, 37, 41, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-pills .nav-link {
            color: #ccc;
            border-radius: 8px;
            padding: 12px 20px;
            margin-bottom: 5px;
            transition: all 0.3s;
        }

        .nav-pills .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.05);
            color: #fff;
            padding-left: 25px;
            /* Hiệu ứng trượt nhẹ */
        }

        .nav-pills .nav-link.active {
            background-color: #0dcaf0;
            /* Màu Info */
            color: #000;
            font-weight: bold;
            box-shadow: 0 4px 15px rgba(13, 202, 240, 0.3);
        }

        .form-control,
        .form-select {
            background-color: #1a1d20;
            border: 1px solid #343a40;
            color: #fff;
            padding: 12px;
        }

        .form-control:focus,
        .form-select:focus {
            background-color: #000;
            border-color: #0dcaf0;
            color: #fff;
            box-shadow: none;
        }

        /* Hiệu ứng cho Empty State */
        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
    </style>
</head>

<body class="bg-dark text-white">

    @include('partials.navbar')

    <div class="container py-5">
        {{-- Đổi tỷ lệ cột thành 4 - 8 cho cân đối --}}
        <div class="row g-5">

            {{-- CỘT TRÁI: SIDEBAR (col-lg-4) --}}
            <div class="col-lg-4">
                <div class="card profile-card rounded-4 p-4 h-100 shadow-lg">
                    {{-- Avatar & Info --}}
                    <div class="text-center mb-4 pb-4 border-bottom border-secondary border-opacity-25">
                        <div class="position-relative d-inline-block">
                            <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=0dcaf0&color=000&size=128' }}"
                                class="rounded-circle mb-3 border border-4 border-dark shadow" width="100" height="100"
                                style="object-fit: cover;">
                            <span
                                class="position-absolute bottom-0 end-0 bg-success border border-dark rounded-circle p-2"
                                title="Online"></span>
                        </div>
                        <h5 class="fw-bold mb-1 text-white">{{ $user->name }}</h5>
                        <p class="text-secondary small mb-0">{{ $user->email }}</p>
                    </div>

                    {{-- Menu --}}
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active d-flex align-items-center gap-3" id="v-pills-profile-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                            <i class="bi bi-person-gear fs-5"></i> Hồ sơ cá nhân
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3" id="v-pills-voucher-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-voucher" type="button" role="tab">
                            <i class="bi bi-ticket-perforated fs-5"></i> Kho Voucher
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3" id="v-pills-orders-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab">
                            <i class="bi bi-box-seam fs-5"></i> Đơn hàng của tôi
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3" id="v-pills-wishlist-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-wishlist" type="button" role="tab">
                            <i class="bi bi-heart fs-5"></i> Sản phẩm yêu thích
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3" id="v-pills-password-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-password" type="button" role="tab">
                            <i class="bi bi-shield-lock fs-5"></i> Đổi mật khẩu
                        </button>

                        <hr class="border-secondary border-opacity-25 my-3">

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="nav-link w-100 text-start text-danger hover-danger d-flex align-items-center gap-3">
                                <i class="bi bi-box-arrow-right fs-5"></i> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: NỘI DUNG (col-lg-8) --}}
            <div class="col-lg-8">
                <div class="card profile-card rounded-4 p-4 p-md-5 shadow-lg h-100">
                    <div class="tab-content" id="v-pills-tabContent">

                        {{-- Thông báo lỗi/thành công --}}
                        @if(session('success'))
                            <div
                                class="alert alert-success bg-success bg-opacity-25 text-white border-0 mb-4 rounded-3 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0 mb-4 rounded-3">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- 1. TAB HỒ SƠ --}}
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                            <h4
                                class="text-info fw-bold text-uppercase mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                                <i class="bi bi-person-lines-fill me-2"></i> Hồ sơ của tôi
                            </h4>

                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary small text-uppercase fw-bold">Họ và
                                            tên</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label
                                            class="form-label text-secondary small text-uppercase fw-bold">Email</label>
                                        <input type="text" class="form-control text-muted fst-italic"
                                            value="{{ $user->email }}" disabled style="cursor: not-allowed;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary small text-uppercase fw-bold">Số điện
                                            thoại</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', $user->phone) }}" placeholder="Chưa cập nhật">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary small text-uppercase fw-bold">Ngày
                                            sinh</label>
                                        <input type="date" name="birthday" class="form-control"
                                            value="{{ old('birthday', $user->birthday) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary small text-uppercase fw-bold">Giới
                                            tính</label>
                                        <select name="gender" class="form-select">
                                            <option value="" disabled selected>Chọn giới tính</option>
                                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>Nam
                                            </option>
                                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>Nữ
                                            </option>
                                            <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>Khác
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-secondary small text-uppercase fw-bold">Địa chỉ
                                            mặc định</label>
                                        <textarea name="address" class="form-control" rows="3"
                                            placeholder="Nhập địa chỉ nhận hàng">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-info px-4 py-2 fw-bold rounded-pill shadow-sm">
                                        <i class="bi bi-save me-2"></i> Lưu thay đổi
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB KHO VOUCHER (Thay thế cho Địa chỉ) --}}
                        <div class="tab-pane fade" id="v-pills-voucher" role="tabpanel">
                            <h4
                                class="text-info fw-bold text-uppercase mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                                <i class="bi bi-ticket-perforated-fill me-2"></i> Kho Voucher của tôi
                            </h4>

                            {{-- Danh sách Voucher --}}
                            <div class="coupon-list-container">
                                @foreach($coupons as $coupon)
                                    <div class="voucher-ticket">
                                        {{-- PHẦN TRÁI --}}
                                        {{-- Thêm class 'money-type' nếu là giảm tiền mặt để đổi màu vàng --}}
                                        <div class="voucher-left {{ $coupon->type == 'fixed' ? 'money-type' : '' }}">
                                            <i class="bi bi-ticket-detailed-fill fs-3 mb-2"></i>
                                            <h3 class="fw-bold m-0 display-6">
                                                {{-- Hiển thị giá trị: 20% hoặc 50K --}}
                                                {{ $coupon->type == 'percent' ? $coupon->value . '%' : number_format($coupon->value / 1000) . 'K' }}
                                            </h3>
                                            <small class="text-uppercase fw-bold opacity-75">Giảm giá</small>
                                        </div>

                                        {{-- PHẦN PHẢI --}}
                                        <div class="voucher-right">
                                            {{-- Thông tin chi tiết --}}
                                            <div class="d-flex flex-column justify-content-center h-100">
                                                <div class="d-flex align-items-center mb-2">
                                                    <span class="badge bg-danger me-2">HOT</span>
                                                    <h5 class="fw-bold text-primary m-0" style="letter-spacing: 1px;">
                                                        {{ $coupon->code }}
                                                    </h5>
                                                </div>

                                                <p class="text-while mb-2" style="line-height: 1.4;">
                                                    @if($coupon->type == 'percent')
                                                        Giảm <b>{{ $coupon->value }}%</b> tối đa cho đơn hàng.
                                                    @else
                                                        Giảm trực tiếp <b>{{ number_format($coupon->value) }}đ</b> vào đơn hàng.
                                                    @endif
                                                    <br>
                                                    <span class="text-secondary small fst-italic">Áp dụng cho tất cả trò
                                                        chơi.</span>
                                                </p>

                                                <div class="mt-auto">
                                                    <small class="text-danger fw-bold">
                                                        <i class="bi bi-clock-history me-1"></i> HSD:
                                                        {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </div>

                                            {{-- Nút hành động --}}
                                            <div class="ms-4">
                                                <button class="btn btn-primary btn-get-code rounded-pill shadow-sm"
                                                    onclick="copyCode('{{ $coupon->code }}')">
                                                    Lấy Mã
                                                </button>
                                                <div class="text-center mt-2">
                                                    <small class="text-while d-block">Số lượng có hạn</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Trạng thái trống --}}
                                @if($coupons->count() == 0)
                                    <div class="text-center text-secondary py-5 my-5 bg-dark bg-opacity-50 rounded-4">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="120"
                                            alt="Empty" class="mb-4 opacity-50">
                                        <h4>Chưa có mã giảm giá nào</h4>
                                        <p>Hãy quay lại sau để săn ưu đãi nhé!</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Nhập mã voucher thủ công --}}
                            <div class="mt-5 p-4 rounded-3 bg-black border border-secondary border-opacity-50">
                                <h6 class="text-white fw-bold mb-3"><i class="bi bi-keyboard me-2"></i>Nhập mã quà tặng
                                </h6>
                                <form class="d-flex gap-2">
                                    <input type="text" class="form-control bg-dark border-secondary text-white"
                                        placeholder="Nhập mã voucher tại đây...">
                                    <button class="btn btn-info fw-bold px-4">LƯU</button>
                                </form>
                            </div>
                        </div>

                        {{-- Script copy mã --}}
                        <script>
                            function copyCode(code) {
                                navigator.clipboard.writeText(code);
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Đã sao chép!',
                                    text: 'Mã: ' + code,
                                    background: '#1a1d20',
                                    color: '#fff',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        </script>

                        {{-- 3. TAB ĐƠN HÀNG (ĐÃ SỬA GIAO DIỆN) --}}
                        <div class="tab-pane fade" id="v-pills-orders" role="tabpanel">
                            <h4
                                class="text-info fw-bold text-uppercase mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                                <i class="bi bi-clock-history me-2"></i> Lịch sử đơn hàng
                            </h4>

                            @if($user->orders->isEmpty())
                                {{-- Giao diện trống đẹp --}}
                                <div class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-cart-x display-4 text-secondary opacity-50"></i>
                                    </div>
                                    <h4 class="text-white fw-bold mb-2">Bạn chưa có đơn hàng nào</h4>
                                    <p class="text-secondary mb-4" style="max-width: 400px; margin: 0 auto;">
                                        Hãy đặt vé ngay hôm nay để trải nghiệm các trò chơi VR đỉnh cao tại Holomia!
                                    </p>
                                    <a href="{{ route('ticket.shop') }}"
                                        class="btn btn-info rounded-pill px-5 py-3 fw-bold shadow hover-scale">
                                        <i class="bi bi-ticket-perforated me-2"></i> MUA VÉ NGAY
                                    </a>
                                </div>
                            @else
                                {{-- Bảng đơn hàng --}}
                                <div class="table-responsive">
                                    <table class="table table-dark table-hover align-middle mb-0">
                                        <thead>
                                            <tr class="text-secondary small text-uppercase border-bottom border-secondary">
                                                <th>Mã đơn</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th class="text-end">Chi tiết</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($user->orders as $order)
                                                <tr>
                                                    <td class="text-info fw-bold">#{{ $order->id }}</td>
                                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="fw-bold">{{ number_format($order->total_amount) }}đ</td>
                                                    <td>
                                                        
                                                        @if($order->status == 'pending')
                                                            <span
                                                                class="badge bg-warning text-dark border border-warning bg-opacity-75">Chờ
                                                                xử lý</span>
                                                        @elseif($order->status == 'paid')
                                                            <span class="badge bg-success border border-success bg-opacity-75">Đã
                                                                thanh toán</span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-danger border border-danger bg-opacity-75">Đã
                                                                hủy</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <button
                                                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-view-order"
                                                            data-id="{{ $order->id }}">
                                                            Xem <i class="bi bi-eye ms-1"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- 4. TAB YÊU THÍCH --}}
                        <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel">
                            <h4
                                class="text-danger fw-bold text-uppercase mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                                <i class="bi bi-heart-fill me-2"></i> Sản phẩm yêu thích
                            </h4>

                            @if($user->favorites->isEmpty())
                                {{-- Giao diện trống --}}
                                <div class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-heart-break display-4 text-secondary opacity-50"></i>
                                    </div>
                                    <h4 class="mt-3 text-secondary">Danh sách yêu thích trống</h4>
                                    <p class="text-white-50">Lưu lại những trò chơi bạn quan tâm để xem sau nhé.</p>
                                    <a href="{{ route('ticket.shop') }}"
                                        class="btn btn-outline-info rounded-pill px-4 mt-2">
                                        Khám phá ngay
                                    </a>
                                </div>
                            @else
                                {{-- Danh sách yêu thích --}}
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    @foreach($user->favorites as $item)
                                        <div class="col wishlist-item-{{ $item->id }}">
                                            <div
                                                class="card h-100 bg-black border border-secondary border-opacity-50 shadow-sm overflow-hidden">
                                                <div class="row g-0 h-100">
                                                    {{-- Ảnh --}}
                                                    <div class="col-4 position-relative">
                                                        <img src="{{ Str::startsWith($item->image_url, 'http') ? $item->image_url : asset($item->image_url) }}"
                                                            class="img-fluid h-100 w-100" style="object-fit: cover;"
                                                            alt="{{ $item->name }}">
                                                    </div>

                                                    {{-- Nội dung --}}
                                                    <div class="col-8">
                                                        <div class="card-body d-flex flex-column h-100 p-3">
                                                            <h6 class="card-title text-info fw-bold text-truncate">
                                                                {{ $item->name }}
                                                            </h6>
                                                            <p class="card-text text-white fw-bold mb-1">
                                                                {{ number_format($item->price) }}đ
                                                            </p>

                                                            <div class="mt-auto d-flex gap-2">
                                                                {{-- Nút Mua ngay --}}
                                                                {{-- Kiểm tra trạng thái bảo trì --}}
                                                                @if($item->status == 'maintenance')
                                                                    <button class="btn btn-sm btn-secondary flex-grow-1 fw-bold"
                                                                        disabled>
                                                                        <i class="bi bi-cone-striped"></i> Bảo trì
                                                                    </button>
                                                                @else
                                                                    {{-- Trỏ vào trang Nhập thông tin --}}
                                                                    <a href="{{ route('booking.form', $item->id) }}"
                                                                        class="btn btn-sm btn-info flex-grow-1 fw-bold text-dark">
                                                                        <i class="bi bi-ticket-perforated"></i> Đặt vé
                                                                    </a>
                                                                @endif

                                                                {{-- Nút Xóa tim (AJAX) --}}
                                                                <button
                                                                    class="btn btn-sm btn-outline-danger btn-toggle-wishlist"
                                                                    data-id="{{ $item->id }}">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        {{-- 5. TAB MẬT KHẨU --}}
                        <div class="tab-pane fade" id="v-pills-password" role="tabpanel">
                            <h4
                                class="text-danger fw-bold text-uppercase mb-4 border-bottom border-secondary border-opacity-25 pb-3">
                                <i class="bi bi-shield-lock-fill me-2"></i> Đổi mật khẩu
                            </h4>

                            <form action="{{ route('profile.password') }}" method="POST" class="mx-auto"
                                style="max-width: 500px;">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label text-secondary small text-uppercase fw-bold">Mật khẩu hiện
                                        tại</label>
                                    <input type="password" name="current_password" class="form-control" required
                                        placeholder="••••••">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-secondary small text-uppercase fw-bold">Mật khẩu
                                        mới</label>
                                    <input type="password" name="new_password" class="form-control" required
                                        placeholder="••••••">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-secondary small text-uppercase fw-bold">Nhập lại mật
                                        khẩu mới</label>
                                    <input type="password" name="new_password_confirmation" class="form-control"
                                        required placeholder="••••••">
                                </div>
                                <div class="text-end">
                                    <button type="submit"
                                        class="btn btn-danger px-4 py-2 fw-bold rounded-pill shadow-sm">
                                        <i class="bi bi-arrow-repeat me-2"></i> Cập nhật
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Thư viện Pháo hoa & Popup --}}
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- Thêm dòng này để chạy được AJAX --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('payment_success'))

                // 1. Tự động chuyển sang Tab "Đơn hàng"
                const ordersTabTrigger = document.getElementById('v-pills-orders-tab');
                if (ordersTabTrigger) {
                    const tab = new bootstrap.Tab(ordersTabTrigger);
                    tab.show();
                }

                // 2. Hiển thị Popup "Mini"
                Swal.fire({
                    title: '',
                    html: `
                                        <div class="text-center">
                                            <div class="mb-3">
                                                <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; background: rgba(25, 135, 84, 0.2); box-shadow: 0 0 15px rgba(25, 135, 84, 0.3);">
                                                    <i class="bi bi-check-lg text-success" style="font-size: 2rem;"></i>
                                                </div>
                                            </div>

                                            <h5 class="text-white fw-bold text-uppercase mb-1">Thanh toán thành công</h5>
                                            <p class="text-secondary" style="font-size: 0.9rem;">Đơn hàng đã được ghi nhận.</p>

                                            <div class="bg-secondary bg-opacity-10 rounded-3 p-3 mb-3 border border-secondary border-opacity-25 mx-auto text-start" style="font-size: 0.9rem;">

                                                <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-secondary border-opacity-25 pb-2">
                                                    <span class="text-white-50">Tổng tiền:</span>
                                                    <span class="text-info fw-bold fs-5">
                                                        {{ number_format(session('total_amount')) }}đ
                                                    </span>
                                                </div>

                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-white-50">Mã đơn:</span>
                                                    <strong class="text-white">#{{ session('order_id') }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-white-50">Phương thức:</span>
                                                    <span class="text-white">{{ session('method') == 'cod' ? 'Tiền mặt' : 'Chuyển khoản' }}</span>
                                                </div>
                                            </div>

                                            <p class="small text-while fst-italic mb-0" style="font-size: 0.8rem;">
                                                <i class="bi bi-envelope-check me-1"></i> Vé đã được gửi tới hệ thống.
                                            </p>
                                        </div>
                                    `,
                    background: '#1a1d20',
                    color: '#fff',
                    showConfirmButton: true,
                    confirmButtonText: 'Đóng',
                    confirmButtonColor: '#0dcaf0',
                    width: '360px',  // Thu nhỏ chiều rộng popup
                    padding: '1.5em', // Thu nhỏ khoảng cách lề
                    allowOutsideClick: false,
                    backdrop: `rgba(0,0,0,0.8)`
                });

                // 3. Hiệu ứng pháo hoa "Nhẹ nhàng"
                var duration = 2000; // Giảm thời gian xuống 2s
                var end = Date.now() + duration;

                (function frame() {
                    // Bắn ít hạt hơn (particleCount thấp)
                    confetti({
                        particleCount: 2,
                        angle: 60,
                        spread: 40, // Góc bắn hẹp lại
                        origin: { x: 0 },
                        scalar: 0.7, // Hạt nhỏ đi (0.7 so với bình thường là 1)
                        colors: ['#0dcaf0', '#ffffff']
                    });
                    confetti({
                        particleCount: 2,
                        angle: 120,
                        spread: 40,
                        origin: { x: 1 },
                        scalar: 0.7,
                        colors: ['#0dcaf0', '#ffffff']
                    });

                    if (Date.now() < end) {
                        requestAnimationFrame(frame);
                    }
                }());

            @endif

            // XỬ LÝ SỰ KIỆN CLICK NÚT "XEM"
            $('.btn-view-order').click(function (e) {
                e.preventDefault();
                let orderId = $(this).data('id');

                // 1. Hiện loading trước
                Swal.fire({
                    title: 'Đang tải hóa đơn...',
                    background: '#1a1d20',
                    color: '#fff',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // 2. Gọi AJAX lấy HTML hóa đơn
                $.ajax({
                    url: '/profile/order/' + orderId, // Gọi route đã tạo ở Bước 1
                    method: 'GET',
                    success: function (response) {
                        // 3. Hiển thị Popup hóa đơn
                        Swal.fire({
                            html: response.html, // Chèn HTML nhận được vào popup
                            background: '#1a1d20',
                            color: '#fff',
                            showConfirmButton: true,
                            confirmButtonText: 'Đóng lại',
                            confirmButtonColor: '#6c757d', // Màu xám
                            showCancelButton: true,
                            cancelButtonText: '<i class="bi bi-printer"></i> In hóa đơn',
                            cancelButtonColor: '#0dcaf0', // Màu xanh
                            width: '500px', // Chiều rộng vừa phải cho hóa đơn
                            padding: '2em',
                            allowOutsideClick: true,
                            backdrop: `rgba(0,0,0,0.85)`
                        }).then((result) => {
                            // Nếu bấm nút In (Cancel button)
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                window.print(); // Hoặc xử lý in riêng
                            }
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Không thể tải chi tiết đơn hàng!',
                            background: '#1a1d20',
                            color: '#fff'
                        });
                    }
                });
            });
        });
    </script>


    {{-- Script Ẩn thông báo tự động --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function () {
            let alertBox = document.querySelector('.alert');
            if (alertBox) {
                alertBox.style.transition = "opacity 0.5s ease";
                alertBox.style.opacity = "0";
                setTimeout(function () {
                    alertBox.remove();
                }, 500);
            }
        }, 3000); 
    </script>

    <script>
        $(document).ready(function () {
            // Xử lý nút Xóa tim ngay tại trang Profile
            $('.btn-toggle-wishlist').click(function (e) {
                e.preventDefault();
                let btn = $(this);
                let id = btn.data('id');
                let container = $('.wishlist-item-' + id); // Tìm cái khung chứa sản phẩm đó

                $.ajax({
                    url: '/wishlist/toggle/' + id,
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        // Hiệu ứng xóa mờ dần rồi biến mất
                        container.fadeOut(300, function () {
                            $(this).remove();

                            // Nếu xóa hết thì reload lại trang để hiện giao diện Trống (cho đẹp)
                            if ($('.col[class*="wishlist-item-"]').length == 0) {
                                location.reload();
                            }
                        });

                        // Thông báo nhỏ góc màn hình (Toast)
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            background: '#212529',
                            color: '#fff'
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Đã xóa khỏi yêu thích'
                        });
                    }
                });
            });
        });
    </script>
</body>

</html>