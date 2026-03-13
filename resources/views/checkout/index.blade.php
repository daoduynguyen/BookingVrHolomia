<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin đặt vé - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-dark text-white">
    @include('partials.navbar')

    <div class="container py-5">
        <h2 class="text-info fw-bold mb-4 text-uppercase"><i class="bi bi-person-lines-fill"></i> Thông tin người đặt
        </h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form action="{{ route('booking.confirm') }}" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket_id ?? '' }}">
            <div id="price-data" data-week="{{ $totalWeek ?? 0 }}" data-weekend="{{ $totalWeekend ?? 0 }}"
                style="display: none;"></div>

            <div class="row g-4">
                {{-- CỘT TRÁI: ĐIỀN THÔNG TIN --}}
                <div class="col-lg-7">
                    <div class="card bg-secondary text-white border-0 rounded-4 p-4 mb-4 shadow">
                        <h5 class="fw-bold mb-3 border-bottom border-dark pb-2">Thông tin cá nhân</h5>
                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="customer_name" class="form-control"
                                value="{{ Auth::check() ? Auth::user()->name : '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="customer_phone" class="form-control"
                                value="{{ Auth::check() ? Auth::user()->phone : '' }}" required>
                        </div>

                        {{-- CHỌN NGÀY VÀ GIỜ --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-info"><i class="bi bi-calendar-check me-1"></i>
                                    Ngày chơi</label>
                                <input type="date" name="booking_date" id="booking_date" class="form-control" required>
                                <div id="date-message" class="mt-2 small text-secondary">Vui lòng chọn ngày...</div>
                            </div>

                            {{-- BỔ SUNG KHUNG GIỜ Ở ĐÂY --}}
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning"><i class="bi bi-clock-history me-1"></i>
                                    Khung giờ</label>
                                <select name="slot_id" id="slot_id"
                                    class="form-select bg-dark text-white border-secondary" required>
                                    <option value="">-- Hãy chọn ngày trước --</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-info"><i class="bi bi-people-fill me-1"></i> Số lượng
                                vé</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="50"
                                class="form-control bg-dark text-white border-secondary" style="width: 120px;">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng (Không bắt buộc)</label>
                            <textarea name="note" class="form-control" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="card bg-secondary text-white border-0 rounded-4 p-4 shadow">
                        <h5 class="fw-bold mb-3 border-bottom border-dark pb-2">Phương thức thanh toán</h5>
                        <div class="form-check mb-3 p-3 border border-dark rounded bg-dark bg-opacity-25">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod"
                                checked>
                            <label class="form-check-label d-flex align-items-center gap-2" for="cod">
                                <i class="bi bi-cash-coin text-success fs-4"></i>
                                <div><strong>Thanh toán tại quầy</strong></div>
                            </label>
                        </div>
                        <div class="form-check p-3 border border-dark rounded bg-dark bg-opacity-25">
                            <input class="form-check-input" type="radio" name="payment_method" id="banking"
                                value="banking">
                            <label class="form-check-label d-flex align-items-center gap-2" for="banking">
                                <i class="bi bi-qr-code-scan text-info fs-4"></i>
                                <div><strong>Chuyển khoản / Mã QR</strong></div>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- CỘT PHẢI: TÓM TẮT TIỀN GỐC --}}
                {{-- CỘT PHẢI: TÓM TẮT TIỀN GỐC --}}
                <div class="col-lg-5">
                    <div class="card bg-black border border-secondary rounded-4 p-4 shadow-lg sticky-top"
                        style="top: 100px;">
                        <h5 class="fw-bold mb-4 text-center text-white border-bottom border-secondary pb-3">TÓM TẮT VÉ
                        </h5>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img src="{{ Str::startsWith($ticket->image ?? $ticket->image_url, 'http') ? ($ticket->image ?? $ticket->image_url) : asset('storage/' . ($ticket->image ?? $ticket->image_url)) }}"
                                class="rounded" width="70" height="70" style="object-fit: cover;">
                            <div>
                                <h5 class="mb-1 text-white fw-bold">{{ $ticket->name }}</h5>
                                <span class="badge bg-secondary" id="display-qty">Số lượng: 1</span>
                            </div>
                        </div>

                        {{-- Tạm tính (Tiền gốc * Số lượng) --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-white-50">Tạm tính (<span id="calc-qty">1</span> vé):</span>
                            {{-- CHÚ Ý: Đã thêm data-unit-price để JS lấy giá trị thực tế tính toán --}}
                            <span class="fs-5 fw-bold text-light" id="temp-total"
                                data-unit-price="{{ $totalWeek }}">{{ number_format($totalWeek) }}đ</span>
                        </div>

                        {{-- Dòng hiển thị số tiền được giảm (Mặc định ẩn, chỉ hiện khi nhập đúng mã) --}}
                        <div class="d-flex justify-content-between align-items-center mb-3 d-none" id="discount-row">
                            <span class="text-success">Giảm giá <span id="discount-label"
                                    class="fw-bold"></span>:</span>
                            <span class="fs-5 fw-bold text-success" id="discount-amount">-0đ</span>
                        </div>

                        {{-- Tổng tiền thanh toán cuối cùng --}}
                        <div
                            class="d-flex justify-content-between align-items-center border-top border-secondary pt-3 mb-4">
                            <span class="text-white fw-bold fs-5">TỔNG TIỀN:</span>
                            <span class="fs-3 fw-bold text-info"
                                id="final-total">{{ number_format($totalWeek) }}đ</span>
                        </div>

                        {{-- KHU VỰC NHẬP MÃ GIẢM GIÁ (Đã thay thế cho thẻ alert cũ) --}}
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" id="coupon_code"
                                    class="form-control bg-dark text-white border-secondary"
                                    placeholder="Nhập mã giảm giá..." style="box-shadow: none;">
                                <button class="btn btn-outline-info fw-bold" type="button" id="btn_apply_coupon">ÁP
                                    DỤNG</button>
                            </div>
                            <small id="coupon_message" class="mt-2 d-block"></small>
                        </div>

                        <button type="submit"
                            class="btn btn-info w-100 py-3 fw-bold text-uppercase rounded-pill shadow">
                            Tiếp tục đến Giỏ Hàng <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                        </button>

                        {{-- Thẻ ẩn để gửi mã giảm giá đã áp dụng lên server khi Submit form --}}
                        <input type="hidden" name="applied_coupon" id="hidden_coupon_code">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

            // Chặn ngày quá khứ
            var today = new Date().toISOString().split('T')[0];
            $('#booking_date').attr('min', today);

            // ==========================================
            // HÀM 1: CẬP NHẬT GIAO DIỆN & TÍNH TIỀN GỐC
            // ==========================================
            function updateSummary() {
                var dateVal = $('#booking_date').val();
                var qty = parseInt($('#quantity').val()) || 1;
                
                // Cập nhật text hiển thị số lượng
                $('#display-qty').text('Số lượng: ' + qty);
                $('#calc-qty').text(qty);

                var weekPrice = parseFloat($('#price-data').data('week'));
                var weekendPrice = parseFloat($('#price-data').data('weekend'));
                var unitPrice = weekPrice; // Mặc định là giá ngày thường

                // Nếu đã chọn ngày, kiểm tra xem có phải cuối tuần không
                if (dateVal) {
                    var dayOfWeek = new Date(dateVal).getUTCDay();
                    var isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                    unitPrice = isWeekend ? weekendPrice : weekPrice;
                    
                    $('#date-message').html(isWeekend ? '<span class="text-warning fw-bold">Giá CUỐI TUẦN</span>' : '<span class="text-success fw-bold">Giá NGÀY THƯỜNG</span>');
                }

                // Tính TỔNG TẠM TÍNH (Chưa giảm giá)
                var total = unitPrice * qty;
                $('#temp-total').text(formatMoney(total));

                // Xử lý khóa phương thức thanh toán
                if (qty >= 4) {
                    $('#cod').prop('disabled', true);
                    $('#banking').prop('checked', true);
                    if ($('#cod-warning').length === 0) {
                        $('#cod').parent().append('<div id="cod-warning" class="text-warning small mt-1">Nhóm >= 4 người vui lòng chuyển khoản!</div>');
                    }
                } else {
                    $('#cod').prop('disabled', false);
                    $('#cod-warning').remove();
                }

                // KIỂM TRA MÃ GIẢM GIÁ
                let appliedCode = $('#hidden_coupon_code').val();
                if (appliedCode) {
                    // Nếu đang có mã, gọi hàm API để tính lại tiền giảm dựa trên Tổng tiền mới
                    applyCoupon(appliedCode, total);
                } else {
                    // Nếu không có mã, Tổng cuối = Tạm tính
                    $('#discount-row').addClass('d-none');
                    $('#final-total').text(formatMoney(total));
                }
            }

            // ==========================================
            // HÀM 2: GỌI API KIỂM TRA & TÍNH MÃ GIẢM GIÁ
            // ==========================================
            function applyCoupon(code, totalAmount) {
    $.ajax({
        // 1. Sử dụng hàm route của Laravel để tự động điền đúng đường dẫn
        url: '{{ route("check.coupon") }}', 
        method: 'POST',
        data: {
            // 2. Phải có token này để Laravel không chặn yêu cầu (Lỗi 419)
            _token: '{{ csrf_token() }}', 
            code: code,
            total_amount: totalAmount
        },
        success: function(response) {
            if (response.status === 'success') {
                $('#hidden_coupon_code').val(code);
                $('#coupon_message').removeClass('text-danger').addClass('text-success')
                                     .html('<i class="bi bi-check-circle-fill"></i> ' + response.message);
                
                $('#discount-row').removeClass('d-none');
                $('#discount-amount').text('-' + formatMoney(response.discount));
                $('#final-total').text(formatMoney(response.new_total));
            } else {
                $('#hidden_coupon_code').val('');
                $('#coupon_message').removeClass('text-success').addClass('text-danger')
                                     .html('<i class="bi bi-x-circle-fill"></i> ' + response.message);
                $('#discount-row').addClass('d-none');
                $('#final-total').text(formatMoney(totalAmount));
            }
        },
        error: function(xhr) {
            // 3. Log lỗi ra console để bạn dễ debug nếu vẫn lỗi
            console.error(xhr.responseText);
            alert('Lỗi kết nối máy chủ, vui lòng kiểm tra lại Route trong web.php');
        }
    });
}

            // LẮNG NGHE SỰ KIỆN TĂNG/GIẢM SỐ LƯỢNG
            $('#quantity').on('input change', updateSummary);

            // BẮT SỰ KIỆN CLICK NÚT "ÁP DỤNG"
            $('#btn_apply_coupon').click(function() {
                let code = $('#coupon_code').val().trim();
                if(code === '') {
                    $('#hidden_coupon_code').val('');
                    $('#coupon_message').text('');
                    updateSummary(); // Xóa mã thì tính lại tiền gốc
                    return;
                }

                // Lấy giá trị Tạm tính hiện tại để gửi lên server
                var dateVal = $('#booking_date').val();
                var qty = parseInt($('#quantity').val()) || 1;
                var weekPrice = parseFloat($('#price-data').data('week'));
                var weekendPrice = parseFloat($('#price-data').data('weekend'));
                var unitPrice = weekPrice;
                
                if (dateVal) {
                    var dayOfWeek = new Date(dateVal).getUTCDay();
                    unitPrice = (dayOfWeek === 0 || dayOfWeek === 6) ? weekendPrice : weekPrice;
                }
                var subTotal = unitPrice * qty;

                applyCoupon(code, subTotal);
            });

            // ==========================================
            // HÀM 3: AJAX CHỌN NGÀY -> LOAD KHUNG GIỜ
            // ==========================================
            $('#booking_date').on('change', function () {
                updateSummary(); // Gọi hàm tính tiền lại (vì giá có thể đổi do khác ngày cuối tuần)
                var date = $(this).val();
                var ticket_id = $('input[name="ticket_id"]').val();
                var slotSelect = $('#slot_id');

                slotSelect.html('<option value="">Đang tải khung giờ...</option>');

                $.ajax({
                    url: '/api/get-slots',
                    data: { ticket_id: ticket_id, date: date },
                    success: function (slots) {
                        slotSelect.empty();
                        if (slots.length === 0) {
                            slotSelect.html('<option value="">Đã hết khung giờ trống ngày này</option>');
                        } else {
                            $.each(slots, function (i, slot) {
                                slotSelect.append('<option value="' + slot.id + '">' + slot.label + ' (Còn ' + slot.available + ' chỗ)</option>');
                            });
                        }
                    },
                    error: function () {
                        slotSelect.html('<option value="">Lỗi tải khung giờ</option>');
                    }
                });
            });

            // Khởi chạy tính toán ngay khi vừa load trang
            updateSummary();
        });
    </script>
</body>

</html>