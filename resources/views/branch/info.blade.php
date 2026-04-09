@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin - {{ $location->name }}</title>
    <meta name="description" content="Tìm hiểu và liên hệ với {{ $location->name }} - trung tâm VR hàng đầu tại Việt Nam.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        :root { --primary: {{ $location->color ?? '#0d6efd' }}; }
    </style>
</head>

<body class="bg-light text-dark">

    @include('branch.partials.navbar')

    {{-- HERO ABOUT --}}
    <div class="py-5 text-white" style="background: linear-gradient(135deg, #0d1b3e 0%, #0a2a6e 55%, {{ $location->color ?? '#1565c0' }} 100%);">
        <div class="container py-3">
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3" style="width: 50px; height: 2px; background: {{ $location->color ?? '#38bdf8' }};"></div>
                        <span class="fw-bold text-uppercase" style="font-size: 0.85rem; letter-spacing: 3px; color: {{ $location->color ?? '#38bdf8' }};">Giới thiệu</span>
                    </div>
                    <h1 class="display-4 fw-bold mb-3">
                        Holomia <br><span style="color: {{ $location->color ?? '#38bdf8' }};">{{ $location->name }}</span>
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        {{ $location->description ?? 'Trải nghiệm thực tế ảo đỉnh cao tại trung tâm ' . $location->name . '. Chúng tôi mang đến những cuộc phiêu lưu VR không giới hạn.' }}
                    </p>
                    <div class="d-flex gap-3 flex-wrap">
                        <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-primary fw-bold px-4 py-2 rounded-pill">
                            <i class="bi bi-controller me-2"></i>Khám phá trò chơi
                        </a>
                        <a href="#contact-section" class="btn btn-outline-light fw-bold px-4 py-2 rounded-pill">
                            <i class="bi bi-telephone me-2"></i>Liên hệ
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="https://holomia.com/images/contents/17194557751_16576174651-homejpgjpg.jpg"
                        class="img-fluid rounded-4 shadow-lg" alt="{{ $location->name }}" style="max-height: 360px; object-fit: cover; width: 100%;">
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        {{-- THỐNG KÊ --}}
        <div class="row g-4 text-center mb-5">
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">50+</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Trò chơi VR</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">10k+</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Lượt khách</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">4.9/5</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Đánh giá</small>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-4 bg-white rounded-4 shadow-sm border border-light h-100">
                    <h2 class="fw-bold mb-1" style="color: var(--primary);">{{ $location->opening_hours ? '7/7' : '7/7' }}</h2>
                    <small class="text-muted text-uppercase" style="font-size: 0.72rem; letter-spacing: 1px;">Ngày mở cửa</small>
                </div>
            </div>
        </div>

        {{-- TẦM NHÌN & SỨ MỆNH --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-eye fs-2 me-3" style="color: var(--primary);"></i>
                        <h4 class="fw-bold text-dark mb-0">Tầm nhìn</h4>
                    </div>
                    <p class="text-muted mb-0">
                        Trở thành điểm đến VR hàng đầu khu vực, mang lại những trải nghiệm thực tế ảo đỉnh cao và khó quên nhất cho mọi lứa tuổi.
                    </p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-rocket-takeoff fs-2 me-3" style="color: var(--primary);"></i>
                        <h4 class="fw-bold text-dark mb-0">Sứ mệnh</h4>
                    </div>
                    <p class="text-muted mb-0">
                        Cung cấp dịch vụ VR chất lượng cao với thiết bị hiện đại, đội ngũ nhiệt tình và không gian trải nghiệm thoải mái, an toàn cho tất cả khách hàng.
                    </p>
                </div>
            </div>
        </div>

        {{-- CONTACT --}}
        <div class="d-flex align-items-center mb-4 mt-5" id="contact-section">
            <div class="me-3" style="width: 50px; height: 3px; background: var(--primary);"></div>
            <h2 class="fw-bold text-uppercase mb-0" style="letter-spacing: 3px; color: var(--primary);">
                Liên hệ {{ $location->name }}
            </h2>
        </div>

        <div class="row g-4 mb-5">
            {{-- INFO COLUMN --}}
            <div class="col-lg-5">
                <div class="profile-content h-100 shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">
                        Thông tin liên hệ
                    </h4>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-geo-alt-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Địa chỉ</h6>
                            <p class="text-muted mb-0">{{ $location->address ?? 'Đang cập nhật' }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-telephone-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Hotline</h6>
                            <p class="text-muted mb-0">
                                <a href="tel:{{ $location->hotline }}" class="text-muted text-decoration-none">
                                    {{ $location->hotline ?? 'Đang cập nhật' }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-envelope-at-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Email</h6>
                            <p class="text-muted mb-0">{{ $location->email ?? 'info@holomia.com' }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-clock-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Giờ mở cửa</h6>
                            <p class="text-muted mb-0">{{ $location->opening_hours ?? '9:00 - 22:00 hàng ngày' }}</p>
                        </div>
                    </div>

                    @if($location->facebook_url)
                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-facebook fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Facebook</h6>
                            <p class="text-muted mb-0">
                                <a href="{{ $location->facebook_url }}" target="_blank" class="text-muted text-decoration-none">
                                    Trang Facebook của chúng tôi
                                </a>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- FORM COLUMN --}}
            <div class="col-lg-7">
                <div class="profile-content shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">
                        Gửi tin nhắn cho chúng tôi
                    </h4>

                    @if(session('success'))
                        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Gửi thành công!</h6>
                                    <small>{{ session('success') }}</small>
                                </div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                            <ul class="mb-0 small ps-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('branch.contact.send', ['subdomain' => $subdomain]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="0987 654 321" value="{{ old('phone', Auth::check() ? Auth::user()->phone : '') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nội dung tin nhắn <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Nhập nội dung cần hỗ trợ..." required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 text-uppercase shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- AI CHAT WIDGET --}}
    <div id="ai-icon" style="position: fixed; bottom: 30px; right: 30px; cursor: pointer; z-index: 9999;">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" width="70" class="shadow-lg rounded-circle">
        <span class="badge rounded-pill text-dark" style="position: absolute; top: 0; right: 0; background: var(--primary);">Online</span>
    </div>

    <div id="ai-chat-box" class="bg-white shadow-lg rounded"
        style="display: none; position: fixed; bottom: 110px; right: 30px; width: 350px; height: 450px; z-index: 10000; border: 1px solid var(--primary); overflow: hidden;">
        <div class="p-3 d-flex justify-content-between align-items-center text-white" style="background: var(--primary);">
            <strong>Hỗ trợ {{ $location->name }}</strong>
            <span id="close-chat" style="cursor: pointer; font-weight: bold;">✕</span>
        </div>
        <div id="chat-content" class="p-3" style="height: 330px; overflow-y: auto; font-size: 14px; background: #f8f9fa;">
            <div class="mb-2" style="color: var(--primary);">Xin chào! Tôi có thể giúp gì cho bạn tại {{ $location->name }}?</div>
        </div>
        <div class="p-2 border-top border-light bg-light">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control bg-white text-dark border-1" placeholder="Nhập câu hỏi...">
                <button class="btn btn-primary" id="send-btn"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#ai-icon').click(function () { $(this).fadeOut(200); $('#ai-chat-box').fadeIn(300); });
            $('#close-chat').click(function () { $('#ai-chat-box').fadeOut(300); $('#ai-icon').fadeIn(200); });

            function sendMessage() {
                let msg = $('#user-input').val().trim();
                if (!msg) return;
                $('#chat-content').append('<div class="text-end mb-3"><div class="p-2 rounded d-inline-block" style="max-width: 80%; background: var(--primary); color:#fff;">' + msg + '</div></div>');
                $('#user-input').val('');
                $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);

                $.post("{{ route('ai.chat') }}", { _token: "{{ csrf_token() }}", message: msg }, function (data) {
                    $('#chat-content').append('<div class="mb-3 text-start"><span class="badge bg-secondary mb-1">AI</span><br><div class="p-2 rounded bg-secondary text-white d-inline-block" style="max-width: 80%">' + data.reply + '</div></div>');
                    $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
                });
            }
            $('#send-btn').click(sendMessage);
            $('#user-input').keypress(function (e) { if (e.which == 13) sendMessage(); });
        });

        setTimeout(function () {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);
    </script>

    @include('branch.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
