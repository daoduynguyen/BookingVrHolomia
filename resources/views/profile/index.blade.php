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
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .nav-pills .nav-link {
            color: var(--text-muted);
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 5px;
            transition: all 0.3s;
            border: 1px solid transparent;
        }

        .nav-pills .nav-link:hover {
            background-color: #f1f5f9;
            color: var(--primary);
            padding-left: 25px;
            border-color: #e2e8f0;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: #fff !important;
            font-weight: bold;
            box-shadow: var(--shadow-sm);
            border: none;
        }

        /* Hiệu ứng cho Empty State */
        .empty-state-icon {
            width: 120px;
            height: 120px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }

        /* Boost Bootstrap Modal Z-Index to stay on top of SweetAlert2 */
        .modal {
            z-index: 9999 !important;
        }
        .modal-backdrop {
            z-index: 9998 !important;
        }
    </style>
</head>

<body>

    @include('partials.navbar')

    <div class="container py-5">
        {{-- Đổi tỷ lệ cột thành 4 - 8 cho cân đối --}}
        <div class="row g-5">

            {{-- CỘT TRÁI: SIDEBAR (col-lg-4) --}}
            <div class="col-lg-4">
                <div class="card profile-card rounded-4 p-4 h-100 shadow-sm border-0">
                    {{-- Avatar & Info --}}
                    <div class="text-center mb-4 pb-4 border-bottom border-light">
                        <div class="position-relative d-inline-block">
                            <!-- Clickable Avatar -->
                            <label for="avatarInput" style="cursor: pointer;">
                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=2563eb&color=fff&size=128' }}"
                                    class="rounded-circle mb-3 border border-3 border-white shadow-sm" width="100" height="100"
                                    style="object-fit: cover; transition: opacity 0.3s;"
                                    title="Bấm để đổi ảnh đại diện">
                                <div class="position-absolute translate-middle px-2 py-1 bg-white rounded-pill shadow-sm border" style="bottom: -10px; left: 50%; font-size: 0.75rem; color: var(--primary); white-space: nowrap; pointer-events: none;">
                                    <i class="bi bi-camera me-1"></i>Đổi ảnh
                                </div>
                            </label>

                            <!-- Hidden Form -->
                            <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                                @csrf
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
                            </form>

                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2"
                                title="Online"></span>
                        </div>
                        <h5 class="fw-bold mb-1 mt-3">{{ $user->name }}</h5>
                        <p class="text-muted small mb-0">{{ $user->email }}</p>
                    </div>

                    {{-- Menu --}}
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active d-flex align-items-center gap-3 mb-2" id="v-pills-profile-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                            <i class="bi bi-person-gear fs-5"></i> Hồ sơ cá nhân
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3 mb-2" id="v-pills-voucher-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-voucher" type="button" role="tab">
                            <i class="bi bi-ticket-perforated fs-5"></i> Kho Voucher
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3 mb-2" id="v-pills-orders-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab">
                            <i class="bi bi-box-seam fs-5"></i> Đơn hàng của tôi
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3 mb-2" id="v-pills-wishlist-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-wishlist" type="button" role="tab">
                            <i class="bi bi-heart fs-5"></i> Sản phẩm yêu thích
                        </button>

                        <button class="nav-link d-flex align-items-center gap-3" id="v-pills-password-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-password" type="button" role="tab">
                            <i class="bi bi-shield-lock fs-5"></i> Đổi mật khẩu
                        </button>

                        <hr class="border-light my-3">

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="nav-link w-100 text-start text-danger hover-danger d-flex align-items-center gap-3 border border-danger">
                                <i class="bi bi-box-arrow-right fs-5"></i> Đăng xuất
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: NỘI DUNG (col-lg-8) --}}
            <div class="col-lg-8">
                <div class="card profile-card rounded-4 p-4 p-md-5 shadow-sm border-0 h-100">
                    <div class="tab-content" id="v-pills-tabContent">

                        {{-- Thông báo lỗi/thành công --}}
                        @if(session('success'))
                            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
                                <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger shadow-sm border-0 mb-4 rounded-3">
                                <ul class="mb-0 ps-3">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- 1. TAB HỒ SƠ --}}
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                            <h4 class="text-primary fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
                                <i class="bi bi-person-lines-fill me-2"></i> Hồ sơ của tôi
                            </h4>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div
                                        class="bg-light border border-info border-opacity-25 rounded-3 p-3 h-100 d-flex align-items-center">
                                        <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                            <i class="bi bi-wallet2 text-info fs-3"></i>
                                        </div>
                                        <div>
                                        <div class="d-flex align-items-center mb-1">
                                            <p class="text-muted mb-0 small text-uppercase fw-bold me-2">Tổng số dư</p>
                                            {{-- Biểu tượng con mắt (Mặc định nhắm) --}}
                                            <i class="bi bi-eye-slash text-secondary fs-5" id="toggleBalanceIcon" style="cursor: pointer; transition: 0.3s;" title="Hiện/Ẩn số dư"></i>
                                        </div>
                                        {{-- Số tiền mặc định là các dấu sao. Tiền thật giấu trong thuộc tính data-balance --}}
                                        <h4 class="text-info fw-bold mb-0" id="balanceAmount" data-balance="{{ number_format(Auth::user()->balance ?? 0) }}đ">
                                            ********
                                        </h4>
                                    </div>
                                    </div>
                                </div>
                               <div class="col-md-6">
                                    {{-- Khởi tạo logic màu sắc theo Hạng --}}
                                   @php
