<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt lại mật khẩu - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: url('https://images.unsplash.com/photo-1622979135228-5b7964b4f53f?q=80&w=2000&auto=format&fit=crop') no-repeat center center fixed;
            background-size: cover;
            height: 100vh;
            display: flex; align-items: center; justify-content: center;
        }
        .glass-card {
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%; max-width: 450px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock-fill text-primary" style="font-size: 48px;"></i>
            <h2 class="fw-bold text-primary mt-2">Đặt lại mật khẩu</h2>
        </div>

        @if($errors->any())
            <div class="alert alert-danger py-2 small">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-3">
                <label class="form-label fw-bold">Mật khẩu mới</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Nhập lại mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock-fill text-muted"></i></span>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2 mb-3">
                <i class="bi bi-check-circle-fill me-2"></i>Xác nhận đặt lại
            </button>
            <div class="text-center">
                <a href="{{ route('login') }}" class="text-muted small text-decoration-none">
                    <i class="bi bi-arrow-left"></i> Quay lại đăng nhập
                </a>
            </div>
        </form>
    </div>
</body>
</html>