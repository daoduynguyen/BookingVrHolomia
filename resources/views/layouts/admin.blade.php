<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Holomia VR</title>

    {{-- CSS --}}
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family=Be+Vietnam+Pro:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- CUSTOM CSS CHO SIDEBAR ADMIN --}}
    <style>
        /* 1. Reset Body */
        body {
            background-color: #f0f4f8;
            font-family: 'Be Vietnam Pro', sans-serif;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        /* 2. Layout */
        .admin-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* 3. Sidebar — gradient xanh VR */
        .sidebar-container {
            width: 272px;
            background: linear-gradient(160deg, #0d1b3e 0%, #0a2a6e 40%, #1565c0 100%);
            height: 100vh;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            z-index: 100;
            box-shadow: 6px 0 24px rgba(13, 27, 62, 0.35);
            position: relative;
            overflow: hidden;
        }

        /* Hiệu ứng ánh sáng nền */
        .sidebar-container::before {
            content: '';
            position: absolute;
            top: -80px;
            left: -80px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(56, 189, 248, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .sidebar-container::after {
            content: '';
            position: absolute;
            bottom: 60px;
            right: -60px;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, transparent 70%);
            pointer-events: none;
        }

        /* 4. Logo */
        .sidebar-brand {
            padding: 2rem 1.5rem 1.6rem;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 0.75rem;
            position: relative;
            z-index: 1;
        }

        .sidebar-brand h3 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.4rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0;
            color: #ffffff;
            text-shadow: 0 0 20px rgba(56, 189, 248, 0.6);
        }

        .sidebar-brand .highlight {
            color: #38bdf8;
            font-weight: 900;
            display: block;
            font-size: 0.8rem;
            letter-spacing: 5px;
            margin-top: 2px;
            opacity: 0.9;
        }

        /* 5. Menu scroll */
        .sidebar-menu-wrapper {
            flex-grow: 1;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0.5rem 0;
            position: relative;
            z-index: 1;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar { width: 3px; }
        .sidebar-menu-wrapper::-webkit-scrollbar-track { background: transparent; }
        .sidebar-menu-wrapper::-webkit-scrollbar-thumb {
            background: rgba(56, 189, 248, 0.3);
            border-radius: 4px;
        }

        /* 6. Menu Item */
        .nav-link {
            color: rgba(255, 255, 255, 0.65) !important;
            padding: 11px 20px 11px 22px;
            margin: 2px 12px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            border-radius: 10px;
            border-left: none !important;
            gap: 12px;
            position: relative;
        }

        .nav-link i {
            font-size: 1.25rem;
            width: 26px;
            text-align: center;
            transition: all 0.25s ease;
            flex-shrink: 0;
            color: rgba(255,255,255,0.55);
        }

        .nav-link span {
            color: inherit;
        }

        /* Hover */
        .nav-link:hover {
            color: #ffffff !important;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(4px);
        }

        .nav-link:hover i {
            color: #38bdf8 !important;
            transform: scale(1.15);
        }

        /* Active */
        .nav-link.active {
            color: #ffffff !important;
            background: linear-gradient(90deg, rgba(56,189,248,0.28) 0%, rgba(56,189,248,0.08) 100%);
            box-shadow: 0 4px 16px rgba(56, 189, 248, 0.15), inset 0 0 0 1px rgba(56,189,248,0.25);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 20%;
            height: 60%;
            width: 3px;
            background: #38bdf8;
            border-radius: 0 3px 3px 0;
            box-shadow: 0 0 10px rgba(56,189,248,0.8);
        }

        .nav-link.active i {
            color: #38bdf8 !important;
            transform: scale(1.2);
            filter: drop-shadow(0 0 6px rgba(56,189,248,0.7));
        }

        /* 7. Logout */
        .logout-container {
            padding: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.1);
            position: relative;
            z-index: 1;
        }

        .btn-logout {
            border: 1px solid rgba(239, 68, 68, 0.6);
            color: rgba(252, 165, 165, 0.9);
            background: rgba(239, 68, 68, 0.08);
            font-weight: 600;
            transition: all 0.3s;
            letter-spacing: 0.5px;
            font-size: 0.88rem;
            border-radius: 10px;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.85);
            color: white;
            border-color: transparent;
            box-shadow: 0 0 18px rgba(239, 68, 68, 0.45);
            transform: translateY(-1px);
        }

        /* 8. Main content */
        .main-content {
            flex-grow: 1;
            height: 100vh;
            overflow-y: auto;
            background-color: #f0f4f8;
            padding: 2rem;
        }

        .main-content::-webkit-scrollbar { width: 8px; }
        .main-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .main-content::-webkit-scrollbar-thumb:hover {
            background: #1565c0;
        }
    </style>
</head>

<body class="text-dark bg-light">

    <div class="admin-wrapper">

        {{-- === SIDEBAR === --}}
        <div class="sidebar-container">

            {{-- LOGO --}}
            <div class="sidebar-brand">
                <h3>Holomia <br><span class="highlight">Admin</span></h3>
            </div>

            {{-- MENU --}}
            <div class="sidebar-menu-wrapper">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Thống kê</span>
                </a>
            </li>
            
            
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.slots.*') ? 'active' : '' }}"
                    href="{{ route('admin.slots.index') }}">
                    <i class="bi bi-calendar3"></i>
                    <span>Lịch khung giờ</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.bookings.index') }}"
                    class="nav-link {{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}">
                    <i class="bi bi-cart-check"></i>
                    <span>Duyệt đơn hàng</span>
                </a>
            </li>

            {{-- BỌC QUYỀN: Chỉ Sếp tổng (super_admin) mới thấy các Menu bên dưới --}}
            @if(Auth::check() && Auth::user()->role === 'super_admin')
                <li class="nav-item">
                <a href="{{ route('admin.tickets.index') }}"
                    class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated"></i>
                    <span>Quản lý vé</span>
                   </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}"
                        href="{{ route('admin.locations.index') }}">
                        <i class="bi bi-geo-alt"></i>
                        <span>Cơ sở</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}"
                        href="{{ route('admin.coupons.index') }}">
                        <i class="bi bi-ticket-perforated-fill"></i>
                        <span>Kho Voucher</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.users') }}"
                        class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i>
                        <span>Người dùng</span>
                        </a>
                        <a href="{{ route('admin.settings.index') }}"
                            class="nav-link {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                            <i class="bi bi-sliders"></i>
                            <span>Hệ thống</span>
                        </a>
                        </li>

                <li class="nav-item">
                    <a href="{{ route('admin.contacts') }}"
                        class="nav-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i>
                        <span>Tin nhắn</span>
                    </a>
                </li>
            @endif
            
