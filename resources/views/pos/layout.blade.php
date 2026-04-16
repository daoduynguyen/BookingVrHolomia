<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'POS') — Holomia VR</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        :root {
            --pos-primary: #7c3aed;
            --pos-primary-dark: #5b21b6;
            --pos-secondary: #06b6d4;
            --pos-success: #10b981;
            --pos-warning: #f59e0b;
            --pos-danger: #ef4444;
            --pos-bg: #0f0c1a;
            --pos-card: #1a1530;
            --pos-card-border: #2d2550;
            --pos-text: #e2e8f0;
            --pos-text-muted: #94a3b8;
            --sidebar-w: 220px;
        }

        * { box-sizing: border-box; }

        body {
            background: var(--pos-bg);
            color: var(--pos-text);
            font-family: 'Be Vietnam Pro', sans-serif;
            margin: 0;
            overflow: hidden;
            height: 100vh;
        }

        /* SIDEBAR */
        .pos-sidebar {
            position: fixed;
            left: 0; top: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--pos-card);
            border-right: 1px solid var(--pos-card-border);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .pos-sidebar .logo {
            padding: 18px 20px;
            border-bottom: 1px solid var(--pos-card-border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pos-sidebar .logo span {
            font-size: 1.1rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--pos-primary), var(--pos-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .pos-sidebar .logo .badge-branch {
            font-size: 0.65rem;
            background: rgba(124,58,237,0.2);
            color: #a78bfa;
            padding: 2px 7px;
            border-radius: 20px;
            margin-top: 2px;
        }

        .pos-nav {
            flex: 1;
            padding: 12px 0;
            overflow-y: auto;
        }

        .pos-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 20px;
            color: var(--pos-text-muted);
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: all .15s;
            border-left: 3px solid transparent;
        }

        .pos-nav a:hover,
        .pos-nav a.active {
            background: rgba(124,58,237,0.12);
            color: #c4b5fd;
            border-left-color: var(--pos-primary);
        }

        .pos-nav a i { font-size: 1rem; width: 20px; text-align: center; }

        .pos-nav .nav-section {
            padding: 8px 20px 4px;
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #4b5563;
            margin-top: 8px;
        }

        .pos-sidebar-bottom {
            padding: 14px 20px;
            border-top: 1px solid var(--pos-card-border);
        }

        .pos-user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .pos-user-info .avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: var(--pos-primary);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700; color: #fff;
            flex-shrink: 0;
        }

        .pos-user-info .name { font-size: 0.82rem; font-weight: 600; color: var(--pos-text); }
        .pos-user-info .role { font-size: 0.7rem; color: var(--pos-text-muted); }

        /* MAIN */
        .pos-main {
            margin-left: var(--sidebar-w);
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .pos-topbar {
            position: sticky; top: 0; z-index: 50;
            background: rgba(15,12,26,0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--pos-card-border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pos-topbar .page-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--pos-text);
        }

        .pos-topbar .shift-badge {
            font-size: 0.75rem;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
        }

        .pos-content {
            flex: 1;
            padding: 20px 24px;
        }

        /* CARDS */
        .pos-card {
            background: var(--pos-card);
            border: 1px solid var(--pos-card-border);
            border-radius: 12px;
            padding: 16px;
        }

        .pos-card-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--pos-text-muted);
            margin-bottom: 12px;
        }

        /* BUTTONS */
        .btn-pos {
            background: var(--pos-primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
        .btn-pos:hover { background: var(--pos-primary-dark); color: #fff; }

        .btn-pos-outline {
            background: transparent;
            color: var(--pos-text-muted);
            border: 1px solid var(--pos-card-border);
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
        .btn-pos-outline:hover { border-color: var(--pos-primary); color: #c4b5fd; }

        .btn-success-pos {
            background: var(--pos-success);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px 18px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
        .btn-success-pos:hover { background: #059669; color: #fff; }

        .btn-danger-pos {
            background: var(--pos-danger);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            text-decoration: none;
        }
        .btn-danger-pos:hover { background: #dc2626; color: #fff; }

        /* BADGES */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.7rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 20px;
        }
        .status-available  { background: rgba(16,185,129,.15); color: #34d399; }
        .status-in_use     { background: rgba(245,158,11,.15);  color: #fbbf24; }
        .status-cleaning   { background: rgba(6,182,212,.15);   color: #22d3ee; }
        .status-charging   { background: rgba(99,102,241,.15);  color: #818cf8; }
        .status-error      { background: rgba(239,68,68,.15);   color: #f87171; }
        .status-open       { background: rgba(16,185,129,.15);  color: #34d399; }
        .status-closed     { background: rgba(148,163,184,.15); color: #94a3b8; }

        /* FORM */
        .pos-form-group { margin-bottom: 14px; }
        .pos-form-group label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--pos-text-muted);
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }
        .pos-form-group input,
        .pos-form-group select,
        .pos-form-group textarea {
            width: 100%;
            background: rgba(255,255,255,.05);
            border: 1px solid var(--pos-card-border);
            border-radius: 8px;
            padding: 10px 14px;
            color: var(--pos-text);
            font-family: 'Be Vietnam Pro', sans-serif;
            font-size: 0.88rem;
            transition: border-color .15s;
        }
        .pos-form-group input:focus,
        .pos-form-group select:focus,
        .pos-form-group textarea:focus {
            outline: none;
            border-color: var(--pos-primary);
            background: rgba(124,58,237,.08);
        }
        .pos-form-group select option { background: #1a1530; }

        /* ALERT */
        .pos-alert {
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.83rem;
            margin-bottom: 14px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        .pos-alert-success { background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.3); color: #34d399; }
        .pos-alert-error   { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.3);  color: #f87171; }
        .pos-alert-info    { background: rgba(6,182,212,.12);  border: 1px solid rgba(6,182,212,.3);  color: #22d3ee; }
        .pos-alert-warning { background: rgba(245,158,11,.12); border: 1px solid rgba(245,158,11,.3); color: #fbbf24; }

        /* TABLE */
        .pos-table { width: 100%; border-collapse: collapse; }
        .pos-table th {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--pos-text-muted);
            padding: 8px 12px;
            border-bottom: 1px solid var(--pos-card-border);
            text-align: left;
        }
        .pos-table td {
            padding: 10px 12px;
            font-size: 0.83rem;
            border-bottom: 1px solid rgba(45,37,80,.5);
        }
        .pos-table tr:last-child td { border-bottom: none; }
        .pos-table tr:hover td { background: rgba(124,58,237,.05); }

        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--pos-card-border); border-radius: 3px; }

        @media print {
            .pos-sidebar, .pos-topbar, .no-print { display: none !important; }
            html, body { 
                height: auto !important; 
                overflow: visible !important; 
                margin: 0 !important; 
                padding: 0 !important; 
                background: #fff !important; 
                color: #000 !important; 
            }
            .pos-main { 
                margin-left: 0 !important; 
                height: auto !important; 
                overflow: visible !important; 
                display: block !important; 
            }
            .pos-content { 
                padding: 0 !important; 
                height: auto !important; 
                overflow: visible !important; 
            }
        }
    </style>

    @yield('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="pos-sidebar">
    <div class="logo">
        <i class="bi bi-controller fs-5" style="color:#7c3aed"></i>
        <div>
            <span>HOLOMIA POS</span>
            <div class="badge-branch">{{ $location->name ?? '' }}</div>
        </div>
    </div>

    <nav class="pos-nav">
        <div class="nav-section">Bán hàng</div>
        <a href="{{ route('pos.dashboard', $subdomain) }}"
           class="{{ request()->routeIs('pos.dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Trang chủ
        </a>
        <a href="{{ route('pos.checkin', $subdomain) }}"
           class="{{ request()->routeIs('pos.checkin*') ? 'active' : '' }}">
            <i class="bi bi-qr-code-scan"></i> Quét mã QR
        </a>

        <div class="nav-section">Quản lý</div>
        <a href="{{ route('pos.devices', $subdomain) }}"
           class="{{ request()->routeIs('pos.devices') ? 'active' : '' }}">
            <i class="bi bi-headset-vr"></i> Thiết bị VR
        </a>
        <a href="{{ route('pos.history', $subdomain) }}"
           class="{{ request()->routeIs('pos.history') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i> Lịch sử giao dịch
        </a>

        <div class="nav-section">Ca làm việc</div>
        @if(isset($activeShift) && $activeShift)
        <a href="{{ route('pos.shift.close.form', $subdomain) }}"
           class="{{ request()->routeIs('pos.shift.close*') ? 'active' : '' }}">
            <i class="bi bi-door-closed"></i> Đóng ca
        </a>
        @else
        <a href="{{ route('pos.shift.open.form', $subdomain) }}"
           class="{{ request()->routeIs('pos.shift.open*') ? 'active' : '' }}">
            <i class="bi bi-door-open"></i> Mở ca
        </a>
        @endif
    </nav>

    <div class="pos-sidebar-bottom">
        <div class="pos-user-info">
            <div class="avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
            <div>
                <div class="name">{{ Auth::user()->name ?? '' }}</div>
                <div class="role">{{ Auth::user()->role ?? 'staff' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('branch.logout', $subdomain) }}">
            @csrf
            <button type="submit" class="btn-pos-outline w-100" style="justify-content:center">
                <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </button>
        </form>
    </div>
</aside>

{{-- MAIN CONTENT --}}
<main class="pos-main">
    <div class="pos-topbar">
        <div class="page-title">@yield('page-title', 'POS')</div>
        <div class="d-flex align-items-center gap-2">
            @if(isset($activeShift) && $activeShift)
                <span class="status-badge status-open">
                    <i class="bi bi-circle-fill" style="font-size:.5rem"></i>
                    Ca đang mở — {{ \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i') }}
                </span>
            @else
                <span class="status-badge status-closed">
                    <i class="bi bi-circle-fill" style="font-size:.5rem"></i>
                    Chưa mở ca
                </span>
            @endif
            <span style="font-size:0.75rem;color:var(--pos-text-muted)" id="pos-clock"></span>
        </div>
    </div>

    <div class="pos-content">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="pos-alert pos-alert-success"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="pos-alert pos-alert-error"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="pos-alert pos-alert-info"><i class="bi bi-info-circle-fill"></i> {{ session('info') }}</div>
        @endif

        @yield('content')
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Đồng hồ realtime
    function updateClock() {
        const now = new Date();
        const h = String(now.getHours()).padStart(2, '0');
        const m = String(now.getMinutes()).padStart(2, '0');
        const s = String(now.getSeconds()).padStart(2, '0');
        const el = document.getElementById('pos-clock');
        if (el) el.textContent = h + ':' + m + ':' + s;
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>

@yield('scripts')
</body>
</html>