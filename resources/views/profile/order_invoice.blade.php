<div class="text-white" style="font-family: 'Consolas', 'Monaco', 'Segoe UI', sans-serif;">
    
    {{-- 1. HEADER --}}
<div class="text-center mb-3 pb-2 border-bottom border-secondary border-opacity-25" style="border-style: dashed !important;">
    <h5 class="fw-bold text-uppercase text-info mb-1 ls-1">HOLOMIA VR</h5>
    <div class="d-flex justify-content-center gap-3 text-white small">
        <span>#{{ $order->id }}</span>
        {{-- THÊM DÒNG NÀY ĐỂ HIỆN GIỜ --}}
        <span>
            <i class="bi bi-clock"></i> 
            @php $slot = $order->slot; @endphp
            {{ $slot ? substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5) : 'N/A' }}
        </span>
        <span>{{ $order->created_at->format('d/m/Y') }}</span>
    </div>
</div>
    {{-- 2. THÔNG TIN KHÁCH --}}
    <div class="mb-3 px-1 small">
        <div class="d-flex justify-content-between mb-1">
            {{-- Đã sửa text-white-50 thành text-white --}}
            <span class="text-white">Khách hàng:</span>
            <span class="fw-bold text-white">{{ $order->customer_name }}</span>
        </div>
        <div class="d-flex justify-content-between mb-1">
            <span class="text-white">Thanh toán:</span>
            <span class="text-info text-uppercase fw-bold">
                {{ $order->payment_method == 'cod' ? 'Tiền mặt' : 'Chuyển khoản' }}
            </span>
        </div>
    </div>

    {{-- 3. BẢNG VÉ --}}
    <div class="mb-3 receipt-scroll" style="max-height: 180px; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">
        <table class="table table-sm table-borderless mb-0" style="--bs-table-bg: transparent; --bs-table-accent-bg: transparent; --bs-table-striped-bg: transparent; --bs-table-hover-bg: transparent; color: #fff;">
            
            {{-- Thêm position: sticky để cố định Header khi cuộn --}}
            <thead class="text-white border-bottom border-secondary border-opacity-25" style="border-style: dashed !important; font-size: 0.85rem; position: sticky; top: 0; background-color: #1a1d20; z-index: 1;">
                <tr>
                    <th class="ps-0 text-start text-white">Loại vé</th>
                    <th class="text-center text-white" style="width: 30px;">SL</th>
                    <th class="pe-0 text-end text-white">Tiền</th>
                </tr>
            </thead>
            
            <tbody style="border-bottom: 1px dashed rgba(255,255,255,0.2);">
                @foreach($order->orderItems as $item)
                <tr>
                    <td class="ps-0 py-2">
                        {{-- Đã thêm title để trỏ chuột vào hiện full tên nếu bị cắt --}}
                        <div class="text-truncate fw-bold text-white" style="max-width: 170px;" title="{{ $item->ticket_name }}">
                            {{ $item->ticket_name }}
                        </div>
                    </td>
                    <td class="text-center py-2 text-white">x{{ $item->quantity }}</td>
                    <td class="pe-0 py-2 text-end text-white">{{ number_format($item->price * $item->quantity) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 4. TỔNG KẾT --}}
    <div class="pt-2">
        @php
            // 1. Tính tổng tiền gốc thực tế từ danh sách vé
            $originalTotal = 0;
            foreach($order->orderItems as $item) {
                $originalTotal += $item->price * $item->quantity;
            }

            // 2. Lấy số tiền khách đã thanh toán (Final)
            $finalTotal = $order->total_amount; 

            // 3. Tự động tính ra số tiền được giảm
            $discount = $originalTotal - $finalTotal;

            // 4. Lấy mã code
            $code = session('coupon_code') ?? ($order->coupon_code ?? '');
        @endphp

        {{-- Dòng 1: Tạm tính (Giá gốc chưa giảm) --}}
        <div class="d-flex justify-content-between mb-1">
            <span class="text-white small">Tạm tính:</span>
            {{--  Thay $subtotal thành $originalTotal --}}
            <span class="small text-white">{{ number_format($originalTotal) }}đ</span>
        </div>

        {{-- Dòng 2: Voucher (CHỈ HIỆN NẾU CÓ GIẢM GIÁ) --}}
        @if($discount > 0)
            <div class="d-flex justify-content-between mb-1 text-success">
                <span class="small">
                    <i class="bi bi-ticket-perforated-fill me-1"></i>
                    Voucher {{ $code ? '('.$code.')' : '' }}:
                </span>
                <span class="small">-{{ number_format($discount) }}đ</span>
            </div>
            <div class="border-top border-secondary border-opacity-25 my-2" style="border-style: dashed !important;"></div>
        @endif

        {{-- Dòng 3: Tổng cộng --}}
        <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="text-white fw-bold text-uppercase small">TỔNG CỘNG:</span>
            <span class="fs-4 fw-bold text-info">{{ number_format($finalTotal) }}đ</span>
        </div>
    </div>

    {{-- 5. FOOTER --}}
    <div class="text-center mt-4 opacity-50">
        <i class="bi bi-upc-scan text-white" style="font-size: 1.5rem;"></i>
        <p class="mb-0 small fst-italic mt-1 text-white" style="font-size: 0.7rem;">Cảm ơn quý khách!</p>
    </div>
</div>