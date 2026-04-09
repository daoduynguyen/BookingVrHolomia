<div class="text-dark p-2" style="font-family: {{ config('i18n.supported.' . app()->getLocale())['font_family'] ?? "'Be Vietnam Pro', sans-serif" }}; background: #fff;">
    
    {{-- 1. HEADER --}}
    <div class="text-center mb-4 border-bottom pb-3" style="border-style: dashed !important; border-color: #cbd5e1 !important;">
        <h4 class="fw-bold text-uppercase mb-1" style="color: #0ea5e9; letter-spacing: 2px;">
            HOLOMIA VR
        </h4>
        <div class="d-flex justify-content-center gap-3 text-muted small mt-2 fw-medium">
            <span><i class="bi bi-receipt me-1"></i>#{{ $order->id }}</span>
            <span>
                <i class="bi bi-clock me-1"></i> 
                @php $slot = $order->slot; @endphp
                {{ $slot ? substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5) : '--:--' }}
            </span>
            <span><i class="bi bi-calendar-event me-1"></i>{{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</span>
        </div>
    </div>

    {{-- 2. THÔNG TIN KHÁCH & ĐƠN HÀNG --}}
    <div class="mb-4">
        <table class="table table-sm table-borderless mb-0" style="font-size: 0.9rem;">
            <tbody>
                <tr>
                    <td class="text-muted ps-0" style="width: 40%;">{{ __('profile.customer') ?? 'Khách hàng' }}:</td>
                    <td class="text-end pe-0 fw-bold text-dark">{{ $order->customer_name }}</td>
                </tr>
                <tr>
                    <td class="text-muted ps-0">{{ __('profile.location') ?? 'Cơ sở' }}:</td>
                    <td class="text-end pe-0 fw-bold text-primary">{{ $order->location->name ?? '---' }}</td>
                </tr>
                <tr>
                    <td class="text-muted ps-0 pb-0">{{ __('profile.payment_method') ?? 'Thanh toán' }}:</td>
                    <td class="text-end pe-0 pb-0 fw-bold text-dark text-uppercase">
                        @if($order->payment_method == 'wallet')
                            <span class="badge bg-info text-dark">{{ __('profile.payment_wallet') ?? 'Ví Holomia' }}</span>
                        @elseif($order->payment_method == 'cod')
                            <span class="badge bg-secondary">{{ __('profile.payment_cash') ?? 'Tại quầy' }}</span>
                        @else
                            <span class="badge bg-primary">{{ __('profile.payment_banking') ?? 'Chuyển khoản' }}</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- 3. BẢNG CHI TIẾT VÉ --}}
    <div class="mb-3 px-2 py-2 bg-light rounded-3" style="max-height: 220px; overflow-y: auto;">
        <table class="table table-sm table-borderless mb-0 align-middle" style="font-size: 0.85rem;">
            <thead class="text-muted text-uppercase" style="font-size: 0.75rem; border-bottom: 2px solid #e2e8f0;">
                <tr>
                    <th class="ps-1 text-start" style="width: 50%;">{{ __('profile.ticket_type') ?? 'Trò chơi/Vé' }}</th>
                    <th class="text-center" style="width: 20%;">{{ __('profile.quantity') ?? 'SL' }}</th>
                    <th class="pe-1 text-end" style="width: 30%;">{{ __('profile.amount') ?? 'Thành tiền' }}</th>
                </tr>
            </thead>
            <tbody style="border-bottom: 1px dashed #cbd5e1;">
                @foreach($order->orderItems as $item)
                <tr>
                    <td class="ps-1 py-3">
                        <div class="fw-bold text-dark lh-sm" style="word-break: break-word;">
                            {{ $item->ticket_name }}
                        </div>
                    </td>
                    <td class="text-center py-3 text-dark fw-medium">x{{ $item->quantity }}</td>
                    <td class="pe-1 py-3 text-end">
                        <div class="fw-bold text-dark mb-1">
                            {{ number_format($item->price * $item->quantity) }}đ
                        </div>
                        
                        {{-- Đánh giá nhanh --}}
                        @if(in_array($order->status, ['paid', 'expired', 'completed']))
                            @if($item->review)
                                <div style="font-size: 0.7rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill text-warning {{ $i > $item->review->rating ? 'opacity-25' : '' }}"></i>
                                    @endfor
                                </div>
                            @else
                                <button id="review-btn-{{ $item->id }}" 
                                        class="btn btn-outline-info btn-sm py-0 px-2 rounded-pill fw-bold" 
                                        style="font-size: 0.65rem;" 
                                        onclick="openReviewModal({{ $item->ticket_id }}, {{ $item->id }}, '{{ addslashes($item->ticket_name) }}')">
                                    <i class="bi bi-pencil-square me-1"></i>{{ __('profile.review') ?? 'Đánh giá' }}
                                </button>
                            @endif
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 4. TỔNG KẾT TIỀN --}}
    <div class="pt-2 px-1 mb-4">
        @php
            $originalTotal = 0;
            foreach($order->orderItems as $item) { $originalTotal += $item->price * $item->quantity; }
            $finalTotal = $order->total_amount; 
            $discount = $originalTotal - $finalTotal;
            $code = session('coupon_code') ?? ($order->coupon_code ?? '');
        @endphp

        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted small fw-medium">{{ __('profile.subtotal') ?? 'Tạm tính:' }}</span>
            <span class="fw-medium text-dark">{{ number_format($originalTotal) }}đ</span>
        </div>

        @if($discount > 0)
            <div class="d-flex justify-content-between mb-2 text-danger">
                <span class="small fw-medium">
                    <i class="bi bi-ticket-perforated me-1"></i>Voucher {{ $code ? '('.$code.')' : '' }}
                </span>
                <span class="fw-bold">-{{ number_format($discount) }}đ</span>
            </div>
            <div class="border-top mb-3" style="border-style: dashed !important; border-color: #cbd5e1 !important;"></div>
        @endif

        <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded-3 mt-2 border">
            <span class="text-dark fw-bold text-uppercase" style="letter-spacing: 1px;">{{ __('profile.total') ?? 'TỔNG CỘNG' }}</span>
            <span class="fs-3 fw-bold text-primary">{{ number_format($finalTotal) }}đ</span>
        </div>
    </div>

    {{-- 5. INFO / QR AREA --}}
    <div class="text-center mt-4">
        <div class="d-inline-block" style="cursor: pointer;" onclick="const icon = this.querySelector('.qr-icon'); const real = this.querySelector('.qr-real'); if(icon) icon.style.display='none'; if(real) real.style.display='block';">
            
            {{-- Nut Click Hien QR --}}
            <div class="qr-icon btn btn-light border rounded-pill px-4 py-2 shadow-sm">
                <i class="bi bi-qr-code-scan text-primary me-2 fs-5 align-middle"></i>
                <span class="fw-bold text-dark align-middle" style="font-size: 0.9rem;">
                    {{ __('profile.click_to_get_qr') ?? 'Hiển thị mã QR Check-in' }}
                </span>
            </div>
            
            {{-- Ma QR that --}}
            <div class="qr-real" style="display: none;">
                <p class="mb-2 small fw-bold text-dark text-uppercase">{{ __('profile.show_qr_to_staff') ?? 'Đưa mã này cho nhân viên' }}</p>
                <div class="bg-white d-inline-block p-2 rounded shadow-sm border">
                    @php $qrData = route('ticket.scan', $order->id); @endphp
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(120)->margin(0)->generate($qrData) !!}
                </div>
            </div>
        </div>
    </div>

    {{-- 6. REFUND BUTTON --}}
    @if($order->status == 'paid' || $order->status == 'pending')
        <div class="mt-4 text-center border-top pt-3" style="border-style: dashed !important; border-color: #cbd5e1 !important;">
            <button class="btn btn-outline-danger px-4 rounded-pill fw-bold shadow-sm" onclick="confirmRefund({{ $order->id }})">
                <i class="bi bi-arrow-return-left me-1"></i> {{ __('profile.refund_ticket') ?? 'YÊU CẦU HOÀN VÉ' }}
            </button>
            <div class="mt-2 text-muted" style="font-size: 0.7rem;">Hoàn vé hợp lệ trong vòng 24H từ khi đặt</div>
        </div>
    @endif
</div>