$tier = Auth::user()->tier ?? 'Thành viên';

// Bảng màu cho từng hạng (Điều chỉnh cho giao diện sáng)
$tierStyles = [
    'Thành viên' => ['color' => '#6c757d', 'bg' => 'rgba(108, 117, 125, 0.15)'], // Xám
    'Bạc' => ['color' => '#6c757d', 'bg' => 'rgba(108, 117, 125, 0.15)'], // Bạc
    'Vàng' => ['color' => '#ffc107', 'bg' => 'rgba(255, 193, 7, 0.15)'],   // Vàng
    'Kim Cương' => ['color' => '#0dcaf0', 'bg' => 'rgba(13, 202, 240, 0.15)'],  // Xanh dương (Cyan)
    'VIP' => ['color' => '#8b5cf6', 'bg' => 'rgba(139, 92, 246, 0.15)']   // Tím VIP
];

// Nếu lỗi không lấy được hạng thì lấy màu mặc định
$style = $tierStyles[$tier] ?? $tierStyles['Thành viên'];
                                    @endphp

                                    <div class="bg-light border rounded-3 p-3 h-100 d-flex align-items-center" 
                                         style="border-color: {{ $style['color'] }}50 !important; box-shadow: 0 0 15px {{ $style['bg'] }};">
                                         
                                        {{-- Icon Ngôi sao đổi màu --}}
                                        <div class="p-3 rounded-circle me-3" style="background-color: {{ $style['bg'] }};">
                                            <i class="bi bi-star-fill fs-3" style="color: {{ $style['color'] }}; text-shadow: 0 0 10px {{ $style['color'] }}50;"></i>
                                        </div>
                                        
                                        {{-- Tên hạng đổi màu --}}
                                        <div>
                                            <p class="text-muted mb-0 small text-uppercase fw-bold">Hạng thành viên</p>
                                            <h4 class="fw-bold mb-0 text-uppercase" style="color: {{ $style['color'] }}; letter-spacing: 1px;">
                                                {{ $tier }}
                                                <span class="fs-6 text-muted fw-normal text-capitalize" style="letter-spacing: 0;">
                                                    ({{ Auth::user()->points ?? 0 }} điểm)
                                                </span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('profile.update') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label">Họ và tên</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email</label>
                                        <input type="text" class="form-control text-muted fst-italic"
                                            value="{{ $user->email }}" disabled style="cursor: not-allowed; background-color: #e2e8f0 !important;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Số điện thoại</label>
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
                                class="text-primary fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
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

                                                <p class="text-dark mb-2" style="line-height: 1.4;">
                                                    @if($coupon->type == 'percent')
                                                        Giảm <b>{{ $coupon->value }}%</b> tối đa cho đơn hàng.
                                                    @else
                                                        Giảm trực tiếp <b>{{ number_format($coupon->value) }}đ</b> vào đơn hàng.
                                                    @endif
                                                    <br>
                                                    <span class="text-muted small fst-italic">Áp dụng cho tất cả trò
                                                        chơi.</span>
                                                </p>

                                                <div class="mt-auto">
                                                    <small class="text-danger fw-bold">
                                                        <i class="bi bi-clock-history me-1"></i> HSD:
                                                        {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="ms-4">
                                                <button class="btn btn-primary btn-get-code rounded-pill shadow-sm"
                                                    onclick="copyCode('{{ $coupon->code }}')">
                                                    Lấy Mã
                                                </button>
                                                <div class="text-center mt-2">
                                                    <small class="text-muted d-block">Số lượng có hạn</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- Trạng thái trống --}}
                                @if($coupons->count() == 0)
                                    <div class="text-center text-muted py-5 my-5 bg-light rounded-4 border">
                                        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="120"
                                            alt="Empty" class="mb-4 opacity-50">
                                        <h4>Chưa có mã giảm giá nào</h4>
                                        <p>Hãy quay lại sau để săn ưu đãi nhé!</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Nhập mã voucher thủ công --}}
                            <div class="mt-5 p-4 rounded-3 bg-light border border-secondary border-opacity-10">
                                <h6 class="text-dark fw-bold mb-3"><i class="bi bi-keyboard me-2"></i>Nhập mã quà tặng
                                </h6>
                                <form class="d-flex gap-2">
                                    <input type="text" class="form-control bg-white text-dark"
                                        placeholder="Nhập mã voucher tại đây...">
                                    <button class="btn btn-primary fw-bold px-4">LƯU</button>
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
                                class="text-primary fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
                                <i class="bi bi-clock-history me-2"></i> Lịch sử đơn hàng
                            </h4>

                            @if($user->orders->isEmpty())
                                {{-- Giao diện trống đẹp --}}
                                <div class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-cart-x display-4 text-muted opacity-50"></i>
                                    </div>
                                    <h4 class="text-dark fw-bold mb-2">Bạn chưa có đơn hàng nào</h4>
                                    <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                                        Hãy đặt vé ngay hôm nay để trải nghiệm các trò chơi VR đỉnh cao tại Holomia!
                                    </p>
                                    <a href="{{ route('ticket.shop') }}"
                                        class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow hover-scale">
                                        <i class="bi bi-ticket-perforated me-2"></i> MUA VÉ NGAY
                                    </a>
                                </div>
                            @else
                                {{-- Bảng đơn hàng --}}
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr class="text-muted small text-uppercase border-bottom">
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
                                                    <td class="text-primary fw-bold">#{{ $order->id }}</td>
                                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="fw-bold">{{ number_format($order->total_amount) }}đ</td>
                                                    <td>
                                                        @if($order->status == 'pending')
                                                            <span class="badge bg-warning text-dark bg-opacity-75">Chờ xử lý</span>
                                                        @elseif($order->status == 'paid')
                                                            <span class="badge bg-success bg-opacity-75">Đã thanh toán</span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-danger bg-opacity-75">Đã hủy</span>

                                                            {{-- Cập nhật thêm Hoàn vé --}}
                                                        @elseif($order->status == 'refunded')
                                                            <span class="badge bg-light text-dark text-uppercase border border-secondary">Hoàn vé</span>

                                                            {{-- Trạng thái Hết hạn --}}
                                                        @elseif($order->status == 'expired')
                                                            <span class="badge bg-light text-dark text-uppercase border border-secondary">Hết hạn</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-view-order" data-id="{{ $order->id }}">
                                                            Xem <i class="bi bi-eye ms-1"></i>
                                                        </button>
                                                    </td>                                 </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>

                        {{-- 4. TAB YÊU THÍCH --}}
                        <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel">
                            <h4
                                class="text-danger fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
                                <i class="bi bi-heart-fill me-2"></i> Sản phẩm yêu thích
                            </h4>

                            @if($user->favorites->isEmpty())
                                {{-- Giao diện trống --}}
                                <div class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-heart-break display-4 text-muted opacity-50"></i>
                                    </div>
                                    <h4 class="mt-3 text-muted">Danh sách yêu thích trống</h4>
                                    <p class="text-muted">Lưu lại những trò chơi bạn quan tâm để xem sau nhé.</p>
                                    <a href="{{ route('ticket.shop') }}"
                                        class="btn btn-outline-primary rounded-pill px-4 mt-2">
                                        Khám phá ngay
                                    </a>
                                </div>
                            @else
                                {{-- Danh sách yêu thích --}}
                                <div class="row row-cols-1 row-cols-md-2 g-4">
                                    @foreach($user->favorites as $item)
                                        <div class="col wishlist-item-{{ $item->id }}">
                                            <div
                                                class="card h-100 bg-white border border-light shadow-sm overflow-hidden mb-0">
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
                                                            <h6 class="card-title text-primary fw-bold text-truncate">
                                                                {{ $item->name }}
                                                            </h6>
                                                            <p class="card-text fw-bold mb-1 text-dark fs-5">
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
                                                                        class="btn btn-sm btn-primary flex-grow-1 fw-bold">
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
                                class="text-danger fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
                                <i class="bi bi-shield-lock-fill me-2"></i> Đổi mật khẩu
                            </h4>

                            <form action="{{ route('profile.password') }}" method="POST" class="mx-auto"
                                style="max-width: 500px;">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Mật khẩu hiện
                                        tại</label>
                                    <input type="password" name="current_password" class="form-control" required
                                        placeholder="••••••">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Mật khẩu
                                        mới</label>
                                    <input type="password" name="new_password" class="form-control" required
                                        placeholder="••••••">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label text-muted small text-uppercase fw-bold">Nhập lại mật
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            @if(session('payment_success'))

                            // MÓC DỮ LIỆU TỪ DATABASE LÊN DỰA VÀO ID ĐƠN HÀNG
                            @php
                $pMethod = 'banking';
                $locationName = 'Đang cập nhật'; // Thêm biến này
                if (session('order_id')) {
                    // Nạp luôn relation location vào
                    $orderData = \App\Models\Order::with('location')->find(session('order_id'));
                    if ($orderData) {
                        $pMethod = $orderData->payment_method;
                        // Lấy tên cơ sở từ Order
                        $locationName = $orderData->location ? $orderData->location->name : 'Đang cập nhật';
                    }
                }
                            @endphp

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

                                        <h5 class="text-dark fw-bold text-uppercase mb-1">Thanh toán thành công</h5>
                                        <p class="text-muted" style="font-size: 0.9rem;">Đơn hàng đã được ghi nhận.</p>

                                        <div class="bg-light rounded-3 p-3 mb-3 border border-light mx-auto text-start" style="font-size: 0.9rem;">

                                            {{-- DÒNG MỚI CHÈN THÊM: HIỂN THỊ CƠ SỞ --}}
                                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-light pb-2">
                                               <span class="text-muted">Cơ sở:</span>
                                               <span class="text-primary fw-bold text-end">{{ $locationName }}</span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-light pb-2">

                                                <span class="text-muted">Tổng tiền:</span>
                                                <span class="text-primary fw-bold fs-5">
                                                    {{ number_format(session('total_amount')) }}đ
                                                </span>
                                            </div>

                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Mã đơn:</span>
                                                <strong class="text-dark">#{{ session('order_id') }}</strong>
                                            </div>

                                            <div class="d-flex justify-content-between">
                                                <span class="text-muted">Phương thức:</span>
                                                <span class="fw-bold text-dark">
                                                    {{ $pMethod == 'wallet' ? 'Ví Holomia' : ($pMethod == 'cod' ? 'Tiền mặt' : 'Chuyển khoản') }}
                                                </span>
                                            </div>
                                        </div>

                                        <p class="small text-muted fst-italic mb-0" style="font-size: 0.8rem;">
                                            <i class="bi bi-envelope-check me-1"></i> Vé đã được gửi tới hệ thống.
                                        </p>
                                    </div>
                                `,
                                background: '#fff',
                                color: '#1f2937',
                                showConfirmButton: true,
                                confirmButtonText: 'Đóng',
                                confirmButtonColor: '#0d6efd',
                                width: '360px',
                                padding: '1.5em',
                                allowOutsideClick: false,
                                backdrop: `rgba(0,0,0,0.8)`
                            });

                            // 3. Hiệu ứng pháo hoa "Nhẹ nhàng"
                            var duration = 2000; 
                            var end = Date.now() + duration;

                            (function frame() {
                                confetti({ particleCount: 2, angle: 60, spread: 40, origin: { x: 0 }, scalar: 0.7, colors: ['#0dcaf0', '#ffffff'] });
                                confetti({ particleCount: 2, angle: 120, spread: 40, origin: { x: 1 }, scalar: 0.7, colors: ['#0dcaf0', '#ffffff'] });
                                if (Date.now() < end) { requestAnimationFrame(frame); }
                            }());

            @endif

            // XỬ LÝ SỰ KIỆN CLICK NÚT "XEM"
            $('.btn-view-order').click(function (e) {
                e.preventDefault();
                let orderId = $(this).data('id');

                Swal.fire({
                    title: 'Đang tải hóa đơn...',
                    background: '#fff',
                    color: '#1f2937',
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '/profile/order/' + orderId,
                    method: 'GET',
                    success: function (response) {
                        Swal.fire({
                            html: response.html,
                            background: '#fff',
                            color: '#1f2937',
                            showConfirmButton: true,
                            confirmButtonText: 'Đóng lại',
                            confirmButtonColor: '#6c757d',
                            showCancelButton: true,
                            cancelButtonText: '<i class="bi bi-printer"></i> In hóa đơn',
                            cancelButtonColor: '#0dcaf0',
                            width: '500px',
                            padding: '2em',
                            allowOutsideClick: true,
                            backdrop: `rgba(0,0,0,0.85)`
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                window.print();
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
                            background: '#fff',
                            color: '#1f2937'
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
    {{-- Script ẩn/hiện số dư --}}
    <script>
        $(document).ready(function () {
            $('#toggleBalanceIcon').click(function () {
                let icon = $(this);
                let balanceText = $('#balanceAmount');
                let realBalance = balanceText.data('balance'); // Lấy số tiền thật đang giấu

                // Nếu mắt đang nhắm -> Mở ra
                if (icon.hasClass('bi-eye-slash')) {
                    icon.removeClass('bi-eye-slash text-secondary').addClass('bi-eye text-info');
                    balanceText.text(realBalance).hide().fadeIn(200); // Hiệu ứng mờ dần cho sang chảnh
                } 
                // Nếu mắt đang mở -> Nhắm lại
                else {
                    icon.removeClass('bi-eye text-info').addClass('bi-eye-slash text-secondary');
                    balanceText.text('********');
                }
            });
        });
    </script>
</body>

{{-- Thư viện Popup và Script Hoàn vé (ĐẶT Ở ĐÂY ĐỂ KHÔNG BỊ AJAX CHẶN) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmRefund(orderId) {
        Swal.fire({
            title: 'Hoàn trả vé?',
            text: "Bạn có chắc chắn muốn hoàn trả vé này không? Tiền sẽ được hoàn về số dư ví của bạn.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Có, hoàn vé!',
            cancelButtonText: 'Không, thoát',
            background: '#fff',
            color: '#1f2937'
        }).then((result) => {
            if (result.isConfirmed) {
                // Chuyển hướng tới route xử lý
                window.location.href = '/order/refund/' + orderId;
            }
        });
    }

    // Hiển thị thông báo khi Controller trả về Success/Error
    document.addEventListener('DOMContentLoaded', function () {
        @if(session('success'))
            Swal.fire({ icon: 'success', title: 'Thành công!', text: "{{ session('success') }}", background: '#fff', color: '#1f2937' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: 'Lỗi!', text: "{{ session('error') }}", background: '#fff', color: '#1f2937' });
        @endif
    });
</script>

{{-- MODAL ĐÁNH GIÁ (REVIEW) --}}
<div class="modal fade text-dark" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content bg-white text-dark border-light shadow-sm">
      <div class="modal-header border-light">
        <h5 class="modal-title fw-bold text-primary" id="reviewModalLabel"><i class="bi bi-star me-2"></i>Đánh giá trò chơi</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="reviewForm">
          @csrf
          <input type="hidden" id="reviewTicketId" name="ticket_id">
          <input type="hidden" id="reviewOrderItemId" name="order_item_id">
          
          <div class="mb-3 text-center">
            <h6 class="text-primary fw-bold mb-3" id="reviewTicketName">Tên trò chơi</h6>
            
            {{-- Chọn sao --}}
            <div class="rating-stars fs-2" style="cursor: pointer; display: inline-flex; flex-direction: row-reverse;">
                <input type="radio" name="rating" id="star5" value="5" class="d-none" checked>
                <label for="star5" class="bi bi-star-fill text-warning px-1 star-label" data-value="5"></label>
                
                <input type="radio" name="rating" id="star4" value="4" class="d-none">
                <label for="star4" class="bi bi-star-fill text-warning px-1 star-label" data-value="4"></label>
                
                <input type="radio" name="rating" id="star3" value="3" class="d-none">
                <label for="star3" class="bi bi-star-fill text-warning px-1 star-label" data-value="3"></label>
                
                <input type="radio" name="rating" id="star2" value="2" class="d-none">
                <label for="star2" class="bi bi-star-fill text-warning px-1 star-label" data-value="2"></label>
                
                <input type="radio" name="rating" id="star1" value="1" class="d-none">
                <label for="star1" class="bi bi-star-fill text-warning px-1 star-label" data-value="1"></label>
            </div>
          </div>

          <div class="mb-3">
            <label for="reviewComment" class="form-label text-secondary small text-uppercase fw-bold">Nhận xét (Tùy chọn)</label>
            <textarea class="form-control" id="reviewComment" name="comment" rows="3" placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>
          </div>
        </form>
      </div>
      <div class="modal-footer border-secondary border-opacity-25">
        <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Hủy</button>
        <button type="button" id="submitReviewBtn" class="btn btn-info rounded-pill fw-bold" onclick="submitReview()">Gửi đánh giá</button>
      </div>
    </div>
  </div>
</div>

<style>
    /* CSS cho hệ thống Rating Stars */
    .rating-stars label {
        color: #6c757d !important; /* Mặc định là xám */
        transition: color 0.2s ease-in-out;
    }
    
    /* Khi hover hoặc checked thì sáng lên màu vàng */
    .rating-stars label:hover,
    .rating-stars label:hover ~ label,
    .rating-stars input:checked ~ label {
        color: #ffc107 !important;
    }

    /* Boost Bootstrap Modal Z-Index to stay on top of SweetAlert2 */
    .modal {
        z-index: 9999 !important;
    }
    .modal-backdrop {
        z-index: 9998 !important;
    }
</style>

<script>
    // Hàm mở modal đánh giá
    function openReviewModal(ticketId, orderItemId, ticketName) {
        document.getElementById('reviewTicketId').value = ticketId;
        document.getElementById('reviewOrderItemId').value = orderItemId;
        document.getElementById('reviewTicketName').innerText = ticketName;
        
        // Reset form
        document.getElementById('reviewForm').reset();
        
        var myModal = new bootstrap.Modal(document.getElementById('reviewModal'));
        myModal.show();
    }

    // Hàm submit đánh giá bằng AJAX
    function submitReview() {
        const form = document.getElementById('reviewForm');
        const formData = new FormData(form);
        const orderItemId = formData.get('order_item_id');
        const rating = formData.get('rating');
        
        const submitBtn = document.getElementById('submitReviewBtn');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Đang gửi...';
        submitBtn.disabled = true;

        $.ajax({
            url: '{{ route("reviews.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    // Cập nhật nút Đánh giá thành các ngôi sao vàng ngay lập tức
                    let parseIntRating = parseInt(rating);
                    let starsHtml = '<div class="mt-1" style="font-size: 0.75rem;">';
                    for(let i = 1; i <= 5; i++) {
                        let opacityClass = i > parseIntRating ? 'opacity-25' : '';
                        starsHtml += '<i class="bi bi-star-fill text-warning ' + opacityClass + '"></i> ';
                    }
                    starsHtml += '</div>';

                    // Thay thế nút bằng sao
                    let btn = document.getElementById('review-btn-' + orderItemId);
                    if(btn) {
                        btn.outerHTML = starsHtml;
                    }

                    // Ẩn modal đánh giá
                    const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
                    if (modal) modal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message,
                        background: '#fff',
                        color: '#1f2937',
                        timer: 2500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                let errorMsg = 'Có lỗi xảy ra, vui lòng thử lại.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: errorMsg,
                    background: '#fff',
                    color: '#1f2937'
                });
            },
            complete: function() {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        });
    }
</script>

</html>