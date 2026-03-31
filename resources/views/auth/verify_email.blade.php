@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận Email – Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0d1b4b 0%, #0a2a6e 40%, #0055c4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .verify-card {
            background: #fff;
            border-radius: 20px;
            padding: 48px 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .3);
            text-align: center;
        }

        .brand {
            font-family: 'Orbitron', monospace;
            font-size: 1.6rem;
            font-weight: 900;
            color: #0d1b4b;
            letter-spacing: 2px;
            margin-bottom: 24px;
        }

        .brand span {
            color: #00c8ff;
        }

        .icon-wrap {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #eff6ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            font-size: 2.2rem;
            color: #0055c4;
        }
    </style>
</head>

<body>
    <div class="verify-card">
        <div class="brand">HOLOMIA <span>VR</span></div>

        <div class="icon-wrap">
            <i class="bi bi-envelope-check-fill"></i>
        </div>

        <h4 class="fw-bold mb-2">Kiểm tra hộp thư của bạn</h4>

        <p class="text-muted mb-4" style="font-size:.95rem; line-height:1.6;">
            Chúng tôi đã gửi email xác nhận đến<br>
            <strong>{{ auth()->user()->email }}</strong>.<br>
            Bấm vào link trong email để kích hoạt tài khoản.
        </p>

        @if(session('success'))
            <div class="alert alert-success text-start" style="font-size:.88rem;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        <p class="text-muted mb-3" style="font-size:.82rem;">
            Không thấy email? Kiểm tra thư mục <strong>Spam</strong>.
        </p>

        {{-- Gửi lại --}}
        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
            @csrf
            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 rounded-pill">
                <i class="bi bi-arrow-clockwise me-2"></i>Gửi lại email xác nhận
            </button>
        </form>

        {{-- Đăng xuất --}}
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            class="text-muted small text-decoration-none">
            <i class="bi bi-box-arrow-left me-1"></i>Đăng xuất
        </a>
    </div>
</body>

</html>