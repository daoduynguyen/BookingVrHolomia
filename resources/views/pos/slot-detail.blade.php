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

    .slot-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    @media (max-width: 991px) {
        .slot-stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    .slot-stat {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 12px 14px;
    }
    .slot-stat .k { font-size: 0.7rem; text-transform: uppercase; letter-spacing: .06em; color: var(--pos-text-muted); font-weight: 700; }
    .slot-stat .v { font-size: 1.1rem; font-weight: 800; margin-top: 2px; }

    .orders-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
    @media (max-width: 991px) {
        .orders-grid { grid-template-columns: 1fr; }
    }

    .order-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 14px;
        padding: 14px;
        cursor: pointer;
        transition: transform .15s ease, border-color .15s ease, background .15s ease;
        position: relative;
        overflow: hidden;
    }
    .order-card:hover { transform: translateY(-2px); border-color: rgba(var(--pos-primary-rgb),.28); }
    .order-card.playing { border-color: rgba(245,158,11,.4); background: linear-gradient(180deg, rgba(245,158,11,.05), rgba(255,255,255,.01)); }
    .order-card.waiting { border-color: rgba(6,182,212,.3); background: linear-gradient(180deg, rgba(6,182,212,.04), rgba(255,255,255,.01)); }
    .order-card.expired { border-color: rgba(148,163,184,.35); opacity: .8; }
    .order-card.cancelled,
    .order-card.refunded { border-color: rgba(239,68,68,.35); opacity: .86; }

    .device-card {
        background: linear-gradient(180deg, rgba(var(--pos-primary-rgb), .06), rgba(var(--pos-primary-rgb), .02));
    }

    .device-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(34,197,94,.9);
        color: #fff;
        font-size: 0.72rem;
        font-weight: 700;
        margin-bottom: 10px;
        border: none;
        text-decoration: none;
    }

    .state-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 9px;
        border-radius: 999px;
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .state-badge.playing { background: rgba(245,158,11,.12); color: #f59e0b; }
    .state-badge.waiting { background: rgba(6,182,212,.12); color: #06b6d4; }
    .state-badge.expired { background: rgba(148,163,184,.15); color: #94a3b8; }
    .state-badge.cancelled { background: rgba(239,68,68,.12); color: #f87171; }
    .state-badge.refunded { background: rgba(139,92,246,.12); color: #a78bfa; }

    .order-name { font-size: 0.92rem; font-weight: 700; margin-bottom: 2px; }
    .order-phone { font-size: 0.75rem; color: var(--pos-text-muted); margin-bottom: 10px; }
    .order-meta { font-size: 0.74rem; color: var(--pos-text-muted); }
    .order-actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-top: 12px; }

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

    .detail-btn,
    .detail-btn:focus {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid var(--pos-card-border);
        background: transparent;
        color: var(--pos-text);
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
    }
    .detail-btn:hover {
        border-color: rgba(var(--pos-primary-rgb),.35);
        color: var(--pos-primary);
    }

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

    .detail-modal .modal-content {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        color: var(--pos-text);
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

<div class="slot-stats">
    <div class="slot-stat">
        <div class="k">Đang chơi</div>
        <div class="v" style="color:#f59e0b">{{ $playingOrders->count() }}</div>
    </div>
    <div class="slot-stat">
        <div class="k">Chờ check-in</div>
        <div class="v" style="color:#06b6d4">{{ $waitingOrders->count() }}</div>
    </div>
    <div class="slot-stat">
        <div class="k">Hết hạn</div>
        <div class="v" style="color:#94a3b8">{{ $expiredOrders->count() }}</div>
    </div>
    <div class="slot-stat">
        <div class="k">Hủy / hoàn</div>
        <div class="v" style="color:#f87171">{{ $cancelledOrders->count() + $refundedOrders->count() }}</div>
    </div>
</div>

<div class="orders-grid">
    @forelse($orders as $order)
    <div class="order-card {{ $order->state }} {{ $order->state === 'playing' ? 'device-card' : '' }}"
         data-order-id="{{ $order->id }}"
         data-state="{{ $order->state }}"
         data-modal-target="#orderModal-{{ $order->id }}"
         onclick="openOrderModal({{ $order->id }})">
        <div class="d-flex align-items-start justify-content-between gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                    <span class="state-badge {{ $order->state }}">
                        @if($order->state === 'playing')
                            <i class="bi bi-play-fill"></i> Đang chơi
                        @elseif($order->state === 'waiting')
                            <i class="bi bi-hourglass-split"></i> Chờ check-in
                        @elseif($order->state === 'expired')
                            <i class="bi bi-clock-history"></i> Hết hạn
                        @elseif($order->state === 'cancelled')
                            <i class="bi bi-x-circle"></i> Đã hủy
                        @elseif($order->state === 'refunded')
                            <i class="bi bi-arrow-counterclockwise"></i> Đã hoàn
                        @endif
                    </span>
                    <span style="font-size:0.72rem;color:var(--pos-text-muted)">#{{ $order->id }}</span>
                </div>
                <div class="order-name">{{ $order->customer_name }}</div>
                <div class="order-phone">{{ $order->customer_phone }}</div>
                <div class="order-meta">
                    <i class="bi bi-ticket-perforated me-1"></i>{{ $slot->ticket->name ?? 'Vé' }}
                </div>
            </div>

            @if($order->state === 'playing' || $order->device_id)
                <button type="button" class="device-chip" onclick="event.stopPropagation(); openOrderModal({{ $order->id }})">
                    <i class="bi bi-headset-vr"></i>{{ $order->device_name }}
                </button>
            @endif
        </div>

        @if($order->state === 'playing')
            <div class="timer-display" id="timer-{{ $order->id }}" data-remain="{{ $order->remain_seconds }}">
                {{ gmdate('i:s', $order->remain_seconds) }}
            </div>
            <div class="timer-label">thời gian còn lại</div>
            <div class="order-actions" onclick="event.stopPropagation()">
                <a href="{{ route('pos.sale.success', [$subdomain, $order->id]) }}" class="detail-btn" target="_blank">
                    <i class="bi bi-receipt"></i> Xem hồ sơ
                </a>
            </div>
        @elseif($order->state === 'waiting')
            <div class="timer-display" id="wait-{{ $order->id }}" data-remain="{{ $order->deadline_seconds }}" style="color:#06b6d4; font-size:1.35rem; margin-top:6px;">
                {{ gmdate('i:s', $order->deadline_seconds) }}
            </div>
            <div class="timer-label">còn trước khi hết hạn check-in</div>
            <div class="order-actions" onclick="event.stopPropagation()">
                <button class="checkin-btn" id="start-btn-{{ $order->id }}" onclick="manualCheckin({{ $order->id }})">
                    <i class="bi bi-play-circle"></i> Bắt đầu
                </button>
                <a href="{{ route('pos.sale.success', [$subdomain, $order->id]) }}" class="detail-btn" target="_blank">
                    <i class="bi bi-eye"></i> Chi tiết
                </a>
            </div>
        @else
            <div class="order-meta mt-2">{{ $order->state === 'expired' ? 'Khách chưa tới đúng giờ, đơn đã hết hạn.' : 'Trạng thái đã thay đổi theo hệ thống.' }}</div>
            <div class="order-actions" onclick="event.stopPropagation()">
                <a href="{{ route('pos.sale.success', [$subdomain, $order->id]) }}" class="detail-btn" target="_blank">
                    <i class="bi bi-eye"></i> Chi tiết
                </a>
            </div>
        @endif
    </div>
    @empty
    <div style="color:var(--pos-text-muted);font-size:0.82rem;padding:12px 0">
        <i class="bi bi-info-circle me-1"></i>Chưa có vé nào trong slot này
    </div>
    @endforelse
</div>

@foreach($orders as $order)
<div class="modal fade detail-modal" id="orderModal-{{ $order->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-1">Đơn #{{ $order->id }}</h5>
                    <div class="text-muted small">{{ $order->customer_name }} · {{ $order->customer_phone }}</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-0">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Trạng thái</div>
                            <div class="fw-bold">{{ strtoupper($order->state) }}</div>
                            <div class="text-muted small mt-2">Máy: {{ $order->device_name }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-3 border h-100">
                            <div class="text-muted small text-uppercase fw-bold mb-1">Vé / slot</div>
                            <div class="fw-bold">{{ $slot->ticket->name ?? 'Vé' }}</div>
                            <div class="text-muted small mt-2">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }} · {{ \Carbon\Carbon::parse($slot->date)->format('d/m/Y') }}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    @if($order->state === 'playing')
                        <div class="alert alert-warning mb-0">Khách đang chơi trên máy này. Thời gian còn lại: <strong>{{ gmdate('i:s', $order->remain_seconds) }}</strong></div>
                    @elseif($order->state === 'waiting')
                        <div class="alert alert-info mb-0">Khách đang chờ check-in. Hết hạn sau <strong>{{ gmdate('i:s', $order->deadline_seconds) }}</strong>.</div>
                    @elseif($order->state === 'expired')
                        <div class="alert alert-secondary mb-0">Đơn đã hết hạn check-in.</div>
                    @elseif($order->state === 'cancelled')
                        <div class="alert alert-danger mb-0">Đơn đã bị hủy.</div>
                    @elseif($order->state === 'refunded')
                        <div class="alert alert-success mb-0">Đơn đã hoàn.</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0">
                @if($order->state === 'waiting')
                    <button type="button" class="checkin-btn" onclick="manualCheckin({{ $order->id }})">
                        <i class="bi bi-play-circle"></i> Bắt đầu
                    </button>
                @endif
                <a href="{{ route('pos.sale.success', [$subdomain, $order->id]) }}" class="detail-btn" target="_blank">
                    <i class="bi bi-receipt"></i> Mở chi tiết đơn
                </a>
                <button type="button" class="btn-pos-outline" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
// Đếm ngược timer
const timers = {};

function startTimers() {
    document.querySelectorAll('[id^="timer-"]').forEach(el => {
        const orderId = el.id.replace('timer-', '');
        let remain = parseInt(el.dataset.remain, 10);
        timers[orderId] = setInterval(() => {
            if (remain <= 0) {
                clearInterval(timers[orderId]);
                el.textContent = '00:00';
                el.classList.add('urgent');
                return;
            }
            remain -= 1;
            el.dataset.remain = remain;
            const m = String(Math.floor(remain / 60)).padStart(2, '0');
            const s = String(remain % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
            if (remain < 120) el.classList.add('urgent');
        }, 1000);
    });

    document.querySelectorAll('[id^="wait-"]').forEach(el => {
        const orderId = el.id.replace('wait-', '');
        let remain = parseInt(el.dataset.remain, 10);
        const startBtn = document.getElementById('start-btn-' + orderId);
        timers['wait-' + orderId] = setInterval(() => {
            if (remain <= 0) {
                clearInterval(timers['wait-' + orderId]);
                el.textContent = '00:00';
                el.classList.add('urgent');
                if (startBtn) {
                    startBtn.outerHTML = '<span class="state-badge expired"><i class="bi bi-clock-history"></i> Hết hạn</span>';
                }
                return;
            }
            remain -= 1;
            el.dataset.remain = remain;
            const m = String(Math.floor(remain / 60)).padStart(2, '0');
            const s = String(remain % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
        }, 1000);
    });
}
startTimers();

// Polling tự động mỗi 30 giây
setInterval(() => {
    fetch(`{{ route('pos.slot.status', [$subdomain, $slot->id]) }}`)
        .then(r => r.json())
        .then(data => {
            if (data.summary) {
                const signature = JSON.stringify(data.summary);
                if (window.__slotSignature && window.__slotSignature !== signature) {
                    location.reload();
                    return;
                }
                window.__slotSignature = signature;
            }
            if (data.active_orders && data.active_orders.length !== {{ $playingOrders->count() }}) {
                location.reload();
            }
        })
        .catch(() => {});
}, 30000);

function openOrderModal(orderId) {
    const modalEl = document.getElementById('orderModal-' + orderId);
    if (!modalEl) return;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
}

// Manual check-in từ slot detail
function manualCheckin(orderId) {
    if (!confirm('Xác nhận bắt đầu cho đơn hàng #' + orderId + '?')) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch('{{ route('pos.checkin.process', $subdomain) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId, manual: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Không thể bắt đầu đơn này.');
        }
    })
    .catch(() => alert('Không thể bắt đầu đơn này.'));
}
</script>
@endsection