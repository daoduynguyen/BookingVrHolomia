<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - Holomia VR</title>
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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            padding: 40px;
            width: 100%; max-width: 450px;
            color: #1f2937;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .form-control {
            background: white;
            border: 1px solid #e5e7eb;
            color: #1f2937;
        }
        .form-control:focus {
            background: white;
            color: #1f2937;
            box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25); border-color: #0dcaf0;
        }
    </style>
</head>
<body>
    <div class="glass-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary text-uppercase">Đăng nhập</h2>
            <p class="text-muted small">Chào mừng trở lại Holomia VR</p>
        </div>

        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            @if($errors->any())
                <div class="alert alert-danger py-2 small bg-danger bg-opacity-10 text-danger border-danger border-opacity-25">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Email hoặc mật khẩu chưa đúng.
                </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-light text-muted"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required value="{{ old('email') }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-light text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input bg-white border-light" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label text-muted small" for="remember">Ghi nhớ tôi</label>
                </div>
                <a href="#" class="text-primary small text-decoration-none">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold text-uppercase py-2 mb-3 shadow-sm">Đăng nhập</button>
            
            <div class="text-center">
                <span class="text-muted">Chưa có tài khoản?</span>
                <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-bold ms-1">Đăng ký ngay</a>
            </div>

            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Về trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>