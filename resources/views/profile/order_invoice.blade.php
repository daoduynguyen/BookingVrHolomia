<div class="text-dark" style="font-family: 'Be Vietnam Pro', sans-serif;">
    
    {{-- 1. HEADER --}}
    <div class="text-center mb-3 pb-2 border-bottom border-light border-opacity-25" style="border-style: dashed !important;">
        <h5 class="fw-bold text-uppercase text-primary mb-1 ls-1">HOLOMIA VR</h5>
        <div class="d-flex justify-content-center gap-3 text-dark small">
            <span>{{ $order->id }}</span>
            <span>
                <i class="bi bi-clock"></i> 
                @php $slot = $order->slot; @endphp
                {{ $slot ? substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5) : 'N/A' }}
            </span>
            <span>{{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- 2. THÔNG TIN KHÁCH --}}
    <div class="mb-3 px-1 small">
        <div class="d-flex justify-content-between mb-1">
            <span class="text-dark">{{ __('profile.customer') ?? 'Khách hàng:' }}</span>
            <span class="fw-bold text-dark">{{ $order->customer_name }}</span>
        </div>

        {{-- Dòng hiển thị Cơ sở mới thêm --}}
        <div class="d-flex justify-content-between mb-1">
            <span class="text-dark-50">{{ __('profile.location') ?? 'Cơ sở:' }}</span>
            <strong class="text-primary text-end">{{ $order->location->name ?? 'Đang cập nhật' }}</strong>
        </div>
        
        <div class="d-flex justify-content-between mb-1">
            <span class="text-dark">{{ __('profile.payment_method') ?? 'Thanh toán:' }}</span>
            <span class="text-primary text-uppercase fw-bold">
                @if($order->payment_method == 'wallet')
                    {{ __('profile.payment_wallet') ?? 'Ví Holomia' }}
                @elseif($order->payment_method == 'cod')
                    {{ __('profile.payment_cash') ?? 'Tiền mặt' }}
                @else
                    {{ __('profile.payment_banking') ?? 'Chuyển khoản' }}
                @endif
            </span>
        </div>
    </div>

    {{-- 3. BẢNG VÉ --}}
    <div class="mb-3 receipt-scroll" style="max-height: 180px; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">
        <table class="table table-sm table-borderless mb-0" style="--bs-table-bg: transparent; --bs-table-accent-bg: transparent; --bs-table-striped-bg: transparent; --bs-table-hover-bg: transparent; color: #212529;">
            <thead class="text-dark border-bottom border-light border-opacity-25" style="border-style: dashed !important; font-size: 0.85rem; position: sticky; top: 0; background-color: #f8f9fa; z-index: 1;">
                <tr>
                    <th class="ps-0 text-start text-dark">{{ __('profile.ticket_type') ?? 'Loại vé' }}</th>
                    <th class="text-center text-dark" style="width: 30px;">{{ __('profile.quantity') ?? 'SL' }}</th>
                    <th class="pe-0 text-end text-dark">{{ __('profile.amount') ?? 'Tiền' }}</th>
                </tr>
            </thead>
            <tbody style="border-bottom: 1px dashed rgba(0,0,0,0.1);">
                @foreach($order->orderItems as $item)
                <tr>
                    <td class="ps-0 py-2">
                        <div class="text-truncate fw-bold text-dark" style="max-width: 170px;" title="{{ $item->ticket_name }}">
                            {{ $item->ticket_name }}
                        </div>
                    </td>
                    <td class="text-center py-2 text-dark">x{{ $item->quantity }}</td>
                    <td class="pe-0 py-2 text-end text-dark">
                        <div class="d-flex flex-column align-items-end">
                            <span>{{ number_format($item->price * $item->quantity) }}</span>
                            
                            {{-- Button đánh giá --}}
                            @if(in_array($order->status, ['paid', 'expired', 'completed']))
                                @if($item->review)
                                    <div class="mt-1" style="font-size: 0.75rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star-fill text-warning {{ $i > $item->review->rating ? 'opacity-25' : '' }}"></i>
                                        @endfor
                                    </div>
                                @else
                                    <button id="review-btn-{{ $item->id }}" class="btn btn-outline-info btn-sm mt-1 px-3 py-1 text-primary fw-bold" style="font-size: 0.75rem; border: 1px solid #0dcaf0 !important; border-radius: 20px;" onclick="openReviewModal({{ $item->ticket_id }}, {{ $item->id }}, '{{ addslashes($item->ticket_name) }}')">
                                        {{ __('profile.review') ?? 'Đánh giá' }}
                                    </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 4. TỔNG KẾT --}}
    <div class="pt-2">
        @php
            $originalTotal = 0;
            foreach($order->orderItems as $item) { $originalTotal += $item->price * $item->quantity; }
            $finalTotal = $order->total_amount; 
            $discount = $originalTotal - $finalTotal;
            $code = session('coupon_code') ?? ($order->coupon_code ?? '');
        @endphp

        <div class="d-flex justify-content-between mb-1">
            <span class="text-dark small">{{ __('profile.subtotal') ?? 'Tạm tính:' }}</span>
            <span class="small text-dark">{{ number_format($originalTotal) }}đ</span>
        </div>

        @if($discount > 0)
            <div class="d-flex justify-content-between mb-1 text-success">
                <span class="small"><i class="bi bi-ticket-perforated-fill me-1"></i>Voucher {{ $code ? '('.$code.')' : '' }}:</span>
                <span class="small">-{{ number_format($discount) }}đ</span>
            </div>
            <div class="border-top border-light border-opacity-25 my-2" style="border-style: dashed !important;"></div>
        @endif

        <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="text-dark fw-bold text-uppercase small">{{ __('profile.total') ?? 'TỔNG CỘNG:' }}</span>
            <span class="fs-4 fw-bold text-primary">{{ number_format($finalTotal) }}đ</span>
        </div>
    </div>

    {{-- 5. FOOTER & MÃ QR --}}
    <div class="text-center mt-3 pt-2" style="border-top: 1px dashed rgba(0,0,0,0.1);">
        <div style="cursor: pointer;" onclick="const icon = this.querySelector('.qr-icon'); const real = this.querySelector('.qr-real'); if(icon) icon.style.display='none'; if(real) real.style.display='block';">
            <div class="qr-icon">
                <i class="bi bi-qr-code-scan text-dark" style="font-size: 2rem;"></i>
                <p class="mb-0 small fst-italic mt-1 text-primary" style="font-size: 0.75rem;">{{ __('profile.click_to_get_qr') ?? 'Nhấn vào đây để lấy mã QR Check-in' }}</p>
            </div>
            <div class="qr-real" style="display: none;">
                @php $qrData = route('ticket.scan', $order->id); @endphp
                <div class="bg-white d-inline-block p-1 rounded mb-1">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(100)->margin(1)->generate($qrData) !!}
                </div>
                <p class="mb-0 small fw-bold mt-1 text-dark">{{ __('profile.show_qr_to_staff') ?? 'Đưa mã này cho nhân viên' }}</p>
            </div>
        </div>
    </div>

    {{-- 🔥 BƯỚC 3: NÚT HOÀN TRẢ VÉ CHÈN TẠI ĐÂY 🔥 --}}
    @if($order->status == 'paid' || $order->status == 'pending')
        <div class="mt-3 text-center border-top border-light border-opacity-10 pt-3">
            <button class="btn btn-outline-danger btn-sm px-4 rounded-pill fw-bold" onclick="confirmRefund({{ $order->id }})">
                <i class="bi bi-arrow-return-left me-1"></i> {{ __('profile.refund_ticket') ?? 'HOÀN TRẢ VÉ' }}
            </button>
        </div>
    @endif
</div>