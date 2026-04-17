@extends('pos.layout')

@section('title', 'Lịch sử giao dịch')
@section('page-title', 'Lịch sử giao dịch')

@section('styles')
<style>
    .filter-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .filter-bar input,
    .filter-bar select {
        background: rgba(255,255,255,.05);
        border: 1px solid var(--pos-card-border);
        border-radius: 8px;
        padding: 8px 14px;
        color: var(--pos-text);
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: 0.83rem;
    }
    .filter-bar input:focus,
    .filter-bar select:focus {
        outline: none;
        border-color: var(--pos-primary);
    }
    .filter-bar select option { background: #1a1530; }

    .summary-chips {
        display: flex;
        gap: 10px;
        margin-bottom: 18px;
        flex-wrap: wrap;
    }
    .summary-chip {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 0.78rem;
    }
    .summary-chip .chip-label { color: var(--pos-text-muted); }
    .summary-chip .chip-val   { font-size: 1rem; font-weight: 800; margin-top: 2px; }

    .order-row {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 14px;
        background: rgba(255,255,255,.02);
        border: 1px solid rgba(255,255,255,.05);
        border-radius: 9px;
        margin-bottom: 8px;
        transition: background .15s;
    }
    .order-row:hover { background: rgba(var(--pos-primary-rgb),.07); border-color: rgba(var(--pos-primary-rgb),.2); }

    .order-id {
        font-size: 0.7rem;
        color: var(--pos-text-muted);
        width: 44px;
        flex-shrink: 0;
        text-align: center;
    }

    .order-avatar {
        width: 36px; height: 36px;
        border-radius: 50%;
        background: rgba(var(--pos-primary-rgb),.15);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--pos-primary);
        flex-shrink: 0;
    }

    .order-info { flex: 1; min-width: 0; }
    .order-info .name  { font-size: 0.88rem; font-weight: 700; }
    .order-info .meta  { font-size: 0.72rem; color: var(--pos-text-muted); }

    .order-ticket { flex: 1; min-width: 0; }
    .order-ticket .ticket-name { font-size: 0.82rem; font-weight: 600; }
    .order-ticket .slot-time   { font-size: 0.7rem; color: var(--pos-text-muted); }

    .order-amount { font-size: 0.95rem; font-weight: 800; color: var(--pos-primary); flex-shrink: 0; }

    .order-pttt {
        font-size: 1.1rem;
        flex-shrink: 0;
        width: 28px;
        text-align: center;
    }

    .order-status { flex-shrink: 0; }

    .order-actions { flex-shrink: 0; }

    /* Modal huỷ vé */
    .pos-modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.75);
        z-index: 999;
        display: none;
        align-items: center;
        justify-content: center;
    }
    .pos-modal-backdrop.show { display: flex; }
    .pos-modal {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 14px;
        padding: 24px;
        width: 380px;
        max-width: 95vw;
    }
    .pos-modal-title { font-size: 1rem; font-weight: 800; margin-bottom: 4px; }
    .pos-modal-sub { font-size: 0.78rem; color: var(--pos-text-muted); margin-bottom: 16px; }

    .checkin-dot {
        display: inline-block;
        width: 7px; height: 7px;
        border-radius: 50%;
        margin-right: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--pos-text-muted);
    }
    .empty-state i { font-size: 3rem; display: block; margin-bottom: 12px; }
</style>
@endsection

@section('content')

{{-- SUMMARY CHIPS — tính trên toàn bộ ca (không phải chỉ trang hiện tại) --}}
@php
    use App\Models\Order;
    $baseQuery = Order::where('location_id', $location->id)->where('is_pos_sale', true)->where('status', 'paid');
    if ($activeShift) $baseQuery->where('pos_shift_id', $activeShift->id);
    $cashRev     = (clone $baseQuery)->where('payment_method','cash')->sum('total_amount');
    $cardRev     = (clone $baseQuery)->where('payment_method','card')->sum('total_amount');
    $transferRev = (clone $baseQuery)->where('payment_method','transfer')->sum('total_amount');
@endphp

