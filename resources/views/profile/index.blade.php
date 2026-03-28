<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>{{ __('profile.page_title') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        .profile-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        /* Sidebar profile - gradient VR */
        .profile-sidebar-card {
            background: linear-gradient(160deg, #0d1b3e 0%, #0a2a6e 45%, #1565c0 100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(13, 27, 62, 0.3);
            position: relative;
            overflow: hidden;
        }

        .profile-sidebar-card::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(56,189,248,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* Avatar section */
        .profile-avatar-section {
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.75rem;
        }

        .profile-avatar-section h5 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .profile-avatar-section p {
            color: rgba(255,255,255,0.55);
            font-size: 0.82rem;
        }

        /* Nav pills override */
        .nav-pills .nav-link {
            color: rgba(255,255,255,0.65) !important;
            border-radius: 10px;
            padding: 10px 16px;
            margin: 2px 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.22s ease;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-pills .nav-link i {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.5);
            transition: all 0.22s;
            width: 20px;
            text-align: center;
        }

        .nav-pills .nav-link:hover {
            background: rgba(255,255,255,0.1) !important;
            color: #ffffff !important;
            transform: translateX(4px);
        }

        .nav-pills .nav-link:hover i {
            color: #38bdf8 !important;
        }

        .nav-pills .nav-link.active {
            background: linear-gradient(90deg, rgba(56,189,248,0.28) 0%, rgba(56,189,248,0.08) 100%) !important;
            color: #ffffff !important;
            font-weight: 600;
            border: 1px solid rgba(56,189,248,0.3) !important;
            box-shadow: 0 4px 14px rgba(56,189,248,0.15);
        }

        .nav-pills .nav-link.active i {
            color: #38bdf8 !important;
            filter: drop-shadow(0 0 5px rgba(56,189,248,0.7));
        }

        /* Logout button */
        .btn-logout-profile {
            border: 1px solid rgba(239,68,68,0.5) !important;
            color: rgba(252,165,165,0.9) !important;
            background: rgba(239,68,68,0.08) !important;
            border-radius: 10px;
            padding: 10px 16px;
            margin: 2px 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.22s;
            display: flex; align-items: center; gap: 10px;
            width: calc(100% - 16px);
            text-align: left;
        }
        .btn-logout-profile:hover {
            background: rgba(239,68,68,0.2) !important;
            color: #fca5a5 !important;
            transform: translateX(4px);
        }

        /* Camera badge */
        .avatar-edit-badge {
            bottom: -10px; left: 50%;
            font-size: 0.72rem;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(4px);
            color: #38bdf8;
            border: 1px solid rgba(56,189,248,0.4);
            white-space: nowrap;
            pointer-events: none;
        }

        /* Empty state */
        .empty-state-icon {
            width: 120px; height: 120px;
            background: #f1f5f9;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
        }

        .modal { z-index: 9999 !important; }
        .modal-backdrop { z-index: 9998 !important; }

        /* SweetAlert2 popup — khoảng cách navbar + cuộn khi nội dung dài */
        .swal-order-container.swal2-container {
            padding-top: 80px !important;
            padding-bottom: 32px !important;
            align-items: flex-start !important;
        }
        .swal-order-popup.swal2-popup {
            max-height: calc(100vh - 120px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            border-radius: 16px !important;
        }
        .swal-order-popup .swal2-html-container {
            overflow: visible !important;
            max-height: none !important;
        }
        /* Thanh cuộn đẹp */
        .swal-order-popup::-webkit-scrollbar { width: 5px; }
        .swal-order-popup::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .swal-order-popup::-webkit-scrollbar-thumb { background: #94a3b8; border-radius: 10px; }
        .swal-order-popup::-webkit-scrollbar-thumb:hover { background: #1e88e5; }
    </style>
</head>

<body>

    @include('partials.navbar')

    <div class="container py-5">
        {{-- Đổi tỷ lệ cột thành 4 - 8 cho cân đối --}}
        <div class="row g-5">

            {{-- CỘT TRÁI: SIDEBAR (col-lg-4) --}}
            <div class="col-lg-4">
                <div class="profile-sidebar-card p-0 h-100 d-flex flex-column">
                    {{-- Avatar & Info --}}
                    <div class="profile-avatar-section">
                        <div class="position-relative d-inline-block">
                            <label for="avatarInput" style="cursor: pointer;">
                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1565c0&color=fff&size=128' }}"
                                    class="rounded-circle mb-3 border border-3 shadow"
                                    style="border-color: rgba(56,189,248,0.5) !important; width:100px; height:100px; object-fit:cover; transition: opacity 0.3s;"
                                    title="{{ __('profile.change_avatar') }}">
                                <div class="position-absolute translate-middle px-2 py-1 rounded-pill avatar-edit-badge">
                                    <i class="bi bi-camera me-1"></i>{{ __('profile.change_avatar') }}
                                </div>
                            </label>
                            <form id="avatarForm" action="{{ route('profile.avatar') }}" method="POST" enctype="multipart/form-data" class="d-none">
                                @csrf
                                <input type="file" id="avatarInput" name="avatar" accept="image/*" onchange="document.getElementById('avatarForm').submit();">
                            </form>
                            <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2" title="Online"></span>
                        </div>
                        <h5 class="fw-bold mb-1 mt-3">{{ $user->name }}</h5>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>

                    {{-- Menu --}}
                    <div class="nav flex-column nav-pills px-1 flex-grow-1 justify-content-evenly py-2" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active" id="v-pills-profile-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab">
                            <i class="bi bi-person-gear"></i> {{ __('profile.my_profile') }}
                        </button>

                        <button class="nav-link" id="v-pills-voucher-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-voucher" type="button" role="tab">
                            <i class="bi bi-ticket-perforated"></i> {{ __('profile.vouchers') ?? 'Kho Voucher' }}
                        </button>

                        <button class="nav-link" id="v-pills-orders-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-orders" type="button" role="tab">
                            <i class="bi bi-box-seam"></i> {{ __('profile.my_orders') ?? 'Đơn hàng của tôi' }}
                        </button>

                        <button class="nav-link" id="v-pills-wishlist-tab"
                            data-bs-toggle="pill" data-bs-target="#v-pills-wishlist" type="button" role="tab">
                            <i class="bi bi-heart"></i> {{ __('profile.wishlist') }}
                        </button>

                        <a href="{{ route('settings.index') }}?tab=security"
                           class="nav-link" style="text-decoration:none">
                            <i class="bi bi-shield-lock"></i> {{ __('profile.change_password') }}
                        </a>

                        <hr style="border-color: rgba(255,255,255,0.12); margin: 4px 8px;">

                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-logout-profile">
                                <i class="bi bi-box-arrow-right"></i> {{ __('profile.logout') }}
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
                                <i class="bi bi-person-lines-fill me-2"></i> {{ __('profile.my_profile') }}
                            </h4>

                            <div class="row mb-4">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="bg-light border border-info border-opacity-25 rounded-3 p-3 h-100">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-info bg-opacity-10 p-3 rounded-circle flex-shrink-0">
                                                <i class="bi bi-wallet2 text-info fs-3"></i>
                                            </div>
                                            <div class="flex-grow-1 min-width-0">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <p class="text-primary mb-0 small text-uppercase fw-bold">{{ __('profile.my_balance') }}</p>
                                                    <i class="bi bi-eye-slash text-primary" id="toggleBalanceIcon" style="cursor:pointer;" title="{{ __('profile.show_hide_balance') }}"></i>
                                                </div>
                                                <h4 class="text-info fw-bold mb-2" id="balanceAmount" data-balance="{{ number_format(Auth::user()->balance ?? 0) }}đ">
                                                    ********
                                                </h4>
                                                <a href="{{ route('wallet.topup') }}" class="btn btn-primary btn-sm fw-bold rounded-pill px-3 py-2 text-white shadow-sm mt-1">
                                                    <i class="bi bi-plus-circle me-1"></i> {{ __('profile.top_up') }}
                                                </a>
                                            </div>
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
                                        <div class="ms-2">
                                            <p class="text-primary mb-0 small text-uppercase fw-bold">{{ __('profile.member_tier') }}</p>
                                            <h4 class="fw-bold mb-0 text-uppercase mt-1" style="color: {{ $style['color'] }}; letter-spacing: 1px;">
                                                @php
                                                    $tierKey = 'profile.tier_' . \Illuminate\Support\Str::slug($tier, '_');
                                                    $translatedTier = __($tierKey);
                                                    if ($translatedTier === $tierKey) $translatedTier = $tier;
                                                @endphp
                                                {{ $translatedTier }}
                                                <span class="fs-6 text-muted fw-normal text-capitalize" style="letter-spacing: 0;">
                                                    ({{ Auth::user()->points ?? 0 }} {{ __('profile.points') }})
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
                                        <label class="form-label text-uppercase mb-2">{{ __('profile.fullname') }}</label>
                                        <input type="text" name="name" class="form-control"
                                            value="{{ old('name', $user->name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2">{{ __('profile.email') }}</label>
                                        <input type="text" class="form-control text-muted fst-italic border-0"
                                            value="{{ $user->email }}" disabled style="cursor: not-allowed; background-color: #e2e8f0;">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2">{{ __('profile.phone') }}</label>
                                        <input type="text" name="phone" class="form-control"
                                            value="{{ old('phone', $user->phone) }}" placeholder="Chưa cập nhật">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2">{{ __('profile.birthday') }}</label>
                                        <input type="date" name="birthday" class="form-control"
                                            value="{{ old('birthday', $user->birthday) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2">{{ __('profile.gender_label') }}</label>
                                        <select name="gender" class="form-select">
                                            <option value="" disabled selected>{{ __('profile.select_gender') }}</option>
                                            <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>{{ __('profile.male') }}</option>
                                            <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>{{ __('profile.female') }}</option>
                                            <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>{{ __('profile.other') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-uppercase mb-2">{{ __('profile.address_label') }}</label>
                                        <textarea name="address" class="form-control" rows="3"
                                            placeholder="{{ __('profile.enter_address') }}">{{ old('address', $user->address) }}</textarea>
                                    </div>

                                </div>
                                <div class="mt-4 pt-3 text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold text-uppercase shadow-sm" style="border-radius: 12px; letter-spacing: 0.5px; transition: all 0.3s;">
                                        <i class="bi bi-floppy-fill me-2"></i> {{ __('profile.save_changes') }}
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB KHO VOUCHER (Thay thế cho Địa chỉ) --}}
                        <div class="tab-pane fade" id="v-pills-voucher" role="tabpanel">
                            <h4
                                class="text-primary fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
                                <i class="bi bi-ticket-perforated-fill me-2"></i> {{ __('profile.my_vouchers') }}
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
                                            <small class="text-uppercase fw-bold opacity-75">{{ __('profile.discount') }}</small>
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
                                                        {{ __('profile.discount_up_to', ['value' => $coupon->value]) ?? 'Giảm ' . $coupon->value . '% tối đa cho đơn hàng.' }}
                                                    @else
                                                        {{ __('profile.direct_discount', ['value' => number_format($coupon->value)]) ?? 'Giảm trực tiếp ' . number_format($coupon->value) . 'đ vào đơn hàng.' }}
                                                    @endif
                                                    <br>
                                                    <span class="text-muted small fst-italic">{{ __('profile.apply_all_games') ?? 'Áp dụng cho tất cả trò chơi.' }}</span>
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
                                                    {{ __('profile.get_code') }}
                                                </button>
                                                <div class="text-center mt-2">
                                                    <small class="text-muted d-block">{{ __('profile.limited_qty') }}</small>
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
                                        <h4>{{ __('profile.no_vouchers') }}</h4>
                                        <p>{{ __('profile.come_back_later') }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Nhập mã voucher thủ công --}}
                            <div class="mt-5 p-4 rounded-3 bg-light border border-secondary border-opacity-10">
                                <h6 class="text-dark fw-bold mb-3"><i class="bi bi-keyboard me-2"></i>{{ __('profile.enter_voucher') }}
                                </h6>
                                <form class="d-flex gap-2">
                                    <input type="text" class="form-control bg-white text-dark"
                                        placeholder="{{ __('profile.enter_voucher_ph') }}">
                                    <button class="btn btn-primary fw-bold px-4">{{ __('profile.save') }}</button>
                                </form>
                            </div>
                        </div>

                        {{-- Script copy mã --}}
                        <script>
                            function copyCode(code) {
                                navigator.clipboard.writeText(code);
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ __("profile.copied") ?? "Đã sao chép!" }}',
                                    text: '{{ __("profile.code") ?? "Mã: " }}' + code,
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
                                <i class="bi bi-clock-history me-2"></i> {{ __('profile.order_history') }}
                            </h4>

                            @if($user->orders->isEmpty())
                                {{-- Giao diện trống đẹp --}}
                                <div class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-cart-x display-4 text-muted opacity-50"></i>
                                    </div>
                                    <h4 class="text-dark fw-bold mb-2">{{ __('profile.no_orders') }}</h4>
                                    <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">
                                        {{ __('profile.no_orders_sub') }}
                                    </p>
                                    <a href="{{ route('ticket.shop') }}"
                                        class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow hover-scale">
                                        <i class="bi bi-ticket-perforated me-2"></i> {{ __('profile.buy_now') }}
                                    </a>
                                </div>
                            @else
                                {{-- Bảng đơn hàng --}}
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead>
                                            <tr class="text-muted small text-uppercase border-bottom">
                                                <th>{{ __('profile.order_code') }}</th>
                                                <th>{{ __('profile.order_date') }}</th>
                                                <th>{{ __('profile.total') }}</th>
                                                <th>{{ __('profile.status') }}</th>
                                                <th class="text-end">{{ __('profile.details') }}</th>
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
                                                            <span class="badge bg-warning text-dark bg-opacity-75">{{ __('profile.status_pending') }}</span>
                                                        @elseif($order->status == 'paid')
                                                            <span class="badge bg-success bg-opacity-75">{{ __('profile.status_paid') }}</span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-danger bg-opacity-75">{{ __('profile.status_cancelled') }}</span>

                                                            {{-- Cập nhật thêm Hoàn vé --}}
                                                        @elseif($order->status == 'refunded')
                                                            <span class="badge bg-light text-dark text-uppercase border border-secondary">{{ __('profile.status_refunded') }}</span>

                                                            {{-- Trạng thái Hết hạn --}}
                                                        @elseif($order->status == 'expired')
                                                            <span class="badge bg-light text-dark text-uppercase border border-secondary">{{ __('profile.status_expired') }}</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-view-order" data-id="{{ $order->id }}">
                                                            {{ __('profile.view') ?? 'Xem' }} <i class="bi bi-eye ms-1"></i>
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
                                <i class="bi bi-heart-fill me-2"></i> {{ __('profile.wishlist') }}
                            </h4>

                            @if($user->favorites->isEmpty())
                                {{-- Giao diện trống --}}
                                <div class="text-center py-5">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-heart-break display-4 text-muted opacity-50"></i>
                                    </div>
                                    <h4 class="mt-3 text-muted">{{ __('profile.no_wishlist') }}</h4>
                                    <p class="text-muted">{{ __('profile.no_wishlist_sub') }}</p>
                                    <a href="{{ route('ticket.shop') }}"
                                        class="btn btn-outline-primary rounded-pill px-4 mt-2">
                                        {{ __('profile.explore') }}
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
                                                                        <i class="bi bi-cone-striped"></i> {{ __('profile.maintenance') }}
                                                                    </button>
                                                                @else
                                                                    {{-- Trỏ vào trang Nhập thông tin --}}
                                                                    <a href="{{ route('booking.form', $item->id) }}"
                                                                        class="btn btn-sm btn-primary flex-grow-1 fw-bold">
                                                                        <i class="bi bi-ticket-perforated"></i> {{ __('profile.book_ticket') }}
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

                        {{-- 5. TAB BẢO MẬT — chuyển sang Settings --}}
                        <div class="tab-pane fade" id="v-pills-password" role="tabpanel">
                            <div class="text-center py-5">
                                <i class="bi bi-gear-fill display-4 text-primary opacity-75 mb-3 d-block"></i>
                                <h5 class="fw-bold">Đổi mật khẩu đã chuyển vào Cài đặt</h5>
                                <p class="text-muted mb-4">Để bảo mật hơn, tính năng đổi mật khẩu và các tùy chọn bảo mật khác đã được gộp vào trang Cài đặt.</p>
                                <a href="{{ route('settings.index') }}?tab=security" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">
                                    <i class="bi bi-shield-lock me-2"></i> Đến trang Cài đặt Bảo mật
                                </a>
                            </div>
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
                $locationName = 'Đang cập nhật';
                if (session('order_id')) {
                    $orderData = \App\Models\Order::with('location')->find(session('order_id'));
                    if ($orderData) {
                        $pMethod = $orderData->payment_method;
                        $locationName = $orderData->location ? $orderData->location->name : 'Đang cập nhật';
                    }
                }
                $methodLabel = match($pMethod) {
                    'wallet'  => 'Ví Holomia',
                    'cod'     => 'Thanh toán tại quầy',
                    'banking' => 'Chuyển khoản ngân hàng',
                    default   => $pMethod,
                };
                            @endphp

                            const ordersTabTrigger = document.getElementById('v-pills-orders-tab');
                            if (ordersTabTrigger) { new bootstrap.Tab(ordersTabTrigger).show(); }

                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("profile.payment_success_title") ?? "Thanh toán thành công! 🎉" }}',
                                html: `
                                    <div class="text-start small">
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="text-muted">{{ __('profile.location') ?? 'Cơ sở:' }}</span>
                                            <span class="fw-bold text-primary">{{ $locationName }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="text-muted">{{ __('profile.order_code') ?? 'Mã đơn:' }}</span>
                                            <span class="fw-bold text-dark">#{{ session('order_id') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="text-muted">{{ __('profile.total') ?? 'Tổng tiền:' }}</span>
                                            <span class="fw-bold text-primary fs-6">{{ number_format(session('total_amount')) }}đ</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">{{ __('profile.payment_method') ?? 'Phương thức:' }}</span>
                                            <span class="fw-bold text-dark">{{ $methodLabel }}</span>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: '{{ __("profile.close") ?? "Đóng" }}',
                                confirmButtonColor: '#0d6efd',
                                width: '380px',
                                allowOutsideClick: false,
                            });

                            // Hiệu ứng pháo hoa
                            var end = Date.now() + 2000;
                            (function frame() {
                                confetti({ particleCount: 2, angle: 60, spread: 40, origin: { x: 0 }, scalar: 0.7, colors: ['#0dcaf0', '#ffffff'] });
                                confetti({ particleCount: 2, angle: 120, spread: 40, origin: { x: 1 }, scalar: 0.7, colors: ['#0dcaf0', '#ffffff'] });
                                if (Date.now() < end) requestAnimationFrame(frame);
                            }());

            @endif

            @if(session('cod_success'))
                            // Tab đơn hàng
                            const codTabTrigger = document.getElementById('v-pills-orders-tab');
                            if (codTabTrigger) { new bootstrap.Tab(codTabTrigger).show(); }

                            Swal.fire({
                                icon: 'info',
                                title: '{{ __("profile.order_success") ?? "Đặt vé thành công!" }}',
                                html: `
                                    <div class="text-start small">
                                        <div class="alert alert-warning border-0 rounded-3 mb-3 py-2" style="font-size:0.85rem;">
                                            <i class="bi bi-shop me-2"></i>
                                            <strong>{{ __("profile.pay_at_counter") ?? "Thanh toán tại quầy" }}</strong><br>
                                            {!! __("profile.pay_at_counter_desc") ?? 'Vui lòng đến quầy thanh toán <strong>trước 15 phút</strong> so với giờ chơi.<br>Đơn hàng sẽ ở trạng thái <strong>"Chờ xử lý"</strong> cho đến khi nhân viên xác nhận.' !!}
                                        </div>
                                        <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                            <span class="text-muted">Mã đơn:</span>
                                            <span class="fw-bold text-dark">#{{ session('order_id') }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Tổng tiền:</span>
                                            <span class="fw-bold text-warning fs-6">{{ number_format(session('total_amount')) }}đ</span>
                                        </div>
                                    </div>
                                `,
                                confirmButtonText: '{{ __("profile.understood") ?? "Đã hiểu" }}',
                                confirmButtonColor: '#ffc107',
                                width: '400px',
                                allowOutsideClick: false,
                            });
            @endif

            // XỬ LÝ SỰ KIỆN CLICK NÚT "XEM"
            $('.btn-view-order').click(function (e) {
                e.preventDefault();
                let orderId = $(this).data('id');

                Swal.fire({
                    title: '{{ __("profile.loading_invoice") ?? "Đang tải hóa đơn..." }}',
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
                            confirmButtonText: '{{ __("profile.close") ?? "Đóng lại" }}',
                            confirmButtonColor: '#6c757d',
                            showCancelButton: true,
                            cancelButtonText: '<i class="bi bi-printer"></i> {{ __("profile.print_receipt") ?? "In hóa đơn" }}',
                            cancelButtonColor: '#0dcaf0',
                            width: '500px',
                            padding: '2em',
                            allowOutsideClick: true,
                            backdrop: `rgba(0,0,0,0.85)`,
                            scrollbarPadding: false,
                            customClass: {
                                container: 'swal-order-container',
                                popup: 'swal-order-popup',
                            },
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.cancel) {
                                window.print();
                            }
                        });
                    },
                    error: function () {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __("profile.error") ?? "Lỗi" }}',
                            text: '{{ __("profile.cannot_load_order") ?? "Không thể tải chi tiết đơn hàng!" }}',
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
                            title: '{{ __("profile.removed_from_wishlist") ?? "Đã xóa khỏi yêu thích" }}'
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
    @include('partials.footer')
</body>

{{-- Thư viện Popup và Script Hoàn vé (ĐẶT Ở ĐÂY ĐỂ KHÔNG BỊ AJAX CHẶN) --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmRefund(orderId) {
        Swal.fire({
            title: '{{ __("profile.refund_confirm_title") ?? "Hoàn trả vé?" }}',
            text: "{{ __("profile.refund_confirm_text") ?? 'Bạn có chắc chắn muốn hoàn trả vé này không? Tiền sẽ được hoàn về số dư ví của bạn.' }}",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '{{ __("profile.yes_refund") ?? "Có, hoàn vé!" }}',
            cancelButtonText: '{{ __("profile.no_cancel") ?? "Không, thoát" }}',
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
            Swal.fire({ icon: 'success', title: '{{ __("profile.success") ?? "Thành công!" }}', text: "{{ session('success') }}", background: '#fff', color: '#1f2937' });
        @endif
        @if(session('error'))
            Swal.fire({ icon: 'error', title: '{{ __("profile.error") ?? "Lỗi!" }}', text: "{{ session('error') }}", background: '#fff', color: '#1f2937' });
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