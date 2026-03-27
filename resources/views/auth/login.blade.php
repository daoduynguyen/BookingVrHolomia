@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.login_title') }}</title>
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
            background: linear-gradient(135deg, #0d1b4b 0%, #0a2a6e 40%, #0055c4 100%);
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
            background: radial-gradient(circle, rgba(0,120,255,0.25) 0%, transparent 70%);
            top: -100px; right: -100px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,200,255,0.15) 0%, transparent 70%);
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
            text-shadow: 0 0 30px rgba(0,150,255,0.6);
            margin-bottom: 8px;
        }

        .brand-logo span { color: #00c8ff; }

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

        .vr-icon svg { width: 100%; height: 100%; filter: drop-shadow(0 0 20px rgba(0,180,255,0.5)); }

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
        .feature-list li i { color: #00c8ff; font-size: 1.1rem; flex-shrink: 0; }

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

        .form-control:focus { border-color: #0055c4; background: #fff; box-shadow: none; outline: none; }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #0055c4;
            background: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, #0055c4, #0078ff);
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
            background: linear-gradient(135deg, #003da0, #0060d4);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,85,196,0.35);
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

        .link-blue { color: #0055c4; font-weight: 600; text-decoration: none; }
        .link-blue:hover { color: #003da0; text-decoration: underline; }

        .link-muted { color: #6b7280; font-size: 0.85rem; text-decoration: none; }
        .link-muted:hover { color: #374151; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 24px; }
        }
    </style>
</head>
<body>

    {{-- LEFT PANEL: BRAND --}}
    <div class="left-panel">
        <div class="grid-bg"></div>
        <div class="brand-content">
            <div class="brand-logo">HOLOMIA <span>VR</span></div>
            <div class="brand-tagline">{{ __('auth.tagline') }}</div>

            <div class="vr-icon">
                <svg viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="95" stroke="rgba(0,200,255,0.2)" stroke-width="1"/>
                    <circle cx="100" cy="100" r="70" stroke="rgba(0,200,255,0.15)" stroke-width="1"/>
                    <rect x="30" y="70" width="140" height="75" rx="20" fill="rgba(0,100,200,0.4)" stroke="#00c8ff" stroke-width="1.5"/>
                    <circle cx="70" cy="107" r="22" fill="rgba(0,30,80,0.9)" stroke="#00c8ff" stroke-width="1.5"/>
                    <circle cx="130" cy="107" r="22" fill="rgba(0,30,80,0.9)" stroke="#00c8ff" stroke-width="1.5"/>
                    <circle cx="63" cy="100" r="6" fill="rgba(0,200,255,0.3)"/>
                    <circle cx="123" cy="100" r="6" fill="rgba(0,200,255,0.3)"/>
                    <rect x="89" y="102" width="22" height="10" rx="5" fill="rgba(0,100,200,0.6)" stroke="#00c8ff" stroke-width="1"/>
                    <path d="M30 85 Q100 55 170 85" stroke="#00c8ff" stroke-width="1.5" fill="none" stroke-dasharray="4 3"/>
                    <circle cx="100" cy="40" r="3" fill="#00c8ff" opacity="0.8"/>
                    <circle cx="140" cy="55" r="2" fill="#00c8ff" opacity="0.5"/>
                    <circle cx="60" cy="55" r="2" fill="#00c8ff" opacity="0.5"/>
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

            <div class="form-header">
                <h2>{{ __('auth.login_heading') }}</h2>
                <p>{{ __('auth.login_subheading') }}</p>
            </div>

            @if($errors->any())
                <div class="alert-custom mb-4">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ __('auth.login_error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success py-2 small mb-4 rounded-3">
                    <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('auth.email') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control"
                            placeholder="email@example.com" required value="{{ old('email') }}" autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('auth.password') }}</label>
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
                    <a href="{{ route('password.request') }}" class="link-blue" style="font-size:0.85rem;">
                        {{ __('auth.forgot_password') }}
                    </a>
                </div>

                <button type="submit" class="btn-login mb-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('auth.login') }}
                </button>

                <div class="divider">{{ __('auth.or') }}</div>

                <div class="text-center" style="font-size:0.9rem;">
                    <span class="text-muted">{{ __('auth.no_account') }}</span>
                    <a href="{{ route('register') }}" class="link-blue ms-1">{{ __('auth.register_now') }}</a>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('home') }}" class="link-muted">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('auth.back_to_home') }}
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
