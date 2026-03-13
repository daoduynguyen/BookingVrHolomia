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
<body class="bg-dark text-white">
    @include('partials.navbar')

    <div class="container py-5">
        <h2 class="text-info fw-bold mb-4 text-uppercase"><i class="bi bi-cart3"></i> Giỏ hàng của bạn</h2>
        
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
                    <div class="card bg-secondary bg-opacity-10 border-secondary border-opacity-25 rounded-4 p-3">
                        <div class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                            <table class="table table-dark table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-secondary small text-uppercase">
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
                                                        <div class="small text-secondary mt-1">
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
                                                       class="form-control text-center update-cart bg-dark text-white border-secondary" 
                                                       style="width: 70px;" min="1">
                                            </td>

                                            {{-- 4. Thành tiền (CÓ XỬ LÝ MÃ GIẢM GIÁ RIÊNG) --}}
                                            <td class="text-end item-subtotal">
                                                @if($itemDiscount > 0)
                                                    {{-- Có mã giảm giá --}}
                                                    <div class="text-decoration-line-through text-secondary small">{{ number_format($subtotal) }}đ</div>
                                                    <div class="fw-bold text-info fs-5">{{ number_format($itemFinalPrice) }}đ</div>
                                                    <div class="text-success small mt-1 fw-bold">
                                                        <i class="bi bi-tag-fill"></i> Mã {{ $item['coupon_code'] }}: -{{ number_format($itemDiscount) }}đ
                                                    </div>
                                                @else
                                                    {{-- Không có mã giảm giá --}}
                                                    <div class="fw-bold text-info fs-5">{{ number_format($subtotal) }}đ</div>
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
                        <a href="{{ route('ticket.shop') }}" class="text-info text-decoration-none small">
                            <i class="bi bi-arrow-left"></i> Tiếp tục chọn vé
                        </a>
                    </div>
                </div>

                {{-- CỘT PHẢI: TỔNG TIỀN & THANH TOÁN --}}
                <div class="col-lg-4">
                    <div class="card bg-black border border-secondary border-opacity-50 rounded-4 p-4 sticky-top" style="top: 100px;">
                        <h5 class="fw-bold mb-4 border-bottom border-secondary pb-3 text-white">Thanh toán</h5>

                        {{-- Hiển thị Tổng tiền gốc --}}
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-secondary">Tổng tiền vé:</span>
                            <span class="fw-bold text-white">{{ number_format($total) }}đ</span>
                        </div>

                        {{-- Hiển thị TỔNG GIẢM GIÁ (Cộng dồn từ các item) --}}
                        @if($totalDiscount > 0)
                            <div class="d-flex justify-content-between text-success mb-3">
                                <span><i class="bi bi-tags-fill"></i> Tổng giảm giá:</span>
                                <span class="fw-bold">-{{ number_format($totalDiscount) }}đ</span>
                            </div>
                            <hr class="border-secondary opacity-50">
                        @else
                            <hr class="border-secondary opacity-50">
                        @endif

                        {{-- TỔNG TIỀN CUỐI CÙNG --}}
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-white fw-bold text-uppercase">Cần thanh toán:</span>
                            <span class="fs-4 fw-bold text-info" id="grand-total">{{ number_format($total - $totalDiscount) }}đ</span>
                        </div>

                        <p class="small text-secondary mb-4 fst-italic">Phí dịch vụ và thuế sẽ được tính ở bước tiếp theo.</p>

                        {{-- Form Submit --}}
                        <form action="{{ route('payment.final') }}" method="POST" 
                              onsubmit="let btn = this.querySelector('button[type=submit]'); btn.disabled = true; btn.innerHTML = '<i class=\'bi bi-hourglass-split\'></i> Đang xử lý...';">
                            @csrf
                            <button type="submit" class="btn btn-info w-100 py-3 fw-bold text-uppercase rounded-pill shadow">
                                XÁC NHẬN THANH TOÁN <i class="bi bi-check-circle-fill ms-2"></i>
                            </button>
                        </form>

                        <div class="text-center mt-3">
                            <a href="{{ route('cart.clear') }}" class="text-secondary small text-decoration-none hover-text-white">
                                <i class="bi bi-trash"></i> Xóa sạch giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else 
            {{-- GIỎ HÀNG TRỐNG --}}
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-secondary opacity-25"></i>
                <h3 class="mt-4 fw-bold">Giỏ hàng đang trống</h3>
                <p class="text-secondary">Bạn chưa chọn bất kỳ trò chơi nào để trải nghiệm.</p>
                <a href="{{ route('ticket.shop') }}" class="btn btn-info px-5 py-3 rounded-pill fw-bold mt-3">
                    XEM DANH SÁCH TRÒ CHƠI
                </a>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
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
                    success: function (response) {
                        // Vì logic giảm giá phụ thuộc vào số lượng, an toàn nhất là tải lại trang
                        // để server tính toán lại mọi mã giảm giá và hiển thị cho chính xác
                        window.location.reload(); 
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText); 
                    }
                });
            });
        });
    </script>
</body>
</html>