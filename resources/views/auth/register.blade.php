<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - Holomia VR</title>
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
            width: 100%; max-width: 500px;
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
    <div class="glass-card animate-bounce">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary text-uppercase">Đăng ký</h2>
            <p class="text-muted small">Tạo tài khoản để đặt vé nhanh hơn</p>
        </div>

        <form action="{{ route('register') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label class="form-label fw-bold">Họ và tên</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-light text-muted"><i class="bi bi-person-fill"></i></span>
                    <input type="text" name="name" class="form-control" placeholder="Nhập tên của bạn" required value="{{ old('name') }}">
                </div>
                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-light text-muted"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="email@example.com" required value="{{ old('email') }}">
                </div>
                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-light text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Tối thiểu 6 ký tự" required>
                </div>
                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Nhập lại mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-light text-muted"><i class="bi bi-shield-lock-fill"></i></span>
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Xác nhận mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold text-uppercase py-2 mb-3 shadow-sm">Đăng ký ngay</button>
            
            <div class="text-center">
                <span class="text-muted">Đã có tài khoản?</span>
                <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-bold ms-1">Đăng nhập</a>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('home') }}" class="text-muted small text-decoration-none"><i class="bi bi-arrow-left"></i> Về trang chủ</a>
            </div>
        </form>
    </div>
</body>
</html>