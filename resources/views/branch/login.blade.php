@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login_title') }} - {{ $location->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family={{ $langConfig['font'] }}&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            display: flex;
            font-family: {{ $langConfig['font_family'] }};
            background: #0a0a0f;
        }

        /* ========== LEFT PANEL ========== */
        .left-panel {
            flex: 1;
            background: linear-gradient(135deg, #0d1b4b 0%, #0a2a6e 40%, {{ $location->color ?? '#0055c4' }} 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 40px;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, {{ hex2rgba($location->color ?? '#0078ff', 0.25) }} 0%, transparent 70%);
            top: -100px; right: -100px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, {{ hex2rgba($location->color ?? '#00c8ff', 0.15) }} 0%, transparent 70%);
            bottom: -80px; left: -80px;
        }

        .grid-bg {
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 50px 50px;
        }

        .brand-content { position: relative; z-index: 2; text-align: center; }

        .brand-logo {
            font-family: 'Orbitron', monospace;
            font-size: 2.8rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: 3px;
            text-shadow: 0 0 30px {{ hex2rgba($location->color ?? '#0096ff', 0.6) }};
            margin-bottom: 8px;
        }

        .brand-logo span { color: {{ $location->color ?? '#00c8ff' }}; }

        .brand-tagline {
            color: rgba(255,255,255,0.6);
            font-size: 0.95rem;
            font-weight: 300;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 50px;
        }

        .vr-icon {
            width: 200px; height: 200px;
            margin: 0 auto 40px;
            position: relative;
        }

        .vr-icon svg { width: 100%; height: 100%; filter: drop-shadow(0 0 20px {{ hex2rgba($location->color ?? '#00b4ff', 0.5) }}); }

        .feature-list {
            list-style: none;
            text-align: left;
            width: 100%;
            max-width: 320px;
        }

        .feature-list li {
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-list li:last-child { border-bottom: none; }
        .feature-list li i { color: {{ $location->color ?? '#00c8ff' }}; font-size: 1.1rem; flex-shrink: 0; }

        /* ========== RIGHT PANEL ========== */
        .right-panel {
            width: 480px;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 50px;
        }

        .form-wrapper { width: 100%; max-width: 380px; }

        .form-header { margin-bottom: 32px; }

        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #0d1b4b;
            margin-bottom: 6px;
        }

        .form-header p { color: #6b7280; font-size: 0.9rem; }

        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .input-group-text {
            background: #f8fafc;
            border: 1.5px solid #e5e7eb;
            border-right: none;
            color: #9ca3af;
        }

        .form-control {
            border: 1.5px solid #e5e7eb;
            border-left: none;
            font-size: 0.9rem;
            padding: 10px 14px;
            color: #1f2937;
            background: #f8fafc;
            transition: all 0.2s;
        }

        .form-control:focus { border-color: {{ $location->color ?? '#0055c4' }}; background: #fff; box-shadow: none; outline: none; }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: {{ $location->color ?? '#0055c4' }};
            background: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, {{ $location->color ?? '#0055c4' }}, {{ $location->color ?? '#0078ff' }});
            border: none;
            color: #fff;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 12px;
            border-radius: 8px;
            width: 100%;
            transition: all 0.2s;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px {{ hex2rgba($location->color ?? '#0055c4', 0.35) }};
            color: #fff;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: #9ca3af;
            font-size: 0.8rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e5e7eb;
        }

        .alert-custom {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            color: #dc2626;
            font-size: 0.85rem;
            padding: 10px 14px;
        }

        .link-blue { color: {{ $location->color ?? '#0055c4' }}; font-weight: 600; text-decoration: none; }
        .link-blue:hover { opacity: 0.8; text-decoration: underline; }

        .link-muted { color: #6b7280; font-size: 0.85rem; text-decoration: none; }
        .link-muted:hover { color: #374151; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 24px; }
        }

        /* Thêm style cho các nút social login (nếu có dùng trong subdomain) */
        .btn-social {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            padding: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            color: #374151;
            background: #fff;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .btn-social:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
            color: #111827;
            transform: translateY(-1px);
        }
        .btn-social img { width: 20px; height: 20px; }
    </style>
</head>
<body>

    @php
    function hex2rgba($color, $opacity = false) {
        $default = 'rgb(0,0,0)';
        if(empty($color)) return $default;
        if ($color[0] == '#' ) { $color = substr($color, 1); }
        if (strlen($color) == 6) { $hex = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]); }
        elseif (strlen($color) == 3) { $hex = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]); }
        else { return $default; }
        $rgb = array(hexdec($hex[0]), hexdec($hex[1]), hexdec($hex[2]));
        if($opacity !== false){ return 'rgba('.implode(",",$rgb).','.$opacity.')'; }
        else { return 'rgb('.implode(",",$rgb).')'; }
    }
    @endphp

    {{-- LEFT PANEL: BRAND --}}
    <div class="left-panel">
        <div class="grid-bg"></div>
        <div class="brand-content">
            <div class="brand-logo">{{ $location->name }} <span>VR</span></div>
            <div class="brand-tagline">Hệ thống đặt vé trực tuyến chi nhánh {{ $location->name }}</div>

            <div class="vr-icon">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="95" stroke="{{ hex2rgba($location->color ?? '#00c8ff', 0.2) }}" stroke-width="1"/>
                    <circle cx="100" cy="100" r="70" stroke="{{ hex2rgba($location->color ?? '#00c8ff', 0.15) }}" stroke-width="1"/>
                    <rect x="30" y="70" width="140" height="75" rx="20" fill="rgba(0,0,0,0.2)" stroke="{{ $location->color ?? '#00c8ff' }}" stroke-width="1.5"/>
                    <circle cx="70" cy="107" r="22" fill="rgba(0,30,80,0.9)" stroke="{{ $location->color ?? '#00c8ff' }}" stroke-width="1.5"/>
                    <circle cx="130" cy="107" r="22" fill="rgba(0,30,80,0.9)" stroke="{{ $location->color ?? '#00c8ff' }}" stroke-width="1.5"/>
                    <circle cx="63" cy="100" r="6" fill="{{ hex2rgba($location->color ?? '#00c8ff', 0.5) }}"/>
                    <circle cx="123" cy="100" r="6" fill="{{ hex2rgba($location->color ?? '#00c8ff', 0.5) }}"/>
                    <rect x="89" y="102" width="22" height="10" rx="5" fill="{{ hex2rgba($location->color ?? '#00c8ff', 0.7) }}" stroke="{{ $location->color ?? '#00c8ff' }}" stroke-width="1"/>
                    <path d="M30 85 Q100 55 170 85" stroke="{{ $location->color ?? '#00c8ff' }}" stroke-width="1.5" fill="none" stroke-dasharray="4 3"/>
                </svg>
            </div>

            <ul class="feature-list">
                <li><i class="bi bi-controller"></i> {{ __('auth.feature_1') }}</li>
                <li><i class="bi bi-geo-alt-fill"></i> {{ __('auth.feature_2') }}</li>
                <li><i class="bi bi-ticket-perforated-fill"></i> {{ __('auth.feature_3') }}</li>
                <li><i class="bi bi-shield-check-fill"></i> {{ __('auth.feature_4') }}</li>
            </ul>
        </div>
    </div>

    {{-- RIGHT PANEL: FORM --}}
    <div class="right-panel">
        <div class="form-wrapper">

            <div class="form-header text-center mb-4">
                <h2 style="font-weight: 700; font-size: 2rem;">{{ __('auth.login_heading') }}</h2>
                <p class="text-muted mt-2">Dành cho khách hàng cơ sở {{ $location->name }}</p>
            </div>

            @if($errors->any())
                <div class="alert-custom mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('auth.login_error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success py-2 small mb-4 rounded-3 text-center">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Social Logins (Giữ nguyên link redirect về main domain vì Social Login thường cần 1 callback duy nhất) --}}
            <div class="d-flex flex-column gap-3 mb-4">
                <a href="{{ route('social.redirect', 'google') }}?subdomain={{ $subdomain }}" class="btn btn-social btn-google">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/c/c1/Google_%22G%22_logo.svg" alt="Google">
                    Tiếp tục với Google
                </a>
                <a href="{{ route('social.redirect', 'facebook') }}?subdomain={{ $subdomain }}" class="btn btn-social btn-facebook">
                    <i class="bi bi-facebook" style="color: #1877F2; font-size: 1.25rem;"></i>
                    Tiếp tục với Facebook
                </a>
            </div>

            <div class="divider">{{ __('auth.or') }} email</div>

            <form action="{{ route('branch.login.post', ['subdomain' => $subdomain]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control"
                            placeholder="Nhập email của bạn" required value="{{ old('email') }}" autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="password"
                            class="form-control" placeholder="{{ __('auth.password_placeholder') }}" required>
                        <button type="button" class="btn border border-start-0"
                            style="background:#f8fafc; border-color:#e5e7eb !important; border-left:none !important;"
                            onclick="togglePwd()">
                            <i class="bi bi-eye-fill text-muted" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label text-muted" style="font-size:0.85rem;" for="remember">
                            {{ __('auth.remember_me') }}
                        </label>
                    </div>
                    {{-- Quên mật khẩu hiện tại chưa có subdomain route, dùng tạm route chính --}}
                    <a href="{{ route('password.request') }}" class="link-blue" style="font-size:0.85rem;">
                        {{ __('auth.forgot_password') }}
                    </a>
                </div>

                <button type="submit" class="btn-login mb-4">
                    {{ __('auth.login') }}
                </button>

                <div class="text-center" style="font-size:0.9rem;">
                    <span class="text-muted">{{ __('auth.no_account') }}</span>
                    <a href="{{ route('branch.register', ['subdomain' => $subdomain]) }}" class="link-blue ms-1">{{ __('auth.register_now') }}</a>
                </div>

                <div class="text-center mt-4 pt-2 border-top">
                    <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="link-muted">
                        <i class="bi bi-arrow-left me-1"></i>Trở về trang chủ {{ $location->name }}
                    </a>
                </div>
            </form>

        </div>
    </div>

    <script>
    function togglePwd() {
        const pwd = document.getElementById('password');
        const icon = document.getElementById('eye-icon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash-fill text-muted';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye-fill text-muted';
        }
    }
    </script>
</body>
</html>
