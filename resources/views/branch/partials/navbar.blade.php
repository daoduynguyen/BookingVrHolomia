<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/7.2.3/css/flag-icons.min.css">
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
        color:
            {{ $location->color ?? '#38bdf8' }}
        ;
        font-size: 1.8rem;
        filter: drop-shadow(0 0 7px rgba(56, 189, 248, 0.7));
    }

    .holomia-navbar .nav-link {
        color: rgba(255, 255, 255, 0.78) !important;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.7px;
        padding: 0.65rem 1.1rem !important;
        border-radius: 9px;
        transition: all 0.22s ease;
        position: relative;
        white-space: nowrap;
        /* không xuống dòng */
    }

    .holomia-navbar .navbar-nav {
        flex-wrap: nowrap !important;
    }

    .holomia-navbar .nav-item {
        min-width: unset;
    }

    .holomia-navbar .nav-link:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.12);
    }

    .holomia-navbar .nav-link.active {
        color:
            {{ $location->color ?? '#38bdf8' }}
            !important;
        background: rgba(56, 189, 248, 0.16);
    }

    .holomia-navbar .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: 3px;
        left: 20%;
        width: 60%;
        height: 2px;
        background:
            {{ $location->color ?? '#38bdf8' }}
        ;
        border-radius: 2px;
        box-shadow: 0 0 8px rgba(56, 189, 248, 0.8);
    }

    /* Dropdown */
    .holomia-navbar .dropdown-menu {
        background: #0f2252 !important;
        border: 1px solid rgba(56, 189, 248, 0.2) !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 32px rgba(13, 27, 62, 0.5) !important;
        padding: 0.5rem !important;
        margin-top: 6px !important;
    }

    .holomia-navbar .dropdown-item {
        color: rgba(255, 255, 255, 0.8) !important;
        border-radius: 8px !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
        padding: 0.6rem 1rem !important;
        transition: all 0.2s !important;
    }

    .holomia-navbar .dropdown-item:hover {
        background: rgba(56, 189, 248, 0.15) !important;
        color:
            {{ $location->color ?? '#38bdf8' }}
            !important;
        transform: translateX(4px);
    }

    .holomia-navbar .dropdown-header {
        color:
            {{ $location->color ?? '#38bdf8' }}
            !important;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .holomia-navbar .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.1);
    }

    /* Cart icon */
    .holomia-navbar .cart-icon-hover {
        color: rgba(255, 255, 255, 0.8) !important;
        transition: all 0.2s;
    }

    .holomia-navbar .cart-icon-hover:hover {
        color:
            {{ $location->color ?? '#38bdf8' }}
            !important;
        transform: scale(1.12);
    }

    .holomia-navbar .cart-icon-hover i {
        font-size: 1.5rem;
    }

    /* User button */
    .holomia-navbar .btn-user {
        background: rgba(255, 255, 255, 0.1);
        border: 1.5px solid rgba(56, 189, 248, 0.45);
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
    background: rgba(255, 255, 255, 0.15);
    border-color: rgba(255, 255, 255, 0.5);
    color: #ffffff;
    box-shadow: none;
}

    /* User dropdown */
    .holomia-navbar .dropdown-menu-user {
        background: #0f2252;
        border: 1px solid rgba(56, 189, 248, 0.2);
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(13, 27, 62, 0.5);
        padding: 0.5rem;
        min-width: 180px;
    }

    .holomia-navbar .dropdown-menu-user .dropdown-item {
        color: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .holomia-navbar .dropdown-menu-user .dropdown-item:hover {
        background: rgba(56, 189, 248, 0.15);
        color:
            {{ $location->color ?? '#38bdf8' }}
        ;
    }

    .holomia-navbar .dropdown-menu-user .dropdown-item.text-danger {
        color: rgba(252, 165, 165, 0.9) !important;
    }

    .holomia-navbar .dropdown-menu-user .dropdown-item.text-danger:hover {
        background: rgba(239, 68, 68, 0.15);
        color: #fca5a5 !important;
    }
</style>

{{-- Menu --}}
<nav class="holomia-navbar navbar navbar-expand-lg">
    <div class="container">
        <div class="d-flex flex-column justify-content-center">
            <a class="navbar-brand m-0 lh-1" href="{{ route('branch.home', ['subdomain' => $subdomain]) }}"
                style="padding-bottom: 2px;">
                <i class="bi bi-vr"></i> {{ $location->name }}
            </a>
            <small
                style="color:rgba(255,255,255,0.7); font-size:10px; letter-spacing:1px; margin-top:3px; text-transform:uppercase;">
                By Holomia VR
            </small>
        </div>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            style="color: rgba(255,255,255,0.8);">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav align-items-center gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('branch.home') ? 'active' : '' }}"
                        href="{{ route('branch.home', ['subdomain' => $subdomain]) }}">{{ __('navbar.home') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('branch.info') ? 'active' : '' }}"
                        href="{{ route('branch.info', ['subdomain' => $subdomain]) }}">Thông tin</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('branch.shop') ? 'active' : '' }}"
                        href="{{ route('branch.shop', ['subdomain' => $subdomain]) }}">
                        {{ __('navbar.book_ticket') }}
                    </a>
                </li>
            </ul>
        

        <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                @include('partials.lang_switcher')

                <a href="{{ route('branch.cart', ['subdomain' => $subdomain]) }}"
                    class="nav-link cart-icon-hover position-relative p-0">
                    <i class="bi bi-cart3 fs-5"></i>
                    @php $cartCount = session('branch_cart_' . $subdomain) ? count(session('branch_cart_' . $subdomain)) : 0; @endphp
                    @if($cartCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.62rem; padding: 0.3em 0.5em;">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                @if(Auth::check())
                    <div class="dropdown">
                        <a href="#" class="btn-user dropdown-toggle" data-bs-toggle="dropdown">
                            @if(Auth::user()->avatar)
                                <img src="{{ Auth::user()->avatar }}" class="rounded-circle"
                                    style="width:28px; height:28px; object-fit:cover;">
                            @else
                                <i class="bi bi-person-circle fs-5"></i>
                            @endif
                            {{ __('navbar.hi') }}, {{ last(explode(' ', Auth::user()->name)) }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user">
                            <li><a class="dropdown-item" href="{{ route('branch.profile', ['subdomain' => $subdomain]) }}"><i class="bi bi-person me-2"></i> {{ __('navbar.profile') }}</a></li>
                            <li><a class="dropdown-item" href="{{ route('branch.settings', ['subdomain' => $subdomain]) }}"><i class="bi bi-gear me-2"></i> Cài đặt</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('branch.logout', ['subdomain' => $subdomain]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-bold">
                                        <i class="bi bi-box-arrow-right me-2"></i> {{ __('navbar.logout') }}
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('branch.login', ['subdomain' => $subdomain]) }}" class="btn-user text-decoration-none">
                        <i class="bi bi-person"></i> {{ __('navbar.login') }}
                    </a>
                @endif
            </div>
        </div>
</nav>

{{-- ===== GLOBAL UI SETTINGS — chạy trên mọi trang ===== --}}
<script>
    (function () {
        @auth
                @php $ui = Auth::user()->ui_settings ?? []; @endphp
            const serverSettings = {
                theme: '{{ $ui["theme"] ?? "light" }}',
                font: '{{ $ui["font"] ?? "Be Vietnam Pro" }}',
                fontSize: '{{ is_numeric($ui["font_size"] ?? "") ? ($ui["font_size"] ?? 15) : 15 }}',
                color: '{{ $location->color ?? ($ui["primary_color"] ?? "#1e88e5") }}',
                highContrast: {{ ($ui["high_contrast"] ?? false) ? 'true' : 'false' }},
                reduceMotion: {{ ($ui["reduce_motion"] ?? false) ? 'true' : 'false' }},
                largeText:    {{ ($ui["large_text"] ?? false) ? 'true' : 'false' }},
            };
            localStorage.setItem('holomia_ui', JSON.stringify(serverSettings));
            applyHolomiaUI(serverSettings);
        @else
                        try {
                const saved = JSON.parse(localStorage.getItem('holomia_ui') || 'null');
                if (saved) {
                    saved.color = '{{ $location->color ?? "#1e88e5" }}';
                    applyHolomiaUI(saved);
                } else {
                    applyHolomiaUI({ color: '{{ $location->color ?? "#1e88e5" }}', theme: 'light' });
                }
            } catch (e) { }
        @endauth

            function applyHolomiaUI(s) {
                const body = document.body;
                const html = document.documentElement;
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const isDark = s.theme === 'dark' || (s.theme === 'auto' && prefersDark);
                body.classList.toggle('dark-mode', isDark);
                if (s.font) body.style.fontFamily = `'${s.font}', sans-serif`;
                const baseSize = parseInt(s.fontSize) || 15;
                html.style.fontSize = s.largeText ? (baseSize * 1.1) + 'px' : baseSize + 'px';
                if (s.color) {
                    html.style.setProperty('--primary', s.color);
                    html.style.setProperty('--primary-dark', shadeColor(s.color, -20));
                    html.style.setProperty('--primary-light', hexToRgba(s.color, 0.1));
                    html.style.setProperty('--primary-glow', hexToRgba(s.color, 0.18));
                }
                body.classList.toggle('high-contrast', !!s.highContrast);
                const existing = document.getElementById('reduce-motion-style');
                if (existing) existing.remove();
                if (s.reduceMotion) {
                    const style = document.createElement('style');
                    style.id = 'reduce-motion-style';
                    style.textContent = '*, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }';
                    document.head.appendChild(style);
                }
            }

        function shadeColor(hex, pct) {
            const num = parseInt(hex.replace('#', ''), 16);
            const r = Math.max(0, Math.min(255, (num >> 16) + pct * 2.55));
            const g = Math.max(0, Math.min(255, ((num >> 8) & 0xff) + pct * 2.55));
            const b = Math.max(0, Math.min(255, (num & 0xff) + pct * 2.55));
            return '#' + [r, g, b].map(v => Math.round(v).toString(16).padStart(2, '0')).join('');
        }
        function hexToRgba(hex, alpha) {
            const num = parseInt(hex.replace('#', ''), 16);
            return `rgba(${(num >> 16) & 255},${(num >> 8) & 255},${num & 255},${alpha})`;
        }
    })();
</script>