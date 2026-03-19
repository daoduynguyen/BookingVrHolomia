<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Holomia VR</title>

    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- CUSTOM CSS CHO SIDEBAR ADMIN --}}
    <style>
        /* 1. Reset Body để ẩn thanh cuộn thừa của toàn trang */
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            overflow: hidden;
            /* CẤM TOÀN TRANG CUỘN */
            margin: 0;
            padding: 0;
        }

        /* 2. Layout Bọc Ngoài Cùng */
        .admin-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        /* 3. Khung Sidebar (Bên trái) */
        .sidebar-container {
            width: 280px;
            background: #ffffff;
            border-right: 1px solid #e5e7eb;
            height: 100vh;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
            flex-shrink: 0;
            /* Ngăn không cho menu bị bóp méo khi màn hình nhỏ */
            z-index: 100;
        }

        /* 4. Logo */
        .sidebar-brand {
            padding: 2.5rem 1.5rem;
            text-align: center;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 1.5rem;
        }

        .sidebar-brand h3 {
            font-size: 1.5rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin: 0;
            color: #1f2937;
        }

        .sidebar-brand .highlight {
            color: #2563eb;
            font-weight: 800;
        }

        /* 5. Menu Container (Có thể cuộn nếu menu quá dài) */
        .sidebar-menu-wrapper {
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
        }

        /* Tinh chỉnh thanh cuộn của Menu (Mỏng, đẹp) */
        .sidebar-menu-wrapper::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 4px;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

       /* 6. Menu Item */
.nav-link {
    color: #000000 !important; /* Đã đổi sang màu đen tuyền */
    padding: 12px 24px;
    margin-bottom: 4px;
    font-weight: 500;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    border-left: 3px solid transparent;
}

        .nav-link i {
            width: 30px;
            font-size: 1.1rem;
            text-align: center;
            margin-right: 10px;
            transition: 0.3s;
        }

        .nav-link:hover {
            color: #1f2937 !important;
            background-color: rgba(37, 99, 235, 0.05);
            padding-left: 28px;
        }

        .nav-link:hover i {
            color: #2563eb;
        }

        .nav-link.active {
            color: #2563eb !important;
            background: linear-gradient(90deg, rgba(37, 99, 235, 0.1) 0%, rgba(0, 0, 0, 0) 100%);
            border-left: 3px solid #2563eb;
        }

        .nav-link.active i {
            color: #2563eb;
            transform: scale(1.1);
        }

        /* 7. Nút đăng xuất */
        .logout-container {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            background: #ffffff;
            /* Đảm bảo màu nền dính liền */
        }

        .btn-logout {
            border: 1px solid #dc3545;
            color: #dc3545;
            background: transparent;
            font-weight: 600;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .btn-logout:hover {
            background: #dc3545;
            color: white;
            box-shadow: 0 0 15px rgba(220, 53, 69, 0.4);
        }

        /* 8. KHU VỰC NỘI DUNG CHÍNH (Bên Phải) */
        .main-content {
            flex-grow: 1;
            height: 100vh;
            overflow-y: auto;
            /* THANH CUỘN CHÍNH NẰM Ở ĐÂY */
            background-color: #f3f4f6;
            padding: 2rem;
            /* Giảm padding xuống một chút cho đỡ trống */
        }

        /* Tinh chỉnh thanh cuộn của phần nội dung */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #2563eb;
        }

        .sidebar-container .nav-link:not(.active),
        .sidebar-container .nav-link:not(.active) span,
        .sidebar-container .nav-link:not(.active) i {
            color: #000000 !important;
            font-weight: 600; 
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
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.contacts') }}"
                        class="nav-link {{ request()->routeIs('admin.contacts*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots"></i>
                        <span>Tin nhắn</span>
                    </a>
                </li>
            @endif

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
</body>

</html>