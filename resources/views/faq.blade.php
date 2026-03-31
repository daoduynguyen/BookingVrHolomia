@extends('layouts.app')
@section('title', 'Hướng dẫn & FAQ')
@section('content')
    <div class="container py-5">
        <h1 class="fw-bold mb-5">Câu hỏi thường gặp</h1>

        <div class="accordion" id="faqAccordion">

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#faq1">
                        Ai không nên trải nghiệm VR?
                    </button>
                </h2>
                <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Người bị say tàu xe nặng, tim mạch, động kinh, phụ nữ mang thai,
                        trẻ em dưới 7 tuổi. Nếu có bệnh nền, vui lòng hỏi nhân viên trước khi trải nghiệm.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                        Cần mang theo gì khi đến?
                    </button>
                </h2>
                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Mang theo mã QR vé điện tử (trong email hoặc trang hồ sơ cá nhân).
                        Không cần in vé giấy. Đến đúng giờ đặt để không mất slot.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">
                        Chính sách hoàn tiền như thế nào?
                    </button>
                </h2>
                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Bạn có thể hoàn vé nếu: (1) trong vòng 24h kể từ lúc đặt VÀ
                        (2) còn ít nhất 48h trước giờ chơi. Tiền được hoàn về ví Holomia ngay lập tức.
                    </div>
                </div>
            </div>

            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">
                        Tôi có thể đặt vé cho nhiều người không?
                    </button>
                </h2>
                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Có! Khi đặt vé, bạn chọn số lượng tương ứng. Mỗi đơn có thể đặt nhiều loại vé
                        khác nhau trong cùng một khung giờ (tùy theo số slot còn trống).
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection