@php
    $orderItems = $order->orderItems ?? collect();
    $subtotal = $orderItems->sum(fn ($item) => $item->price * $item->quantity);
    $discount = max(0, $subtotal - $order->total_amount);
    $slot = $order->slot;
    $paymentLabel = match($order->payment_method) {
        'wallet' => __('profile.payment_wallet') ?? 'Ví Holomia',
        'banking' => __('profile.payment_banking') ?? 'Chuyển khoản',
        default => __('profile.payment_cash') ?? 'Tại quầy',
    };
@endphp

<style>
    .thermal-receipt {
        width: 100%;
        max-width: 80mm;
        margin: 0 auto;
        background: #fff;
        color: #111827;
        font-family: {{ config('i18n.supported.' . app()->getLocale())['font_family'] ?? "'Be Vietnam Pro', sans-serif" }};
        font-size: 12px;
        line-height: 1.35;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
    }
    .receipt-brand {
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 1px dashed #cbd5e1;
        margin-bottom: 10px;
    }
    .receipt-brand h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 900;
        letter-spacing: 1px;
        color: #0f172a;
    }
    .receipt-brand .sub {
        font-size: 10px;
        color: #64748b;
        margin-top: 3px;
    }
    .receipt-meta, .receipt-summary {
        display: grid;
        gap: 6px;
        margin-bottom: 10px;
    }
    .receipt-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
    .receipt-row .label {
        color: #64748b;
        flex: 0 0 42%;
    }
    .receipt-row .value {
        text-align: right;
        font-weight: 700;
        flex: 1;
        word-break: break-word;
    }
    .receipt-divider {
        border-top: 1px dashed #cbd5e1;
        margin: 10px 0;
    }
    .items-head, .receipt-total {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        align-items: start;
    }
    .items-head {
        padding-bottom: 6px;
        margin-bottom: 6px;
        border-bottom: 1px solid #e5e7eb;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        font-weight: 700;
    }
    .receipt-item {
        padding: 7px 0;
        border-bottom: 1px dotted #e5e7eb;
    }
    .receipt-item:last-child { border-bottom: 0; }
    .receipt-item .name { font-weight: 700; color: #111827; }
    .receipt-item .meta { font-size: 10px; color: #64748b; margin-top: 2px; }
    .receipt-item .amount { text-align: right; font-weight: 800; color: #111827; white-space: nowrap; }
    .receipt-total {
        margin-top: 8px;
        padding: 10px 0 0;
        border-top: 2px solid #111827;
        font-size: 13px;
        font-weight: 900;
    }
    .receipt-total .value { font-size: 16px; color: #0ea5e9; }
    .receipt-note {
        text-align: center;
        font-size: 10px;
        color: #64748b;
        margin-top: 10px;
    }
    .receipt-qr {
        text-align: center;
        margin-top: 10px;
    }
    .receipt-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 12px;
    }
    @media print {
        @page { size: 80mm auto; margin: 4mm; }
        body { background: #fff !important; }
        body * { visibility: hidden !important; }
        #print-area, #print-area * { visibility: visible !important; }
        #print-area {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 72mm !important;
            max-width: 72mm !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print { display: none !important; }
        .thermal-receipt { border: none !important; box-shadow: none !important; }
        .receipt-brand h4 { color: #000 !important; }
        .receipt-row .label, .receipt-row .value, .receipt-item .name, .receipt-item .meta, .receipt-note { color: #000 !important; }
        .receipt-total { border-top-color: #000 !important; }
        .receipt-total .value { color: #000 !important; }
    }
</style>

<div class="thermal-receipt" id="print-area">
    <div class="receipt-brand">
        <h4>HOLOMIA VR</h4>
        <div class="sub">PHIẾU THANH TOÁN / CHECK-IN</div>
        <div class="sub">Mã đơn #{{ $order->id }}</div>
    </div>

    <div class="receipt-meta">
        <div class="receipt-row">
            <span class="label">Khách hàng</span>
            <span class="value">{{ $order->customer_name }}</span>
        </div>
        <div class="receipt-row">
            <span class="label">Cơ sở</span>
            <span class="value">{{ $order->location->name ?? '---' }}</span>
        </div>
        <div class="receipt-row">
            <span class="label">Ngày chơi</span>
            <span class="value">{{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</span>
        </div>
        <div class="receipt-row">
            <span class="label">Khung giờ</span>
            <span class="value">{{ $slot ? substr($slot->start_time, 0, 5) . ' - ' . substr($slot->end_time, 0, 5) : '--:--' }}</span>
        </div>
        <div class="receipt-row">
            <span class="label">Thanh toán</span>
            <span class="value">{{ $paymentLabel }}</span>
        </div>
    </div>

    <div class="receipt-divider"></div>

    <div class="items-head">
        <div>Vé / Dịch vụ</div>
        <div>Thành tiền</div>
    </div>

    @foreach($orderItems as $item)
        <div class="receipt-item">
            <div class="receipt-row">
                <div>
                    <div class="name">{{ $item->ticket_name }}</div>
                    <div class="meta">SL: {{ $item->quantity }} x {{ number_format($item->price) }}đ</div>
                </div>
                <div class="amount">{{ number_format($item->price * $item->quantity) }}đ</div>
            </div>
        </div>
    @endforeach

    <div class="receipt-divider"></div>

    <div class="receipt-summary">
        <div class="receipt-row">
            <span class="label">Tạm tính</span>
            <span class="value">{{ number_format($subtotal) }}đ</span>
        </div>
        @if($discount > 0)
        <div class="receipt-row">
            <span class="label">Giảm giá</span>
            <span class="value" style="color:#dc2626">-{{ number_format($discount) }}đ</span>
        </div>
        @endif
        <div class="receipt-total">
            <span>TỔNG CỘNG</span>
            <span class="value">{{ number_format($order->total_amount) }}đ</span>
        </div>
    </div>

    <div class="receipt-qr no-print">
        <div class="text-muted small mb-2">Nhấn để xem mã QR check-in</div>
        <div class="d-inline-block" style="cursor:pointer;" onclick="const icon=this.querySelector('.qr-icon'); const real=this.querySelector('.qr-real'); if(icon) icon.style.display='none'; if(real) real.style.display='block';">
            <div class="qr-icon btn btn-light border rounded-pill px-3 py-2 shadow-sm">
                <i class="bi bi-qr-code-scan text-primary me-1"></i>
                <span class="fw-bold text-dark" style="font-size:0.85rem;">Hiển thị QR</span>
            </div>
            <div class="qr-real" style="display:none; margin-top:8px;">
                @php $qrData = route('ticket.scan', $order->id); @endphp
                <div class="bg-white d-inline-block p-2 rounded border">
                    {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(112)->margin(0)->generate($qrData) !!}
                </div>
            </div>
        </div>
    </div>

    <div class="receipt-actions no-print">
        <button class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-3" onclick="confirmRefund({{ $order->id }})">
            <i class="bi bi-arrow-return-left me-1"></i>Hoàn vé
        </button>
    </div>

    <div class="receipt-note">
        Vui lòng giữ phiếu này cho đến khi hoàn tất trải nghiệm.
    </div>
</div>