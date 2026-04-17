@extends('pos.layout')

@section('title', 'Thiết bị VR')
@section('page-title', 'Quản lý Thiết bị VR')

@section('styles')
<style>
    .devices-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 14px; }

    .device-card {
        background: var(--pos-card);
        border: 2px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 16px;
        transition: border-color .2s;
    }
    .device-card.status-available { border-color: rgba(16,185,129,.3); }
    .device-card.status-in_use    { border-color: rgba(245,158,11,.3); }
    .device-card.status-cleaning  { border-color: rgba(6,182,212,.3); }
    .device-card.status-charging  { border-color: rgba(99,102,241,.3); }
    .device-card.status-error     { border-color: rgba(239,68,68,.4); }

    .device-icon { font-size: 2rem; margin-bottom: 10px; }
    .device-name { font-size: 1rem; font-weight: 800; margin-bottom: 4px; }
    .device-note { font-size: 0.72rem; color: var(--pos-text-muted); margin-bottom: 12px; min-height: 18px; }

    .device-actions { display: flex; flex-direction: column; gap: 6px; }
    .status-btn {
        width: 100%;
        padding: 7px;
        border-radius: 7px;
        border: 1px solid var(--pos-card-border);
        background: rgba(255,255,255,.04);
        color: var(--pos-text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        text-align: center;
    }
    .status-btn:hover { background: rgba(var(--pos-primary-rgb),.15); border-color: var(--pos-primary); color: var(--pos-primary); }
    .status-btn.active { background: rgba(var(--pos-primary-rgb),.2); border-color: var(--pos-primary); color: var(--pos-primary); }

    .stats-chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px; }
    .stats-chip {
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid var(--pos-card-border);
    }

    /* Modal */
    .pos-modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(0,0,0,.7);
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
        width: 360px;
        max-width: 95vw;
    }
    .pos-modal-title { font-size: 1rem; font-weight: 800; margin-bottom: 16px; }
</style>
@endsection

@section('content')

{{-- STATS --}}
<div class="stats-chips mb-4">
    @php
        $countByStatus = $devices->groupBy('status')->map->count();
    @endphp
    <div class="stats-chip status-available"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Sẵn sàng: {{ $countByStatus['available'] ?? 0 }}</div>
    <div class="stats-chip status-in_use"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Đang dùng: {{ $countByStatus['in_use'] ?? 0 }}</div>
    <div class="stats-chip status-cleaning"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Vệ sinh: {{ $countByStatus['cleaning'] ?? 0 }}</div>
    <div class="stats-chip status-charging"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Sạc pin: {{ $countByStatus['charging'] ?? 0 }}</div>
    <div class="stats-chip status-error"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Lỗi: {{ $countByStatus['error'] ?? 0 }}</div>
</div>

@if($devices->isEmpty())
<div class="pos-alert pos-alert-info">
    <i class="bi bi-info-circle"></i>
    Chưa có thiết bị nào. Vui lòng thêm thiết bị trong trang Admin.
</div>
@else
<div class="devices-grid">
    @foreach($devices as $device)
    @php
        $statusLabel = [
            'available' => 'Sẵn sàng',
            'in_use'    => 'Đang dùng',
            'cleaning'  => 'Chờ vệ sinh',
            'charging'  => 'Đang sạc',
            'error'     => 'Lỗi',
        ][$device->status] ?? $device->status;

        $icons = [
            'available' => '🟢',
            'in_use'    => '🟡',
            'cleaning'  => '🔵',
            'charging'  => '🟣',
            'error'     => '🔴',
        ];
    @endphp
    <div class="device-card status-{{ $device->status }}" id="device-card-{{ $device->id }}">
        <div class="device-icon">{{ $icons[$device->status] ?? '⚫' }}</div>
        <div class="device-name">{{ $device->name }}</div>
        <div class="device-note">
            <span class="status-badge status-{{ $device->status }}">{{ $statusLabel }}</span>
            @if($device->battery_percent)
                &nbsp;·&nbsp;🔋{{ $device->battery_percent }}%
            @endif
            @if($device->error_note)
                <div style="margin-top:4px;color:#f87171">{{ $device->error_note }}</div>
            @endif
        </div>

        <div class="device-actions">
            @foreach(['available' => '✅ Sẵn sàng', 'cleaning' => '🧹 Vệ sinh', 'charging' => '🔌 Sạc pin', 'error' => '❌ Lỗi'] as $s => $label)
            <button class="status-btn {{ $device->status === $s ? 'active' : '' }}"
                    onclick="updateStatus({{ $device->id }}, '{{ $s }}')">
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endif

{{-- MODAL LỖI --}}
<div class="pos-modal-backdrop" id="error-modal">
    <div class="pos-modal">
        <div class="pos-modal-title">📝 Ghi chú lỗi</div>
        <div class="pos-form-group">
            <label>Mô tả lỗi</label>
            <textarea id="error-note-input" rows="3" placeholder="Ví dụ: Thấu kính mờ, dây sạc đứt..."></textarea>
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end">
            <button onclick="closeErrorModal()" class="btn-pos-outline">Huỷ</button>
            <button onclick="confirmError()" class="btn-danger-pos">Xác nhận</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
let pendingDeviceId = null;
let pendingStatus   = null;

function updateStatus(deviceId, status) {
    if (status === 'error') {
        pendingDeviceId = deviceId;
        pendingStatus   = status;
        document.getElementById('error-note-input').value = '';
        document.getElementById('error-modal').classList.add('show');
        return;
    }
    doUpdate(deviceId, status, null);
}

function confirmError() {
    const note = document.getElementById('error-note-input').value.trim();
    closeErrorModal();
    doUpdate(pendingDeviceId, pendingStatus, note);
}

function closeErrorModal() {
    document.getElementById('error-modal').classList.remove('show');
}

function doUpdate(deviceId, status, errorNote) {
    fetch(`/chi-nhanh/{{ $subdomain }}/pos/thiet-bi/${deviceId}/trang-thai`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status, error_note: errorNote })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

// Click backdrop để đóng modal
document.getElementById('error-modal').addEventListener('click', function(e) {
    if (e.target === this) closeErrorModal();
});
</script>
@endsection