@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('profile.page_title') }} - {{ $location->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body {
            font-family:
                {{ $langConfig['font_family'] }}
            ;
        }

        .profile-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
        }

        .profile-sidebar-card {
            background: linear-gradient(160deg, #0d1b3e 0%, #0a2a6e 45%,
                    {{ $location->color ?? '#1565c0' }}
                    100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(13, 27, 62, 0.3);
            position: relative;
            overflow: hidden;
        }

        .profile-sidebar-card::before {
            content: '';
            position: absolute;
            top: -60px;
            left: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        .profile-avatar-section {
            padding: 2rem 1.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.75rem;
        }

        .profile-avatar-section h5 {
            color: #ffffff;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .profile-avatar-section p {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.82rem;
        }

        .nav-pills .nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
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
            background: rgba(255, 255, 255, 0.1) !important;
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

        .btn-logout-profile {
            border: 1px solid rgba(239, 68, 68, 0.5) !important;
            color: rgba(252, 165, 165, 0.9) !important;
            background: rgba(239, 68, 68, 0.08) !important;
            border-radius: 10px;
            padding: 10px 16px;
            margin: 2px 8px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.22s;
            display: flex;
            align-items: center;
            gap: 10px;
            width: calc(100% - 16px);
            text-align: left;
        }

        .avatar-edit-badge {
            bottom: -10px;
            left: 50%;
            font-size: 0.72rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.4);
            white-space: nowrap;
        }

        .swal-order-container.swal2-container {
            padding-top: 80px !important;
        }

        :root {
            --primary:
                {{ $location->color ?? '#0dcaf0' }}
            ;
        }
    </style>
</head>

<body class="bg-light text-dark">
    @include('branch.partials.navbar')

    <div class="container py-5">
        <div class="row g-5">
            {{-- CỘT TRÁI: SIDEBAR --}}
            <div class="col-lg-4">
                <div class="profile-sidebar-card p-0 h-100 d-flex flex-column">
                    <div class="profile-avatar-section">
                        <div class="position-relative d-inline-block">
                            <label for="avatarInput" style="cursor: pointer;">
                                <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=1565c0&color=fff&size=128' }}"
                                    class="rounded-circle mb-3 border border-3 shadow"
                                    style="border-color: rgba(255,255,255,0.5) !important; width:100px; height:100px; object-fit:cover;">
                                <div
                                    class="position-absolute translate-middle px-2 py-1 rounded-pill avatar-edit-badge">
                                    <i class="bi bi-camera me-1"></i>{{ __('profile.change_avatar') }}
                                </div>
                            </label>
                            <form id="avatarForm"
                                action="{{ route('branch.profile.avatar', ['subdomain' => $subdomain]) }}" method="POST"
                                enctype="multipart/form-data" class="d-none">
                                @csrf
                                <input type="file" id="avatarInput" name="avatar" accept="image/*"
                                    onchange="document.getElementById('avatarForm').submit();">
                            </form>
                        </div>
                        <h5 class="fw-bold mb-1 mt-3">{{ $user->name }}</h5>
                        <p class="mb-0 small opacity-75">Thành viên tại {{ $location->name }}</p>
                    </div>

                    <div class="nav flex-column nav-pills px-1 flex-grow-1 py-2" id="v-pills-tab" role="tablist">
                        <button class="nav-link active" id="v-pills-profile-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-profile" type="button" role="tab">
                            <i class="bi bi-person-gear"></i> {{ __('profile.my_profile') }}
                        </button>
                        <button class="nav-link" id="v-pills-orders-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-orders" type="button" role="tab">
                            <i class="bi bi-box-seam"></i> {{ __('profile.my_orders') ?? 'Đơn hàng của tôi' }}
                        </button>
                        <button class="nav-link" id="v-pills-voucher-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-voucher" type="button" role="tab">
                            <i class="bi bi-ticket-perforated"></i> {{ __('profile.vouchers') ?? 'Kho Voucher' }}
                        </button>
                        <button class="nav-link" id="v-pills-wishlist-tab" data-bs-toggle="pill"
                            data-bs-target="#v-pills-wishlist" type="button" role="tab">
                            <i class="bi bi-heart"></i> {{ __('profile.wishlist') ?? 'Yêu thích' }}
                        </button>
                        <a href="{{ route('branch.settings', ['subdomain' => $subdomain]) }}" class="nav-link" style="text-decoration:none">
                            <i class="bi bi-gear"></i> {{ __('profile.settings') ?? 'Cài đặt' }}
                        </a>
                        <hr style="border-color: rgba(255,255,255,0.12); margin: 4px 8px;">
                        <form action="{{ route('branch.logout', ['subdomain' => $subdomain]) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-logout-profile">
                                <i class="bi bi-box-arrow-right"></i> {{ __('profile.logout') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: NỘI DUNG --}}
            <div class="col-lg-8">
                <div class="card profile-card rounded-4 p-4 p-md-5 shadow-sm border-0 h-100">
                    <div class="tab-content" id="v-pills-tabContent">

                        {{-- TAB HỒ SƠ --}}
                        <div class="tab-pane fade show active" id="v-pills-profile" role="tabpanel">
                            <h4 class="text-primary fw-bold text-uppercase mb-4 border-bottom border-light pb-3">
                                <i class="bi bi-person-lines-fill me-2"></i> {{ __('profile.my_profile') }}
                            </h4>
                            <form action="{{ route('branch.profile.update', ['subdomain' => $subdomain]) }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2 small fw-bold text-muted">{{ __('profile.fullname') }}</label>
                                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2 small fw-bold text-muted">{{ __('profile.email') }}</label>
                                        <input type="text" class="form-control bg-light" value="{{ $user->email }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2 small fw-bold text-muted">{{ __('profile.phone') }}</label>
                                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                     <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2 small fw-bold text-muted">Ngày sinh</label>
                                        <input type="date" name="birthday" class="form-control"
                                            value="{{ old('birthday', $user->birthday ? \Carbon\Carbon::parse($user->birthday)->format('Y-m-d') : '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-uppercase mb-2 small fw-bold text-muted">Giới tính</label>
                                        <select name="gender" class="form-select">
                                            <option value="">-- Chọn giới tính --</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Nam</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Nữ</option>
                                            <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Khác</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label text-uppercase mb-2 small fw-bold text-muted">Địa chỉ</label>
                                        <textarea name="address" class="form-control" rows="2">{{ old('address', $user->address) }}</textarea>
                                    </div>
                                </div>
                                <div class="mt-4 text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-3 fw-bold rounded-pill shadow-sm">
                                        LƯU THAY ĐỔI
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- TAB ĐƠN HÀNG --}}
                        <div class="tab-pane fade" id="v-pills-orders" role="tabpanel">
                            <h4 class="fw-bold text-uppercase mb-4 border-bottom border-light pb-3" style="color: var(--primary);">
                                <i class="bi bi-clock-history me-2"></i> Lịch sử đặt vé
                            </h4>
                            @if($orders->isEmpty())
                                <div class="text-center py-5">
                                    <i class="bi bi-cart-x display-4 text-muted opacity-50"></i>
                                    <h4 class="text-dark fw-bold mb-2 mt-3">Chưa có đơn hàng nào</h4>
                                    <p class="text-muted mb-4">Hãy đặt vé và tận hưởng trải nghiệm VR tuyệt vời!</p>
                                    <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-controller me-2"></i>ĐẶT VÉ NGAY
                                    </a>
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle">
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
                                            @foreach($orders as $order)
                                                <tr>
                                                    <td class="fw-bold" style="color: var(--primary);">{{ $order->id }}</td>
                                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                    <td class="fw-bold">{{ number_format($order->total_amount) }}đ</td>
                                                    <td>
                                                        @if($order->status == 'pending')
                                                            <span class="badge bg-warning text-dark bg-opacity-75">Chờ xác nhận</span>
                                                        @elseif($order->status == 'paid')
                                                            <span class="badge bg-success bg-opacity-75">Đã thanh toán</span>
                                                        @elseif($order->status == 'cancelled')
                                                            <span class="badge bg-danger bg-opacity-75">Đã hủy</span>
                                                        @elseif($order->status == 'refunded')
                                                            <span class="badge bg-light text-dark border border-secondary">Đã hoàn tiền</span>
                                                        @elseif($order->status == 'expired')
                                                            <span class="badge bg-light text-dark border border-secondary">Hết hạn</span>
                                                        @else
                                                            <span class="badge bg-secondary">{{ $order->status }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 btn-view-order" data-id="{{ $order->id }}">
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

                        {{-- TAB VOUCHER --}}
                        <div class="tab-pane fade" id="v-pills-voucher" role="tabpanel">
                            <h4 class="fw-bold text-uppercase mb-4 border-bottom border-light pb-3" style="color: var(--primary);">
                                <i class="bi bi-ticket-perforated-fill me-2"></i> Kho Voucher
                            </h4>

                            @if($coupons->count() > 0)
                                <div class="coupon-list-container">
                                    @foreach($coupons as $coupon)
                                        <div class="voucher-ticket mb-3" style="display:flex; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,0.08); border:1px solid #e2e8f0;">
                                            {{-- PHẦN TRÁI --}}
                                            <div class="voucher-left d-flex flex-column align-items-center justify-content-center px-4 py-3 text-white {{ $coupon->type == 'fixed' ? '' : '' }}"
                                                 style="min-width:100px; background: {{ $coupon->type == 'fixed' ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'linear-gradient(135deg,' . ($location->color ?? '#0d6efd') . ',#0a2a6e)' }};">
                                                <i class="bi bi-ticket-detailed-fill fs-3 mb-1 opacity-75"></i>
                                                <h3 class="fw-bold m-0 fs-2 lh-1">
                                                    {{ $coupon->type == 'percent' ? $coupon->value . '%' : number_format($coupon->value / 1000) . 'K' }}
                                                </h3>
                                                <small class="text-uppercase fw-bold opacity-75" style="font-size:0.65rem; letter-spacing:1px;">GIẢM GIÁ</small>
                                            </div>

                                            {{-- PHẦN PHẢI --}}
                                            <div class="voucher-right flex-grow-1 bg-white px-4 py-3 d-flex justify-content-between align-items-center">
                                                <div>
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="badge bg-danger">HOT</span>
                                                        <h5 class="fw-bold mb-0" style="color: var(--primary); letter-spacing:1px;">{{ $coupon->code }}</h5>
                                                    </div>
                                                    <p class="text-dark mb-1 small">
                                                        @if($coupon->type == 'percent')
                                                            Giảm {{ $coupon->value }}% cho đơn hàng tại {{ $location->name }}
                                                        @else
                                                            Giảm {{ number_format($coupon->value) }}đ cho đơn hàng của bạn
                                                        @endif
                                                    </p>
                                                    <small class="text-danger fw-bold">
                                                        <i class="bi bi-clock-history me-1"></i>HSD: {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d/m/Y') }}
                                                    </small>
                                                </div>
                                                <div class="ms-3 text-center">
                                                    <button class="btn btn-primary btn-sm rounded-pill shadow-sm fw-bold px-3"
                                                        onclick="copyBranchCode('{{ $coupon->code }}')">
                                                        LẤY MÃ
                                                    </button>
                                                    <div class="text-center mt-1">
                                                        <small class="text-muted" style="font-size:0.7rem;">Số lượng có hạn</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-5 my-3 bg-light rounded-4 border">
                                    <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="100" alt="Empty" class="mb-3 opacity-50">
                                    <h5>Chưa có voucher nào</h5>
                                    <p class="small">Hãy quay lại sau để nhận ưu đãi hấp dẫn từ {{ $location->name }}!</p>
                                </div>
                            @endif

                            {{-- Nhập mã thủ công --}}
                            <div class="mt-4 p-4 rounded-3 bg-light border border-secondary border-opacity-10">
                                <h6 class="text-dark fw-bold mb-3"><i class="bi bi-keyboard me-2"></i>Nhập mã voucher</h6>
                                <div class="d-flex gap-2">
                                    <input type="text" id="manualVoucherInput" class="form-control bg-white text-dark" placeholder="Nhập mã voucher của bạn...">
                                    <button class="btn btn-primary fw-bold px-4" onclick="copyBranchCode(document.getElementById('manualVoucherInput').value)">Áp dụng</button>
                                </div>
                            </div>
                        </div>

                        {{-- TAB YÊU THÍCH --}}
                        <div class="tab-pane fade" id="v-pills-wishlist" role="tabpanel">
                            <h4 class="fw-bold text-uppercase mb-4 border-bottom border-light pb-3" style="color: var(--primary);">
                                <i class="bi bi-heart-fill me-2"></i> Trò chơi yêu thích
                            </h4>
                            @php
                                $wishlist = auth()->user()->wishlists ?? collect();
                            @endphp
                            @if($wishlist->count() > 0)
                                <div class="row g-3">
                                    @foreach($wishlist as $ticket)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}"
                style="height:140px; object-fit:cover;" alt="{{ $ticket->name }}">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-2">{{ $ticket->name }}</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('branch.booking.form', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}"
                        class="btn btn-primary btn-sm flex-grow-1 fw-bold">ĐẶT VÉ</a>
                    <a href="{{ route('branch.detail', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}"
                        class="btn btn-outline-secondary btn-sm px-3">
                        <i class="bi bi-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-5 my-3 bg-light rounded-4 border">
                                    <i class="bi bi-heart display-3 opacity-25"></i>
                                    <h5 class="mt-3">Chưa có trò chơi yêu thích</h5>
                                    <a href="{{ route('branch.shop', ['subdomain' => $subdomain]) }}"
                                        class="btn btn-primary rounded-pill px-4 mt-2">Khám phá ngay</a>
                                </div>
                            @endif
                        </div>

                        {{-- TAB CÀI ĐẶT --}}
                        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel">
                            <h4 class="fw-bold text-uppercase mb-4 border-bottom border-light pb-3" style="color: var(--primary);">
                                <i class="bi bi-gear-fill me-2"></i> Cài đặt tài khoản
                            </h4>

                            {{-- GIAO DIỆN --}}
                            <div class="card border-0 bg-light rounded-3 p-4 mb-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-palette me-2 text-primary"></i>Giao diện</h6>
                                <form action="{{ route('settings.ui') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_back" value="{{ url()->current() }}">

                                    {{-- CHỦ ĐỀ --}}
                                    <label class="form-label small fw-bold text-uppercase text-muted mb-2">Chủ đề</label>
                                    @php $ui = Auth::user()->ui_settings ?? []; @endphp
                                    <input type="hidden" name="theme" id="inp-theme-branch" value="{{ $ui['theme'] ?? 'light' }}">
                                    <div class="row g-2 mb-3">
                                        @foreach([['light','Sáng','#f8f9fa','#dee2e6'],['dark','Tối','#1a1d20','#343a40'],['auto','Tự động','#6c757d','#adb5bd']] as [$val,$label,$c1,$c2])
                                        <div class="col-4">
                                            <div class="theme-card {{ ($ui['theme'] ?? 'light') == $val ? 'selected' : '' }}" onclick="selectThemeBranch('{{ $val }}', this)">
                                                <div class="theme-preview" style="background:{{ $c1 }};">
                                                    <div class="theme-preview-bar" style="background:{{ $c2 }};"></div>
                                                    <div class="theme-preview-bar" style="background:{{ $c2 }}; opacity:.5;"></div>
                                                </div>
                                                <small class="fw-semibold">{{ $label }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    {{-- MÀU CHỦ ĐẠO --}}
                                    <label class="form-label small fw-bold text-uppercase text-muted mb-2">Màu chủ đạo</label>
                                    <input type="hidden" name="primary_color" id="inp-color-branch" value="{{ $ui['primary_color'] ?? '#1e88e5' }}">
                                    <div class="d-flex gap-2 flex-wrap mb-3">
                                        @foreach(['#1e88e5','#7c3aed','#059669','#d97706','#dc2626','#db2777','#0891b2'] as $c)
                                        <div class="color-swatch {{ ($ui['primary_color'] ?? '#1e88e5') == $c ? 'selected' : '' }}"
                                             style="background:{{ $c }};"
                                             onclick="selectColorBranch('{{ $c }}', this)"></div>
                                        @endforeach
                                    </div>

                                    {{-- FONT --}}
                                    <label class="form-label small fw-bold text-uppercase text-muted mb-2">Font chữ</label>
                                    <input type="hidden" name="font" id="inp-font-branch" value="{{ $ui['font'] ?? 'Be Vietnam Pro' }}">
                                    <div class="row g-2 mb-3">
                                        @foreach([['Be Vietnam Pro','Gọn, hiện đại'],['Roboto','Phổ biến, dễ đọc'],['Merriweather','Thanh lịch, serif'],['Nunito','Tròn, thân thiện']] as [$f,$desc])
                                        <div class="col-6">
                                            <div class="font-card {{ ($ui['font'] ?? 'Be Vietnam Pro') == $f ? 'selected' : '' }}" onclick="selectFontBranch('{{ $f }}', this)" style="font-family:'{{ $f }}'">
                                                <span>Aa — {{ $f }}</span>
                                                <small>{{ $desc }}</small>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <button type="submit" class="btn btn-primary fw-bold px-4">Lưu giao diện</button>
                                </form>
                            </div>

                            {{-- BẢO MẬT - ĐỔI MẬT KHẨU --}}
                            <div class="card border-0 bg-light rounded-3 p-4 mb-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-lock me-2 text-primary"></i>Bảo mật</h6>
                                <form action="{{ route('settings.password') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_back" value="{{ url()->current() }}">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Mật khẩu hiện tại</label>
                                        <input type="password" name="current_password" class="form-control" placeholder="Nhập mật khẩu hiện tại...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Mật khẩu mới</label>
                                        <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu mới...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Xác nhận mật khẩu</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu...">
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold px-4">Lưu mật khẩu</button>
                                </form>
                            </div>

                            {{-- NGÔN NGỮ --}}
                            <div class="card border-0 bg-light rounded-3 p-4 mb-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-translate me-2 text-primary"></i>Ngôn ngữ & Vùng</h6>
                                <form action="{{ route('settings.language') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_back" value="{{ url()->current() }}">
                                    <div class="mb-3">
                                        <label class="form-label small fw-semibold">Ngôn ngữ hiển thị</label>
                                        <select name="language" class="form-select">
                                            @php $langs = config('i18n.supported', []); $curLang = Auth::user()->language ?? app()->getLocale(); @endphp
                                            @foreach($langs as $code => $info)
                                                <option value="{{ $code }}" {{ $curLang == $code ? 'selected' : '' }}>
                                                    {{ $info['flag'] ?? '' }} {{ $info['name'] ?? $code }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-bold px-4">Lưu ngôn ngữ</button>
                                </form>
                            </div>

                            {{-- THÔNG BÁO --}}
                            <div class="card border-0 bg-light rounded-3 p-4 mb-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-bell me-2 text-primary"></i>Thông báo</h6>
                                <form action="{{ route('settings.notifications') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_back" value="{{ url()->current() }}">
                                    @php $notif = Auth::user()->notification_settings ?? []; @endphp
                                    @foreach([['email_booking','Email xác nhận đặt vé'],['email_promo','Email khuyến mãi'],['email_reminder','Email nhắc lịch chơi']] as [$key,$label])
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                                        <span class="small fw-semibold">{{ $label }}</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                                   {{ ($notif[$key] ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @endforeach
                                    <button type="submit" class="btn btn-primary fw-bold px-4 mt-3">Lưu thông báo</button>
                                </form>
                            </div>

                            {{-- QUYỀN RIÊNG TƯ --}}
                            <div class="card border-0 bg-light rounded-3 p-4 mb-4">
                                <h6 class="fw-bold mb-3"><i class="bi bi-shield-check me-2 text-primary"></i>Quyền riêng tư</h6>
                                <form action="{{ route('settings.privacy') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="redirect_back" value="{{ url()->current() }}">
                                    @php $priv = Auth::user()->privacy_settings ?? []; @endphp
                                    @foreach([['show_profile','Hiển thị hồ sơ công khai'],['show_activity','Hiển thị lịch sử hoạt động'],['allow_review_visible','Hiển thị đánh giá của tôi']] as [$key,$label])
                                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                                        <span class="small fw-semibold">{{ $label }}</span>
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="{{ $key }}" value="1"
                                                   {{ ($priv[$key] ?? true) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    @endforeach
                                    <button type="submit" class="btn btn-primary fw-bold px-4 mt-3">Lưu quyền riêng tư</button>
                                </form>
                            </div>

                            {{-- ĐĂNG XUẤT --}}
                            <div class="card border-0 bg-light rounded-3 p-4">
                                <h6 class="fw-bold mb-3 text-danger"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</h6>
                                <p class="text-muted small mb-3">Đăng xuất khỏi tài khoản tại chi nhánh {{ $location->name }}.</p>
                                <form action="{{ route('branch.logout', ['subdomain' => $subdomain]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger fw-bold px-4">Đăng xuất</button>
                                </form>
                            </div>
                        </div>

                        <script>
                            function selectThemeBranch(val, el) {
                                document.getElementById('inp-theme-branch').value = val;
                                document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('selected'));
                                el.classList.add('selected');
                                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                                const isDark = val === 'dark' || (val === 'auto' && prefersDark);
                                document.body.classList.toggle('dark-mode', isDark);
                            }
                            function selectColorBranch(hex, el) {
                                document.getElementById('inp-color-branch').value = hex;
                                document.querySelectorAll('.color-swatch').forEach(c => c.classList.remove('selected'));
                                el.classList.add('selected');
                                document.documentElement.style.setProperty('--primary', hex);
                            }
                            function selectFontBranch(val, el) {
                                document.getElementById('inp-font-branch').value = val;
                                document.querySelectorAll('.font-card').forEach(c => c.classList.remove('selected'));
                                el.classList.add('selected');
                                document.body.style.fontFamily = `'${val}', sans-serif`;
                            }
                            function copyBranchCode(code) {
                                if (!code) return;
                                navigator.clipboard.writeText(code);
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Đã sao chép!',
                                        text: 'Mã: ' + code,
                                        background: '#1a1d20',
                                        color: '#fff',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                } else {
                                    alert('Đã copy mã: ' + code);
                                }
                            }
                        </script>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            $('.btn-view-order').click(function () {
                let orderId = $(this).data('id');
                Swal.fire({ title: 'Đang tải...', didOpen: () => { Swal.showLoading(); } });

                $.ajax({
                    url: '{{ route("branch.profile.order.detail", ["subdomain" => $subdomain, "orderId" => "ORDER_ID"]) }}'.replace('ORDER_ID', orderId),
                    method: 'GET',
                    success: function (response) {
                        Swal.fire({
                            html: response.html,
                            showConfirmButton: true,
                            confirmButtonText: 'Đóng',
                            customClass: { container: 'swal-order-container' },
                            width: '500px'
                        });
                    }
                });
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
    @elseif(session('payment_success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ __("profile.payment_success") }} 🎮',
            html: `
                <div class="text-start small">
                    <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                        <span class="text-muted">{{ __("profile.order_id_label") ?? "Mã đơn:" }}</span>
                        <span class="fw-bold text-dark">#{{ session('order_id') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">{{ __("profile.deducted_label") ?? "Đã trừ ví:" }}</span>
                        <span class="fw-bold text-primary">{{ number_format(session('total_amount')) }}đ</span>
                    </div>
                    <div class="text-muted text-center mt-2" style="font-size:0.8rem;">
                        🎯 {{ __("profile.see_you_soon") ?? "Vé đã xác nhận. Hẹn gặp bạn tại cơ sở!" }}
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
    @elseif(session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: '{{ __("profile.success") ?? "Thành công!" }}',
            text: '{{ session("success") }}',
            background: '#fff',
            color: '#1f2937',
            timer: 3000,
            showConfirmButton: false
        });
    </script>
    @endif
    
    @include('branch.partials.footer')
</body>
</html>