<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Liên hệ - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5">
        <div class="d-flex align-items-center mb-5">
            <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0" style="letter-spacing: 3px;">Liên hệ với Holomia</h2>
        </div>

        <div class="row g-4">
            <div class="col-lg-5">
                <div class="profile-content h-100 shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">Thông tin trụ sở</h4>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-geo-alt-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">Địa chỉ chính</h6>
                            <p class="text-muted mb-0">Tầng 6, Viwaseen Tower, 48 Tố Hữu, P. Đại Mỗ, Hà Nội,
                                Việt Nam</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-telephone-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">Hotline hỗ trợ</h6>
                            <p class="text-muted mb-0">024.6666.8888</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-envelope-at-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">Email liên hệ</h6>
                            <p class="text-muted mb-0">info@holomia.com</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-clock-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">Giờ mở cửa</h6>
                            <p class="text-muted mb-0">08:00 - 17:30 (Từ thứ 2 - Thứ 6)</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="profile-content shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">Gửi tin nhắn cho chúng tôi
                    </h4>

                    <form action="{{ route('contact') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">Họ tên của bạn</label>
                                <input type="text" name="name" class="form-control" placeholder="Nhập họ tên..."
                                    required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Địa chỉ Email <span
                                            class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" placeholder="vidu@gmail.com"
                                        required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold">Số điện thoại <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" placeholder="0912..." required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Nội dung chi tiết</label>
                            <textarea name="message" class="form-control" rows="5"
                                placeholder="Bạn cần chúng tôi giúp gì?" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 text-uppercase shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Gửi tin nhắn ngay
                        </button>

                        @if(session('success'))
                            <div class="alert alert-success shadow-lg border-0 rounded-3 mb-4" role="alert"
                                style="background-color: #d1e7dd; color: #0f5132;">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                                    <div>
                                        <h6 class="fw-bold mb-0">Gửi thành công!</h6>
                                        <small>{{ session('success') }}</small>
                                    </div>
                                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger shadow-lg border-0 rounded-3 mb-4" role="alert"
                                style="background-color: #f8d7da; color: #842029;">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                                    <div>
                                        <h6 class="fw-bold mb-0">Gửi thất bại!</h6>
                                        <small>Vui lòng kiểm tra lại các thông tin sau:</small>
                                    </div>
                                </div>
                                <ul class="mb-0 small ps-4">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger shadow-lg border-0 rounded-3 mb-4" role="alert">
                                <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                            </div>
                        @endif

                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="ai-icon" style="position: fixed; bottom: 30px; right: 30px; cursor: pointer; z-index: 9999;">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" width="70" class="shadow-lg rounded-circle">
        <span class="badge rounded-pill bg-info text-dark" style="position: absolute; top: 0; right: 0;">Online</span>
    </div>

    <div id="ai-chat-box" class="bg-white shadow-lg rounded"
        style="display: none; position: fixed; bottom: 110px; right: 30px; width: 350px; height: 450px; z-index: 10000; border: 1px solid #0dcaf0; overflow: hidden;">
        <div class="bg-primary p-3 d-flex justify-content-between align-items-center">
            <strong class="text-white">Trợ lý Holomia AI</strong>
            <span id="close-chat" style="cursor: pointer; color: white; font-weight: bold;">X</span>
        </div>
        <div id="chat-content" class="p-3"
            style="height: 330px; overflow-y: auto; font-size: 14px; background: #f8f9fa;">
            <div class="text-primary mb-2">AI: Chào bạn! Bạn cần hỏi gì về vé VR không?</div>
        </div>
        <div class="p-2 border-top border-light bg-light">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control bg-white text-dark border-1"
                    placeholder="Hỏi AI...">
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
                $('#chat-content').append('<div class="text-white text-end mb-3"><div class="p-2 rounded bg-info text-dark d-inline-block" style="max-width: 80%">' + msg + '</div></div>');
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
    </script>
    <script>
        // 1. Tự động tắt sau 4 giây
        setTimeout(function () {
            let alert = document.querySelector('.alert');
            if (alert) {
                // Hiệu ứng mờ dần
                alert.style.transition = "opacity 0.5s ease";
                alert.style.opacity = "0";

                // Xóa hẳn khỏi giao diện sau khi mờ xong
                setTimeout(() => alert.remove(), 500);
            }
        }, 4000); // 4000ms = 4 giây
    </script>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>