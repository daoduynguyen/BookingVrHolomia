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
    .ticket-card.ticket-maintenance {
        background: #e5e7eb;
        border: 1px solid #cbd5e1;
    }
    .ticket-card.ticket-maintenance:hover {
        transform: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.04);
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
    .ticket-card.ticket-maintenance .ticket-card-img-wrap img {
        transform: none;
        opacity: .5;
        filter: grayscale(100%);
    }
    .ticket-card-body {
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .ticket-maintenance-overlay {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(15, 23, 42, 0.45);
        color: #fff;
        font-weight: 800;
        letter-spacing: .08em;
        font-size: .9rem;
        text-transform: uppercase;
    }

    .ticket-card-title { font-size: 1.05rem; font-weight: 800; margin-bottom: 4px; color: var(--pos-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .ticket-card-price { font-size: 0.9rem; color: var(--pos-primary); font-weight: 700; display:flex; align-items:center; gap:6px; margin-bottom: 12px; }

    .slot-list { padding: 10px 0; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
    @media (max-width: 768px) {
        .slot-list { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 576px) {
        .slot-list { grid-template-columns: 1fr; }
    }

    .slot-item {
        position: relative;
        min-height: 104px;
        padding: 16px 18px 14px;
        border-radius: 16px;
        background: linear-gradient(180deg, rgba(var(--pos-primary-rgb), 0.05) 0%, rgba(var(--pos-primary-rgb), 0.02) 100%);
        border: 1px solid var(--pos-card-border);
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        transition: all .2s ease-in-out;
        color: var(--pos-text);
        cursor: pointer;
        overflow: hidden;
    }
    .slot-item:hover {
        background: rgba(var(--pos-primary-rgb), .10);
        border-color: rgba(var(--pos-primary-rgb), .28);
        color: var(--pos-text);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    }
    .slot-item.slot-full { opacity: .5; cursor: not-allowed; pointer-events: none; }

    .slot-time { font-size: 0.98rem; font-weight: 800; letter-spacing: -0.01em; }
    .slot-time .date-tag { font-size: 0.68rem; color: var(--pos-text-muted); }

    .slot-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .slot-main {
        min-width: 0;
    }

    .slot-badge {
        position: absolute;
        top: 10px;
        right: 12px;
        font-size: 0.68rem;
        font-weight: 700;
        color: #fff;
        background: rgba(71, 85, 105, 0.78);
        padding: 4px 8px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .slot-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-top: 12px;
    }

    .slot-meta {
        font-size: 0.78rem;
        color: var(--pos-text-muted);
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 600;
    }

    .slot-add-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--pos-primary);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        font-weight: 700;
        flex-shrink: 0;
        transition: all 0.2s;
        border: none;
        text-decoration: none;
    }
    .slot-add-btn:hover {
        background: #000;
        transform: rotate(90deg);
        color: #fff;
    }
    .slot-add-btn.disabled {
        background: rgba(255,255,255,.15);
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
    .stat-card-link {
        text-decoration: none;
        color: inherit;
        display: flex;
    }
    .stat-card-link:hover,
    .stat-card-link:focus {
        color: inherit;
    }
    
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

    @keyframes warningPulse {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.36); }
        70% { box-shadow: 0 0 0 14px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    .warning-pulse {
        animation: warningPulse 1.9s ease-in-out infinite;
    }

    .workboard {
        display: grid;
        grid-template-columns: minmax(0, 1.55fr) minmax(280px, 0.75fr);
        gap: 18px;
        margin-bottom: 26px;
    }
    @media (max-width: 992px) {
        .workboard { grid-template-columns: 1fr; }
    }

    .work-panel {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    .work-summary {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin-top: 14px;
    }
    @media (max-width: 768px) {
        .work-summary { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    .work-summary-card {
        border-radius: 16px;
        padding: 14px 16px;
        background: linear-gradient(180deg, rgba(var(--pos-primary-rgb), 0.05), rgba(var(--pos-primary-rgb), 0.02));
        border: 1px solid rgba(var(--pos-primary-rgb), 0.08);
    }
    .work-summary-card .label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--pos-text-muted);
        margin-bottom: 6px;
    }
    .work-summary-card .value {
        display: block;
        font-size: 1.35rem;
        font-weight: 800;
        line-height: 1;
        color: var(--pos-text);
    }

    .task-list {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }

    .task-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 14px;
    }
    .task-filter-btn {
        border: 1px solid rgba(var(--pos-primary-rgb), 0.12);
        background: rgba(var(--pos-primary-rgb), 0.04);
        color: var(--pos-text-muted);
        border-radius: 999px;
        padding: 8px 12px;
        font-size: 0.78rem;
        font-weight: 700;
        transition: all .2s ease;
    }
    .task-filter-btn:hover {
        color: var(--pos-text);
        border-color: rgba(var(--pos-primary-rgb), 0.18);
        transform: translateY(-1px);
    }
    .task-filter-btn.active {
        background: var(--pos-primary);
        color: #fff;
        border-color: var(--pos-primary);
    }

    .task-card {
        border-radius: 18px;
        padding: 16px;
        border: 1px solid var(--pos-card-border);
        background: rgba(var(--pos-primary-rgb), 0.03);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .task-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
        border-color: rgba(var(--pos-primary-rgb), 0.18);
    }
    .task-card[data-state="expired"] {
        border-color: rgba(245, 158, 11, 0.42);
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.10), rgba(var(--pos-primary-rgb), 0.03));
        box-shadow: 0 0 0 1px rgba(245, 158, 11, 0.12), 0 10px 28px rgba(245, 158, 11, 0.10);
    }
    .task-card[data-state="expired"]::before {
        content: 'QUÁ HẠN';
        position: absolute;
        top: 12px;
        right: -32px;
        width: 120px;
        text-align: center;
        font-size: 0.68rem;
        font-weight: 900;
        letter-spacing: .08em;
        color: #fff;
        background: linear-gradient(135deg, #f59e0b, #ef4444);
        transform: rotate(38deg);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.25);
        z-index: 2;
    }
    .task-card[data-state="expired"] .task-chip {
        border-color: rgba(245, 158, 11, 0.28);
    }
    .task-card[data-state="expired"] .task-title {
        color: #ffedd5;
    }
    .task-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }
    .task-title {
        font-weight: 800;
        font-size: 1rem;
        color: var(--pos-text);
        margin-bottom: 3px;
    }
    .task-sub {
        font-size: 0.82rem;
        color: var(--pos-text-muted);
    }
    .task-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 12px;
    }
    .task-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 700;
        background: rgba(255,255,255,.05);
        color: var(--pos-text);
        border: 1px solid rgba(var(--pos-primary-rgb), 0.08);
    }
    .task-chip.primary { color: #38bdf8; }
    .task-chip.success { color: #10b981; }
    .task-chip.warning { color: #f59e0b; }
    .task-chip.danger { color: #f87171; }
    .task-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 14px;
    }
    .task-action-btn {
        border: none;
        border-radius: 12px;
        padding: 9px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all .2s ease;
    }
    .task-action-btn.primary {
        background: var(--pos-primary);
        color: #fff;
    }
    .task-action-btn.primary:hover {
        color: #fff;
        transform: translateY(-1px);
    }
    .task-action-btn.soft {
        background: rgba(var(--pos-primary-rgb), 0.08);
        color: var(--pos-text);
    }
    .task-action-btn.soft:hover {
        background: rgba(var(--pos-primary-rgb), 0.14);
        color: var(--pos-text);
    }

    .quick-actions {
        display: grid;
        gap: 12px;
        margin-top: 16px;
    }
    .quick-action {
        border-radius: 16px;
        padding: 14px 16px;
        text-decoration: none;
        color: var(--pos-text);
        background: linear-gradient(180deg, rgba(var(--pos-primary-rgb), 0.06), rgba(var(--pos-primary-rgb), 0.02));
        border: 1px solid rgba(var(--pos-primary-rgb), 0.08);
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all .2s ease;
    }
    .quick-action:hover {
        color: var(--pos-text);
        transform: translateY(-2px);
        border-color: rgba(var(--pos-primary-rgb), 0.2);
        box-shadow: 0 10px 24px rgba(0,0,0,0.06);
    }
    .quick-action .icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        background: rgba(var(--pos-primary-rgb), 0.1);
        color: var(--pos-primary);
        flex-shrink: 0;
    }
    .quick-action .label {
        font-weight: 800;
        display: block;
        line-height: 1.15;
    }
    .quick-action .desc {
        display: block;
        font-size: 0.78rem;
        color: var(--pos-text-muted);
        margin-top: 3px;
    }
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
    <a href="{{ route('pos.history', $subdomain) }}" class="stat-card stat-card-link" title="Xem danh sách vé đã bán">
        <div class="stat-icon green"><i class="bi bi-receipt"></i></div>
        <div class="stat-info">
            <span class="stat-label">Vé đã bán (ca này)</span>
            <span class="stat-value">{{ $totalOrders }}</span>
        </div>
    </a>
    <a href="{{ route('pos.shift.report', [$subdomain, $activeShift->id]) }}" class="stat-card stat-card-link" title="Xem báo cáo ca hiện tại">
        <div class="stat-icon purple"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-info">
            <span class="stat-label">Doanh thu ca</span>
            <span class="stat-value">{{ number_format($totalRevenue,0,',','.') }}₫</span>
        </div>
    </a>
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
    <a href="#tickets-section" class="stat-card stat-card-link" title="Xem vé hôm nay">
        <div class="stat-icon amber"><i class="bi bi-calendar-date"></i></div>
        <div class="stat-info">
            <span class="stat-label">Hôm nay</span>
            <span class="stat-value" style="font-size:1.15rem;">{{ now()->format('d/m/Y') }}</span>
        </div>
    </a>
</div>

@php
    $urgentWaiting = $taskStats['waiting'] ?? 0;
    $urgentExpired = $taskStats['expired'] ?? 0;
    $dashboardSubdomain = $subdomain ?? request()->route('subdomain');
@endphp

@if(($urgentWaiting + $urgentExpired) > 0)
<div class="pos-alert pos-alert-warning warning-pulse d-flex align-items-center justify-content-between gap-3 mb-4" style="padding: 16px 18px;">
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
        <div>
            <div class="fw-bold">Có công việc cần xử lý ngay</div>
            <div class="small mb-0">
                {{ $urgentWaiting }} vé chờ bắt đầu, {{ $urgentExpired }} vé quá hạn check-in.
            </div>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <button type="button" class="btn-pos-outline" onclick="document.getElementById('tickets-section').scrollIntoView({behavior:'smooth', block:'start'})">
            <i class="bi bi-arrow-down-circle"></i> Tới khu xử lý
        </button>
        <button type="button" class="btn-pos" onclick="filterTasks('waiting', document.querySelector('.task-filter-btn[data-filter=\'waiting\']'))">
            <i class="bi bi-hourglass-split"></i> Lọc vé chờ
        </button>
    </div>
</div>
@endif

<div class="workboard">
    <div class="work-panel">
        <div class="section-header mb-0">
            <div>
                <div class="section-title"><i class="bi bi-lightning-charge me-2"></i>Việc cần làm ngay</div>
                <div class="section-sub">Khách đang chờ check-in, khách đã vào máy và vé quá hạn sẽ hiện ở đây.</div>
            </div>
        </div>

        <div class="task-filters" role="tablist" aria-label="Bộ lọc công việc">
            <button type="button" class="task-filter-btn active" data-filter="all" onclick="filterTasks('all', this)">Tất cả</button>
            <button type="button" class="task-filter-btn" data-filter="waiting" onclick="filterTasks('waiting', this)">Chờ bắt đầu</button>
            <button type="button" class="task-filter-btn" data-filter="playing" onclick="filterTasks('playing', this)">Đang chơi</button>
            <button type="button" class="task-filter-btn" data-filter="expired" onclick="filterTasks('expired', this)">Quá hạn</button>
        </div>

        <div class="work-summary">
            <div class="work-summary-card">
                <span class="label">Chờ bắt đầu</span>
                <span class="value" style="color:#06b6d4">{{ $taskStats['waiting'] ?? 0 }}</span>
            </div>
            <div class="work-summary-card">
                <span class="label">Đang chơi</span>
                <span class="value" style="color:#10b981">{{ $taskStats['playing'] ?? 0 }}</span>
            </div>
            <div class="work-summary-card">
                <span class="label">Quá hạn</span>
                <span class="value" style="color:#f59e0b">{{ $taskStats['expired'] ?? 0 }}</span>
            </div>
            <div class="work-summary-card">
                <span class="label">Đóng / hoàn</span>
                <span class="value" style="color:#f87171">{{ $taskStats['closed'] ?? 0 }}</span>
            </div>
        </div>

        <div class="task-list">
            @if(isset($taskOrders) && $taskOrders->isNotEmpty())
                @foreach($taskOrders as $order)
                <div class="task-card" data-state="{{ $order->work_state }}" onclick="window.location='{{ $order->slot_id ? route('pos.slot.detail', [$subdomain, $order->slot_id]) : route('pos.history', $subdomain) }}'">
                    <div class="task-card-header">
                        <div>
                            <div class="task-title">{{ $order->customer_name ?? 'Khách lẻ' }}</div>
                            <div class="task-sub">{{ $order->ticket_name }} • {{ $order->slot_label }}</div>
                        </div>
                        <span class="task-chip {{ $order->work_state === 'playing' ? 'success' : (in_array($order->work_state, ['waiting', 'pending']) ? 'primary' : ($order->work_state === 'expired' ? 'warning' : 'danger')) }}">
                            <i class="bi bi-{{ $order->work_state === 'playing' ? 'play-fill' : (in_array($order->work_state, ['waiting', 'pending']) ? 'hourglass-split' : ($order->work_state === 'expired' ? 'exclamation-triangle-fill' : 'x-circle-fill')) }}"></i>
                            {{ $order->work_state === 'playing' ? 'Đang chơi' : (in_array($order->work_state, ['waiting', 'pending']) ? 'Chờ bắt đầu' : ($order->work_state === 'expired' ? 'Hết hạn' : ucfirst($order->work_state))) }}
                        </span>
                    </div>

                    <div class="task-meta">
                        <span class="task-chip"><i class="bi bi-person"></i> {{ $order->customer_phone ?? 'Không có SĐT' }}</span>
                        <span class="task-chip"><i class="bi bi-headset-vr"></i> {{ $order->device_name }}</span>
                        <span class="task-chip"><i class="bi bi-ticket-perforated"></i> SL {{ $order->quantity }}</span>
                        @if(in_array($order->work_state, ['waiting', 'pending']))
                            <span class="task-chip primary"><i class="bi bi-alarm"></i> {{ gmdate('i:s', $order->deadline_seconds) }}</span>
                        @elseif($order->work_state === 'playing')
                            <span class="task-chip success"><i class="bi bi-stopwatch"></i> {{ gmdate('i:s', $order->remain_seconds) }}</span>
                        @endif
                    </div>

                    <div class="task-actions">
                        <button type="button" class="task-action-btn soft" onclick="event.stopPropagation(); openOrderModal({{ $order->id }})">
                            <i class="bi bi-eye"></i> Xem nhanh
                        </button>
                        @if($order->slot_id)
                            <a href="{{ route('pos.slot.detail', [$subdomain, $order->slot_id]) }}" class="task-action-btn soft" onclick="event.stopPropagation()">
                                <i class="bi bi-box-arrow-up-right"></i> Mở chi tiết
                            </a>
                        @endif

                        @if($order->work_state === 'waiting')
                            <button type="button" class="task-action-btn primary" onclick="event.stopPropagation(); startOrder({{ $order->id }})">
                                <i class="bi bi-play-fill"></i> Bắt đầu
                            </button>
                        @elseif($order->work_state === 'playing' && $order->device_id)
                            <button type="button" class="task-action-btn soft" onclick="event.stopPropagation(); openDevicesListModal()">
                                <i class="bi bi-gear"></i> Xem máy
                            </button>
                        @elseif($order->work_state === 'expired')
                            <span class="task-action-btn soft" style="pointer-events:none; opacity:.75;">
                                <i class="bi bi-clock-history"></i> Hết hạn
                            </span>
                        @endif
                    </div>
                </div>
                @endforeach
            @else
                <div class="pos-alert pos-alert-info mb-0">
                    <i class="bi bi-check2-circle"></i>
                    Chưa có đơn cần xử lý ngay lúc này.
                </div>
            @endif
        </div>
    </div>

    <div class="work-panel">
        <div class="section-header mb-0">
            <div>
                <div class="section-title"><i class="bi bi-stars me-2"></i>Thao tác nhanh</div>
                <div class="section-sub">Nhân viên có thể mở chức năng phổ biến chỉ bằng một chạm.</div>
            </div>
        </div>

        <div class="quick-actions">
            <a href="{{ route('pos.checkin', $subdomain) }}" class="quick-action">
                <span class="icon"><i class="bi bi-qr-code-scan"></i></span>
                <span>
                    <span class="label">Quét check-in</span>
                    <span class="desc">Mở camera để nhận QR hoặc bắt đầu thủ công</span>
                </span>
            </a>
            <a href="{{ route('pos.history', $subdomain) }}" class="quick-action">
                <span class="icon"><i class="bi bi-receipt"></i></span>
                <span>
                    <span class="label">Danh sách vé đã bán</span>
                    <span class="desc">Xem nhanh các đơn trong ca làm việc</span>
                </span>
            </a>
            <a href="{{ route('pos.shift.report', [$subdomain, $activeShift->id]) }}" class="quick-action">
                <span class="icon"><i class="bi bi-clipboard2-data"></i></span>
                <span>
                    <span class="label">Báo cáo ca</span>
                    <span class="desc">Mở bill chốt ca và đối soát tiền mặt</span>
                </span>
            </a>
            <button type="button" class="quick-action text-start" style="width:100%;" onclick="openDevicesListModal()">
                <span class="icon"><i class="bi bi-headset-vr"></i></span>
                <span>
                    <span class="label">Danh sách máy</span>
                    <span class="desc">Kiểm tra máy trống, máy đang dùng, máy lỗi</span>
                </span>
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="orderQuickModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background:var(--pos-card); border:1px solid var(--pos-card-border); color:var(--pos-text)">
            <div class="modal-header" style="border-bottom:1px solid var(--pos-card-border); background: rgba(var(--pos-primary-rgb), 0.03);">
                <div>
                    <h5 class="modal-title fs-5 fw-bold mb-1" id="orderQuickTitle">Chi tiết đơn</h5>
                    <div class="small text-muted" id="orderQuickSubTitle">Xem nhanh thông tin đơn đang xử lý</div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border" style="border-color: var(--pos-card-border) !important; background: rgba(var(--pos-primary-rgb), 0.03);">
                            <div class="small text-muted mb-1">Khách hàng</div>
                            <div class="fw-bold" id="orderQuickCustomer">-</div>
                            <div class="small text-muted" id="orderQuickPhone">-</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 border" style="border-color: var(--pos-card-border) !important; background: rgba(var(--pos-primary-rgb), 0.03);">
                            <div class="small text-muted mb-1">Trạng thái</div>
                            <div class="fw-bold" id="orderQuickState">-</div>
                            <div class="small text-muted" id="orderQuickDeadline">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 border h-100" style="border-color: var(--pos-card-border) !important; background: rgba(var(--pos-primary-rgb), 0.03);">
                            <div class="small text-muted mb-1">Vé</div>
                            <div class="fw-bold" id="orderQuickTicket">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 border h-100" style="border-color: var(--pos-card-border) !important; background: rgba(var(--pos-primary-rgb), 0.03);">
                            <div class="small text-muted mb-1">Máy</div>
                            <div class="fw-bold" id="orderQuickDevice">-</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 rounded-4 border h-100" style="border-color: var(--pos-card-border) !important; background: rgba(var(--pos-primary-rgb), 0.03);">
                            <div class="small text-muted mb-1">Số lượng</div>
                            <div class="fw-bold" id="orderQuickQty">-</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 rounded-4 border" style="border-color: var(--pos-card-border) !important; background: rgba(var(--pos-primary-rgb), 0.03);">
                            <div class="small text-muted mb-1">Ghi chú</div>
                            <div id="orderQuickNote" class="fw-semibold">Không có ghi chú</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--pos-card-border)">
                <button type="button" class="btn-pos-outline" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn-pos" id="orderQuickActionBtn" onclick="openSelectedOrderDetail()">
                    <i class="bi bi-box-arrow-up-right"></i> Mở chi tiết slot
                </button>
            </div>
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
<div class="section-header d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4" id="tickets-section">
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
    @php $isMaintenance = strtolower(trim((string) ($ticket->status ?? ''))) === 'maintenance'; @endphp
    <div class="ticket-card {{ $isMaintenance ? 'ticket-maintenance' : '' }}" data-ticket-id="{{ $ticket->id }}" @if(!$isMaintenance)onclick="openTicketModal({{ $ticket->id }}, '{{ htmlspecialchars($ticket->name) }}')"@endif>
        <div class="ticket-card-img-wrap">
            @php 
                $img = Str::startsWith($ticket->image ?? $ticket->image_url, 'http') ? ($ticket->image ?? $ticket->image_url) : asset('storage/' . ($ticket->image ?? $ticket->image_url)); 
                if(!$ticket->image && !$ticket->image_url) $img = "https://ui-avatars.com/api/?name=".urlencode($ticket->name)."&background=random&size=400";
            @endphp
            <img src="{{ $img }}" alt="{{ $ticket->name }}">
            @if($isMaintenance)
                <div class="ticket-maintenance-overlay">Bảo trì</div>
            @endif
        </div>
        <div class="ticket-card-body">
            <div class="ticket-card-title">{{ $ticket->name }}</div>
            <div class="ticket-card-price"><i class="bi bi-tag-fill"></i> {{ number_format($ticket->price,0,',','.') }}₫ / người</div>
            
            @if($isMaintenance)
                <button class="btn btn-secondary w-100 mt-auto" style="padding: 10px; border-radius: 12px; font-weight: 700;" disabled>
                    Bảo trì
                </button>
            @else
                <button class="btn-pos w-100 mt-auto" style="padding: 10px; border-radius: 12px; font-weight: 600; display:flex; align-items:center; justify-content:center; gap:6px;">
                    <i class="bi bi-calendar2-range"></i> Xem Giờ Chơi
                </button>
            @endif
        </div>

        {{-- DOM ẩn chứa danh sách Slot để Load vào Modal --}}
        <div id="slots-data-{{ $ticket->id }}" class="d-none">
            @if($isMaintenance)
                <div style="color:var(--pos-text-muted);font-size:0.9rem;padding:16px;text-align:center;">
                    <i class="bi bi-cone-striped me-1"></i> Vé này đang bảo trì.
                </div>
            @else
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
                <div class="slot-item {{ $isFull ? 'slot-full' : '' }}"
                   data-slot-id="{{ $slot->id }}"
                   onclick="window.location='{{ route('pos.slot.detail', [$subdomain, $slot->id]) }}'">
                    <div class="slot-badge">@if(!$isFull) Còn chỗ @else Hết chỗ @endif</div>
                    <div class="slot-card-top">
                        <div class="slot-main">
                            <div class="slot-time">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }} – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}</div>
                            <div class="slot-meta mt-2">
                                @if(!$isFull)
                                    <i class="bi bi-headset-vr text-success"></i>
                                    Còn <b class="text-success">{{ $available }}</b> thiết bị
                                @else
                                    <i class="bi bi-headset-vr text-danger"></i>
                                    <b class="text-danger">Hết thiết bị</b>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="slot-card-footer">
                        <div class="slot-meta">
                            <i class="bi bi-ticket-perforated text-primary"></i>
                            Bán vé nhanh
                        </div>
                        @if(!$isFull)
                            <a href="{{ route('pos.sale.form', [$subdomain, $slot->id]) }}"
                               class="slot-add-btn"
                               onclick="event.stopPropagation()"
                               title="Bán vé cho slot này">+</a>
                        @else
                            <div class="slot-add-btn disabled text-muted">–</div>
                        @endif
                    </div>
                </div>
                @endforeach
                
                @if(!$hasVisibleSlots)
                <div style="color:var(--pos-text-muted);font-size:0.85rem;padding:16px;text-align:center;">Không có giờ chơi nào phù hợp hôm nay.</div>
                @endif
            </div>
            @endif
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
let orderQuickModal;
let selectedOrder = null;

