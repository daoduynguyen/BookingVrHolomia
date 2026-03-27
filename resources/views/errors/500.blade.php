<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>500 - Lỗi hệ thống</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center p-5">
        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 80px;"></i>
        <h1 class="display-1 fw-bold text-danger mt-3">500</h1>
        <h4 class="fw-bold mb-3">Lỗi hệ thống</h4>
        <p class="text-muted mb-4">Máy chủ gặp sự cố. Chúng tôi đang<br>xử lý và sẽ sớm khắc phục.</p>
        <a href="{{ url('/') }}" class="btn btn-danger px-5 py-2 fw-bold rounded-pill">
            <i class="bi bi-house-fill me-2"></i>Về trang chủ
        </a>
    </div>
</body>
</html>