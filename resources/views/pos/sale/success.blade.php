@extends('pos.layout')

@section('title', 'Bán vé thành công')
@section('page-title', 'Thanh toán thành công')

@section('styles')
<style>
    .success-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; max-width: 1100px; margin: 0 auto; }

    /* ===== THERMAL RECEIPT (giống profile/order_invoice) ===== */
    .thermal-receipt {
        width: 100%;
        max-width: 80mm;
        margin: 0 auto;
        background: #fff;
        color: #111827;
        font-family: 'Be Vietnam Pro', sans-serif;
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
    .items-head {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        align-items: start;
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
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 10px;
        align-items: start;
        margin-top: 8px;
        padding: 10px 0 0;
        border-top: 2px solid #111827;
        font-size: 13px;
        font-weight: 900;
    }
    .receipt-total .value { font-size: 16px; color: #0ea5e9; }
    .receipt-qr {
        text-align: center;
        margin-top: 10px;
    }
    .receipt-note {
        text-align: center;
        font-size: 10px;
        color: #64748b;
        margin-top: 10px;
    }

    /* PRINT */
    @media print {
        @page { size: 80mm auto; margin: 4mm; }
        body { background: #fff !important; }
        body * { visibility: hidden !important; }
        #print-area, #print-area * { visibility: visible !important; }
        #print-area {
            position: absolute !important;
            left: 0 !important; top: 0 !important;
            width: 72mm !important; max-width: 72mm !important;
            border: none !important; border-radius: 0 !important;
            padding: 0 !important; margin: 0 !important;
        }
        .no-print { display: none !important; }
        .thermal-receipt { border: none !important; box-shadow: none !important; }
        .receipt-brand h4 { color: #000 !important; }
        .receipt-row .label, .receipt-row .value,
        .receipt-item .name, .receipt-item .meta,
        .receipt-note { color: #000 !important; }
        .receipt-total { border-top-color: #000 !important; }
        .receipt-total .value { color: #000 !important; }
        .success-layout { display: block !important; }
        .action-panel { display: none !important; }
    }
</style>
@endsection

@section('content')

<div class="success-layout">

    <div>
        <div class="thermal-receipt" id="print-area">

            {{-- HEADER --}}
            <div class="receipt-brand">
                <h4>{{ strtoupper($location->name) }}</h4>
                <div class="sub">PHIẾU THANH TOÁN / CHECK-IN</div>
                <div class="sub">Mã đơn #{{ $order->id }}</div>
                @if($location->address)
                    <div class="sub">{{ $location->address }}</div>
                @endif
                @if($location->hotline)
                    <div class="sub">Hotline: {{ $location->hotline }}</div>
                @endif
            </div>

            {{-- META --}}
            <div class="receipt-meta">
                <div class="receipt-row">
                    <span class="label">Khách hàng</span>
                    <span class="value">{{ $order->customer_name }}</span>
                </div>
                <div class="receipt-row">
                    <span class="label">Số điện thoại</span>
                    <span class="value">{{ $order->customer_phone }}</span>
                </div>
                <div class="receipt-row">
                    <span class="label">Ngày chơi</span>
                    <span class="value">{{ \Carbon\Carbon::parse($order->slot->date)->format('d/m/Y') }}</span>
                </div>
                <div class="receipt-row">
                    <span class="label">Khung giờ</span>
                    <span class="value">
                        {{ \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') }}
                        – {{ \Carbon\Carbon::parse($order->slot->end_time)->format('H:i') }}
                    </span>
                </div>
                <div class="receipt-row">
                    <span class="label">Thanh toán</span>
                    <span class="value">
                        @php $pm = $order->payment_method; @endphp
                        @if($pm === 'cash') Tiền mặt
                        @elseif($pm === 'card') Thẻ POS
                        @else Chuyển khoản
                        @endif
                    </span>
                </div>
            </div>

            <div class="receipt-divider"></div>

            {{-- ITEMS --}}
            <div class="items-head">
                <div>Vé / Dịch vụ</div>
                <div>Thành tiền</div>
            </div>

            @php
                $pricePerPerson = $order->slot->ticket->price ?? 0;
                $qty = $order->quantity ?? 1;
                $subtotal = $pricePerPerson * $qty;
            @endphp

            <div class="receipt-item">
                <div class="receipt-row">
                    <div>
                        <div class="name">{{ $order->slot->ticket->name ?? 'Dịch vụ VR' }}</div>
                        <div class="meta">SL: {{ $qty }} x {{ number_format($pricePerPerson) }}đ</div>
                    </div>
                    <div class="amount">{{ number_format($subtotal) }}đ</div>
                </div>
            </div>

            <div class="receipt-divider"></div>

            {{-- SUMMARY --}}
            <div class="receipt-summary">
                <div class="receipt-row">
                    <span class="label">Tạm tính</span>
                    <span class="value">{{ number_format($subtotal) }}đ</span>
                </div>
                @if($order->discount_amount > 0)
                <div class="receipt-row">
                    <span class="label">Giảm giá</span>
                    <span class="value" style="color:#dc2626">-{{ number_format($order->discount_amount) }}đ</span>
                </div>
                @endif
                <div class="receipt-total">
                    <span>TỔNG CỘNG</span>
                    <span class="value">{{ number_format($order->total_amount) }}đ</span>
                </div>
            </div>

            {{-- QR --}}
            <div class="receipt-qr">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $order->qr_token }}&bgcolor=ffffff&color=000000"
                     alt="QR Check-in" style="width:120px;height:120px;margin-top:10px;">
                <div class="text-muted small mt-2">Nhấn để xem mã QR check-in</div>
            </div>

            <div class="receipt-note">
                Vui lòng giữ phiếu này cho đến khi hoàn tất trải nghiệm.
            </div>

        </div>
    </div>

    {{-- ACTIONS --}}
    <div class="action-panel no-print">
        <div class="pos-card shadow-sm">
            <div class="pos-card-title"><i class="bi bi-printer me-1"></i>Hành động</div>
            <button onclick="window.print()" class="btn-pos w-100 mb-3" style="justify-content:center; padding: 12px">
                <i class="bi bi-printer"></i> IN VÉ (80MM)
            </button>
            <div style="font-size:0.75rem;color:var(--pos-text-muted);text-align:center">
                <i class="bi bi-info-circle me-1"></i>
                Hỗ trợ in qua máy in nhiệt Bluetooth/USB
            </div>
        </div>

        <div class="pos-card shadow-sm">
            <div class="pos-card-title"><i class="bi bi-arrow-right-circle me-1"></i>Tiếp theo</div>

            <a href="{{ route('pos.dashboard', $subdomain) }}" class="btn-success-pos w-100 mb-2" style="justify-content:center">
                <i class="bi bi-grid-1x2"></i> VỀ TRANG CHỦ
            </a>

            <a href="{{ route('pos.checkin', $subdomain) }}" class="btn-pos-outline w-100 mb-2" style="justify-content:center">
                <i class="bi bi-qr-code-scan"></i> QUÉT QR CHECK-IN
            </a>

            <a href="{{ route('pos.sale.form', [$subdomain, $order->slot_id]) }}" class="btn-pos-outline w-100" style="justify-content:center">
                <i class="bi bi-plus-circle"></i> TIẾP TỤC BÁN VÉ
            </a>
        </div>
    </div>
</div>

@endsection