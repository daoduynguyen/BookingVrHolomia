@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
    $cartKey    = 'branch_cart_' . $subdomain;
    $cart       = session($cartKey, []);
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('cart.page_branch', ['name' => $location->name]) }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        /* Làm đẹp thanh cuộn cho bảng giỏ hàng */
        .table-responsive::-webkit-scrollbar { width: 6px; }
        .table-responsive::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb { background: {{ $location->color ?? '#0dcaf0' }}; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: {{ $location->color ?? '#0bacce' }}; }
        :root {
            --primary: {{ $location->color ?? '#0dcaf0' }};
        }
    </style>
</head>
<body class="bg-light text-dark">
    @include('branch.partials.navbar')

    <div class="container py-5">
        <h2 class="text-primary fw-bold mb-4 text-uppercase"><i class="bi bi-cart3"></i> {{ __('cart.cart_title') }} - {{ $location->name }}</h2>
        
        @if(session()->has($cartKey . '_created_at') && count($cart) > 0)
        <div id="slot-warning" class="alert alert-warning d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-clock-fill fs-5"></i>
            <div>
                {{ __('cart.reserve_timer') }}
                <strong id="countdown-timer">10:00</strong> —
                {{ __('cart.reserve_timer_desc') }}
            </div>
        </div>
        @endif

        {{-- HIỂN THỊ LỖI NẾU CÓ --}}
        @if(session('error'))
            <div class="alert alert-danger bg-danger bg-opacity-25 text-white border-0 mb-4">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if(count($cart) > 0)
            <div class="row g-4">
                {{-- CỘT TRÁI: DANH SÁCH SẢN PHẨM --}}
                <div class="col-lg-8">
                    <div class="card bg-white shadow-sm border border-light rounded-4 p-3">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th>{{ __('cart.col_product') }}</th>
                                        <th>{{ __('cart.col_price') }}</th>
                                        <th>{{ __('cart.col_qty') }}</th>
                                        <th class="text-end">{{ __('cart.col_subtotal') }}</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php 
                                        $total = 0;
                                    @endphp

                                    @foreach($cart as $id => $item)
                                        @php 
                                            $subtotal = ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
                                            $total += $subtotal;
                                        @endphp

                                        <tr data-id="{{ $id }}">
                                            @php
                                                $realTicketId = $item['id'] ?? $item['ticket_id'] ?? explode('_', $id)[0];
                                            @endphp
                                            {{-- 1. Tên, Ảnh & Giờ chơi --}}
                                            <td>
                                                <a href="{{ route('branch.booking.form', ['subdomain' => $subdomain, 'id' => $realTicketId]) }}?replace_cart_id={{ $id }}" class="text-decoration-none text-dark d-block ticket-cart-link" title="{{ __('cart.update_datetime') }}">
                                                    <div class="d-flex align-items-center gap-3">
                                                        <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset($item['image']) }}" 
                                                             class="rounded shadow-sm" width="70" height="70" style="object-fit: cover;">
                                                        <div>
                                                            <h6 class="mb-0 fw-bold text-primary">{{ $item['name'] }}</h6>
                                                            <div class="small text-muted mt-1">
                                                                <i class="bi bi-calendar3"></i> {{ isset($item['booking_date']) ? \Carbon\Carbon::parse($item['booking_date'])->format('d/m/Y') : __('cart.no_date') }}
                                                                <br>
                                                                <i class="bi bi-clock"></i> 
                                                                @php
                                                                    $slot = isset($item['slot_id']) ? \App\Models\TimeSlot::find($item['slot_id']) : null;
                                                                @endphp
                                                               @if($slot)
                                                                    {{ substr($slot->start_time, 0, 5) }} - {{ substr($slot->end_time, 0, 5) }}
                                                                @else
                                                                    <span class="text-danger fw-bold">{{ __('cart.no_slot') }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </td>

                                            {{-- 2. Giá gốc 1 vé --}}
                                            <td>{{ number_format($item['price']) }}đ</td>

                                            {{-- 3. Số lượng --}}
                                            <td>
                                                <input type="number" value="{{ $item['quantity'] }}" 
                                                       class="form-control text-center update-cart bg-white text-dark border-light shadow-sm" 
                                                       style="width: 70px;" min="1">
                                            </td>

                                            {{-- 4. Thành tiền --}}
                                            <td class="text-end item-subtotal">
                                                <div class="fw-bold text-primary fs-5">{{ number_format($subtotal) }}đ</div>
                                            </td>

                                            {{-- 5. Nút xóa --}}
                                            <td class="text-end">
                                                <a href="{{ route('branch.cart.remove', ['subdomain' => $subdomain, 'id' => $id]) }}" class="text-danger fs-5" onclick="return confirm('{{ __('cart.remove') }}?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="text-primary text-decoration-none small fw-bold">
                            <i class="bi bi-arrow-left"></i> {{ __('cart.continue_shop') }}
                        </a>
                    </div>
                </div>

                {{-- CỘT PHẢI: TỔNG TIỀN & THANH TOÁN --}}
                <div class="col-lg-4">
                    <div class="card bg-white border border-light shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                        <h5 class="fw-bold mb-4 border-bottom border-light pb-3 text-dark">{{ __('cart.title') }}</h5>

                        {{-- Hiển thị phương thức đã chọn --}}
                        @php
                            $firstCartItem = reset($cart);
                            $selectedMethod = $firstCartItem['payment_method'] ?? 'cod';
                        @endphp
                        <div class="p-3 rounded-3 mb-3 d-flex align-items-center gap-2
                            {{ $selectedMethod == 'cod' ? 'bg-warning bg-opacity-10 border border-warning border-opacity-25' : '' }}
                            {{ $selectedMethod == 'banking' ? 'bg-primary bg-opacity-10 border border-primary border-opacity-25' : '' }}
                            {{ $selectedMethod == 'wallet' ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : '' }}">
                            @if($selectedMethod == 'cod')
                                <i class="bi bi-shop text-warning fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">{{ __('checkout.pay_counter') }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ __('checkout.pay_counter_desc', ['name' => $location->name]) }}</div>
                                </div>
                            @elseif($selectedMethod == 'banking')
                                <i class="bi bi-qr-code-scan text-primary fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">{{ __('Chuyển khoản / QR') }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ __('Quét mã QR sau khi xác nhận') }}</div>
                                </div>
                            @else
                                <i class="bi bi-wallet2 text-success fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">{{ __('checkout.wallet') }}</div>
                                    <div class="text-muted" style="font-size:0.75rem;">{{ __('checkout.wallet_desc') }}</div>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">{{ __('cart.total') }}:</span>
                            <span class="fw-bold text-dark">{{ number_format($total) }}đ</span>
                        </div>

                        <hr class="border-light opacity-50">

                        {{-- TỔNG TIỀN CUỐI CÙNG --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-dark fw-bold text-uppercase">{{ __('cart.final_total') }}</span>
                            <span class="fs-4 fw-bold text-primary" id="grand-total">{{ number_format($total) }}đ</span>
                        </div>

                        {{-- ✅ MÃ GIẢM GIÁ --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-muted text-uppercase">{{ __('cart.coupon_code') }}</label>
                            <div class="input-group">
                                <input type="text" id="coupon-input" class="form-control bg-light border-light"
                                       placeholder="{{ __('cart.coupon_ph') }}" value="{{ session('applied_coupon_code_'.$subdomain) }}">
                                <button class="btn btn-outline-primary fw-bold" type="button" id="apply-coupon-btn">{{ __('cart.coupon_apply') }}</button>
                            </div>
                            <div id="coupon-msg" class="small mt-1"></div>
                        </div>  
                        <p class="small text-muted mb-4 fst-italic">{{ __('cart.empty_desc') }}</p>

                        {{-- Form Submit --}}
                        <form action="{{ route('branch.checkout', ['subdomain' => $subdomain]) }}" method="POST" id="checkout-form" onsubmit="this.querySelector('button[type=submit]').disabled=true; this.querySelector('button[type=submit]').innerHTML='<i class=\'spinner-border spinner-border-sm me-2\'></i>{{ __('cart.processing') }}';">
                            @csrf
                            <input type="hidden" name="payment_method" value="{{ $selectedMethod }}">
                            
                            @if($selectedMethod == 'cod')
                                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold text-uppercase rounded-pill shadow-sm text-dark">
                                    <i class="bi bi-shop me-2"></i>{{ __('cart.btn_cod') }}
                                </button>
                            @elseif($selectedMethod == 'banking')
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-pill shadow-sm">
                                    <i class="bi bi-qr-code-scan me-2"></i>{{ __('cart.btn_banking') }}
                                </button>
                            @else
                                <button type="submit" class="btn btn-success w-100 py-3 fw-bold text-uppercase rounded-pill shadow-sm">
                                    <i class="bi bi-wallet2 me-2"></i>{{ __('cart.btn_wallet') }}
                                </button>
                            @endif
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('branch.cart.empty', ['subdomain' => $subdomain]) }}" class="text-muted small text-decoration-none hover-text-dark" onclick="return confirm('{{ __('cart.clear_all') }}?')">
                                <i class="bi bi-trash"></i> {{ __('cart.clear_all') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else 
            {{-- GIỎ HÀNG TRỐNG --}}
            <div class="text-center py-5">
                @if(isset($pendingOrder) && $pendingOrder)
                    <div class="alert alert-warning mb-4 mx-auto text-start shadow-sm" style="max-width: 600px; border-radius: 12px; background-color: #fff8e1;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-1"></i>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Bạn có đơn hàng chờ thanh toán!</h5>
                                <p class="mb-2 text-muted small">Hệ thống ghi nhận bạn vừa tạo đơn hàng <strong>#{{ $pendingOrder->id }}</strong> nhưng chưa hoàn tất thanh toán. Vui lòng tiếp tục thanh toán để nhận vé.</p>
                                <a href="{{ route('branch.payment.banking', ['subdomain' => $subdomain, 'id' => $pendingOrder->id]) }}" class="btn btn-primary fw-bold px-4 rounded-pill btn-sm">
                                    <i class="bi bi-qr-code-scan me-2"></i>Tiếp tục quét mã QR
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <i class="bi bi-cart-x display-1 text-muted opacity-25"></i>
                <h3 class="mt-4 fw-bold text-dark">{{ __('cart.empty_cart') }}</h3>
                <p class="text-muted">{{ __('cart.empty_desc_branch', ['name' => $location->name]) }}</p>
                <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-primary shadow-sm px-5 py-3 rounded-pill fw-bold mt-3">
                    {{ __('cart.btn_shop') }}
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            // CẬP NHẬT SỐ LƯỢNG GIỎ HÀNG
            $(".update-cart").change(function (e) {
                e.preventDefault();
                var ele = $(this);
                var row = ele.parents("tr"); 
                var id = row.attr("data-id"); 
                var quantity = ele.val(); 

                $.ajax({
                    url: '{{ route('branch.cart.update', ['subdomain' => $subdomain]) }}',
                    method: "PATCH",
                    data: {
                        _token: '{{ csrf_token() }}', 
                        id: id, 
                        quantity: quantity
                    },
                     beforeSend: function() {
                       ele.prop('disabled', true);
                    },
                    success: function (response) {
                        window.location.reload(); 
                    },
                    error: function(xhr) {
                        ele.prop('disabled', false);
                        alert('{{ __('cart.error_try_again') }}');
                    }
                });
            });
        });
        // ÁP DỤNG MÃ GIẢM GIÁ
        $('#apply-coupon-btn').click(function () {
            var code = $('#coupon-input').val().trim();
            var msg  = $('#coupon-msg');

            if (!code) {
                msg.html('<span class="text-danger">{{ __('cart.enter_coupon') }}</span>');
                return;
            }

            $.ajax({
                url: '{{ route('branch.coupon.apply', ['subdomain' => $subdomain]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coupon_code: code
                },
                success: function (res) {
                    if (res.success) {
                        msg.html('<span class="text-success"><i class="bi bi-check-circle-fill"></i> ' + res.message + '</span>');

                        // Tính lại thành tiền
                        var total = {{ $total }};
                        var discount = 0;
                        if (res.type === 'percent') {
                            discount = total * res.value / 100;
                        } else {
                            discount = res.value;
                        }
                        var finalTotal = Math.max(0, total - discount);
                        $('#grand-total').text(finalTotal.toLocaleString('vi-VN') + 'đ');
                    } else {
                        msg.html('<span class="text-danger"><i class="bi bi-x-circle-fill"></i> ' + res.message + '</span>');
                    }
                },
                error: function () {
                    msg.html('<span class="text-danger">{{ __('cart.error_try_again') }}</span>');
                }
            });
        });

        // Cho phép nhấn Enter trong ô mã
        $('#coupon-input').keypress(function (e) {
            if (e.which === 13) $('#apply-coupon-btn').click();
        });
    </script>

    <script>
    (function() {
        const cartKey = '{{ $cartKey }}';
        const cartCreatedAt = {{ session($cartKey . '_created_at', 'null') }};
        if (!cartCreatedAt) return;

        const expireAt = cartCreatedAt + (10 * 60); // 10 phút
        const warning  = document.getElementById('slot-warning');
        const timer    = document.getElementById('countdown-timer');
        if (!warning || !timer) return;

        const interval = setInterval(() => {
            const remaining = expireAt - Math.floor(Date.now() / 1000);

            if (remaining <= 0) {
                clearInterval(interval);
                timer.textContent = '00:00';
                warning.className = 'alert alert-danger d-flex align-items-center gap-2 mb-4';
                warning.innerHTML = '<i class="bi bi-exclamation-triangle-fill fs-5"></i>'
                    + '<div>{{ __('cart.timeout_msg') }}</div>';
                document.body.insertAdjacentHTML('beforeend', '<form id="form-clear-cart" action="{{ route('branch.cart.empty', ['subdomain' => $subdomain, 'redirect' => 'home']) }}" method="GET" style="display:none"></form>');
                document.getElementById('form-clear-cart').submit();
                return;
            }

            const m = Math.floor(remaining / 60).toString().padStart(2, '0');
            const s = (remaining % 60).toString().padStart(2, '0');
            timer.textContent = `${m}:${s}`;

            if (remaining < 120) {
                warning.className = 'alert alert-danger d-flex align-items-center gap-2 mb-4';
            }
        }, 1000);
    })();
    </script>
    @include('branch.partials.footer')
</body>
</html>