<div class="summary-chips">
    <div class="summary-chip">
        <div class="chip-label">Vé trong trang này</div>
        <div class="chip-val" style="color:#a78bfa">{{ $orders->count() }} / {{ $orders->total() }}</div>
    </div>
    <div class="summary-chip">
        <div class="chip-label">💵 Tiền mặt</div>
        <div class="chip-val" style="color:#34d399">{{ number_format($cashRev, 0, ',', '.') }}₫</div>
    </div>
    <div class="summary-chip">
        <div class="chip-label">💳 Thẻ POS</div>
        <div class="chip-val" style="color:#818cf8">{{ number_format($cardRev, 0, ',', '.') }}₫</div>
    </div>
    <div class="summary-chip">
        <div class="chip-label">📱 Chuyển khoản</div>
        <div class="chip-val" style="color:#22d3ee">{{ number_format($transferRev, 0, ',', '.') }}₫</div>
    </div>
    @if($activeShift)
    <div class="summary-chip" style="margin-left:auto">
        <div class="chip-label">Ca hiện tại</div>
        <div class="chip-val" style="color:#fbbf24">{{ \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i d/m') }}</div>
    </div>
    @endif
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
    <input type="text" id="search-input" placeholder="🔍 Tìm khách, SĐT, mã vé..."
           oninput="filterRows(this.value)" style="min-width:220px">
    <select id="pttt-filter" onchange="filterRows()">
        <option value="">Tất cả PTTT</option>
        <option value="cash">💵 Tiền mặt</option>
        <option value="card">💳 Thẻ POS</option>
        <option value="transfer">📱 Chuyển khoản</option>
    </select>
    <select id="status-filter" onchange="filterRows()">
        <option value="">Tất cả trạng thái</option>
        <option value="paid">Đã thanh toán</option>
        <option value="cancelled">Đã huỷ</option>
    </select>
</div>

{{-- DANH SÁCH --}}
<div id="order-list">
    @forelse($orders as $order)
    <div class="order-row"
         data-name="{{ strtolower($order->customer_name) }}"
         data-phone="{{ $order->customer_phone }}"
         data-id="{{ $order->id }}"
         data-pttt="{{ $order->payment_method }}"
         data-status="{{ $order->status }}">

        <div class="order-id">#{{ $order->id }}</div>

        <div class="order-avatar">{{ strtoupper(mb_substr($order->customer_name ?? 'K', 0, 1)) }}</div>

        <div class="order-info">
            <div class="name">{{ $order->customer_name }}</div>
            <div class="meta">{{ $order->customer_phone }} · {{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</div>
        </div>

        <div class="order-ticket">
            <div class="ticket-name">{{ $order->slot->ticket->name ?? '—' }}</div>
            <div class="slot-time">
                @if($order->slot)
                    {{ \Carbon\Carbon::parse($order->slot->start_time)->format('H:i') }}–{{ \Carbon\Carbon::parse($order->slot->end_time)->format('H:i') }}
                @endif
            </div>
        </div>

        <div class="order-pttt">
            @if($order->payment_method === 'cash') 💵
            @elseif($order->payment_method === 'card') 💳
            @else 📱
            @endif
        </div>

        <div class="order-amount">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>

        <div class="order-status">
            @if($order->status === 'paid')
                <span class="status-badge status-available">Đã TT</span>
            @elseif($order->status === 'cancelled')
                <span class="status-badge status-error">Đã huỷ</span>
            @else
                <span class="status-badge" style="background:rgba(148,163,184,.1);color:#94a3b8">{{ $order->status }}</span>
            @endif

            @if($order->checkin_at)
                <span style="margin-left:4px;font-size:0.68rem;color:#34d399">
                    <span class="checkin-dot" style="background:#34d399"></span>Đã vào
                </span>
            @else
                <span style="margin-left:4px;font-size:0.68rem;color:#94a3b8">
                    <span class="checkin-dot" style="background:#94a3b8"></span>Chờ
                </span>
            @endif
        </div>

        <div class="order-actions">
            @if($order->status === 'paid' && in_array(Auth::user()->role, ['admin','branch_admin']))
            <button class="btn-danger-pos" style="padding:5px 10px;font-size:0.72rem"
                    onclick="openCancelModal({{ $order->id }}, '{{ addslashes($order->customer_name) }}')">
                <i class="bi bi-x-circle"></i> Huỷ
            </button>
            @endif
        </div>
    </div>
    @empty
    <div class="empty-state">
        <i class="bi bi-receipt-cutoff"></i>
        Chưa có giao dịch nào
        @if(!$activeShift)
        <div style="font-size:0.82rem;margin-top:8px">
            Mở ca để bắt đầu bán vé
        </div>
        @endif
    </div>
    @endforelse
</div>

{{-- PAGINATION --}}
@if($orders->hasPages())
<div style="margin-top:18px;display:flex;justify-content:center;gap:6px">
    {{ $orders->links() }}
</div>
@endif

{{-- MODAL HUỶ VÉ --}}
<div class="pos-modal-backdrop" id="cancel-modal">
    <div class="pos-modal">
        <div class="pos-modal-title">🚫 Huỷ vé</div>
        <div class="pos-modal-sub">Đơn hàng #<span id="cancel-order-id"></span> — <span id="cancel-customer-name"></span></div>

        <form id="cancel-form" method="POST">
            @csrf
            <div class="pos-form-group">
                <label>Lý do huỷ <span style="color:#f87171">*</span></label>
                <textarea name="cancel_reason" rows="3"
                          placeholder="VD: Khách bận đột xuất, nhập nhầm thông tin..."
                          required
                          style="background:rgba(255,255,255,.05);border:1px solid var(--pos-card-border);border-radius:8px;padding:10px 14px;color:var(--pos-text);font-family:inherit;font-size:0.85rem;width:100%;resize:none"></textarea>
            </div>
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:8px">
                <button type="button" onclick="closeCancelModal()" class="btn-pos-outline">Đóng</button>
                <button type="submit" class="btn-danger-pos">
                    <i class="bi bi-x-circle"></i> Xác nhận huỷ
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
function filterRows(val) {
    const search = (document.getElementById('search-input').value || '').toLowerCase();
    const pttt   = document.getElementById('pttt-filter').value;
    const status = document.getElementById('status-filter').value;

    document.querySelectorAll('.order-row').forEach(row => {
        const matchSearch = !search ||
            row.dataset.name.includes(search) ||
            row.dataset.phone.includes(search) ||
            row.dataset.id.includes(search);
        const matchPttt   = !pttt   || row.dataset.pttt === pttt;
        const matchStatus = !status || row.dataset.status === status;

        row.style.display = (matchSearch && matchPttt && matchStatus) ? '' : 'none';
    });
}

function openCancelModal(orderId, customerName) {
    document.getElementById('cancel-order-id').textContent = orderId;
    document.getElementById('cancel-customer-name').textContent = customerName;
    document.getElementById('cancel-form').action = `/chi-nhanh/{{ $subdomain }}/pos/huy-ve/${orderId}`;
    document.getElementById('cancel-modal').classList.add('show');
}

function closeCancelModal() {
    document.getElementById('cancel-modal').classList.remove('show');
}

// Click backdrop đóng modal
document.getElementById('cancel-modal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
</script>
@endsection