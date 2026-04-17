@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('checkout.page_booking', ['name' => $location->name]) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap"
        rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            font-family:
                {{ $langConfig['font_family'] }}
            ;
        }

        :root {
            --primary:
                {{ $location->color ?? '#0dcaf0' }}
            ;
        }
    </style>
</head>

<body class="bg-light text-dark">
    @include('branch.partials.navbar')

    <div class="container py-5">
        <h2 class="text-info fw-bold mb-4 text-uppercase"><i class="bi bi-person-lines-fill"></i> {{ __('checkout.personal_info') }}</h2>

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

        <form action="{{ route('branch.cart.add', ['subdomain' => $subdomain]) }}" method="POST">
            @csrf
            <input type="hidden" name="ticket_id" value="{{ $ticket->id ?? '' }}">

            <input type="hidden" name="replace_cart_id" value="{{ $replaceId ?? '' }}"> 
            
            <div id="price-data" data-base="{{ $ticket->price }}" data-surcharge="{{ $surchargeRate ?? 0 }}"
                style="display: none;"></div>

            <div class="row g-4">
                {{-- CỘT TRÁI: ĐIỀN THÔNG TIN --}}
                <div class="col-lg-7">
                    <div class="card border-0 rounded-4 p-4 mb-4 shadow">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">{{ __('checkout.personal_info') }}</h5>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('checkout.field_name') }}</label>
                           <input type="text" name="customer_name" class="form-control"
    value="{{ $oldCartData['customer_name'] ?? (Auth::check() ? Auth::user()->name : '') }}" required>

                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('checkout.field_phone') }}</label>
                            <input type="text" name="customer_phone" class="form-control"
    value="{{ $oldCartData['customer_phone'] ?? (Auth::check() ? Auth::user()->phone : '') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('checkout.field_email') }}</label>
                            <input type="email" name="customer_email" class="form-control"
    value="{{ $oldCartData['customer_email'] ?? (Auth::check() ? Auth::user()->email : '') }}" required
                                placeholder="{{ __('checkout.field_email_ph') }}">
                        </div>

                        {{-- CHỌN NGÀY VÀ GIỜ --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-primary"><i class="bi bi-calendar-check me-1"></i> {{ __('checkout.play_date') }}</label>
                               <input type="date" name="booking_date" id="booking_date" class="form-control"
    value="{{ $oldCartData['booking_date'] ?? '' }}" required>
                                <div id="date-message" class="mt-2 small text-muted">{{ __('checkout.date_choose') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-warning"><i class="bi bi-clock-history me-1"></i> {{ __('checkout.time_slot') }}</label>
                                <select name="slot_id" id="slot_id"
                                    class="form-select bg-white text-dark border-light shadow-sm" required>
                                    <option value="">{{ __('checkout.slot_choose_date') }}</option>
                                </select>
                            </div>
                        </div>

                        {{-- CHỌN SỐ LƯỢNG / DUYỆT CÁC LOẠI VÉ MỚI --}}
                        @if($ticket->ticket_types && is_array($ticket->ticket_types) && count($ticket->ticket_types) > 0)
                            <div class="mb-4">
                                <label class="form-label fw-bold text-primary"><i class="bi bi-ticket-detailed me-1"></i> {{ __('checkout.ticket_qty') }}</label>
                                @php
                                    $selectedType = request()->query('selected_ticket_type');
                                @endphp
                                @foreach($ticket->ticket_types as $index => $type)
                                    <div
                                        class="d-flex justify-content-between align-items-center p-3 border border-light rounded mb-2 bg-white shadow-sm hover-scale">
                                        <div>
                                            <div class="fw-bold text-dark">{{ $type['name'] }}</div>
                                            <div class="text-primary fw-bold small">{{ number_format($type['price']) }}đ/vé
                                            </div>
                                        </div>
                                        <div style="width: 120px;">
                                            <input type="number" name="ticket_types[{{ $type['name'] }}]"
                                                value="{{ ($selectedType === $type['name'] || (!$selectedType && $index === 0)) ? 1 : 0 }}"
                                                min="0" max="50"
                                                class="form-control bg-light text-dark border-light ticket-qty-input"
                                                data-price="{{ $type['price'] }}" data-name="{{ $type['name'] }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label fw-bold text-primary"><i class="bi bi-people-fill me-1"></i> {{ __('checkout.ticket_qty_simple') }}</label>
                                <input type="number" name="quantity" id="quantity" value="1" min="1" max="50"
                                    class="form-control bg-light text-dark border-light ticket-qty-input"
                                    data-price="{{ $ticket->price }}" data-name="{{ $ticket->name }}" style="width: 120px;">
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label text-dark fw-bold">{{ __('checkout.note') }}</label>
                            <textarea name="note" class="form-control bg-light" rows="2"></textarea>
                        </div>
                    </div>

                    {{-- PHƯƠNG THỨC THANH TOÁN --}}
                    <div class="card border-0 rounded-4 p-4 shadow">
                        <h5 class="fw-bold mb-3 border-bottom pb-2">{{ __('checkout.payment_method') }}</h5>

                        {{-- Tiền mặt --}}
                        <div class="form-check mb-3 p-3 border border-light rounded bg-white shadow-sm payment-box"
                            style="transition: 0.3s; cursor: pointer;">
                            <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod"
                                checked>
                            <label class="form-check-label d-flex align-items-center gap-2 w-100" for="cod"
                                style="cursor: pointer;">
                                <i class="bi bi-cash-coin text-success fs-4"></i>
                                <div class="text-dark"><strong>{{ __('checkout.pay_counter') }}</strong></div>
                            </label>
                        </div>

                        {{-- Chuyển khoản --}}
                        <div class="form-check mb-3 p-3 border border-light rounded bg-white shadow-sm payment-box"
                            style="transition: 0.3s; cursor: pointer;">
                            <input class="form-check-input" type="radio" name="payment_method" id="banking"
                                value="banking">
                            <label class="form-check-label d-flex align-items-center gap-2 w-100" for="banking"
                                style="cursor: pointer;">
                                <i class="bi bi-qr-code-scan text-primary fs-4"></i>
                                <div class="text-dark"><strong>{{ __('checkout.pay_banking') }}</strong></div>
                            </label>
                        </div>

                        {{-- Ví Holomia --}}
                        @if(Auth::check())
                            <div class="form-check p-3 border border-light rounded bg-white shadow-sm payment-box"
                                style="transition: 0.3s; cursor: pointer;">
                                <input class="form-check-input" type="radio" name="payment_method" id="wallet"
                                    value="wallet">
                                <label class="form-check-label d-flex align-items-center gap-2 w-100" for="wallet"
                                    style="cursor: pointer;">
                                    <i class="bi bi-wallet2 text-primary fs-4"></i>
                                    <div>
                                        <strong class="text-dark text-uppercase">{{ __('checkout.pay_wallet') }}</strong>
                                        <div class="small text-muted">{{ __('checkout.wallet_balance') }}: <span>{{ number_format(Auth::user()->balance) }}đ</span></div>
                                    </div>
                                </label>
                            </div>
                        @else
                            <div class="alert alert-info small mt-3">
                                <i class="bi bi-info-circle me-1"></i> <a href="{{ route('branch.login', ['subdomain' => $subdomain]) }}" class="fw-bold text-primary">Đăng nhập</a> để thanh toán bằng ví.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- CỘT PHẢI: TÓM TẮT TIỀN GỐC --}}
                <div class="col-lg-5">
                    <div class="card border-0 rounded-4 p-4 shadow-sm sticky-top"
                        style="top: 100px; background-color: var(--bg-card);">
                        <h5 class="fw-bold mb-4 text-center text-primary border-bottom pb-3">{{ __('checkout.summary_title') }}</h5>

                        <div
                            class="d-flex justify-content-between align-items-center mb-3 bg-white shadow-sm p-2 rounded border border-light">
                            <span class="text-muted"><i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ __('cart.branch') }}:</span>
                            <span class="text-dark fw-bold text-end">{{ $location->name }}</span>
                        </div>

                        <div class="d-flex align-items-center gap-3 mb-4">
                            <img src="{{ Str::startsWith($ticket->image ?? $ticket->image_url, 'http') ? ($ticket->image ?? $ticket->image_url) : asset('storage/' . ($ticket->image ?? $ticket->image_url)) }}"
                                class="rounded shadow-sm" width="70" height="70" style="object-fit: cover;">
                            <div>
                                <h5 class="mb-1 text-dark fw-bold">{{ $ticket->name }}</h5>
                                <span class="badge bg-primary bg-opacity-10 text-primary" id="display-qty">{{ __('cart.quantity') }}: 0</span>
                            </div>
                        </div>

                        <div id="summary-ticket-list" class="mb-3"></div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted">{{ __('checkout.subtotal') }}:</span>
                            <span class="fs-5 fw-bold text-dark" id="temp-total">0đ</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 text-danger d-none"
                            id="surcharge-row">
                            <span class="small">Phụ phí cuối tuần (<span id="surcharge-percent">0</span>%):</span>
                            <span class="fw-bold" id="surcharge-amount">0đ</span>
                        </div>

                        <div
                            class="d-flex justify-content-between align-items-center border-top border-light pt-3 mb-4">
                            <span class="text-dark fw-bold fs-5">{{ __('checkout.grand_total') }}</span>
                            <span class="fs-3 fw-bold text-primary" id="final-total">0đ</span>
                        </div>

                        <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-uppercase rounded-pill shadow">
                            {{ __('checkout.add_to_cart') }} <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {
            const formatMoney = (amount) => new Intl.NumberFormat('vi-VN').format(amount) + 'đ';

            var dateObj = new Date();
            var today = dateObj.getFullYear() + '-' + String(dateObj.getMonth() + 1).padStart(2, '0') + '-' + String(dateObj.getDate()).padStart(2, '0');
            $('#booking_date').attr('min', today);

            // Giữ giá trị cũ nếu có, hoặc mặc định là hôm nay
            var initialDate = $('#booking_date').val() || today;
            $('#booking_date').val(initialDate);

            var ticket_id = $('input[name="ticket_id"]').val();
            var slotSelect = $('#slot_id');

            function loadSlotsForDate(date) {
                slotSelect.html('<option value="">' + '{{ __('checkout.slot_loading') }}' + '</option>');
                slotSelect.prop('disabled', true);

                $.ajax({
                    url: '{{ route("branch.slots", ["subdomain" => $subdomain]) }}',
                    data: { ticket_id: ticket_id, date: date },
                    success: function (slots) {
                        slotSelect.prop('disabled', false);
                        slotSelect.empty();

                        if (!slots || slots.length === 0) {
                            slotSelect.html('<option value="">' + (date === today ? '{{ __('checkout.slot_empty_today') }}' : '{{ __('checkout.slot_empty') }}') + '</option>');
                            updateSummary();
                            return;
                        }

                        var now = new Date();
                        var currentMinutes = now.getHours() * 60 + now.getMinutes();
                        var bestIndex = -1;
                        var bestDiff = Infinity;
                        var validSlotsCount = 0;

                        try {
                            $.each(slots, function (i, slot) {
                                if (!slot || !slot.start_time || !slot.end_time) return;

                                var start = slot.start_time.substring(0, 5); // "HH:mm"
                                var end = slot.end_time.substring(0, 5);
                                var parts = start.split(':');
                                var slotMinutes = parseInt(parts[0], 10) * 60 + parseInt(parts[1], 10);
                                var diff = slotMinutes - currentMinutes;

                                if (date === today && diff >= 0 && diff < bestDiff) {
                                    bestDiff = diff;
                                    bestIndex = validSlotsCount;
                                }

                                var available = slot.available_devices;
                                slotSelect.append(
                                    '<option value="' + slot.id + '" data-available="' + available + '">' +
                                    start + ' - ' + end +
                                    ' (Còn ' + available + ' thiết bị)</option>'
                                );
                                validSlotsCount++;
                            });

                            if (validSlotsCount === 0) {
                                slotSelect.html('<option value="">' + (date === today ? '{{ __('checkout.slot_empty_today') }}' : '{{ __('checkout.slot_empty') }}') + '</option>');
                                updateSummary();
                                return;
                            }

                            if (bestIndex !== -1) {
                                slotSelect.find('option').eq(bestIndex).prop('selected', true);
                            } else if (validSlotsCount > 0) {
                                slotSelect.find('option').eq(0).prop('selected', true);
                            }
                        } catch (e) {
                            console.error('Lỗi phân tích thời gian:', e);
                            slotSelect.html('<option value="">' + '{{ __('checkout.slot_error') }}' + '</option>');
                        }

                        updateSummary();
                        enforceSlotCapacity();
                    },
                    error: function () {
                        slotSelect.prop('disabled', false);
                        slotSelect.html('<option value="">' + '{{ __('checkout.slot_error') }}' + '</option>');
                    }
                });
            }

            loadSlotsForDate(initialDate);

            // Cập nhật thông báo ngày
            updateSummary();

            function updateSummary() {
                var dateVal = $('#booking_date').val();
                var surchargeRate = parseFloat($('#price-data').data('surcharge')) || 0;
                var isWeekend = false;

                if (dateVal) {
                    // Parse thủ công để tránh lỗi timezone (new Date("YYYY-MM-DD") trả về UTC midnight
                    // → khi convert sang giờ địa phương UTC+7 bị lùi 1 ngày → sai isWeekend)
                    var parts = dateVal.split('-');
                    var localDate = new Date(parts[0], parts[1] - 1, parts[2]);
                    var dayOfWeek = localDate.getDay(); // 0=CN, 6=T7
                    isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                }

                var totalQty = 0;
                var baseTotalPrice = 0;
                var summaryHtml = '';

                $('.ticket-qty-input').each(function () {
                    var qty = parseInt($(this).val()) || 0;
                    if (qty > 0) {
                        totalQty += qty;
                        var basePrice = parseFloat($(this).data('price'));
                        var name = $(this).data('name');
                        var subtotal = basePrice * qty;
                        baseTotalPrice += subtotal;

                        summaryHtml += `
                            <div class="d-flex justify-content-between align-items-center mb-2 small border-bottom border-light pb-2">
                                <span class="text-muted"><i class="bi bi-check-circle-fill text-primary me-1"></i> ${name} x${qty}</span>
                                <span class="text-dark fw-bold">${formatMoney(subtotal)}</span>
                            </div>
                        `;
                    }
                });

                var surchargeAmount = 0;
                if (isWeekend && surchargeRate > 0) {
                    surchargeAmount = baseTotalPrice * (surchargeRate / 100);
                    $('#surcharge-row').removeClass('d-none');
                    $('#surcharge-percent').text(surchargeRate);
                    $('#surcharge-amount').text('+' + formatMoney(surchargeAmount));
                    $('#date-message').html('<span class="text-danger fw-bold"><i class="bi bi-info-circle"></i> ' + '{{ __('checkout.date_weekend') }}' + '</span>');
                } else {
                    $('#surcharge-row').addClass('d-none');
                    if (dateVal) {
                        $('#date-message').html('<span class="text-success fw-bold"><i class="bi bi-check-circle"></i> ' + '{{ __('checkout.date_weekday') }}' + '</span>');
                    }
                }

                var finalTotal = baseTotalPrice + surchargeAmount;

                $('#display-qty').text('Số lượng: ' + totalQty);
                $('#summary-ticket-list').html(summaryHtml);
                $('#temp-total').text(formatMoney(baseTotalPrice));
                $('#final-total').text(formatMoney(finalTotal));
            }

            // VALIDATE SLOT CAPACITY 
            function enforceSlotCapacity() {
                var selectedSlot = $('#slot_id').find(':selected');
                if (selectedSlot.length > 0 && selectedSlot.data('available') !== undefined) {
                    var maxAvail = parseInt(selectedSlot.data('available'));
                    var totalQty = 0;
                    var $inputs = $('.ticket-qty-input');
                    
                    // Gán max attribute cho từng ô
                    $inputs.attr('max', maxAvail);

                    $inputs.each(function() {
                        totalQty += parseInt($(this).val()) || 0;
                    });
                    
                    if (totalQty > maxAvail) {
                        if (maxAvail === 0) {
                            $inputs.val(0);
                        } else {
                            // Tự động giản lược số lượng từ dưới lên
                            for (let i = $inputs.length - 1; i >= 0; i--) {
                                let $inp = $($inputs[i]);
                                let val = parseInt($inp.val()) || 0;
                                if (totalQty > maxAvail && val > 0) {
                                    let reduce = Math.min(val, totalQty - maxAvail);
                                    $inp.val(val - reduce);
                                    totalQty -= reduce;
                                }
                            }
                        }
                        
                        $('#capacity-warning').remove();
                        $('#quantity').closest('.mb-3, .mb-4').append('<div id="capacity-warning" class="text-danger small fw-bold mt-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (maxAvail === 0 ? 'Khung giờ này đã hết chỗ!' : 'Số lượng đã tự động điều chỉnh vì khung giờ này chỉ còn '+maxAvail+' suất!') + '</div>');
                        setTimeout(function() { $('#capacity-warning').fadeOut(1000, function(){ $(this).remove(); }); }, 4500);
                    }
                }
            }

            $('.ticket-qty-input').on('input change', function() {
                enforceSlotCapacity();
                updateSummary();
            });
            $('#slot_id').on('change', enforceSlotCapacity);

            // Xử lý đổi màu phương thức thanh toán
            function updatePaymentStyle() {
                $('.payment-box').removeClass('border-primary bg-primary bg-opacity-10').addClass('border-light bg-white');
                $('input[name="payment_method"]:checked').closest('.payment-box').removeClass('border-light bg-white').addClass('border-primary bg-primary bg-opacity-10');
            }

            $(document).on('change', 'input[name="payment_method"]', updatePaymentStyle);
            updatePaymentStyle();

            $('#booking_date').on('change', function () {
                var date = $(this).val();

                updateSummary();
                loadSlotsForDate(date);
            });

            updateSummary();
        });
    </script>
    @include('branch.partials.footer')
</body>

</html>