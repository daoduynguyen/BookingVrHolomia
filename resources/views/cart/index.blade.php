<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        /* Làm đẹp thanh cuộn cho bảng giỏ hàng */
        .table-responsive::-webkit-scrollbar { width: 6px; }
        .table-responsive::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb { background: #0dcaf0; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: #0bacce; }
    </style>
</head>
<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5">
        <h2 class="text-primary fw-bold mb-4 text-uppercase"><i class="bi bi-cart3"></i> Giỏ hàng của bạn</h2>
        
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
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th class="text-end">Thành tiền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Khởi tạo biến tính tổng --}}
                                    @php 
                                        $total = 0;
    $totalDiscount = 0; 
                                    @endphp

                                    @foreach($cart as $id => $item)
                                        @php 
                                            // Tính toán cho từng item
        $subtotal = $item['price'] * $item['quantity'];
        $itemDiscount = $item['discount'] ?? 0;
        $itemFinalPrice = $subtotal - $itemDiscount;

        // Cộng dồn vào tổng đơn hàng
        $total += $subtotal;
        $totalDiscount += $itemDiscount;
                                        @endphp

                                        <tr data-id="{{ $id }}">
                                            {{-- 1. Tên, Ảnh & Giờ chơi --}}
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="{{ Str::startsWith($item['image'], 'http') ? $item['image'] : asset($item['image']) }}" 
                                                         class="rounded" width="60" height="60" style="object-fit: cover;">
                                                    <div>
                                                        <h6 class="mb-0 fw-bold">{{ $item['name'] }}</h6>
                                                        <div class="small text-muted mt-1">
                                                            <i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($item['booking_date'])->format('d/m/Y') }}
                                                            <br>
                                                            <i class="bi bi-clock"></i> 
                                                            @php
        $slot = \App\Models\TimeSlot::find($item['slot_id']);
                                                            @endphp
                                                            {{ $slot ? substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5) : 'Chưa chọn giờ' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- 2. Giá gốc 1 vé --}}
                                            <td>{{ number_format($item['price']) }}đ</td>

                                            {{-- 3. Số lượng --}}
                                            <td>
                                                <input type="number" value="{{ $item['quantity'] }}" 
                                                       class="form-control text-center update-cart bg-white text-dark border-light shadow-sm" 
                                                       style="width: 70px;" min="1">
                                            </td>

                                            {{-- 4. Thành tiền (CÓ XỬ LÝ MÃ GIẢM GIÁ RIÊNG) --}}
                                            <td class="text-end item-subtotal">
                                                @if($itemDiscount > 0)
                                                    {{-- Có mã giảm giá --}}
                                                    <div class="text-decoration-line-through text-muted small">{{ number_format($subtotal) }}đ</div>
                                                    <div class="fw-bold text-primary fs-5">{{ number_format($itemFinalPrice) }}đ</div>
                                                    <div class="text-success small mt-1 fw-bold">
                                                        <i class="bi bi-tag-fill"></i> Mã {{ $item['coupon_code'] }}: -{{ number_format($itemDiscount) }}đ
                                                    </div>
                                                @else
                                                    {{-- Không có mã giảm giá --}}
                                                    <div class="fw-bold text-primary fs-5">{{ number_format($subtotal) }}đ</div>
                                                @endif
                                            </td>

                                            {{-- 5. Nút xóa --}}
                                            <td class="text-end">
                                                <a href="{{ route('cart.remove', $id) }}" class="text-danger fs-5" onclick="return confirm('Bạn chắc chắn muốn xóa vé này?')">
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
                        <a href="{{ route('ticket.shop') }}" class="text-primary text-decoration-none small fw-bold">
                            <i class="bi bi-arrow-left"></i> Tiếp tục chọn vé
                        </a>
                    </div>
                </div>

                {{-- CỘT PHẢI: TỔNG TIỀN & THANH TOÁN --}}
                <div class="col-lg-4">
                    <div class="card bg-white border border-light shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
                        <h5 class="fw-bold mb-4 border-bottom border-light pb-3 text-dark">Thanh toán</h5>

                        {{-- Hiển thị phương thức đã chọn --}}
                        @php
    $cartItems = session('cart', []);
    $firstCartItem = reset($cartItems);
    $selectedMethod = $firstCartItem['customer_info']['payment_method'] ?? 'cod';
                        @endphp
                        <div class="p-3 rounded-3 mb-3 d-flex align-items-center gap-2
                            {{ $selectedMethod == 'cod' ? 'bg-warning bg-opacity-10 border border-warning border-opacity-25' : '' }}
                            {{ $selectedMethod == 'banking' ? 'bg-primary bg-opacity-10 border border-primary border-opacity-25' : '' }}
                            {{ $selectedMethod == 'wallet' ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : '' }}">
                            @if($selectedMethod == 'cod')
                                <i class="bi bi-shop text-warning fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">Thanh toán tại quầy</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Vui lòng đến quầy thanh toán trước giờ chơi</div>
                                </div>
                            @elseif($selectedMethod == 'banking')
                                <i class="bi bi-qr-code-scan text-primary fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">Chuyển khoản / QR</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Quét mã QR sau khi xác nhận</div>
                                </div>
                            @else
                                <i class="bi bi-wallet2 text-success fs-5"></i>
                                <div>
                                    <div class="fw-bold text-dark small">Ví Holomia</div>
                                    <div class="text-muted" style="font-size:0.75rem;">Trừ tiền ví ngay lập tức</div>
                                </div>
                            @endif
                        </div>

                        {{-- Hiển thị Tổng tiền gốc --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tổng tiền vé:</span>
                            <span class="fw-bold text-dark">{{ number_format($total) }}đ</span>
                        </div>

                        {{-- Hiển thị TỔNG GIẢM GIÁ (Cộng dồn từ các item) --}}
                        @if($totalDiscount > 0)
                            <div class="d-flex justify-content-between text-success mb-3">
                                <span><i class="bi bi-tags-fill"></i> Tổng giảm giá:</span>
                                <span class="fw-bold">-{{ number_format($totalDiscount) }}đ</span>
                            </div>
                            <hr class="border-light opacity-50">
                        @else
                            <hr class="border-light opacity-50">
                        @endif

                        {{-- TỔNG TIỀN CUỐI CÙNG --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-dark fw-bold text-uppercase">Cần thanh toán:</span>
                            <span class="fs-4 fw-bold text-primary" id="grand-total">{{ number_format($total - $totalDiscount) }}đ</span>
                        </div>

                        <p class="small text-muted mb-4 fst-italic">Hãy kiểm tra lại thông tin trước khi xác nhận!</p>

                        {{-- Form Submit --}}
                        <form action="{{ route('payment.final') }}" method="POST" id="checkout-form">
                            @csrf
                            @if($selectedMethod == 'cod')
                                <button type="button" class="btn btn-warning w-100 py-3 fw-bold text-uppercase rounded-pill shadow-sm text-dark" onclick="confirmCOD()">
                                    <i class="bi bi-shop me-2"></i>ĐẶT VÉ - THANH TOÁN TẠI QUẦY
                                </button>
                            @elseif($selectedMethod == 'banking')
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold text-uppercase rounded-pill shadow-sm">
                                    <i class="bi bi-qr-code-scan me-2"></i>TIẾP TỤC CHUYỂN KHOẢN
                                </button>
                            @else
                                <button type="submit" class="btn btn-success w-100 py-3 fw-bold text-uppercase rounded-pill shadow-sm">
                                    <i class="bi bi-wallet2 me-2"></i>THANH TOÁN BẰNG VÍ
                                </button>
                            @endif
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('cart.clear') }}" class="text-muted small text-decoration-none hover-text-dark">
                                <i class="bi bi-trash"></i> Xóa sạch giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else 
            {{-- GIỎ HÀNG TRỐNG --}}
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted opacity-25"></i>
                <h3 class="mt-4 fw-bold text-dark">Giỏ hàng đang trống</h3>
                <p class="text-muted">Bạn chưa chọn bất kỳ trò chơi nào để trải nghiệm.</p>
                <a href="{{ route('ticket.shop') }}" class="btn btn-primary shadow-sm px-5 py-3 rounded-pill fw-bold mt-3">
                    XEM DANH SÁCH TRÒ CHƠI
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript">

        // Xác nhận COD
        function confirmCOD() {
            Swal.fire({
                html: `
                    <div class="text-center">
                        <div class="mb-3">
                            <div class="rounded-circle d-inline-flex align-items-center justify-content-center"
                                 style="width:60px;height:60px;background:rgba(255,193,7,0.15);">
                                <i class="bi bi-shop" style="font-size:2rem;color:#ffc107;"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Xác nhận đặt vé</h5>
                        <p class="text-muted mb-3" style="font-size:0.9rem;">
                            Bạn đã chọn <strong>Thanh toán tại quầy</strong>.<br>
                            Vui lòng đến quầy thanh toán <strong>trước 15 phút</strong> so với giờ chơi.
                        </p>
                        <div class="bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 text-start" style="font-size:0.85rem;">
                            <i class="bi bi-info-circle-fill text-warning me-2"></i>
                            Đơn hàng sẽ ở trạng thái <strong>"Chờ xử lý"</strong> cho đến khi nhân viên xác nhận thanh toán tại quầy.
                        </div>
                    </div>
                `,
                background: '#fff',
                showCancelButton: true,
                confirmButtonText: 'Xác nhận đặt vé',
                cancelButtonText: 'Quay lại',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                width: '380px',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('checkout-form').submit();
                }
            });
        }
        $(document).ready(function () {
            
            // --- 1. CODE ẨN THÔNG BÁO SAU 3 GIÂY ---
            if ($(".alert").length) {
                setTimeout(function() {
                    $(".alert").slideUp(500, function() {
                        $(this).remove();
                    }); 
                }, 3000); 
            }

            // --- 2. CẬP NHẬT SỐ LƯỢNG GIỎ HÀNG ---
            $(".update-cart").change(function (e) {
                e.preventDefault();
        
                var ele = $(this);
                var row = ele.parents("tr"); 
                var id = row.attr("data-id"); 
                var quantity = ele.val(); 

                $.ajax({
                    url: '{{ route('cart.update') }}',
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
                        // Vì logic giảm giá phụ thuộc vào số lượng, an toàn nhất là tải lại trang
                        // để server tính toán lại mọi mã giảm giá và hiển thị cho chính xác
                        window.location.reload(); 
                    },
                    error: function(xhr) {
                        ele.prop('disabled', false);
                        console.log(xhr.responseText); 
                    }
                });
            });
        });
    </script>
</body>
</html>