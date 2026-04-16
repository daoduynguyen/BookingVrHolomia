@extends('pos.layout')

@section('title', 'Dashboard — ' . $location->name)
@section('page-title', 'Trang chủ')

@section('styles')
<style>
    .ticket-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; }

    .ticket-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color .2s, transform .1s;
    }
    .ticket-card:hover { border-color: var(--pos-primary); transform: translateY(-1px); }

    .ticket-card-header {
        padding: 14px 16px 10px;
        border-bottom: 1px solid var(--pos-card-border);
    }
    .ticket-card-title { font-size: 0.95rem; font-weight: 700; margin-bottom: 4px; }
    .ticket-card-price { font-size: 0.82rem; color: #a78bfa; font-weight: 600; }

    .slot-list { padding: 12px 16px; display: flex; flex-direction: column; gap: 8px; }

    .slot-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        border-radius: 8px;
        background: rgba(255,255,255,.03);
        border: 1px solid rgba(255,255,255,.05);
        transition: all .15s;
        text-decoration: none;
        color: var(--pos-text);
    }
    .slot-item:hover { background: rgba(124,58,237,.1); border-color: var(--pos-primary); color: var(--pos-text); }
    .slot-item.slot-full { opacity: .5; cursor: not-allowed; pointer-events: none; }

    .slot-time { font-size: 0.85rem; font-weight: 600; }
    .slot-time .date-tag { font-size: 0.68rem; color: var(--pos-text-muted); }

    .slot-avail {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
    }
    .slot-avail .add-btn {
        width: 26px; height: 26px;
        border-radius: 50%;
        background: var(--pos-primary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
        font-weight: 700;
        flex-shrink: 0;
    }
    .slot-avail .add-btn.disabled {
        background: rgba(255,255,255,.1);
        color: #4b5563;
    }

    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 20px; }
    .stat-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 10px;
        padding: 14px 16px;
    }
    .stat-label { font-size: 0.7rem; color: var(--pos-text-muted); text-transform: uppercase; letter-spacing: .05em; margin-bottom: 6px; }
    .stat-value { font-size: 1.4rem; font-weight: 800; }
    .stat-value.green { color: #34d399; }
    .stat-value.amber { color: #fbbf24; }
    .stat-value.purple { color: #a78bfa; }
    .stat-value.cyan   { color: #22d3ee; }

    .device-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .device-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid var(--pos-card-border);
        cursor: pointer;
        transition: opacity .15s;
    }
    .device-chip:hover { opacity: .8; }
    .device-chip .dot { width: 8px; height: 8px; border-radius: 50%; }
    .dot-available { background: #34d399; }
    .dot-in_use    { background: #fbbf24; }
    .dot-cleaning  { background: #22d3ee; }
    .dot-charging  { background: #818cf8; }
    .dot-error     { background: #f87171; }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
    }
    .section-title { font-size: 0.85rem; font-weight: 700; color: var(--pos-text); }
    .section-sub { font-size: 0.75rem; color: var(--pos-text-muted); }
</style>
@endsection

@section('content')

{{-- STATS --}}
@php
$totalOrders = isset($activeShift) && $activeShift
    ? \App\Models\Order::where('pos_shift_id', $activeShift->id)->where('status','paid')->count()
    : 0;
$totalRevenue = isset($activeShift) && $activeShift
    ? \App\Models\Order::where('pos_shift_id', $activeShift->id)->where('status','paid')->sum('total_amount')
    : 0;
$availableDevices = $devices->where('status','available')->count();
$totalDevices = $devices->count();
@endphp

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-receipt me-1"></i>Vé đã bán (ca này)</div>
        <div class="stat-value green">{{ $totalOrders }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-cash-stack me-1"></i>Doanh thu ca</div>
        <div class="stat-value purple">{{ number_format($totalRevenue,0,',','.') }}₫</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-headset-vr me-1"></i>Kính sẵn sàng</div>
        <div class="stat-value cyan">{{ $availableDevices }}/{{ $totalDevices }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label"><i class="bi bi-calendar-date me-1"></i>Hôm nay</div>
        <div class="stat-value amber">{{ now()->format('d/m/Y') }}</div>
    </div>
</div>

{{-- THIẾT BỊ VR (quick view) --}}
@if($devices->count() > 0)
<div class="section-header">
    <div>
        <div class="section-title"><i class="bi bi-headset-vr me-2"></i>Thiết bị VR</div>
        <div class="section-sub">Click để cập nhật trạng thái</div>
    </div>
    <a href="{{ route('pos.devices', $subdomain) }}" class="btn-pos-outline" style="font-size:0.75rem;padding:5px 12px">
        Quản lý <i class="bi bi-arrow-right"></i>
    </a>
</div>
<div class="device-row mb-4">
    @foreach($devices as $device)
    <div class="device-chip" onclick="quickUpdateDevice({{ $device->id }}, '{{ $device->status }}')">
        <div class="dot dot-{{ $device->status }}"></div>
        {{ $device->name }}
        @if($device->battery_percent)
            <span style="color:var(--pos-text-muted)">{{ $device->battery_percent }}%</span>
        @endif
    </div>
    @endforeach
</div>
@endif

{{-- DANH SÁCH VÉ + SLOT --}}
<div class="section-header">
    <div>
        <div class="section-title"><i class="bi bi-ticket-perforated me-2"></i>Vé hôm nay</div>
        <div class="section-sub">Chọn slot để xem chi tiết hoặc bán vé</div>
    </div>
    <a href="{{ route('pos.checkin', $subdomain) }}" class="btn-pos" style="font-size:0.8rem;padding:7px 14px">
        <i class="bi bi-qr-code-scan"></i> Quét vào
    </a>
</div>

@if($tickets->isEmpty())
    <div class="pos-alert pos-alert-info">
        <i class="bi bi-info-circle"></i>
        Chưa có vé nào được thiết lập cho hôm nay. Vui lòng tạo slot trong trang Admin.
    </div>
@else
<div class="ticket-grid">
    @foreach($tickets as $ticket)
    <div class="ticket-card">
        <div class="ticket-card-header">
            <div class="ticket-card-title">{{ $ticket->name }}</div>
            <div class="ticket-card-price">{{ number_format($ticket->price,0,',','.') }}₫ / người</div>
        </div>
        <div class="slot-list">
            @forelse($ticket->timeSlots as $slot)
            @php
                $available = $slot->capacity - $slot->booked_count;
                $isFull = $available <= 0;
            @endphp
            <a href="{{ route('pos.slot.detail', [$subdomain, $slot->id]) }}"
               class="slot-item {{ $isFull ? 'slot-full' : '' }}">
                <div>
                    <div class="slot-time">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                    <div class="slot-time date-tag">{{ \Carbon\Carbon::parse($slot->date)->format('d/m') }} · {{ $slot->booked_count }}/{{ $slot->capacity }} chỗ</div>
                </div>
                <div class="slot-avail">
                    @if(!$isFull)
                        <span style="color:var(--pos-text-muted)">còn {{ $available }}</span>
                        <a href="{{ route('pos.sale.form', [$subdomain, $slot->id]) }}"
                           class="add-btn"
                           onclick="event.stopPropagation()"
                           title="Bán vé cho slot này">+</a>
                    @else
                        <span style="color:#f87171;font-size:0.72rem">Hết chỗ</span>
                        <div class="add-btn disabled">–</div>
                    @endif
                </div>
            </a>
            @empty
            <div style="color:var(--pos-text-muted);font-size:0.78rem;padding:6px 0">Chưa có slot hôm nay</div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endif

@endsection

@section('scripts')
<script>
// Quick update device status
function quickUpdateDevice(deviceId, currentStatus) {
    const statuses = {
        'available': 'Sẵn sàng',
        'in_use': 'Đang dùng',
        'cleaning': 'Chờ vệ sinh',
        'charging': 'Đang sạc',
        'error': 'Lỗi'
    };

    const options = Object.entries(statuses).map(([k,v]) =>
        `<option value="${k}" ${k === currentStatus ? 'selected' : ''}>${v}</option>`
    ).join('');

    const html = `
        <p style="color:var(--pos-text-muted);font-size:0.85rem">Chọn trạng thái mới:</p>
        <select id="new-status" style="width:100%;padding:8px 12px;background:rgba(255,255,255,.05);border:1px solid var(--pos-card-border);border-radius:8px;color:var(--pos-text);margin-bottom:10px;">
            ${options}
        </select>
        <textarea id="error-note" placeholder="Ghi chú lỗi (nếu chọn Lỗi)..." style="width:100%;padding:8px 12px;background:rgba(255,255,255,.05);border:1px solid var(--pos-card-border);border-radius:8px;color:var(--pos-text);resize:none;height:60px;display:none;"></textarea>
    `;

    if (!confirm('Cập nhật trạng thái thiết bị?')) return;

    // Simple prompt fallback
    const newStatus = prompt(`Trạng thái mới cho thiết bị:\n${Object.entries(statuses).map(([k,v]) => k+': '+v).join('\n')}\n\nNhập key:`, currentStatus);
    if (!newStatus || !statuses[newStatus]) return;

    fetch(`/chi-nhanh/{{ $subdomain }}/pos/thiet-bi/${deviceId}/trang-thai`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) location.reload();
    });
}
</script>
@endsection