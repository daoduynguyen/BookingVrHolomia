@extends('pos.layout')

@section('title', 'Bán vé thành công')
@section('page-title', 'Thanh toán thành công')

@section('styles')
<style>
    .success-layout { display: grid; grid-template-columns: 1fr 360px; gap: 20px; }

    .bill-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .bill-header {
        background: linear-gradient(135deg, var(--pos-primary), #4f46e5);
        padding: 20px 24px;
        text-align: center;
    }
    .bill-header .success-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 8px;
    }
    .bill-header h2 { font-size: 1.2rem; font-weight: 800; margin: 0 0 4px; }
    .bill-header p { font-size: 0.82rem; opacity: .8; margin: 0; }

    .bill-body { padding: 20px 24px; }

    .bill-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 0.85rem;
        border-bottom: 1px solid rgba(255,255,255,.04);
    }
    .bill-row:last-child { border-bottom: none; }
    .bill-row .label { color: var(--pos-text-muted); }
    .bill-row .val { font-weight: 600; }

    .bill-total {
        background: rgba(124,58,237,.1);
        border: 1px solid rgba(124,58,237,.2);
        border-radius: 10px;
        padding: 14px 20px;
        margin-top: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .bill-total .label { font-size: 0.85rem; color: var(--pos-text-muted); font-weight: 600; }
    .bill-total .val { font-size: 1.4rem; font-weight: 800; color: #a78bfa; }

    .payment-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .payment-cash     { background: rgba(16,185,129,.15); color: #34d399; }
    .payment-card     { background: rgba(99,102,241,.15); color: #818cf8; }
    .payment-transfer { background: rgba(6,182,212,.15);  color: #22d3ee; }

    .qr-section {
        text-align: center;
        padding: 16px;
        border-top: 1px solid var(--pos-card-border);
    }
    .qr-section img {
        border-radius: 8px;
        border: 4px solid #fff;
    }
    .qr-section .qr-label {
        font-size: 0.72rem;
        color: var(--pos-text-muted);
        margin-top: 8px;
    }

    .action-panel {
        position: sticky;
        top: 80px;
    }
    .action-panel .pos-card { margin-bottom: 12px    /* PRINT RECEIPT */
    @media print {
        @page { 
            margin: 0; 
            size: 80mm auto; 
        }
        .pos-sidebar, .pos-topbar, .action-panel, .no-print { display: none !important; }
        .pos-main { margin: 0 !important; width: 80mm !important; }
        .pos-content { padding: 0 !important; }
        .success-layout { display: block !important; }
        body { 
            background: #fff !important; 
            color: #000 !important; 
            width: 80mm !important; 
            margin: 0 !important;
            padding: 0 !important;
            font-size: 11pt !important;
        }
        .bill-card { 
            border: none !important; 
            border-radius: 0 !important; 
            width: 100% !important;
            box-shadow: none !important;
        }
        .bill-header { 
            background: #fff !important; 
            padding: 10mm 5mm !important; 
            border-bottom: 2px dashed #000 !important; 
            color: #000 !important;
        }
        .bill-header h2 { font-size: 14pt !important; }
        .bill-row { padding: 2mm 0 !important; border-bottom: 1px dotted #ccc !important; }
        .bill-row .label { color: #000 !important; }
        .bill-total { 
            background: #fff !important; 
            border: 2px solid #000 !important; 
            color: #000 !important;
            margin-top: 5mm !important;
        }
        .bill-total .val { color: #000 !important; font-size: 16pt !important; }
        .qr-section { border-top: 2px dashed #000 !important; margin-top: 5mm !important; }
        .qr-section img { border: 1px solid #000 !important; width: 40mm !important; height: 40mm !important; }
    }
</style>
@endsection

@section('content')

<div class="success-layout">

    {{-- BILL --}}
    <div>
        <div class="bill-card" id="print-area">
            <div class="bill-header">
                <span class="success-icon no-print">✅</span>
                <h2>THÔNG TIN VÉ</h2>
                <p>Mã đơn #{{ $order->id }}</p>
                <div class="fs-7 mt-1">{{ now()->format('H:i d/m/Y') }}</div>
            </div>
            <div class="bill-body">
                <div class="bill-row">
                    <span class="label">Khách hàng</span>
                    <span class="val">{{ $order->customer_name }}</span>
                </div>
                <div class="bill-row">
                    <span class="label">SĐT</span>
                    <span class="val">{{ $order->customer_phone }}</span>
                </div>
                <div class="bill-row">
                    <span class="label">Vé</span>
                    <span class="val">{{ $order->slot->ticket->name ?? '' }}</span>
                </div>
                <div class="bill-row">
                    <span class="label">Giờ chơi</span>
                    <span class="val">
                        {{ \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') }} –
                        {{ \Carbon\Carbon::parse($order->slot->end_time)->format('H:i') }}
                    </span>
                </div>
                <div class="bill-row">
                    <span class="label">Ngày</span>
                    <span class="val">{{ \Carbon\Carbon::parse($order->slot->date)->format('d/m/Y') }}</span>
                </div>
                <div class="bill-row">
                    <span class="label">Số lượng</span>
                    <span class="val">{{ $order->quantity ?? 1 }} người</span>
                </div>
                
                @if($order->discount_amount > 0)
                <div class="bill-row text-danger">
                    <span class="label text-danger">Giảm giá</span>
                    <span class="val">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                </div>
                @endif
                
                <div class="bill-row">
                    <span class="label">Thanh toán</span>
                    <span>
                        @php $pm = $order->payment_method; @endphp
                        <span class="payment-badge payment-{{ $pm }}">
                            @if($pm === 'cash') 🏧 Tiền mặt
                            @elseif($pm === 'card') 💳 Thẻ POS
                            @else 📱 Chuyển khoản
                            @endif
                        </span>
                    </span>
                </div>

                <div class="bill-total">
                    <div class="label">TỔNG TIỀN</div>
                    <div class="val">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>
                </div>
            </div>

            {{-- QR CODE --}}
            <div class="qr-section">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $order->qr_token }}&bgcolor=ffffff&color=000000"
                     alt="QR Check-in" width="150" height="150">
                <div class="qr-label">
                    QUÉT QR ĐỂ CHECK-IN VÀO CHƠI
                </div>
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