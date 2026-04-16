@extends('pos.layout')

@section('title', 'Bán vé — ' . ($slot->ticket->name ?? ''))
@section('page-title', 'Bán vé tại quầy')

@section('styles')
<style>
    .sale-layout { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }

    .payment-btns { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; }
    .payment-btn {
        padding: 12px 8px;
        border-radius: 10px;
        border: 2px solid var(--pos-card-border);
        background: rgba(255,255,255,.03);
        color: var(--pos-text-muted);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        transition: all .15s;
    }
    .payment-btn:hover { border-color: var(--pos-primary); color: #c4b5fd; }
    .payment-btn.selected {
        border-color: var(--pos-primary);
        background: rgba(124,58,237,.15);
        color: #c4b5fd;
    }
    .payment-btn i { font-size: 1.3rem; display: block; margin-bottom: 4px; }

    .order-summary {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 20px;
        position: sticky;
        top: 80px;
    }
    .order-summary-title { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing:.06em; color: var(--pos-text-muted); margin-bottom: 16px; }

    .summary-row { display: flex; justify-content: space-between; font-size: 0.83rem; margin-bottom: 8px; }
    .summary-row .label { color: var(--pos-text-muted); }
    .summary-divider { border: none; border-top: 1px solid var(--pos-card-border); margin: 12px 0; }
    .summary-total { font-size: 1.2rem; font-weight: 800; color: #a78bfa; }

    .customer-found-card {
        background: rgba(16,185,129,.1);
        border: 1px solid rgba(16,185,129,.3);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.82rem;
        margin-top: 8px;
        display: none;
    }

    .qty-control { display: flex; align-items: center; gap: 12px; }
    .qty-control button {
        width: 34px; height: 34px;
        border-radius: 8px;
        border: 1px solid var(--pos-card-border);
        background: rgba(255,255,255,.05);
        color: var(--pos-text);
        font-size: 1.1rem;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
    }
    .qty-control button:hover { background: rgba(124,58,237,.2); border-color: var(--pos-primary); }
    .qty-control .qty-val { font-size: 1.1rem; font-weight: 700; min-width: 30px; text-align: center; }
</style>
@endsection

@section('content')

<div style="margin-bottom:16px">
    <a href="{{ route('pos.slot.detail', [$subdomain, $slot->id]) }}" class="btn-pos-outline">
        <i class="bi bi-arrow-left"></i> Quay lại
    </a>
</div>

<form action="{{ route('pos.sale.store', $subdomain) }}" method="POST" id="saleForm">
@csrf
<input type="hidden" name="slot_id" value="{{ $slot->id }}">
<input type="hidden" name="user_id" id="user_id_input" value="">
<input type="hidden" name="payment_method" id="payment_method_input" value="cash">

<div class="sale-layout">

    {{-- FORM LEFT --}}
    <div>

        {{-- THÔNG TIN KHÁCH --}}
        <div class="pos-card mb-4">
            <div class="pos-card-title"><i class="bi bi-person me-1"></i>Thông tin khách hàng</div>

            <div class="pos-form-group">
                <label>Số điện thoại</label>
                <div style="display:flex;gap:8px">
                    <input type="tel" name="customer_phone" id="phone_input"
                           placeholder="0912 345 678"
                           style="flex:1"
                           oninput="debounceSearch(this.value)"
                           required>
                    <button type="button" onclick="searchCustomer()" class="btn-pos" style="padding:0 14px;flex-shrink:0">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <div class="customer-found-card" id="customer-card">
                    <div style="display:flex;align-items:center;gap:8px">
                        <i class="bi bi-person-check-fill" style="color:#34d399"></i>
                        <div>
                            <div style="font-weight:700" id="cust-name-display"></div>
                            <div style="color:var(--pos-text-muted);font-size:0.72rem">
                                Tích điểm: <span id="cust-points">0</span> điểm
                                <span id="cust-redeem-info"></span>
                            </div>
                        </div>
                        <button type="button" onclick="clearCustomer()" style="margin-left:auto;background:none;border:none;color:#f87171;cursor:pointer;font-size:1rem">✕</button>
                    </div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="pos-form-group">
                    <label>Họ tên <span style="color:#f87171">*</span></label>
                    <input type="text" name="customer_name" id="customer_name_input"
                           placeholder="Nguyễn Văn A" required>
                </div>
                <div class="pos-form-group">
                    <label>Email</label>
                    <input type="email" name="customer_email" placeholder="(tuỳ chọn)">
                </div>
            </div>
        </div>

        {{-- SỐ LƯỢNG --}}
        <div class="pos-card mb-4">
            <div class="pos-card-title"><i class="bi bi-people me-1"></i>Số lượng vé</div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div style="font-size:0.83rem;color:var(--pos-text-muted)">
                        Còn trống: <strong style="color:#34d399">{{ $slot->capacity - $slot->booked_count }}</strong> chỗ
                    </div>
                </div>
                <div class="qty-control">
                    <button type="button" onclick="changeQty(-1)"><i class="bi bi-dash"></i></button>
                    <div class="qty-val" id="qty-display">1</div>
                    <button type="button" onclick="changeQty(1)"><i class="bi bi-plus"></i></button>
                    <input type="hidden" name="quantity" id="qty_input" value="1">
                </div>
            </div>
        </div>

        {{-- THANH TOÁN --}}
        <div class="pos-card">
            <div class="pos-card-title"><i class="bi bi-credit-card me-1"></i>Phương thức thanh toán</div>
            <div class="payment-btns">
                <div class="payment-btn selected" data-method="cash" onclick="selectPayment('cash')">
                    <i class="bi bi-cash-coin"></i> Tiền mặt
                </div>
                <div class="payment-btn" data-method="card" onclick="selectPayment('card')">
                    <i class="bi bi-credit-card-2-front"></i> Thẻ POS
                </div>
                <div class="payment-btn" data-method="transfer" onclick="selectPayment('transfer')">
                    <i class="bi bi-qr-code"></i> Chuyển khoản
                </div>
            </div>
        </div>
    </div>

    {{-- ORDER SUMMARY --}}
    <div>
        <div class="order-summary">
            <div class="order-summary-title"><i class="bi bi-receipt me-1"></i>Thông tin vé</div>

            <div class="summary-row">
                <span class="label">Loại vé</span>
                <span>{{ $slot->ticket->name ?? '' }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Thời gian</span>
                <span>{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Ngày</span>
                <span>{{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}</span>
            </div>
            <div class="summary-row">
                <span class="label">Đơn giá</span>
                <span>{{ number_format($slot->ticket->price ?? 0, 0, ',', '.') }}₫</span>
            </div>
            <div class="summary-row">
                <span class="label">Số lượng</span>
                <span id="summary-qty">1</span>
            </div>

            <hr class="summary-divider">

            <div class="summary-row">
                <span class="label" style="font-weight:700">Tổng cộng</span>
                <span class="summary-total" id="summary-total">{{ number_format($slot->ticket->price ?? 0, 0, ',', '.') }}₫</span>
            </div>

            <div style="margin-top:20px">
                <button type="submit" class="btn-pos w-100" style="justify-content:center;padding:12px;font-size:0.95rem">
                    <i class="bi bi-check2-all"></i> Xác nhận thanh toán
                </button>
            </div>

            <div style="margin-top:10px">
                <a href="{{ route('pos.slot.detail', [$subdomain, $slot->id]) }}" class="btn-pos-outline w-100" style="justify-content:center">
                    Huỷ
                </a>
            </div>
        </div>
    </div>
</div>
</form>

@endsection

@section('scripts')
<script>
const unitPrice = {{ $slot->ticket->price ?? 0 }};
const maxQty    = {{ $slot->capacity - $slot->booked_count }};
let currentQty  = 1;

function changeQty(delta) {
    currentQty = Math.max(1, Math.min(maxQty, currentQty + delta));
    document.getElementById('qty-display').textContent = currentQty;
    document.getElementById('qty_input').value = currentQty;
    document.getElementById('summary-qty').textContent = currentQty;
    const total = unitPrice * currentQty;
    document.getElementById('summary-total').textContent = total.toLocaleString('vi-VN') + '₫';
}

function selectPayment(method) {
    document.querySelectorAll('.payment-btn').forEach(b => b.classList.remove('selected'));
    document.querySelector(`[data-method="${method}"]`).classList.add('selected');
    document.getElementById('payment_method_input').value = method;
}

// Tìm khách theo SĐT
let searchTimeout;
function debounceSearch(val) {
    clearTimeout(searchTimeout);
    if (val.length >= 9) searchTimeout = setTimeout(() => searchCustomer(), 600);
}

function searchCustomer() {
    const phone = document.getElementById('phone_input').value.trim();
    if (phone.length < 9) return;

    fetch(`{{ route('pos.find.customer', $subdomain) }}?phone=` + encodeURIComponent(phone))
        .then(r => r.json())
        .then(data => {
            if (data.found) {
                document.getElementById('customer_name_input').value = data.name;
                document.getElementById('user_id_input').value = data.id;
                document.getElementById('cust-name-display').textContent = data.name;
                document.getElementById('cust-points').textContent = data.points;
                if (data.points > 0) {
                    document.getElementById('cust-redeem-info').textContent = ` (tương đương ${(data.points * 1000).toLocaleString('vi-VN')}₫)`;
                }
                document.getElementById('customer-card').style.display = 'block';
            }
        });
}

function clearCustomer() {
    document.getElementById('user_id_input').value = '';
    document.getElementById('customer-card').style.display = 'none';
}
</script>
@endsection