<style>
    /* Hiệu ứng khi di chuột vào các dòng trong menu xám */
    .dropdown-menu[style*="background-color: #f3f4f6"] .dropdown-item:hover {
        background-color: #ffffff; /* Nổi nền trắng khi di chuột */
        border-radius: 8px;
        transform: translateX(5px);
        transition: all 0.2s;
    }
</style>
{{-- Menu --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-black py-3 border-bottom border-secondary sticky-top"
    style="box-shadow: 0 4px 20px rgba(0,0,0,0.5);">
    <div class="container">
        <a class="navbar-brand fw-bold text-info fs-3 d-flex align-items-center" href="{{ route('home') }}">
            <i class="bi bi-vr text-warning me-2"></i> HOLOMIA VR
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">Trang chủ</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                        href="{{ route('about') }}">Giới thiệu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                        href="{{ route('contact') }}">Liên hệ</a>
                </li>

                {{-- 5. ĐẶT VÉ (Dropdown Hover) --}}
                <li class="nav-item dropdown group-hover">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('ticket.*') ? 'active' : '' }}"
                        href="{{ route('ticket.shop') }}" id="navbarDropdown" role="button">
                        Đặt vé
                    </a>

                    {{-- Menu xổ xuống --}}
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarDropdown" style="background-color: #f3f4f6; border-radius: 15px;">
                        {{-- Mục xem tất cả --}}
                        <li>
                            <a class="dropdown-item fw-bold" href="{{ route('ticket.shop') }}">
                                <i class="bi bi-grid-fill me-2 text-info"></i> Xem tất cả
                            </a>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        {{-- Tiêu đề nhỏ --}}
                        <li>
                            <h6 class="dropdown-header text-info" style="font-size: 1.0rem;">Chọn cơ sở
                            </h6>
                        </li>

                        {{-- Vòng lặp hiển thị các cơ sở --}}
                        @if(isset($globalLocations) && count($globalLocations) > 0)
                            @foreach($globalLocations as $loc)
                                <li>
                                    {{-- Link trỏ về trang Shop kèm theo ID địa điểm --}}
                                    <a class="dropdown-item" href="{{ route('ticket.shop', ['location_id' => $loc->id]) }}">
                                        <i class="bi bi-geo-alt-fill me-2 text-danger"></i> {{ $loc->name }}
                                    </a>
                                </li>
                            @endforeach     
                        @else
                            <li><span class="dropdown-item text-muted">Đang cập nhật...</span></li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center d-none d-lg-flex">

            <a href="{{ route('cart.index') }}" class="nav-link text-white position-relative me-4 p-0 cart-icon-hover">
                <i class="bi bi-cart3 fs-4"></i>
                @if(session('cart') && count(session('cart')) > 0)
                    <span
                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-black"
                        style="font-size: 0.65rem; padding: 0.35em 0.5em;">
                        {{ count(session('cart')) }}
                    </span>
                @endif
            </a>

            @if(Auth::check())
                <div class="dropdown">
                   <a href="#" class="btn btn-outline-primary rounded-pill px-4 py-2 dropdown-toggle fw-bold text-uppercase d-flex align-items-center" data-bs-toggle="dropdown">
                     <i class="bi bi-person-circle fs-5 me-2"></i> Hi, {{ Auth::user()->name }}
                   </a>
                   <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="background-color: #f3f4f6; border-radius: 15px;">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.index') }}">Hồ sơ cá nhân</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-bold" style= "color: #dc3545 !important;">
                                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-info rounded-pill px-4 fw-bold text-uppercase">
                    Đăng nhập
                </a>
            @endif
        </div>
    </div>
</nav>