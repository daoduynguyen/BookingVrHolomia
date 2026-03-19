<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Giới thiệu - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

</head>
</head>

<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="https://holomia.com/images/contents/17194557751_16576174651-homejpgjpg.jpg"
                    class="img-fluid rounded shadow" alt="Holomia Team">
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info me-3" style="width: 50px; height: 2px;"></div>
                    <h4 class="text-info fw-bold text-uppercase letter-spacing-2 mb-0"
                        style="font-size: 1.25rem; letter-spacing: 3px;">
                        Về chúng tôi
                    </h4>
                </div>
                <h1 class="display-4 fw-bold mb-4">HOLOMIA VR <br><span class="text-primary">BEYOND REALITY</span></h1>
                <p class="lead text-muted mb-4">
                    Được thành lập vào năm 2024, Holomia tự hào là hệ thống khu vui chơi thực tế ảo (VR) tiên phong,
                    mang đến cuộc cách mạng trong lĩnh vực giải trí kỹ thuật số tại Việt Nam.
                </p>
                <p class="text-dark opacity-75 mb-4">
                    Tại Holomia, chúng tôi không chỉ cung cấp trò chơi; chúng tôi kiến tạo những thế giới song song. Với
                    sự kết hợp hoàn hảo giữa thiết bị VR tiên tiến nhất (Kính Oculus Quest 3, Vive Pro 2) và nội dung
                    độc quyền, mỗi bước chân của bạn tại đây là một hành trình khám phá những giới hạn mới của trí tưởng
                    tượng.
                </p>
                <div class="d-flex gap-3">
                    <div class="text-center p-3 border border-light rounded-3 bg-white shadow-sm">
                        <h3 class="text-primary fw-bold mb-0">50+</h3>
                        <small class="text-uppercase text-muted" style="font-size: 0.7rem;">Trò chơi</small>
                    </div>
                    <div class="text-center p-3 border border-light rounded-3 bg-white shadow-sm">
                        <h3 class="text-primary fw-bold mb-0">10k+</h3>
                        <small class="text-uppercase text-muted" style="font-size: 0.7rem;">Khách hàng</small>
                    </div>
                    <div class="text-center p-3 border border-light rounded-3 bg-white shadow-sm">
                        <h3 class="text-primary fw-bold mb-0">4.9/5</h3>
                        <small class="text-uppercase text-muted" style="font-size: 0.7rem;">Đánh giá</small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container py-5"> 
        <div class="row g-4"> {{-- Thêm g-4 để tạo khoảng cách giữa 2 khối --}}

            {{-- Khối Tầm nhìn --}}
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-eye fs-2 text-primary me-3"></i>
                        <h4 class="fw-bold text-dark mb-0">TẦM NHÌN</h4>
                    </div>
                    <p class="text-muted">
                        Trở thành chuỗi hệ thống giải trí công nghệ cao và thực tế ảo số 1 khu vực Đông Nam Á vào năm
                        2030.
                        Chúng tôi hướng tới việc đưa công nghệ thực tế ảo vào đời sống, từ giải trí, giáo dục cho đến
                        đào tạo chuyên sâu.
                    </p>
                </div>
            </div>

            {{-- Khối Sứ mệnh --}}
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-rocket-takeoff fs-2 text-primary me-3"></i>
                        <h4 class="fw-bold text-dark mb-0">SỨ MỆNH</h4>
                    </div>
                    <p class="text-muted">
                        Mang đến cho khách hàng Việt Nam những trải nghiệm quốc tế với giá thành hợp lý nhất.
                        Holomia cam kết cập nhật những công nghệ VR/AR mới nhất thế giới mỗi tháng để phục vụ người
                        chơi.
                    </p>
                </div>
            </div>

        </div>
    </div>
</body>

</html>