<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Cài đặt - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* ===== SIDEBAR ===== */
        .settings-sidebar-card {
            background: linear-gradient(160deg, #0d1b3e 0%, #0a2a6e 45%, #1565c0 100%);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(13, 27, 62, 0.3);
            position: relative;
            overflow: hidden;
        }
        .settings-sidebar-card::before {
            content: '';
            position: absolute;
            top: -60px; left: -60px;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(56,189,248,0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* ===== SIDEBAR HEADER ===== */
        .settings-sidebar-header {
            padding: 1.5rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.5rem;
        }
        .settings-sidebar-header h6 {
            color: rgba(255,255,255,0.5);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin: 0;
        }
        .settings-sidebar-header p {
            color: #ffffff;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 4px 0 0;
        }

        /* ===== NAV PILLS ===== */
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
            font-size: 1.05rem;
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
        .nav-pills .nav-link:hover i { color: #38bdf8 !important; }
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
        .nav-pills .nav-link.text-danger-nav {
            color: rgba(252,165,165,0.8) !important;
        }
        .nav-pills .nav-link.text-danger-nav i { color: rgba(239,68,68,0.6) !important; }
        .nav-pills .nav-link.text-danger-nav:hover {
            background: rgba(239,68,68,0.15) !important;
            color: #fca5a5 !important;
        }

        /* ===== CONTENT CARD ===== */
        .settings-content-card {
            background-color: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
        }

        /* ===== SECTION HEADING ===== */
        .section-heading {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border);
        }

        /* ===== TOGGLE SWITCH ===== */
        .form-check-input:checked { background-color: #1e88e5; border-color: #1e88e5; }

        /* ===== SETTING ROW ===== */
        .setting-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid var(--border);
        }
        .setting-row:last-child { border-bottom: none; }
        .setting-row-info h6 { margin: 0; font-weight: 600; font-size: 0.9rem; color: var(--text-heading); }
        .setting-row-info p  { margin: 0; font-size: 0.8rem; color: var(--text-secondary); margin-top: 2px; }

        /* ===== THEME CARDS ===== */
        .theme-card {
            cursor: pointer;
            border-radius: 12px;
            padding: 14px;
            border: 2px solid var(--border);
            text-align: center;
            transition: all 0.2s;
        }
        .theme-card:hover { border-color: #1e88e5; transform: translateY(-2px); }
        .theme-card.selected { border-color: #1e88e5; background: rgba(30,136,229,0.06); }
        .theme-preview {
            width: 100%;
            height: 52px;
            border-radius: 8px;
            margin-bottom: 8px;
            display: flex;
            gap: 4px;
            padding: 6px;
        }
        .theme-preview-bar { border-radius: 4px; height: 100%; flex: 1; }

        /* ===== FONT CARD ===== */
        .font-card {
            cursor: pointer;
            border: 2px solid var(--border);
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.2s;
        }
        .font-card:hover { border-color: #1e88e5; }
        .font-card.selected { border-color: #1e88e5; background: rgba(30,136,229,0.05); }
        .font-card span { font-size: 1.1rem; display: block; margin-bottom: 2px; }
        .font-card small { font-size: 0.72rem; color: var(--text-secondary); }

        /* ===== COLOR SWATCH ===== */
        .color-swatch {
            width: 34px; height: 34px;
            border-radius: 50%;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.2s;
            display: inline-block;
        }
        .color-swatch:hover { transform: scale(1.15); }
        .color-swatch.selected { border-color: #1f2937; box-shadow: 0 0 0 2px #fff, 0 0 0 4px currentColor; }

        /* ===== PAYMENT CARD ===== */
        .payment-card {
            cursor: pointer;
            border: 2px solid var(--border);
            border-radius: 12px;
            padding: 14px 18px;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .payment-card:hover { border-color: #1e88e5; }
        .payment-card.selected { border-color: #1e88e5; background: rgba(30,136,229,0.05); }

        /* ===== DANGER ZONE ===== */
        .danger-zone { border: 1px solid rgba(220,53,69,0.25); border-radius: 12px; padding: 1.25rem; }

        /* ===== BACK LINK ===== */
        .btn-back-profile {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: rgba(255,255,255,0.65);
            text-decoration: none;
            font-size: 0.82rem;
            padding: 6px 12px;
            margin: 8px 8px 4px;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-back-profile:hover { background: rgba(255,255,255,0.08); color: #fff; }
    </style>
</head>

<body>
    @include('branch.partials.navbar')

    <div class="container py-5">
        <div class="row g-5">

            {{-- ===== SIDEBAR ===== --}}
            <div class="col-lg-4">
                <div class="settings-sidebar-card p-0 h-100 d-flex flex-column">
                    <div class="settings-sidebar-header">
                        <h6><i class="bi bi-gear me-1"></i> Trang cài đặt</h6>
                        <p>{{ Auth::user()->name }}</p>
                    </div>

                    <div class="nav flex-column nav-pills px-1 py-2 gap-1" id="settings-tab" role="tablist" style="gap: 4px !important;">

                        <button class="nav-link active" id="tab-ui-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-ui" type="button" role="tab">
                            <i class="bi bi-palette"></i> Giao diện
                        </button>

                        <button class="nav-link" id="tab-security-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-security" type="button" role="tab">
                            <i class="bi bi-shield-lock"></i> Bảo mật
                        </button>

                        <button class="nav-link" id="tab-lang-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-lang" type="button" role="tab">
                            <i class="bi bi-translate"></i> Ngôn ngữ & Vùng
                        </button>

                        <button class="nav-link" id="tab-notif-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-notif" type="button" role="tab">
                            <i class="bi bi-bell"></i> Thông báo
                        </button>

                        <button class="nav-link" id="tab-privacy-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-privacy" type="button" role="tab">
                            <i class="bi bi-shield-check"></i> Quyền riêng tư
                        </button>

                        <button class="nav-link" id="tab-payment-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-payment" type="button" role="tab">
                            <i class="bi bi-credit-card"></i> Thanh toán
                        </button>

                        <button class="nav-link" id="tab-access-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-access" type="button" role="tab">
                            <i class="bi bi-universal-access"></i> Trợ năng
                        </button>

                        <hr style="border-color: rgba(255,255,255,0.12); margin: 4px 8px;">

                        <button class="nav-link text-danger-nav" id="tab-account-btn"
                            data-bs-toggle="pill" data-bs-target="#tab-account" type="button" role="tab">
                            <i class="bi bi-person-x"></i> Tài khoản
                        </button>
                    </div>

                    <div class="px-2 py-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                        <a href="{{ route('branch.profile', ['subdomain' => $subdomain]) }}" class="btn-back-profile">
                            <i class="bi bi-arrow-left-circle"></i> Quay lại Hồ sơ
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== NỘI DUNG ===== --}}
            <div class="col-lg-8">
                <div class="settings-content-card p-4 p-md-5">

                    {{-- Flash messages --}}
                    @if(session('success'))
                        <div class="alert alert-success border-0 rounded-3 d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill me-2 fs-5"></i> {{ session('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger border-0 rounded-3 mb-4">
                            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <div class="tab-content" id="settings-tabContent">

                        {{-- ========================================
                             TAB 1: GIAO DIỆN
                        ======================================== --}}
                        <div class="tab-pane fade show active" id="tab-ui" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-palette-fill me-2"></i> Giao diện
                            </h4>

                            <form action="{{ route('branch.settings.ui', ['subdomain' => $subdomain]) }}" method="POST" id="formUI">
                                @csrf
                                @php $ui = Auth::user()->ui_settings ?? []; @endphp

                                {{-- Theme sáng / tối --}}
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size:0.8rem; color:var(--text-secondary);">Chủ đề</h6>
                                <div class="row g-3 mb-4">
                                    @foreach([
                                        ['value'=>'light','label'=>'Sáng','bars'=>['#f0f4f8','#1e88e5','#ffffff']],
                                        ['value'=>'dark', 'label'=>'Tối', 'bars'=>['#0d1b2a','#1e88e5','#1a2537']],
                                        ['value'=>'auto', 'label'=>'Tự động','bars'=>['#e3f2fd','#0d1b2a','#1e88e5']],
                                    ] as $t)
                                        <div class="col-4">
                                            <div class="theme-card {{ ($ui['theme'] ?? 'light') == $t['value'] ? 'selected' : '' }}"
                                                 onclick="selectTheme('{{ $t['value'] }}', this)">
                                                <div class="theme-preview" style="background:{{ $t['bars'][0] }}">
                                                    <div class="theme-preview-bar" style="background:{{ $t['bars'][1] }}"></div>
                                                    <div class="theme-preview-bar" style="background:{{ $t['bars'][2] }}"></div>
                                                    <div class="theme-preview-bar" style="background:{{ $t['bars'][1] }}; opacity:.5"></div>
                                                </div>
                                                <small class="fw-bold" style="font-size:0.82rem">{{ $t['label'] }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    <input type="hidden" name="theme" id="inp-theme" value="{{ $ui['theme'] ?? 'light' }}">
                                </div>

                                {{-- Màu chủ đạo --}}
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size:0.8rem; color:var(--text-secondary);">Màu chủ đạo</h6>
                                <div class="d-flex gap-3 flex-wrap mb-4">
                                    @foreach([
                                        ['#1e88e5','Xanh dương'],['#8b5cf6','Tím'],['#10b981','Xanh lá'],
                                        ['#f59e0b','Cam vàng'],['#ef4444','Đỏ'],['#ec4899','Hồng'],['#06b6d4','Cyan'],
                                    ] as [$hex, $name])
                                        <div class="color-swatch {{ ($ui['primary_color'] ?? '#1e88e5') == $hex ? 'selected' : '' }}"
                                             style="background:{{ $hex }}; color:{{ $hex }}"
                                             title="{{ $name }}"
                                             onclick="selectColor('{{ $hex }}', this)">
                                        </div>
                                    @endforeach
                                    <input type="hidden" name="primary_color" id="inp-color" value="{{ $ui['primary_color'] ?? '#1e88e5' }}">
                                </div>

                                {{-- Font chữ --}}
                                <h6 class="fw-bold text-uppercase mb-3" style="font-size:0.8rem; color:var(--text-secondary);">Font chữ</h6>
                                <div class="row g-2 mb-4">
                                    @foreach([
                                        ['Be Vietnam Pro','Be Vietnam Pro','Gọn, hiện đại'],
                                        ['Roboto','Roboto','Phổ biến, dễ đọc'],
                                        ['Merriweather','Merriweather','Thanh lịch, serif'],
                                        ['Nunito','Nunito','Tròn, thân thiện'],
                                    ] as [$val, $label, $desc])
                                        <div class="col-6">
                                            <div class="font-card {{ ($ui['font'] ?? 'Be Vietnam Pro') == $val ? 'selected' : '' }}"
                                                 style="font-family:'{{ $val }}', sans-serif"
                                                 onclick="selectFont('{{ $val }}', this)">
                                                <span>Aa — {{ $label }}</span>
                                                <small>{{ $desc }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    <input type="hidden" name="font" id="inp-font" value="{{ $ui['font'] ?? 'Be Vietnam Pro' }}">
                                </div>

                                {{-- Cỡ chữ --}}
                                <h6 class="fw-bold text-uppercase mb-2" style="font-size:0.8rem; color:var(--text-secondary);">
                                    Cỡ chữ — <span id="fontsize-label" class="text-primary fw-bold">
                                        {{ $ui['font_size'] ?? '15' }}px
                                    </span>
                                </h6>
                                <div class="mb-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <span style="font-size:0.75rem; color:var(--text-muted); min-width:24px;">A</span>
                                        <input type="range" id="fontsize-slider"
                                               min="12" max="32" step="1"
                                               value="{{ is_numeric($ui['font_size'] ?? '') ? ($ui['font_size'] ?? 15) : 15 }}"
                                               class="form-range flex-grow-1"
                                               oninput="onFontSizeSlider(this.value)"
                                               style="accent-color: var(--primary);">
                                        <span style="font-size:1.3rem; color:var(--text-muted); font-weight:700; min-width:24px;">A</span>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1" style="font-size:0.72rem; color:var(--text-muted); padding: 0 2px;">
                                        <span>12px</span>
                                        <span>18px</span>
                                        <span>24px</span>
                                        <span>32px</span>
                                    </div>
                                    <input type="hidden" name="font_size" id="inp-fontsize" value="{{ is_numeric($ui['font_size'] ?? '') ? ($ui['font_size'] ?? 15) : 15 }}">
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">
                                        <i class="bi bi-floppy-fill me-2"></i> Lưu giao diện
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ========================================
                             TAB 2: BẢO MẬT
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-security" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-shield-lock-fill me-2"></i> Bảo mật
                            </h4>

                            {{-- Mật khẩu và Xác thực --}}
                            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px; background:var(--bg-subtle);">
                                <div class="card-body p-4 p-md-5">
                                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                        <div>
                                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-key-fill text-warning me-2"></i> Đổi mật khẩu</h5>
                                            <p class="text-muted small mb-0">Thay đổi mật khẩu đăng nhập để bảo vệ tài khoản tốt hơn.</p>
                                        </div>
                                        @if(Auth::user()->provider_name)
                                            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill shadow-sm border border-danger border-opacity-25">
                                                <i class="bi bi-google me-1"></i> Đăng nhập Google
                                            </span>
                                        @endif
                                    </div>

                                    @if(Auth::user()->provider_name)
                                        <div class="alert alert-warning border border-warning border-opacity-50 rounded-3 d-flex align-items-center p-4">
                                            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning me-4"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">Tài khoản liên kết Mạng Xã Hội</h6>
                                                <p class="mb-0 text-dark small">Bạn đang đăng nhập bằng nền tảng <b>{{ ucfirst(Auth::user()->provider_name) }}</b>. 
                                                Hệ thống không quản lý mật khẩu của phương thức này. Để đặt mật khẩu riêng cho Holomia, bạn cần Hủy liên kết ở tab <b>Tài khoản</b> ở phía dưới.</p>
                                            </div>
                                        </div>
                                    @else
                                        @if($errors->has('current_password'))
                                            <div class="alert alert-danger border-0 rounded-3 mb-4 d-flex align-items-center p-3 shadow-sm">
                                                <i class="bi bi-shield-exclamation fs-3 me-3 text-danger"></i> 
                                                <div>
                                                    <strong>Lỗi xác thực:</strong><br/>
                                                    {{ $errors->first('current_password') }}
                                                </div>
                                            </div>
                                        @endif

                                        <form action="{{ route('branch.settings.password', ['subdomain' => $subdomain]) }}" method="POST">
                                            @csrf
                                            <div class="row g-4">
                                                <div class="col-12">
                                                    <label class="form-label text-uppercase small fw-bold text-muted">Mật khẩu hiện tại</label>
                                                    <div class="input-group input-group-lg shadow-sm">
                                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                                        <input type="password" name="current_password" class="form-control border-start-0 ps-0 @error('current_password') is-invalid @enderror" placeholder="Nhập mật khẩu cũ của bạn">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-uppercase small fw-bold text-muted">Mật khẩu mới</label>
                                                    <div class="input-group input-group-lg shadow-sm">
                                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-key text-primary"></i></span>
                                                        <input type="password" name="new_password" class="form-control border-start-0 ps-0 @error('new_password') is-invalid @enderror" placeholder="Tối thiểu 6 ký tự">
                                                    </div>
                                                    @error('new_password')<small class="text-danger mt-1 d-block">{{ $message }}</small>@enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label text-uppercase small fw-bold text-muted">Xác nhận mật khẩu mới</label>
                                                    <div class="input-group input-group-lg shadow-sm">
                                                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-check2-all text-success"></i></span>
                                                        <input type="password" name="new_password_confirmation" class="form-control border-start-0 ps-0" placeholder="Nhập lại mật khẩu mới">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-4 pt-3 border-top text-end">
                                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-pill shadow">
                                                    <i class="bi bi-check-circle-fill me-2"></i> Lưu mật khẩu mới
                                                </button>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Quản lý Phiên Đăng Nhập (Sessions) --}}
                            <div class="card border-0 shadow-sm mb-4" style="border-radius:14px; background:var(--bg-subtle);">
                                <div class="card-body p-4 p-md-5">
                                    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                                        <div>
                                            <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-pc-display-horizontal text-primary me-2"></i> Lịch sử và Thiết bị</h5>
                                            <p class="text-muted small mb-0">Kiểm tra các thiết bị đang truy cập vào tài khoản của bạn.</p>
                                        </div>
                                    </div>
                                    
                                    {{-- Thiết bị giả lập hiện tại --}}
                                    <div class="d-flex align-items-center mb-4 p-3 rounded-4 shadow-sm" style="background:#ffffff; border:1px solid var(--border);">
                                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="bi bi-laptop fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold text-dark">Ứng dụng Web / Máy tính</h6>
                                            <small class="text-success fw-bold d-flex align-items-center gap-1 mt-1">
                                                <span class="spinner-grow spinner-grow-sm text-success" style="width: 10px; height: 10px;"></span> 
                                                Thiết bị hiện tại đang dùng
                                            </small>
                                        </div>
                                        <div class="text-end d-none d-sm-block">
                                            <small class="text-muted d-block">IP: {{ request()->ip() }}</small>
                                            <small class="text-muted border border-secondary rounded-1 px-1" style="font-size:0.7rem;">Chỉ bạn thấy</small>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-center mb-4 p-3 rounded-4 shadow-sm opacity-75" style="background:#ffffff; border:1px solid var(--border);">
                                        <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                            <i class="bi bi-phone fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold text-dark text-decoration-line-through">Thiết bị di động không rõ</h6>
                                            <small class="text-muted fw-bold mt-1 d-block"><i class="bi bi-clock-history"></i> Đăng nhập lần cuối: 2 ngày trước</small>
                                        </div>
                                    </div>

                                    <div class="alert alert-danger border border-danger border-opacity-25 rounded-3 mt-4">
                                        <div class="d-flex align-items-start">
                                            <i class="bi bi-exclamation-square-fill fs-3 text-danger me-3"></i>
                                            <div>
                                                <h6 class="fw-bold text-danger mb-1">Cảnh báo An Toàn</h6>
                                                <p class="small text-danger mb-3">Nếu bạn phát hiện thiết bị lạ lạ hoặc tài khoản có nguy cơ bị xâm nhập, hãy tiến hành Đăng xuất khỏi toàn bộ các thiết bị (bao gồm thiết bị này) ngay lập tức!</p>
                                                
                                                <form action="{{ route('settings.logout_all') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm px-4 py-2 rounded-pill fw-bold shadow-sm"
                                                            onclick="return confirm('Toàn bộ thiết bị (kể cả máy tính này) sẽ bị đăng xuất. Bạn cần đăng nhập lại bằng mật khẩu. Bạn có chắc chắn?')">
                                                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất tất cả thiết bị
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ========================================
                             TAB 3: NGÔN NGỮ & VÙNG
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-lang" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-translate me-2"></i> Ngôn ngữ & Vùng
                            </h4>

                            <form action="{{ route('settings.language') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Ngôn ngữ hiển thị</label>
                                    <p class="text-muted small mb-2">Thay đổi ngôn ngữ giao diện ứng dụng (không ảnh hưởng thanh menu chính).</p>
                                    <div class="row g-2">
                                        @foreach([
                                            ['vi','🇻🇳','Tiếng Việt'],
                                            ['en','🇺🇸','English'],
                                            ['ja','🇯🇵','日本語'],
                                            ['zh','🇨🇳','中文'],
                                            ['ko','🇰🇷','한국어'],
                                            ['hi','🇮🇳','हिन्दी'],
                                        ] as [$code,$flag,$name])
                                            <div class="col-6 col-md-4">
                                                <label class="payment-card w-100 {{ (Auth::user()->language ?? 'vi') == $code ? 'selected' : '' }}"
                                                       style="cursor:pointer">
                                                    <input type="radio" name="language" value="{{ $code }}"
                                                           class="form-check-input me-0"
                                                           {{ (Auth::user()->language ?? 'vi') == $code ? 'checked' : '' }}>
                                                    <span style="font-size:1.4rem">{{ $flag }}</span>
                                                    <span class="fw-bold" style="font-size:0.9rem">{{ $name }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-bold">Đơn vị tiền tệ</label>
                                    <p class="text-muted small mb-2">Ảnh hưởng tới cách hiển thị giá tiền trong ứng dụng.</p>
                                    <select name="currency" class="form-select" style="max-width:260px">
                                        @foreach(['VND'=>'🇻🇳 VND — Việt Nam Đồng','USD'=>'🇺🇸 USD — US Dollar','JPY'=>'🇯🇵 JPY — Yên Nhật','KRW'=>'🇰🇷 KRW — Won Hàn Quốc'] as $v=>$l)
                                            <option value="{{ $v }}" {{ (Auth::user()->currency ?? 'VND') == $v ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">
                                        <i class="bi bi-floppy-fill me-2"></i> Lưu ngôn ngữ
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ========================================
                             TAB 4: THÔNG BÁO
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-notif" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-bell-fill me-2"></i> Thông báo
                            </h4>

                            <form action="{{ route('settings.notifications') }}" method="POST">
                                @csrf
                                @php $notif = Auth::user()->notification_prefs ?? []; @endphp

                                <h6 class="fw-bold text-uppercase small text-muted mb-3">Email</h6>
                                <div class="mb-4">
                                    @foreach([
                                        ['email_booking',  'Xác nhận đặt vé',  'Nhận email xác nhận mỗi khi đặt vé thành công'],
                                        ['email_promotion','Khuyến mãi & ưu đãi','Nhận thông báo về các chương trình giảm giá, voucher mới'],
                                        ['remind_schedule','Nhắc lịch trước giờ chơi','Nhắc trước 2 tiếng khi có lịch hẹn sắp tới'],
                                    ] as [$key,$title,$desc])
                                        <div class="setting-row">
                                            <div class="setting-row-info">
                                                <h6>{{ $title }}</h6>
                                                <p>{{ $desc }}</p>
                                            </div>
                                            <div class="form-check form-switch ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                       name="{{ $key }}" role="switch"
                                                       style="width:2.4em; height:1.3em"
                                                       {{ ($notif[$key] ?? true) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <h6 class="fw-bold text-uppercase small text-muted mb-3 mt-4">Trong ứng dụng</h6>
                                <div class="mb-4">
                                    @foreach([
                                        ['inapp_alerts','Thông báo đơn hàng','Cập nhật trạng thái đơn hàng trong ứng dụng'],
                                    ] as [$key,$title,$desc])
                                        <div class="setting-row">
                                            <div class="setting-row-info">
                                                <h6>{{ $title }}</h6>
                                                <p>{{ $desc }}</p>
                                            </div>
                                            <div class="form-check form-switch ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                       name="{{ $key }}" role="switch"
                                                       style="width:2.4em; height:1.3em"
                                                       {{ ($notif[$key] ?? true) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">
                                        <i class="bi bi-floppy-fill me-2"></i> Lưu cài đặt thông báo
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ========================================
                             TAB 5: QUYỀN RIÊNG TƯ
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-privacy" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-shield-check me-2"></i> Quyền riêng tư
                            </h4>

                            <form action="{{ route('settings.privacy') }}" method="POST">
                                @csrf
                                @php $priv = Auth::user()->privacy_settings ?? []; @endphp

                                <div class="mb-4">
                                    @foreach([
                                        ['hide_balance','Ẩn số dư mặc định','Số dư ví sẽ hiển thị dạng ****** cho đến khi bạn chủ động mở'],
                                        ['hide_history','Ẩn lịch sử tìm kiếm','Không lưu lịch sử tìm kiếm trò chơi của bạn'],
                                    ] as [$key,$title,$desc])
                                        <div class="setting-row">
                                            <div class="setting-row-info">
                                                <h6>{{ $title }}</h6>
                                                <p>{{ $desc }}</p>
                                            </div>
                                            <div class="form-check form-switch ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                       name="{{ $key }}" role="switch"
                                                       style="width:2.4em; height:1.3em"
                                                       {{ ($priv[$key] ?? false) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">
                                        <i class="bi bi-floppy-fill me-2"></i> Lưu quyền riêng tư
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ========================================
                             TAB 6: THANH TOÁN
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-payment" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-credit-card-fill me-2"></i> Thanh toán
                            </h4>

                            <p class="text-muted small mb-4">Chọn phương thức thanh toán mặc định khi checkout. Bạn vẫn có thể thay đổi từng lần đặt vé.</p>

                            <form action="{{ route('settings.payment') }}" method="POST">
                                @csrf
                                @php $defPay = Auth::user()->default_payment ?? 'wallet'; @endphp

                                <div class="row g-3 mb-4">
                                    @foreach([
                                        ['wallet','bi-wallet2','Ví Holomia','Thanh toán bằng số dư ví, nhanh nhất','text-info'],
                                        ['banking','bi-bank2','Chuyển khoản ngân hàng','Quét QR hoặc chuyển khoản thủ công','text-success'],
                                        ['cod','bi-shop','Thanh toán tại quầy','Đến cơ sở trả tiền mặt trực tiếp','text-warning'],
                                    ] as [$val,$icon,$title,$desc,$color])
                                        <div class="col-12">
                                            <label class="payment-card w-100 {{ $defPay == $val ? 'selected' : '' }}" style="cursor:pointer">
                                                <input type="radio" name="default_payment" value="{{ $val }}"
                                                       class="form-check-input"
                                                       {{ $defPay == $val ? 'checked' : '' }}
                                                       onchange="document.querySelectorAll('.payment-card').forEach(c=>c.classList.remove('selected')); this.closest('.payment-card').classList.add('selected')">
                                                <i class="bi {{ $icon }} fs-4 {{ $color }}"></i>
                                                <div class="flex-grow-1">
                                                    <div class="fw-bold" style="font-size:0.92rem">{{ $title }}</div>
                                                    <div class="text-muted" style="font-size:0.78rem">{{ $desc }}</div>
                                                </div>
                                                @if($defPay == $val)
                                                    <span class="badge bg-primary rounded-pill">Mặc định</span>
                                                @endif
                                            </label>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">
                                        <i class="bi bi-floppy-fill me-2"></i> Lưu thanh toán
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ========================================
                             TAB 7: TRỢ NĂNG
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-access" role="tabpanel">
                            <h4 class="section-heading text-primary">
                                <i class="bi bi-universal-access me-2"></i> Trợ năng
                            </h4>

                            <form action="{{ route('settings.accessibility') }}" method="POST">
                                @csrf
                                @php $ui = Auth::user()->ui_settings ?? []; @endphp

                                <div class="mb-4">
                                    @foreach([
                                        ['high_contrast','Tương phản cao','Tăng độ tương phản màu sắc để dễ đọc hơn'],
                                        ['reduce_motion','Giảm hiệu ứng chuyển động','Tắt animation, confetti và các hiệu ứng động trong ứng dụng'],
                                        ['large_text','Chữ lớn hơn','Tự động tăng cỡ chữ toàn bộ trang lên 110%'],
                                    ] as [$key,$title,$desc])
                                        <div class="setting-row">
                                            <div class="setting-row-info">
                                                <h6>{{ $title }}</h6>
                                                <p>{{ $desc }}</p>
                                            </div>
                                            <div class="form-check form-switch ms-3">
                                                <input class="form-check-input" type="checkbox"
                                                       name="{{ $key }}" role="switch"
                                                       style="width:2.4em; height:1.3em"
                                                       {{ ($ui[$key] ?? false) ? 'checked' : '' }}>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary px-5 py-2 fw-bold rounded-3">
                                        <i class="bi bi-floppy-fill me-2"></i> Lưu trợ năng
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- ========================================
                             TAB 8: TÀI KHOẢN
                        ======================================== --}}
                        <div class="tab-pane fade" id="tab-account" role="tabpanel">
                            <h4 class="section-heading text-danger">
                                <i class="bi bi-person-x-fill me-2"></i> Tài khoản
                            </h4>

                            {{-- Liên kết mạng xã hội --}}
                            <h6 class="fw-bold mb-3">Liên kết mạng xã hội</h6>

                            @if($errors->has('unlink'))
                                <div class="alert alert-danger border-0 rounded-3 mb-3">
                                    <i class="bi bi-exclamation-circle me-2"></i> {{ $errors->first('unlink') }}
                                </div>
                            @endif

                            <div class="mb-4">
                                <div class="setting-row">
                                    <div class="setting-row-info d-flex align-items-center gap-3">
                                        <i class="bi bi-google fs-4 text-danger"></i>
                                        <div>
                                            <h6>Google</h6>
                                            <p>
                                                @if(Auth::user()->provider_name == 'google')
                                                    <span class="text-success">Đã liên kết ({{ Auth::user()->email }})</span>
                                                @else
                                                    Chưa liên kết tài khoản Google
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    @if(Auth::user()->provider_name == 'google')
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i> Đã liên kết
                                            </span>
                                            {{-- Nút hủy liên kết --}}
                                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                                                    onclick="document.getElementById('unlinkForm').classList.toggle('d-none')">
                                                <i class="bi bi-x-circle me-1"></i> Hủy liên kết
                                            </button>
                                        </div>
                                    @else
                                        <a href="{{ route('social.redirect', 'google') }}"
                                           class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            <i class="bi bi-link-45deg me-1"></i> Liên kết
                                        </a>
                                    @endif
                                </div>

                                {{-- Form xác nhận hủy liên kết --}}
                                @if(Auth::user()->provider_name == 'google')
                                    <form id="unlinkForm" action="{{ route('settings.unlink_social') }}" method="POST"
                                          class="mt-3 d-none p-3 rounded-3"
                                          style="background: rgba(239,68,68,0.06); border: 1px solid rgba(239,68,68,0.2);">
                                        @csrf
                                        <p class="small text-danger fw-bold mb-2">
                                            <i class="bi bi-exclamation-triangle me-1"></i>
                                            Sau khi hủy liên kết, bạn sẽ cần đăng nhập bằng mật khẩu. Chắc chắn muốn hủy?
                                        </p>
                                        <button type="submit" class="btn btn-danger btn-sm fw-bold rounded-3"
                                                onclick="return confirm('Hủy liên kết tài khoản Google? Bạn sẽ cần mật khẩu để đăng nhập sau.')">
                                            <i class="bi bi-x-lg me-1"></i> Xác nhận hủy liên kết
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm ms-2 rounded-3"
                                                onclick="document.getElementById('unlinkForm').classList.add('d-none')">
                                            Hủy bỏ
                                        </button>
                                    </form>
                                @endif
                            </div>

                            <hr class="my-4">

                            {{-- Xóa tài khoản --}}
                            <h6 class="fw-bold text-danger mb-2">Xóa tài khoản</h6>
                            <div class="danger-zone">
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-exclamation-triangle-fill text-danger me-1"></i>
                                    Hành động này <strong>không thể hoàn tác</strong>. Toàn bộ dữ liệu của bạn bao gồm đơn hàng, ví, điểm thưởng sẽ bị xóa vĩnh viễn.
                                </p>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-3 px-4"
                                    onclick="document.getElementById('deleteAccountForm').classList.toggle('d-none')">
                                    <i class="bi bi-trash me-2"></i> Xóa tài khoản của tôi
                                </button>

                                <form id="deleteAccountForm" action="{{ route('settings.delete_account') }}" method="POST"
                                      class="mt-3 d-none" style="max-width:380px">
                                    @csrf
                                    <p class="small text-danger fw-bold mb-2">Nhập <code>XOA_TAI_KHOAN</code> để xác nhận:</p>
                                    <div class="input-group">
                                        <input type="text" name="confirm_delete" class="form-control form-control-sm"
                                               placeholder="XOA_TAI_KHOAN" required>
                                        <button type="submit" class="btn btn-danger btn-sm fw-bold"
                                                onclick="return confirm('Bạn CHẮC CHẮN muốn xóa tài khoản? Không thể khôi phục!')">
                                            Xác nhận xóa
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>{{-- end tab-content --}}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- ===== APPLY SETTINGS ON LOAD ===== --}}
    <script>
    (function() {
        @php $ui = Auth::user()->ui_settings ?? []; @endphp
        const settings = {
            theme:        '{{ $ui["theme"]         ?? "light" }}',
            font:         '{{ $ui["font"]           ?? "Be Vietnam Pro" }}',
            fontSize:     '{{ is_numeric($ui["font_size"] ?? "") ? ($ui["font_size"] ?? 15) : 15 }}',
            color:        '{{ $ui["primary_color"]  ?? "#1e88e5" }}',
            highContrast: {{ ($ui["high_contrast"]  ?? false) ? 'true' : 'false' }},
            reduceMotion: {{ ($ui["reduce_motion"]  ?? false) ? 'true' : 'false' }},
            largeText:    {{ ($ui["large_text"]     ?? false) ? 'true' : 'false' }},
        };

        // Lưu settings vào localStorage để các trang khác đọc được
        localStorage.setItem('holomia_ui', JSON.stringify(settings));

        applySettings(settings);

        function applySettings(s) {
            const body = document.body;
            const html = document.documentElement;

            // --- THEME (dark/light/auto) ---
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const isDark = s.theme === 'dark' || (s.theme === 'auto' && prefersDark);
            body.classList.toggle('dark-mode', isDark);

            // --- FONT ---
            body.style.fontFamily = `'${s.font}', sans-serif`;

            // --- FONT SIZE (px trực tiếp) ---
            const baseSize = parseInt(s.fontSize) || 15;
            html.style.fontSize = s.largeText ? (baseSize * 1.1) + 'px' : baseSize + 'px';

            // --- PRIMARY COLOR ---
            html.style.setProperty('--primary', s.color);
            html.style.setProperty('--primary-dark', shadeColor(s.color, -20));
            html.style.setProperty('--primary-light', hexToRgba(s.color, 0.1));
            html.style.setProperty('--primary-glow', hexToRgba(s.color, 0.18));

            // --- HIGH CONTRAST ---
            body.classList.toggle('high-contrast', s.highContrast);

            // --- REDUCE MOTION ---
            const existingStyle = document.getElementById('reduce-motion-style');
            if (existingStyle) existingStyle.remove();
            if (s.reduceMotion) {
                const style = document.createElement('style');
                style.id = 'reduce-motion-style';
                style.textContent = '*, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }';
                document.head.appendChild(style);
            }
        }

        // Helper: làm tối màu hex
        function shadeColor(hex, percent) {
            const num = parseInt(hex.replace('#', ''), 16);
            const r = Math.max(0, Math.min(255, (num >> 16) + percent * 2.55));
            const g = Math.max(0, Math.min(255, ((num >> 8) & 0xff) + percent * 2.55));
            const b = Math.max(0, Math.min(255, (num & 0xff) + percent * 2.55));
            return '#' + [r, g, b].map(v => Math.round(v).toString(16).padStart(2, '0')).join('');
        }
        // Helper: hex → rgba
        function hexToRgba(hex, alpha) {
            const num = parseInt(hex.replace('#', ''), 16);
            return `rgba(${(num >> 16) & 255}, ${(num >> 8) & 255}, ${num & 255}, ${alpha})`;
        }

        // Auto-detect nếu theme = auto và OS đổi chế độ
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            const saved = JSON.parse(localStorage.getItem('holomia_ui') || '{}');
            if (saved.theme === 'auto') applySettings(saved);
        });
    })();

    // Selector helpers
    function selectTheme(val, el) {
        document.getElementById('inp-theme').value = val;
        document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');

        // Preview ngay lập tức
        const body = document.body;
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        const isDark = val === 'dark' || (val === 'auto' && prefersDark);
        body.classList.toggle('dark-mode', isDark);

        // Cập nhật localStorage
        const saved = JSON.parse(localStorage.getItem('holomia_ui') || '{}');
        saved.theme = val;
        localStorage.setItem('holomia_ui', JSON.stringify(saved));
    }
    function selectColor(hex, el) {
        document.getElementById('inp-color').value = hex;
        document.querySelectorAll('.color-swatch').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.documentElement.style.setProperty('--primary', hex);
        const saved = JSON.parse(localStorage.getItem('holomia_ui') || '{}');
        saved.color = hex;
        localStorage.setItem('holomia_ui', JSON.stringify(saved));
    }
    function selectFont(val, el) {
        document.getElementById('inp-font').value = val;
        document.querySelectorAll('.font-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.body.style.fontFamily = `'${val}', sans-serif`;
    }
    function onFontSizeSlider(val) {
        document.getElementById('inp-fontsize').value = val;
        document.getElementById('fontsize-label').textContent = val + 'px';
        document.documentElement.style.fontSize = val + 'px';
        const saved = JSON.parse(localStorage.getItem('holomia_ui') || '{}');
        saved.fontSize = val;
        localStorage.setItem('holomia_ui', JSON.stringify(saved));
    }

    // Auto-open tab từ ?tab= query param
    (function() {
        const tabMap = {
            'ui':           'tab-ui-btn',
            'security':     'tab-security-btn',
            'language':     'tab-lang-btn',
            'notifications':'tab-notif-btn',
            'privacy':      'tab-privacy-btn',
            'payment':      'tab-payment-btn',
            'accessibility':'tab-access-btn',
            'account':      'tab-account-btn',
        };
        const param = new URLSearchParams(window.location.search).get('tab');
        if (param && tabMap[param]) {
            const btn = document.getElementById(tabMap[param]);
            if (btn) bootstrap.Tab.getOrCreateInstance(btn).show();
        }
    })();

    // Tự ẩn alert sau 3 giây
    setTimeout(function() {
        document.querySelectorAll('.alert').forEach(a => {
            a.style.transition = 'opacity .5s';
            a.style.opacity = '0';
            setTimeout(() => a.remove(), 500);
        });
    }, 3000);
    </script>

    @include('partials.footer')
</body>
</html>