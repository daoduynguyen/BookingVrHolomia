@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('auth.register_title') }}</title>
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

        .vr-icon { width: 180px; height: 180px; margin: 0 auto 40px; }
        .vr-icon svg { width: 100%; height: 100%; filter: drop-shadow(0 0 20px rgba(0,180,255,0.5)); }

        .steps-list {
            list-style: none;
            text-align: left;
            width: 100%;
            max-width: 320px;
        }

        .steps-list li {
            color: rgba(255,255,255,0.75);
            font-size: 0.88rem;
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .steps-list li:last-child { border-bottom: none; }

        .step-num {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(0,200,255,0.2);
            border: 1px solid #00c8ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            font-weight: 600;
            color: #00c8ff;
            flex-shrink: 0;
        }

        /* RIGHT PANEL */
        .right-panel {
            width: 500px;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 50px 50px;
            overflow-y: auto;
        }

        .form-wrapper { width: 100%; max-width: 400px; }

        .form-header { margin-bottom: 28px; }

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

        .form-control:focus { border-color: #0055c4; background: #fff; box-shadow: none; }

        .input-group:focus-within .input-group-text,
        .input-group:focus-within .form-control {
            border-color: #0055c4;
            background: #fff;
        }

        .btn-register {
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

        .btn-register:hover {
            background: linear-gradient(135deg, #003da0, #0060d4);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(0,85,196,0.35);
            color: #fff;
        }

        .link-blue { color: #0055c4; font-weight: 600; text-decoration: none; }
        .link-blue:hover { color: #003da0; text-decoration: underline; }

        .link-muted { color: #6b7280; font-size: 0.85rem; text-decoration: none; }
        .link-muted:hover { color: #374151; }

        .pwd-strength { height: 4px; border-radius: 2px; margin-top: 6px; transition: all 0.3s; }

        @media (max-width: 768px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; padding: 40px 24px; }
        }
    </style>
</head>
<body>

    {{-- LEFT PANEL --}}
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

            <ul class="steps-list">
                <li><div class="step-num">1</div><span>{{ __('auth.feature_1') }}</span></li>
                <li><div class="step-num">2</div><span>{{ __('auth.feature_2') }}</span></li>
                <li><div class="step-num">3</div><span>{{ __('auth.feature_3') }}</span></li>
                <li><div class="step-num">4</div><span>{{ __('auth.feature_4') }}</span></li>
            </ul>
        </div>
    </div>

    {{-- RIGHT PANEL: FORM --}}
    <div class="right-panel">
        <div class="form-wrapper">

            <div class="form-header">
                <h2>{{ __('auth.create_account') }}</h2>
                <p>{{ __('auth.register_subheading') }}</p>
            </div>

            <form action="{{ route('register') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">{{ __('auth.full_name') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="{{ __('auth.name_placeholder') }}" required value="{{ old('name') }}" autofocus>
                    </div>
                    @error('name')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('auth.email') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            placeholder="email@example.com" required value="{{ old('email') }}">
                    </div>
                    @error('email')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('auth.phone') }}</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-phone-fill"></i></span>
                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            placeholder="0912..." value="{{ old('phone') }}">
                    </div>
                    @error('phone')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">{{ __('auth.password') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="{{ __('auth.password_min') }}" required
                            oninput="checkStrength(this.value)">
                        <button type="button" class="btn border"
                            style="background:#f8fafc; border-color:#e5e7eb !important; border-left:none !important;"
                            onclick="togglePwd('password','eye1')">
                            <i class="bi bi-eye-fill text-muted" id="eye1"></i>
                        </button>
                    </div>
                    <div id="strength-bar" class="pwd-strength bg-secondary mt-1" style="width:0%;"></div>
                    <small id="strength-text" class="text-muted d-block mt-1" style="font-size:0.78rem;"></small>
                    @error('password')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">{{ __('auth.password_confirm') }} <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-shield-lock-fill"></i></span>
                        <input type="password" name="password_confirmation" id="password2"
                            class="form-control" placeholder="{{ __('auth.password_confirm_placeholder') }}" required>
                        <button type="button" class="btn border"
                            style="background:#f8fafc; border-color:#e5e7eb !important; border-left:none !important;"
                            onclick="togglePwd('password2','eye2')">
                            <i class="bi bi-eye-fill text-muted" id="eye2"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-register mb-4">
                    <i class="bi bi-person-plus-fill me-2"></i>{{ __('auth.register_btn') }}
                </button>

                <div class="text-center" style="font-size:0.9rem;">
                    <span class="text-muted">{{ __('auth.have_account') }}</span>
                    <a href="{{ route('login') }}" class="link-blue ms-1">{{ __('auth.login_now') }}</a>
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
    // Strength labels from PHP (passed via meta or inline — we use JS object)
    const strengthLabels = {
        0: '{{ __('auth.strength_very_weak') }}',
        1: '{{ __('auth.strength_very_weak') }}',
        2: '{{ __('auth.strength_weak') }}',
        3: '{{ __('auth.strength_medium') }}',
        4: '{{ __('auth.strength_strong') }}',
        5: '{{ __('auth.strength_very_strong') }}',
    };
    const strengthPrefix = '{{ __('auth.strength_label') }}';

    function togglePwd(fieldId, iconId) {
        const pwd = document.getElementById(fieldId);
        const icon = document.getElementById(iconId);
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.className = 'bi bi-eye-slash-fill text-muted';
        } else {
            pwd.type = 'password';
            icon.className = 'bi bi-eye-fill text-muted';
        }
    }

    function checkStrength(val) {
        const bar = document.getElementById('strength-bar');
        const txt = document.getElementById('strength-text');
        if (!val) { bar.style.width = '0%'; txt.textContent = ''; return; }

        let score = 0;
        if (val.length >= 6)  score++;
        if (val.length >= 10) score++;
        if (/[A-Z]/.test(val)) score++;
        if (/[0-9]/.test(val)) score++;
        if (/[^A-Za-z0-9]/.test(val)) score++;

        const levels = [
            { w: '20%', cls: 'bg-danger',  color: '#dc2626' },
            { w: '40%', cls: 'bg-warning', color: '#d97706' },
            { w: '60%', cls: 'bg-info',    color: '#0891b2' },
            { w: '80%', cls: 'bg-primary', color: '#0055c4' },
            { w: '100%',cls: 'bg-success', color: '#16a34a' },
        ];
        const l = levels[Math.min(score - 1, 4)] || levels[0];
        bar.style.width = l.w;
        bar.className = 'pwd-strength ' + l.cls;
        txt.textContent = strengthPrefix + ' ' + (strengthLabels[score] || strengthLabels[1]);
        txt.style.color = l.color;
    }
    </script>
</body>
</html>