<a href="{{ route('admin.chatbot.index') }}"
     class="nav-link {{ request()->routeIs('admin.chatbot.*') ? 'active' : '' }}">
      <i class="bi bi-robot"></i>
     <span>Chatbot AI</span>
     @php $pendingChat = \App\Models\ChatCache::where('approved', false)->count(); @endphp
      @if($pendingChat > 0)
          <span class="badge bg-danger ms-auto rounded-pill">{{ $pendingChat }}</span>
      @endif
  </a>
            {{-- FOOTER (LOGOUT) --}}
            <div class="logout-container">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-logout w-100 py-2 rounded-3">
                        <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
        </div>
        {{-- === END SIDEBAR === --}}

        {{-- === MAIN CONTENT === --}}
        <div class="main-content">
            @yield('admin_content')
        </div>

    </div>

    {{-- Script JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Script tự động ẩn thông báo sau 3 giây --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                var alerts = document.querySelectorAll('.alert');
                alerts.forEach(function (alert) {
                    var bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 3000);
        });
    </script>

{{-- SweetAlert2 Confirm Xóa --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Bắt tất cả form có class "form-delete"
    document.querySelectorAll('.form-delete').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const label = form.dataset.label || 'mục này';
            Swal.fire({
                title: 'Xác nhận xóa?',
                html: 'Bạn sắp xóa <strong>' + label + '</strong>.<br>Hành động này <u>không thể hoàn tác</u>!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash3"></i> Xóa ngay',
                cancelButtonText: 'Hủy',
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
</body>

</html>