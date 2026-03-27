<style>
    .holomia-navbar {
        background: linear-gradient(90deg, #0d1b3e 0%, #0a2a6e 55%, #1565c0 100%);
        box-shadow: 0 4px 24px rgba(13, 27, 62, 0.45);
        padding: 1rem 0;
        position: sticky;
        top: 0;
        z-index: 1030;
    }

    .holomia-navbar .navbar-brand {
        font-family: 'Orbitron', sans-serif;
        font-size: 1.45rem;
        font-weight: 900;
        letter-spacing: 2.5px;
        color: #ffffff !important;
        text-shadow: 0 0 20px rgba(56, 189, 248, 0.55);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .holomia-navbar .navbar-brand i {
        color: #38bdf8;
        font-size: 1.8rem;
        filter: drop-shadow(0 0 7px rgba(56,189,248,0.7));
    }

    .holomia-navbar .nav-link {
        color: rgba(255,255,255,0.78) !important;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        padding: 0.65rem 1.1rem !important;
        border-radius: 9px;
        transition: all 0.22s ease;
        position: relative;
        white-space: nowrap; /* không xuống dòng */
    }

    .holomia-navbar .navbar-nav {
        flex-wrap: nowrap !important;
    }

    .holomia-navbar .nav-item {
        min-width: unset;
    }

    .holomia-navbar .nav-link:hover {
        color: #ffffff !important;
        background: rgba(255,255,255,0.12);
    }

    .holomia-navbar .nav-link.active {
        color: #38bdf8 !important;
        background: rgba(56, 189, 248, 0.16);
    }

    .holomia-navbar .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 3px;
        left: 20%;
        width: 60%;
        height: 2px;
        background: #38bdf8;
        border-radius: 2px;
        box-shadow: 0 0 8px rgba(56,189,248,0.8);
    }

    /* Dropdown */
    .holomia-navbar .dropdown-menu {
        background: #0f2252;
        border: 1px solid rgba(56,189,248,0.2);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(13,27,62,0.5);
        padding: 0.5rem;
        margin-top: 6px;
    }

    .holomia-navbar .dropdown-item {
        color: rgba(255,255,255,0.8);
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        padding: 0.6rem 1rem;
        transition: all 0.2s;
    }

    .holomia-navbar .dropdown-item:hover {
        background: rgba(56,189,248,0.15);
        color: #38bdf8;
        transform: translateX(4px);
    }

    .holomia-navbar .dropdown-header {
        color: #38bdf8 !important;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .holomia-navbar .dropdown-divider {
        border-color: rgba(255,255,255,0.1);
    }

    /* Cart icon */
    .holomia-navbar .cart-icon-hover {
        color: rgba(255,255,255,0.8) !important;
        transition: all 0.2s;
    }
    .holomia-navbar .cart-icon-hover:hover {
        color: #38bdf8 !important;
        transform: scale(1.12);
    }
    .holomia-navbar .cart-icon-hover i {
        font-size: 1.5rem;
    }

    /* User button */
    .holomia-navbar .btn-user {
        background: rgba(255,255,255,0.1);
        border: 1.5px solid rgba(56,189,248,0.45);
        color: #ffffff;
        border-radius: 999px;
        padding: 0.5rem 1.3rem;
        font-weight: 600;
        font-size: 0.92rem;
        transition: all 0.25s;
        display: flex;
        align-items: center;
        gap: 9px;
        text-decoration: none;
    }
    .holomia-navbar .btn-user:hover {
        background: rgba(56,189,248,0.2);
        border-color: #38bdf8;
        color: #38bdf8;
        box-shadow: 0 0 16px rgba(56,189,248,0.28);
    }

    /* User dropdown */
    .holomia-navbar .dropdown-menu-user {
        background: #0f2252;
        border: 1px solid rgba(56,189,248,0.2);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(13,27,62,0.5);
        padding: 0.5rem;
        min-width: 180px;
    }
    .holomia-navbar .dropdown-menu-user .dropdown-item {
        color: rgba(255,255,255,0.8);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .holomia-navbar .dropdown-menu-user .dropdown-item:hover {
        background: rgba(56,189,248,0.15);
        color: #38bdf8;
    }
    .holomia-navbar .dropdown-menu-user .dropdown-item.text-danger {
        color: rgba(252,165,165,0.9) !important;
    }
    .holomia-navbar .dropdown-menu-user .dropdown-item.text-danger:hover {
        background: rgba(239,68,68,0.15);
        color: #fca5a5 !important;
    }
</style>

{{-- Menu --}}
<nav class="holomia-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="bi bi-vr"></i> HOLOMIA VR
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            style="color: rgba(255,255,255,0.8);">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">{{ __('navbar.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}"
                        href="{{ route('about') }}">{{ __('navbar.about') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}"
                        href="{{ route('contact') }}">{{ __('navbar.contact') }}</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('ticket.*') ? 'active' : '' }}"
                        href="{{ route('ticket.shop') }}" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        {{ __('navbar.book_ticket') }}
                    </a>
                    <ul class="dropdown-menu shadow border-0" aria-labelledby="navbarDropdown">
                        <li>
                            <a class="dropdown-item fw-bold" href="{{ route('ticket.shop') }}">
                                <i class="bi bi-grid-fill me-2 text-info"></i> {{ __('navbar.view_all') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <h6 class="dropdown-header">{{ __('navbar.choose_branch') }}</h6>
                        </li>
                        @if(isset($globalLocations) && count($globalLocations) > 0)
                            @foreach($globalLocations as $loc)
                                <li>
                                    <a class="dropdown-item" href="{{ route('ticket.shop', ['location_id' => $loc->id]) }}">
                                        <i class="bi bi-geo-alt-fill me-2" style="color:#f87171;"></i> {{ $loc->name }}
                                    </a>
                                </li>
                            @endforeach
                        @else
                            <li><span class="dropdown-item text-muted">{{ __('navbar.updating') }}</span></li>
                        @endif
                    </ul>
                </li>
            </ul>
        </div>

        <div class="d-flex align-items-center gap-3 d-none d-lg-flex">

            {{-- ===== LANG SWITCHER ===== --}}
            @include('partials.lang_switcher')

            {{-- ===== GIỎ HÀNG ===== --}}
            <a href="{{ route('cart.index') }}" class="nav-link cart-icon-hover position-relative p-0"
               title="{{ __('navbar.cart') }}">
                <i class="bi bi-cart3 fs-5"></i>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                        style="font-size: 0.62rem; padding: 0.3em 0.5em;">
                        {{ count(session('cart')) }}
                    </span>
                @endif
            </a>

            {{-- ===== USER MENU ===== --}}
            @if(Auth::check())
                <div class="dropdown">
                    <a href="#" class="btn-user dropdown-toggle" data-bs-toggle="dropdown">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar }}" class="rounded-circle"
                                style="width:28px; height:28px; object-fit:cover;">
                        @else
                            <i class="bi bi-person-circle fs-5"></i>
                        @endif
                        {{ __('navbar.hi') }}, {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.index') }}">
                                <i class="bi bi-person me-2"></i> {{ __('navbar.profile') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger fw-bold">
                                    <i class="bi bi-box-arrow-right me-2"></i> {{ __('navbar.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn-user text-decoration-none">
                    <i class="bi bi-person"></i> {{ __('navbar.login') }}
                </a>
            @endif
        </div>
    </div>
</nav>