<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>404 - Không tìm thấy trang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="text-center p-5">
        <i class="bi bi-map text-primary" style="font-size: 80px;"></i>
        <h1 class="display-1 fw-bold text-primary mt-3">404</h1>
        <h4 class="fw-bold mb-3">Trang không tồn tại</h4>
        <p class="text-muted mb-4">Trang bạn tìm kiếm đã bị xóa, đổi tên<br>hoặc chưa bao giờ tồn tại.</p>
        <a href="{{ url('/') }}" class="btn btn-primary px-5 py-2 fw-bold rounded-pill">
            <i class="bi bi-house-fill me-2"></i>Về trang chủ
        </a>
    </div>
</body>
</html>