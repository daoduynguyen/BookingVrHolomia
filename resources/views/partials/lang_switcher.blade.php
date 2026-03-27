{{--
    PARTIAL: partials/lang_switcher.blade.php
    Dropdown chọn ngôn ngữ — nhúng vào navbar hoặc bất kỳ layout nào.
    Sử dụng: @include('partials.lang_switcher')
--}}

@php
    $currentLocale = app()->getLocale();
    $languages     = config('i18n.supported', []);
    $current       = $languages[$currentLocale] ?? $languages['vi'];
@endphp

<div class="dropdown lang-switcher">
    <button
        class="btn-lang dropdown-toggle"
        type="button"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        title="{{ __('navbar.language') }}"
    >
        <span class="lang-flag">{{ $current['flag'] }}</span>
        <span class="lang-code d-none d-lg-inline">{{ strtoupper($currentLocale) }}</span>
        <i class="bi bi-chevron-down lang-chevron"></i>
    </button>

    <ul class="dropdown-menu dropdown-menu-end lang-menu">
        @foreach($languages as $code => $lang)
            <li>
                <a
                    class="dropdown-item lang-item {{ $currentLocale === $code ? 'active' : '' }}"
                    href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}"
                >
                    <span class="lang-item-flag">{{ $lang['flag'] }}</span>
                    <span class="lang-item-name">{{ $lang['native'] }}</span>
                    @if($currentLocale === $code)
                        <i class="bi bi-check2 ms-auto text-info"></i>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
    /* ===== LANG SWITCHER ===== */
    .lang-switcher .btn-lang {
        background: rgba(255, 255, 255, 0.14);
        border: 1.5px solid rgba(56, 189, 248, 0.35);
        color: #ffffff;
        border-radius: 999px;
        padding: 0.42rem 0.85rem;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.22s ease;
        line-height: 1;
        white-space: nowrap;
    }

    .lang-switcher .btn-lang span,
    .lang-switcher .lang-code,
    .lang-switcher .lang-flag {
        white-space: nowrap;
    }

    .lang-switcher .btn-lang,
    .lang-switcher .btn-lang:hover,
    .lang-switcher .btn-lang:focus,
    .lang-switcher.show .btn-lang {
        color: #ffffff !important;
    }

    .lang-switcher .btn-lang:not(:hover) {
        opacity: 1;
    }

    .lang-switcher .btn-lang:hover,
    .lang-switcher .btn-lang:focus,
    .lang-switcher.show .btn-lang {
        background: rgba(56, 189, 248, 0.18);
        border-color: #38bdf8;
        color: #38bdf8;
        box-shadow: 0 0 14px rgba(56, 189, 248, 0.22);
        outline: none;
    }

    .lang-switcher .btn-lang::after {
        display: none; /* ẩn caret mặc định của bootstrap */
    }

    .lang-flag   { font-size: 1.1rem; line-height: 1; }
    .lang-code   { font-size: 0.78rem; letter-spacing: 0.5px; }
    .lang-chevron { font-size: 0.65rem; opacity: 0.7; transition: transform 0.22s; }
    .lang-switcher.show .lang-chevron { transform: rotate(180deg); }

    /* Dropdown menu */
    .lang-switcher .dropdown-menu {
        background: #0f2252 !important;
        border: 1px solid rgba(56, 189, 248, 0.2) !important;
        border-radius: 14px !important;
        box-shadow: 0 10px 40px rgba(13, 27, 62, 0.6) !important;
        margin-top: 8px !important;
    }

    .lang-menu {
        background: transparent;
        border: 0;
        box-shadow: none;
        padding: 0.25rem 0;
        min-width: 210px;
    }

    .lang-item {
        color: rgba(255, 255, 255, 0.95) !important;
        border-radius: 9px;
        font-size: 0.95rem;
        font-weight: 500;
        padding: 0.55rem 0.9rem !important;
        display: flex;
        align-items: center;
        gap: 9px;
        transition: all 0.18s;
        text-decoration: none;
        background: rgba(255,255,255,0.03) !important;
    }


    .lang-item:hover {
        background: rgba(56, 189, 248, 0.14) !important;
        color: #38bdf8 !important;
        transform: translateX(3px);
    }

    .lang-item.active {
        background: rgba(56, 189, 248, 0.18) !important;
        color: #38bdf8 !important;
        font-weight: 700;
    }

    .lang-item-flag { font-size: 1.1rem; flex-shrink: 0; }
    .lang-item-name { flex: 1; }
</style>