@php
$taskOrdersPayloadData = (isset($taskOrders) ? $taskOrders : collect())->map(function ($order) use ($dashboardSubdomain) {
    return [
        'id' => $order->id,
        'customer_name' => $order->customer_name ?? 'Khách lẻ',
        'customer_phone' => $order->customer_phone ?? '',
        'ticket_name' => $order->ticket_name ?? 'Vé',
        'slot_label' => $order->slot_label ?? '',
        'device_name' => $order->device_name ?? 'Chưa gán máy',
        'work_state' => $order->work_state ?? 'pending',
        'deadline_seconds' => $order->deadline_seconds ?? 0,
        'remain_seconds' => $order->remain_seconds ?? 0,
        'quantity' => $order->quantity ?? 1,
        'note' => $order->note ?? '',
        'slot_id' => $order->slot_id ?? null,
        'slot_url' => $order->slot_id ? route('pos.slot.detail', [$dashboardSubdomain, $order->slot_id]) : route('pos.history', $dashboardSubdomain),
    ];
})->values();
@endphp
const taskOrdersPayload = @json($taskOrdersPayloadData);
document.addEventListener('DOMContentLoaded', function() {
    deviceModal = new bootstrap.Modal(document.getElementById('deviceModal'));
    devicesListModal = new bootstrap.Modal(document.getElementById('devicesListModal'));
    ticketModal = new bootstrap.Modal(document.getElementById('ticketModal'));
    orderQuickModal = new bootstrap.Modal(document.getElementById('orderQuickModal'));
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

function startOrder(orderId) {
    const buttons = document.querySelectorAll(`button[onclick*="startOrder(${orderId})"]`);
    buttons.forEach(btn => {
        btn.disabled = true;
        btn.dataset.oldHtml = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang bắt đầu...';
    });

    fetch(`{{ route('pos.checkin.process', $subdomain) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ order_id: orderId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Đã bắt đầu đơn và gán máy thành công', 'success');
            setTimeout(() => {
                if (data.slot_id) {
                    window.location.href = `/chi-nhanh/{{ $subdomain }}/pos/slot/${data.slot_id}`;
                } else {
                    window.location.reload();
                }
            }, 600);
            return;
        }

        showToast(data.message || 'Không thể bắt đầu đơn này', 'error');
    })
    .catch(() => {
        showToast('Không thể kết nối đến máy chủ', 'error');
    })
    .finally(() => {
        buttons.forEach(btn => {
            btn.disabled = false;
            if (btn.dataset.oldHtml) {
                btn.innerHTML = btn.dataset.oldHtml;
            }
        });
    });
}

function openOrderModal(orderId) {
    selectedOrder = taskOrdersPayload.find(item => String(item.id) === String(orderId)) || null;
    if (!selectedOrder) {
        showToast('Không tìm thấy dữ liệu đơn này', 'error');
        return;
    }

    const stateLabelMap = {
        playing: 'Đang chơi',
        waiting: 'Chờ bắt đầu',
        expired: 'Hết hạn',
        cancelled: 'Đã hủy',
        refunded: 'Đã hoàn',
        pending: 'Chờ bắt đầu'
    };

    document.getElementById('orderQuickTitle').textContent = selectedOrder.customer_name + ' • ' + selectedOrder.ticket_name;
    document.getElementById('orderQuickSubTitle').textContent = selectedOrder.slot_label || 'Chưa có slot';
    document.getElementById('orderQuickCustomer').textContent = selectedOrder.customer_name || 'Khách lẻ';
    document.getElementById('orderQuickPhone').textContent = selectedOrder.customer_phone ? ('SĐT: ' + selectedOrder.customer_phone) : 'Không có SĐT';
    document.getElementById('orderQuickState').textContent = stateLabelMap[selectedOrder.work_state] || selectedOrder.work_state;
    document.getElementById('orderQuickDeadline').textContent = (selectedOrder.work_state === 'waiting' || selectedOrder.work_state === 'pending')
        ? ('Còn ' + formatCountdown(selectedOrder.deadline_seconds) + ' để bắt đầu')
        : (selectedOrder.work_state === 'playing'
            ? ('Còn ' + formatCountdown(selectedOrder.remain_seconds) + ' để kết thúc')
            : '');
    document.getElementById('orderQuickTicket').textContent = selectedOrder.ticket_name || '-';
    document.getElementById('orderQuickDevice').textContent = selectedOrder.device_name || '-';
    document.getElementById('orderQuickQty').textContent = selectedOrder.quantity || 1;
    document.getElementById('orderQuickNote').textContent = selectedOrder.note ? selectedOrder.note : 'Không có ghi chú';
    document.getElementById('orderQuickActionBtn').style.display = selectedOrder.slot_url ? 'inline-flex' : 'none';

    orderQuickModal.show();
}

function openSelectedOrderDetail() {
    if (!selectedOrder || !selectedOrder.slot_url) {
        return;
    }

    window.location.href = selectedOrder.slot_url;
}

function formatCountdown(totalSeconds) {
    const seconds = Math.max(0, parseInt(totalSeconds || 0, 10));
    const minutes = Math.floor(seconds / 60);
    const remainSeconds = seconds % 60;
    return String(minutes).padStart(2, '0') + ':' + String(remainSeconds).padStart(2, '0');
}

function filterTasks(state, button) {
    document.querySelectorAll('.task-filter-btn').forEach(btn => btn.classList.remove('active'));
    if (button) {
        button.classList.add('active');
    }

    document.querySelectorAll('.task-card').forEach(card => {
        const cardState = card.dataset.state || 'all';
        const matchWaiting = state === 'waiting' && (cardState === 'waiting' || cardState === 'pending');
        card.style.display = (state === 'all' || cardState === state || matchWaiting) ? '' : 'none';
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
    window.location.reload();
}

setInterval(autoRefresh, 1000);
</script>
@endsection
