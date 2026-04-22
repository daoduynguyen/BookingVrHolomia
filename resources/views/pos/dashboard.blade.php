@extends('pos.layout')

@section('title', 'Dashboard — ' . $location->name)
@section('page-title', 'Trang chủ')

@section('styles')
<style>
    .ticket-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }

    .ticket-card {
        background: var(--pos-card);
        border: none;
        border-radius: 16px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
        display: flex;
        flex-direction: column;
    }
    .ticket-card:hover { 
        transform: translateY(-4px); 
        box-shadow: 0 12px 25px rgba(0,0,0,0.08); 
    }
    .ticket-card-img-wrap {
        width: 100%;
        height: 160px;
        position: relative;
        overflow: hidden;
    }
    .ticket-card-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .ticket-card:hover .ticket-card-img-wrap img {
        transform: scale(1.05);
    }
    .ticket-card-body {
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .ticket-card-title { font-size: 1.05rem; font-weight: 800; margin-bottom: 4px; color: var(--pos-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ticket-card-price { font-size: 0.9rem; color: var(--pos-primary); font-weight: 700; display:flex; align-items:center; gap:6px; margin-bottom: 12px; }

    .slot-list { padding: 10px 0; display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; }
    @media (max-width: 768px) {
        .slot-list { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .slot-list { grid-template-columns: 1fr; }
    }

    .slot-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 18px;
        border-radius: 12px;
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all .2s ease-in-out;
        text-decoration: none;
        color: var(--pos-text);
    }
    .slot-item:hover { background: rgba(var(--pos-primary-rgb),.12); border-color: var(--pos-primary); color: var(--pos-text); transform: scale(1.02); }
    .slot-item.slot-full { opacity: .5; cursor: not-allowed; pointer-events: none; }

    .slot-time { font-size: 0.85rem; font-weight: 700; }
    .slot-time .date-tag { font-size: 0.68rem; color: var(--pos-text-muted); }

    .slot-avail {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.75rem;
    }
    .slot-avail .add-btn {
        width: 32px; height: 32px;
        border-radius: 50%;
        background: var(--pos-primary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem;
        font-weight: 700;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .slot-avail .add-btn:hover {
        background: #000;
        transform: rotate(90deg);
    }
    .slot-avail .add-btn.disabled {
        background: rgba(255,255,255,.1);
        color: #4b5563;
    }

    .stats-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card {
        background: var(--pos-card);
        border: none;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
    
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px; 
        display: flex; align-items: center; justify-content: center; 
        font-size: 1.6rem; flex-shrink: 0;
    }
    .stat-icon.green  { background: rgba(52, 211, 153, 0.15); color: #10b981; }
    .stat-icon.purple { background: rgba(139, 92, 246, 0.15); color: #8b5cf6; }
    .stat-icon.cyan   { background: rgba(34, 211, 238, 0.15); color: #06b6d4; }
    .stat-icon.amber  { background: rgba(251, 191, 36, 0.15);  color: #f59e0b; }

    .stat-card.clickable { cursor: pointer; border: 1px solid transparent; }
    .stat-card.clickable:hover { border-color: rgba(var(--pos-primary-rgb), 0.3); background: rgba(var(--pos-primary-rgb), 0.02); }
    .stat-card.clickable:active { transform: scale(0.97); }

    .stat-info { display: flex; flex-direction: column; }
    .stat-label { font-size: 0.75rem; color: var(--pos-text-muted); text-transform: uppercase; letter-spacing: .05em; font-weight: 600; margin-bottom: 4px; }
    .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; color: var(--pos-text); }

    .device-row { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 24px; }
    .device-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 0.8rem;
        font-weight: 600;
        background: var(--pos-card);
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        cursor: pointer;
        transition: all .2s;
    }
    .device-chip:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0,0,0,0.06); }
    .device-chip .dot { width: 10px; height: 10px; border-radius: 50%; box-shadow: inset 0 0 0 2px rgba(255,255,255,0.2); }
    .dot-available { background: #34d399; }
    .dot-in_use    { background: #fbbf24; }
    .dot-cleaning  { background: #22d3ee; }
    .dot-charging  { background: #818cf8; }
    .dot-error     { background: #f87171; }

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    .section-title { font-size: 1.1rem; font-weight: 800; color: var(--pos-text); letter-spacing: -0.02em; }
    .section-sub { font-size: 0.8rem; color: var(--pos-text-muted); font-weight: 500; }
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
        <div class="stat-icon green"><i class="bi bi-receipt"></i></div>
        <div class="stat-info">
            <span class="stat-label">Vé đã bán (ca này)</span>
            <span class="stat-value">{{ $totalOrders }}</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-info">
            <span class="stat-label">Doanh thu ca</span>
            <span class="stat-value">{{ number_format($totalRevenue,0,',','.') }}₫</span>
        </div>
    </div>
    <div class="stat-card clickable" onclick="openDevicesListModal()">
        <div class="stat-icon cyan"><i class="bi bi-headset-vr"></i></div>
        <div class="stat-info">
            <span class="stat-label">Kính sẵn sàng</span>
            <span class="stat-value">{{ $availableDevices }}<span style="font-size:1rem; color:var(--pos-text-muted)">/{{ $totalDevices }}</span></span>
        </div>
        <div class="ms-auto no-print">
            <i class="bi bi-chevron-right text-muted"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="bi bi-calendar-date"></i></div>
        <div class="stat-info">
            <span class="stat-label">Hôm nay</span>
            <span class="stat-value" style="font-size:1.15rem;">{{ now()->format('d/m/Y') }}</span>
        </div>
    </div>
</div>

{{-- Danh sách kính đã được chuyển vào Modal --}}
<div id="device-container-hidden" class="d-none">
    @foreach($devices as $device)
    <div class="device-chip" data-id="{{ $device->id }}" data-status="{{ $device->status }}" data-name="{{ $device->name }}" onclick="openDeviceModal(this)">
        <div class="dot dot-{{ $device->status }}"></div>
        {{ $device->name }}
    </div>
    @endforeach
</div>

{{-- DANH SÁCH VÉ + SLOT --}}
<div class="section-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div style="min-width: 200px; flex-shrink: 0;">
        <div class="section-title"><i class="bi bi-ticket-perforated me-2"></i>Vé hôm nay</div>
        <div class="section-sub" id="refresh-status" style="font-variant-numeric: tabular-nums;">Tự động cập nhật sau 30s</div>
    </div>
    
    <div class="d-flex align-items-center gap-2 flex-grow-1" style="max-width: 400px; margin: 0 auto;">
        <form action="{{ route('pos.dashboard', $subdomain) }}" method="GET" class="w-100 position-relative">
            <input type="text" name="search" class="form-control" placeholder="Tra cứu nhanh bộ sưu tập trò chơi..." 
                   value="{{ request('search') }}"
                   style="border-radius: 30px; padding: 12px 20px 12px 42px; background: var(--pos-card); color: var(--pos-text); border: transparent; box-shadow: 0 4px 15px rgba(0,0,0,0.03); font-size: 0.9rem; font-weight: 500;">
            <i class="bi bi-search position-absolute text-primary" style="top: 50%; left: 16px; transform: translateY(-50%); font-size: 1rem;"></i>
            @if(request('search'))
                <a href="{{ route('pos.dashboard', $subdomain) }}" class="position-absolute text-muted" style="top: 50%; right: 12px; transform: translateY(-50%); text-decoration: none;">
                    <i class="bi bi-x-circle-fill"></i>
                </a>
            @endif
        </form>
    </div>

    <a href="{{ route('pos.checkin', $subdomain) }}" class="btn-pos" style="font-size:0.8rem;padding:7px 14px; white-space: nowrap;">
        <i class="bi bi-qr-code-scan"></i> Quét vào
    </a>
</div>

@if($tickets->isEmpty())
    <div class="pos-alert pos-alert-info">
        <i class="bi bi-info-circle"></i>
        Chưa có vé nào được thiết lập cho hôm nay. Vui lòng tạo slot trong trang Admin.
    </div>
@else
@php
    $showPastSlots = auth()->user()->ui_settings['pos_show_past_slots'] ?? false;
@endphp
<div class="ticket-grid" id="ticket-grid">
    @foreach($tickets as $ticket)
    <div class="ticket-card" data-ticket-id="{{ $ticket->id }}" onclick="openTicketModal({{ $ticket->id }}, '{{ htmlspecialchars($ticket->name) }}')">
        <div class="ticket-card-img-wrap">
            @php 
                $img = Str::startsWith($ticket->image ?? $ticket->image_url, 'http') ? ($ticket->image ?? $ticket->image_url) : asset('storage/' . ($ticket->image ?? $ticket->image_url)); 
                if(!$ticket->image && !$ticket->image_url) $img = "https://ui-avatars.com/api/?name=".urlencode($ticket->name)."&background=random&size=400";
            @endphp
            <img src="{{ $img }}" alt="{{ $ticket->name }}">
        </div>
        <div class="ticket-card-body">
            <div class="ticket-card-title">{{ $ticket->name }}</div>
            <div class="ticket-card-price"><i class="bi bi-tag-fill"></i> {{ number_format($ticket->price,0,',','.') }}₫ / người</div>
            
            <button class="btn-pos w-100 mt-auto" style="padding: 10px; border-radius: 12px; font-weight: 600; display:flex; align-items:center; justify-content:center; gap:6px;">
                <i class="bi bi-calendar2-range"></i> Xem Giờ Chơi
            </button>
        </div>

        {{-- DOM ẩn chứa danh sách Slot để Load vào Modal --}}
        <div id="slots-data-{{ $ticket->id }}" class="d-none">
            <div class="slot-list p-0">
                @php $hasVisibleSlots = false; @endphp
                @foreach($ticket->timeSlots as $slot)
                @php
                    // Lọc giờ cũ
                    if (!$showPastSlots) {
                        $targetDate = \Carbon\Carbon::parse($slot->date)->startOfDay();
                        $today = \Carbon\Carbon::today();
                        if ($targetDate->lt($today)) continue;
                        if ($targetDate->equalTo($today) && $slot->start_time <= \Carbon\Carbon::now()->format('H:i:s')) continue;
                    }
                    $hasVisibleSlots = true;
                    
                    $available = $availabilities[$slot->id] ?? 0;
                    // Chỉ dùng $available (đã tính đúng cả capacity lẫn device limit)
                    // Không cần check $slot->status vì getTrueAvailabilitiesForDate đã xử lý
                    $isFull = $available <= 0;
                @endphp
                <a href="{{ route('pos.slot.detail', [$subdomain, $slot->id]) }}" 
                   class="slot-item {{ $isFull ? 'slot-full' : '' }}" 
                   data-slot-id="{{ $slot->id }}">
                    <div>
                        <div class="slot-time">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                        <div class="slot-time date-tag mt-1">
                            @if(!$isFull)
                                <i class="bi bi-headset-vr text-success"></i> Còn <b class="text-success">{{ $available }}</b> thiết bị
                            @else
                                <i class="bi bi-headset-vr text-danger"></i> <b class="text-danger">Hết thiết bị</b>
                            @endif
                        </div>
                    </div>
                    <div class="slot-avail">
                        @if(!$isFull)
                            <a href="{{ route('pos.sale.form', [$subdomain, $slot->id]) }}" 
                               class="add-btn" 
                               onclick="event.stopPropagation()"
                               title="Bán vé cho slot này">+</a>
                        @else
                            <div class="add-btn disabled text-muted">–</div>
                        @endif
                    </div>
                </a>
                @endforeach
                
                @if(!$hasVisibleSlots)
                <div style="color:var(--pos-text-muted);font-size:0.85rem;padding:16px;text-align:center;">Không có giờ chơi nào phù hợp hôm nay.</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-4">
    {{ $tickets->links('pagination::bootstrap-5') }}
</div>
@endif

{{-- MODAL DANH SÁCH THIẾT BỊ --}}
<div class="modal fade" id="devicesListModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:var(--pos-card); border:1px solid var(--pos-card-border); color:var(--pos-text)">
            <div class="modal-header" style="border-bottom:1px solid var(--pos-card-border)">
                <h5 class="modal-title fs-5 fw-bold"><i class="bi bi-headset-vr me-2"></i>Tình trạng thiết bị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-muted mb-4 small"><i class="bi bi-info-circle me-1"></i> Bấm vào từng kính để cập nhật trạng thái (Hỏng, Sạc, Vệ sinh...)</div>
                <div class="d-flex flex-wrap gap-3 justify-content-center" id="devicesListContainer">
                    {{-- Load từ device-container-hidden --}}
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--pos-card-border)">
                <a href="{{ route('pos.devices', $subdomain) }}" class="btn-pos-outline ms-0 me-auto">
                    <i class="bi bi-gear"></i> Cài đặt nâng cao
                </a>
                <button type="button" class="btn-pos" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL CẬP NHẬT THIẾT BỊ --}}
<div class="modal fade" id="deviceModal" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--pos-card); border:1px solid var(--pos-card-border); color:var(--pos-text); box-shadow: 0 10px 40px rgba(0,0,0,0.5)">
            <div class="modal-header" style="border-bottom:1px solid var(--pos-card-border)">
                <h5 class="modal-title fs-6 fw-bold" id="modalDeviceName">Cập nhật thiết bị</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalDeviceId">
                <div class="pos-form-group">
                    <label>Trạng thái mới</label>
                    <select id="modalNewStatus" class="form-select" onchange="toggleErrorNote()">
                        <option value="available">Sẵn sàng (Xanh)</option>
                        <option value="in_use">Đang dùng (Vàng)</option>
                        <option value="cleaning">Chờ vệ sinh (Cyan)</option>
                        <option value="charging">Đang sạc (Tím)</option>
                        <option value="error">Lỗi / Hỏng (Đỏ)</option>
                    </select>
                </div>
                <div class="pos-form-group" id="errorNoteGroup" style="display:none">
                    <label>Ghi chú lỗi</label>
                    <textarea id="modalErrorNote" rows="2" placeholder="Mô tả tình trạng lỗi..."></textarea>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--pos-card-border)">
                <button type="button" class="btn-pos-outline" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn-pos" id="saveDeviceBtn" onclick="saveDeviceUpdate()">Lưu thay đổi</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL XEM CHI TIẾT GAME --}}
<div class="modal fade" id="ticketModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:var(--pos-card); border:1px solid var(--pos-card-border); color:var(--pos-text)">
            <div class="modal-header d-flex align-items-center" style="border-bottom:1px solid var(--pos-card-border); background: rgba(var(--pos-primary-rgb), 0.03);">
                <h5 class="modal-title fs-5 fw-bold text-primary mb-0" id="ticketModalName">Tên Trò Chơi</h5>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" onclick="toggleTicketModalFullscreen()" style="width:32px; height:32px; border-radius: 8px;" title="Phóng to / Thu nhỏ">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto; background: var(--pos-bg);">
                <div class="d-flex align-items-center justify-content-between mb-3 text-muted" style="font-size:0.9rem;">
                    <div><i class="bi bi-clock-history me-1"></i> Danh sách giờ chơi hôm nay</div>
                    <div><span class="badge bg-secondary">Tối đa 15 người / slot</span></div>
                </div>
                {{-- Container dể JS load html các slots vào đây --}}
                <div id="ticketModalContent"></div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let deviceModal;
let devicesListModal;
let ticketModal;
document.addEventListener('DOMContentLoaded', function() {
    deviceModal = new bootstrap.Modal(document.getElementById('deviceModal'));
    devicesListModal = new bootstrap.Modal(document.getElementById('devicesListModal'));
    ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));
});

function openDevicesListModal() {
    const content = document.getElementById('device-container-hidden').innerHTML;
    document.getElementById('devicesListContainer').innerHTML = content;
    devicesListModal.show();
}

function openTicketModal(ticketId, ticketName) {
    document.getElementById('ticketModalName').textContent = ticketName;
    const slotsHtml = document.getElementById('slots-data-' + ticketId).innerHTML;
    document.getElementById('ticketModalContent').innerHTML = slotsHtml;
    ticketModal.show();
}

function toggleTicketModalFullscreen() {
    const dialog = document.querySelector('#ticketModal .modal-dialog');
    dialog.classList.toggle('modal-fullscreen');
}

function openDeviceModal(el) {
    const id = el.dataset.id;
    const status = el.dataset.status;
    const name = el.dataset.name;

    document.getElementById('modalDeviceId').value = id;
    document.getElementById('modalDeviceName').textContent = 'Cập nhật - ' + name;
    document.getElementById('modalNewStatus').value = status;
    
    toggleErrorNote();
    deviceModal.show();
}

function toggleErrorNote() {
    const status = document.getElementById('modalNewStatus').value;
    document.getElementById('errorNoteGroup').style.display = (status === 'error') ? 'block' : 'none';
}

function saveDeviceUpdate() {
    const id = document.getElementById('modalDeviceId').value;
    const status = document.getElementById('modalNewStatus').value;
    const errorNote = document.getElementById('modalErrorNote').value;
    const btn = document.getElementById('saveDeviceBtn');

    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang lưu...';

    fetch(`/chi-nhanh/{{ $subdomain }}/pos/thiet-bi/${id}/trang-thai`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status, error_note: errorNote })
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            deviceModal.hide();
            // Cập nhật chip mà không reload
            const chip = document.querySelector(`.device-chip[data-id="${id}"]`);
            if (chip) {
                chip.dataset.status = status;
                const dot = chip.querySelector('.dot');
                dot.className = 'dot dot-' + status;
            }
            // Update stats logic could be here too
            showToast('Đã cập nhật trạng thái thiết bị', 'success');
        }
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'Lưu thay đổi';
    });
}

function showToast(msg, type = 'success') {
    // Simple alert for now, can be improved to a real toast
    const alertDiv = document.createElement('div');
    alertDiv.className = `pos-alert pos-alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.style.minWidth = '300px';
    alertDiv.innerHTML = `<i class="bi bi-${type==='success'?'check-circle':'exclamation-triangle'}-fill"></i> ${msg}`;
    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.remove(), 3000);
}

// REAL-TIME AUTO REFRESH
let countdown = 30;
function autoRefresh() {
    countdown--;
    if (countdown <= 0) {
        refreshData();
        countdown = 30;
    }
    const refreshEl = document.getElementById('refresh-status');
    if (refreshEl) refreshEl.textContent = `Tự động cập nhật sau ${countdown}s`;
}

function refreshData() {
    // We can use a custom logic here to only update needed parts or just reload
    // For now, let's keep it simple and just do a location.reload() or targeted fetch
    // To make it feel better, we'll just reload the page for now
    // But in a real "perfected" POS, we'd use AJAX to update slot counts
    console.log('Refreshing POS data...');
    // location.reload(); 
}

setInterval(autoRefresh, 1000);
</script>
@endsection
