@extends('pos.layout')

@section('title', 'Bán vé thành công')
@section('page-title', 'Thanh toán thành công')

@section('styles')
<style>
    .success-layout { display: grid; grid-template-columns: 1fr 380px; gap: 24px; max-width: 1100px; margin: 0 auto; }

    .bill-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .bill-header {
        background: rgba(var(--pos-primary-rgb), 0.05);
        padding: 30px 24px;
        text-align: center;
        border-bottom: 1px dashed var(--pos-card-border);
    }
    .bill-header .success-icon {
        font-size: 3.5rem;
        display: block;
        margin-bottom: 12px;
        filter: drop-shadow(0 4px 10px rgba(52, 211, 153, 0.3));
    }
    .bill-header h2 { font-size: 1.4rem; font-weight: 900; margin: 0 0 6px; color: var(--pos-text); letter-spacing: 1px; }
    .bill-header p { font-size: 0.9rem; color: var(--pos-text-muted); margin: 0; font-weight: 500; }

    .bill-body { padding: 30px 40px; }

    .bill-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 12px 0;
        font-size: 0.95rem;
        border-bottom: 1px solid rgba(255,255,255,.05);
    }
    .bill-row:last-child { border-bottom: none; }
    .bill-row .label { color: var(--pos-text-muted); font-weight: 500; }
    .bill-row .val { font-weight: 700; color: var(--pos-text); text-align: right; }

    .bill-item-box {
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 15px;
        margin: 20px 0;
    }

    .bill-total {
        background: var(--pos-primary);
        color: #fff;
        border-radius: 14px;
        padding: 18px 24px;
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 15px rgba(var(--pos-primary-rgb), 0.3);
    }
    .bill-total .label { font-size: 1rem; font-weight: 700; color: rgba(255,255,255,0.9); }
    .bill-total .val { font-size: 1.6rem; font-weight: 900; }

    .qr-section {
        text-align: center;
        padding: 30px;
        background: #fff;
        margin: 0 40px 30px;
        border-radius: 16px;
    }
    .qr-section img {
        margin-bottom: 15px;
    }
    .qr-section .qr-label {
        font-size: 0.8rem;
        color: #000;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    /* PRINT RECEIPT OPTIMIZATION (80mm) */
    @media print {
        @page { margin: 0; size: 80mm auto; }
        body { margin: 0; padding: 0; width: 80mm; background: #fff !important; color: #000 !important; font-family: 'Courier New', Courier, monospace !important; }
        .no-print, .pos-sidebar, .pos-topbar, .action-panel { display: none !important; }
        
        .success-layout { display: block !important; width: 80mm !important; margin: 0 !important; padding: 0 !important; }
        .bill-card { border: none !important; box-shadow: none !important; width: 80mm !important; border-radius: 0 !important; background: #fff !important; }
        
        .bill-header { background: #fff !important; padding: 5mm 0 !important; border-bottom: 2px solid #000 !important; }
        .bill-header h2 { font-size: 16pt !important; color: #000 !important; }
        .bill-header p { font-size: 10pt !important; color: #000 !important; }
        
        .bill-body { padding: 4mm 2mm !important; }
        .bill-row { border-bottom: 1px dashed #000 !important; padding: 2mm 0 !important; font-size: 10pt !important; }
        .bill-row .label, .bill-row .val { color: #000 !important; font-weight: bold !important; }
        
        .bill-item-box { border: 1px solid #000 !important; margin: 3mm 0 !important; background: #fff !important; }
        
        .bill-total { background: #000 !important; color: #fff !important; padding: 4mm !important; margin-top: 4mm !important; -webkit-print-color-adjust: exact; }
        .bill-total .label { color: #fff !important; font-size: 11pt !important; }
        .bill-total .val { color: #fff !important; font-size: 18pt !important; }
        
        .qr-section { margin: 5mm 0 !important; padding: 0 !important; border: none !important; }
        .qr-section img { width: 45mm !important; height: 45mm !important; border: 1px solid #000; }
        .qr-label { font-size: 9pt !important; margin-top: 2mm !important; }
        
        .print-divider { border-top: 2px dashed #000; margin: 5mm 0; }
        .print-footer { text-align: center; font-size: 9pt; margin-top: 5mm; font-weight: bold; }
    }
</style>
</style>
@endsection

@section('content')

<div class="success-layout">

    <div>
        <div class="bill-card" id="print-area">
            {{-- HEADER (PHẦN IN) --}}
            <div class="bill-header">
                <h2 style="text-transform: uppercase;">{{ $location->name }}</h2>
                <p style="margin-bottom: 2px;">{{ $location->address ?? 'Địa chỉ đang cập nhật' }}</p>
                <p>Hotline: {{ $location->hotline ?? 'Hotline đang cập nhật' }}</p>
                
                <div class="print-divider no-print" style="margin: 15px 0; border-top: 1px dashed var(--pos-card-border); opacity: 0.5;"></div>
                
                <p class="mt-3" style="font-size: 1.1rem; color: var(--pos-text); font-weight: 800;">HÓA ĐƠN BÁN VÉ</p>
                <p>Mã đơn: #{{ $order->id }}</p>
                <p>{{ now()->format('H:i - d/m/Y') }}</p>
            </div>

            <div class="bill-body">
                <div class="bill-row">
                    <span class="label">Khách hàng:</span>
                    <span class="val">{{ $order->customer_name }}</span>
                </div>
                <div class="bill-row">
                    <span class="label">Số điện thoại:</span>
                    <span class="val">{{ $order->customer_phone }}</span>
                </div>

                <div class="bill-item-box">
                    <div style="font-size: 1.1rem; font-weight: 800; margin-bottom: 8px; color: var(--pos-primary);">
                        {{ $order->slot->ticket->name ?? 'Dịch vụ VR' }}
                    </div>
                    <div style="font-size: 0.85rem; display: flex; flex-direction: column; gap: 4px;">
                        <span><i class="bi bi-calendar-event me-1"></i> Ngày: {{ \Carbon\Carbon::parse($order->slot->date)->format('d/m/Y') }}</span>
                        <span><i class="bi bi-clock me-1"></i> Khung giờ: {{ \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($order->slot->end_time)->format('H:i') }}</span>
                    </div>
                </div>

                <div class="bill-row">
                    <span class="label">Số lượng:</span>
                    <span class="val">{{ $order->quantity ?? 1 }} người</span>
                </div>
                
                @php 
                    $pricePerPerson = $order->slot->ticket->price ?? 0;
                    $totalBeforeDiscount = $pricePerPerson * ($order->quantity ?? 1);
                @endphp
                <div class="bill-row">
                    <span class="label">Đơn giá:</span>
                    <span class="val">{{ number_format($pricePerPerson, 0, ',', '.') }}₫</span>
                </div>

                @if($order->discount_amount > 0)
                <div class="bill-row">
                    <span class="label">Giảm giá:</span>
                    <span class="val text-danger">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                </div>
                @endif
                
                <div class="bill-row">
                    <span class="label">Hình thức:</span>
                    <span class="val">
                        @php $pm = $order->payment_method; @endphp
                        @if($pm === 'cash') 💵 Tiền mặt
                        @elseif($pm === 'card') 💳 Thẻ POS
                        @else 📱 Chuyển khoản
                        @endif
                    </span>
                </div>

                <div class="bill-total">
                    <div class="label">THÀNH TIỀN</div>
                    <div class="val">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>
                </div>
            </div>

            {{-- FOOTER QR --}}
            <div class="qr-section">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ $order->qr_token }}&bgcolor=ffffff&color=000000"
                     alt="QR Check-in">
                <div class="qr-label">
                    VUI LÒNG QUÉT QR ĐỂ VÀO CHƠI
                </div>
                <div class="print-footer mt-4" style="font-size: 0.85rem; text-align: center; color: var(--pos-text-muted);">
                    --- Hẹn gặp lại quý khách ---
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