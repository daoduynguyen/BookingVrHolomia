@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hướng dẫn & FAQ - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        .accordion-button:not(.collapsed) { background-color: rgba(13,110,253,0.08); color: #0d6efd; font-weight: 600; }
        .accordion-button:focus { box-shadow: none; }
        .accordion-item { border: 1px solid #e9ecef; border-radius: 10px !important; overflow: hidden; margin-bottom: 0.75rem; }
        .accordion-button { border-radius: 10px !important; font-weight: 500; }
    </style>
</head>
<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5" style="max-width: 860px;">

        <div class="d-flex align-items-center mb-5">
            <div class="bg-primary me-3" style="width:50px; height:3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0" style="letter-spacing:3px;">Câu hỏi thường gặp</h2>
        </div>

        <div class="accordion" id="faqAccordion">

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        <i class="bi bi-person-fill-exclamation text-warning me-2"></i>
                        Ai không nên trải nghiệm VR?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Người bị say tàu xe nặng, tim mạch, động kinh, phụ nữ mang thai,
                        trẻ em dưới 7 tuổi. Nếu có bệnh nền, vui lòng hỏi nhân viên trước khi trải nghiệm.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                        <i class="bi bi-bag-check-fill text-info me-2"></i>
                        Cần mang theo gì khi đến?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Mang theo mã QR vé điện tử (trong email hoặc trang hồ sơ cá nhân).
                        Không cần in vé giấy. Đến đúng giờ đặt để không mất slot.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">
                        <i class="bi bi-arrow-counterclockwise text-success me-2"></i>
                        Chính sách hoàn tiền như thế nào?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Bạn có thể hoàn vé nếu: (1) trong vòng 24h kể từ lúc đặt VÀ
                        (2) còn ít nhất 48h trước giờ chơi. Tiền được hoàn về ví Holomia ngay lập tức.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">
                        <i class="bi bi-people-fill text-primary me-2"></i>
                        Tôi có thể đặt vé cho nhiều người không?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Có! Khi đặt vé, bạn chọn số lượng tương ứng. Mỗi đơn có thể đặt nhiều loại vé
                        khác nhau trong cùng một khung giờ (tùy theo số slot còn trống).
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq5">
                        <i class="bi bi-ticket-perforated-fill text-danger me-2"></i>
                        Voucher và mã giảm giá áp dụng thế nào?
                    </button>
                </h2>
                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Nhập mã voucher tại bước thanh toán trong giỏ hàng. Mỗi đơn hàng chỉ áp dụng một mã.
                        Xem danh sách voucher đang có trong mục <strong>Kho Voucher</strong> trên trang hồ sơ cá nhân.
                    </div>
                </div>
            </div>

            <div class="accordion-item shadow-sm">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq6">
                        <i class="bi bi-wallet2 text-warning me-2"></i>
                        Ví Holomia là gì? Nạp tiền bằng cách nào?
                    </button>
                </h2>
                <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body text-muted">
                        Ví Holomia là ví điện tử nội bộ để thanh toán nhanh. Bạn có thể nạp tiền vào ví
                        qua mục <strong>Nạp tiền</strong> trong trang hồ sơ. Số dư ví cũng sẽ được cộng
                        khi bạn hoàn vé thành công.
                    </div>
                </div>
            </div>

        </div>

        <div class="mt-5 p-4 rounded-4 text-center" style="background: linear-gradient(135deg,#eff6ff,#dbeafe);">
            <p class="text-primary fw-semibold mb-2">Không tìm thấy câu trả lời bạn cần?</p>
            <a href="{{ route('contact') }}" class="btn btn-primary rounded-pill fw-bold px-5 py-2 shadow-sm">
                <i class="bi bi-headset me-2"></i>Liên hệ hỗ trợ
            </a>
        </div>
    </div>

    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>