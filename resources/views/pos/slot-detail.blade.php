@extends('pos.layout')

@section('title', 'Chi tiết Slot')
@section('page-title', 'Chi tiết Slot')

@section('styles')
<style>
    .slot-header-card {
        background: linear-gradient(135deg, rgba(var(--pos-primary-rgb),.2), rgba(var(--pos-secondary-rgb, 56, 189, 248),.1));
        border: 1px solid rgba(var(--pos-primary-rgb),.3);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .slot-header-info h2 { font-size: 1.1rem; font-weight: 800; margin: 0 0 4px; }
    .slot-header-info p { font-size: 0.82rem; color: var(--pos-text-muted); margin: 0; }

    .orders-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    .order-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 10px;
        padding: 14px;
    }
    .order-card.playing { border-color: rgba(245,158,11,.4); }
    .order-card.waiting { border-color: rgba(6,182,212,.3); }

    .order-name { font-size: 0.92rem; font-weight: 700; margin-bottom: 2px; }
    .order-phone { font-size: 0.75rem; color: var(--pos-text-muted); margin-bottom: 10px; }

    .timer-display {
        font-size: 1.8rem;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        color: #fbbf24;
        letter-spacing: .05em;
    }
    .timer-display.urgent { color: #f87171; animation: pulse 1s infinite; }
    .timer-label { font-size: 0.65rem; color: var(--pos-text-muted); text-transform: uppercase; }

    @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.5; } }

    .checkin-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        border-radius: 7px;
        background: var(--pos-success);
        color: #fff;
        font-size: 0.78rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s;
    }
    .checkin-btn:hover { background: #059669; color: #fff; }

    .section-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .06em;
        color: var(--pos-text-muted);
        margin-bottom: 10px;
    }

    .capacity-bar {
        background: rgba(255,255,255,.08);
        border-radius: 20px;
        height: 6px;
        overflow: hidden;
        margin-top: 6px;
    }
    .capacity-fill {
        height: 100%;
        border-radius: 20px;
        background: linear-gradient(90deg, var(--pos-primary), var(--pos-secondary));
        transition: width .3s;
    }
</style>
@endsection

@section('content')

{{-- SLOT HEADER --}}
<div class="slot-header-card">
    <div class="slot-header-info">
        <h2>{{ $slot->ticket->name ?? 'Vé' }} · {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</h2>
        <p>
            <i class="bi bi-calendar3 me-1"></i>{{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}
            &nbsp;·&nbsp;
            <i class="bi bi-people me-1"></i>{{ $slot->booked_count }}/{{ $slot->capacity }} người
        </p>
        @php $available = $slot->capacity - $slot->booked_count; @endphp
        <div class="capacity-bar" style="width:160px">
            <div class="capacity-fill" style="width:{{ $slot->capacity > 0 ? ($slot->booked_count/$slot->capacity*100) : 0 }}%"></div>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        @if($available > 0)
        <a href="{{ route('pos.sale.form', [$subdomain, $slot->id]) }}" class="btn-pos">
            <i class="bi bi-plus-lg"></i> Bán vé ({{ $available }} thiết bị trống)
        </a>
        @endif
        <a href="{{ route('pos.dashboard', $subdomain) }}" class="btn-pos-outline">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<div class="orders-grid">
    {{-- ĐANG CHƠI --}}
    <div>
        <div class="section-label"><i class="bi bi-controller me-1" style="color:#fbbf24"></i>Đang chơi ({{ $activeOrders->count() }})</div>
        @forelse($activeOrders as $order)
        <div class="order-card playing mb-3">
            <div class="order-name">{{ $order->customer_name }}</div>
            <div class="order-phone">{{ $order->customer_phone }} · #{{ $order->id }}</div>
            <div class="timer-display" id="timer-{{ $order->id }}"
                 data-remain="{{ $order->remain_seconds }}">
                {{ gmdate('i:s', $order->remain_seconds) }}
            </div>
            <div class="timer-label">còn lại</div>
            @if($order->remain_seconds < 120)
            <div class="pos-alert pos-alert-warning mt-2 py-1 px-2" style="font-size:0.72rem">
                ⚠️ Sắp hết giờ!
            </div>
            @endif
        </div>
        @empty
        <div style="color:var(--pos-text-muted);font-size:0.82rem;padding:12px 0">
            <i class="bi bi-moon-stars me-1"></i>Chưa có khách nào đang chơi
        </div>
        @endforelse
    </div>

    {{-- CHỜ VÀO --}}
    <div>
        <div class="section-label"><i class="bi bi-hourglass-split me-1" style="color:#22d3ee"></i>Chờ check-in ({{ $waitingOrders->count() }})</div>
        @forelse($waitingOrders as $order)
        <div class="order-card waiting mb-3">
            <div class="order-name">{{ $order->customer_name }}</div>
            <div class="order-phone">{{ $order->customer_phone }} · #{{ $order->id }}</div>
            <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
                <span class="status-badge status-in_use">Chờ vào</span>
                @if($available > 0 || $activeOrders->count() < $slot->capacity)
                <button class="checkin-btn" onclick="manualCheckin({{ $order->id }})">
                    <i class="bi bi-check2-circle"></i> Check-in
                </button>
                @endif
            </div>
        </div>
        @empty
        <div style="color:var(--pos-text-muted);font-size:0.82rem;padding:12px 0">
            <i class="bi bi-check-all me-1"></i>Tất cả đã check-in
        </div>
        @endforelse
    </div>
</div>

@endsection

@section('scripts')
<script>
// Đếm ngược timer
const timers = {};

function startTimers() {
    document.querySelectorAll('[id^="timer-"]').forEach(el => {
        const orderId = el.id.replace('timer-', '');
        let remain = parseInt(el.dataset.remain);
        timers[orderId] = setInterval(() => {
            if (remain <= 0) {
                clearInterval(timers[orderId]);
                el.textContent = '00:00';
                el.classList.add('urgent');
                return;
            }
            remain--;
            el.dataset.remain = remain;
            const m = String(Math.floor(remain / 60)).padStart(2, '0');
            const s = String(remain % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
            if (remain < 120) el.classList.add('urgent');
        }, 1000);
    });
}
startTimers();

// Polling tự động mỗi 30 giây
setInterval(() => {
    fetch(`{{ route('pos.slot.status', [$subdomain, $slot->id]) }}`)
        .then(r => r.json())
        .then(data => {
            // Chỉ reload nếu số lượng thay đổi (tránh reset timer)
            if (data.active_orders && data.active_orders.length !== {{ $activeOrders->count() }}) {
                location.reload();
            }
        })
        .catch(() => {});
}, 30000);

// Manual check-in từ slot detail
function manualCheckin(orderId) {
    if (!confirm('Xác nhận check-in cho đơn hàng #' + orderId + '?')) return;

    // Dùng form ẩn để POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('pos.checkin.process', $subdomain) }}';
    form.innerHTML = `
        @csrf
        <input type="hidden" name="order_id" value="${orderId}">
        <input type="hidden" name="manual" value="1">
    `;
    // Không có qr_token → sẽ cần thêm xử lý trong controller
    // Tạm thời navigate sang trang check-in
    window.location = '{{ route('pos.checkin', $subdomain) }}?highlight=' + orderId;
}
</script>
@endsection