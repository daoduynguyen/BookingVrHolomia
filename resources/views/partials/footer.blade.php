<footer class="bg-black border-top border-secondary mt-5">
    <div class="container py-5">
        <div class="row g-4">

            {{-- CỘT 1: THƯƠNG HIỆU --}}
            <div class="col-md-4">
                <h4 class="text-info fw-bold mb-3">
                    <i class="bi bi-badge-vr-fill me-2"></i>HOLOMIA VR
                </h4>
                <p class="text-secondary small mb-3">
                    {{ __('common.footer_desc') }}
                </p>
                <div class="d-flex gap-3">
                    <a href="#" class="text-secondary fs-5 hover-text-info" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    <a href="#" class="text-secondary fs-5" title="Instagram">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="text-secondary fs-5" title="YouTube">
                        <i class="bi bi-youtube"></i>
                    </a>
                    <a href="#" class="text-secondary fs-5" title="TikTok">
                        <i class="bi bi-tiktok"></i>
                    </a>
                </div>
            </div>

            {{-- CỘT 2: LIÊN KẾT NHANH --}}
            <div class="col-md-2">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">
                    {{ __('common.footer_links') }}
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('home') }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('navbar.home') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('ticket.shop') }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('navbar.book_ticket') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('info') }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>Thông tin
                        </a>
                    </li>
                    @auth
                    <li class="mb-2">
                        <a href="{{ route('profile.index') }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('common.profile') }}
                        </a>
                    </li>
                    @endauth
                </ul>
            </div>

            {{-- CỘT 3: CHI NHÁNH --}}
            <div class="col-md-3">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">
                    {{ __('common.footer_branches') }}
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Holomia Royal City
                    </li>
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Holomia Aeon Mall
                    </li>
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-geo-alt-fill text-danger me-2"></i>Lotte HaNoi Sky
                    </li>
                </ul>
            </div>

            {{-- CỘT 4: LIÊN HỆ --}}
            <div class="col-md-3">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">
                    {{ __('common.footer_contact') }}
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-telephone-fill text-info me-2"></i>
                        <a href="tel:19001234" class="text-secondary text-decoration-none">1900 1234</a>
                    </li>
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-envelope-fill text-info me-2"></i>
                        <a href="mailto:support@holomia.vn" class="text-secondary text-decoration-none">support@holomia.vn</a>
                    </li>
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-clock-fill text-info me-2"></i>
                        {{ __('common.open_hours') }}
                    </li>
                </ul>
            </div>

        </div>
    </div>

    {{-- BOTTOM BAR --}}
    <div class="border-top border-secondary py-3">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <small class="text-muted">
                {{ __('common.footer_copyright', ['year' => date('Y')]) }}
            </small>
            <small class="text-muted">
                <i class="bi bi-shield-check text-success me-1"></i>{{ __('common.footer_secure') }}
            </small>
        </div>
    </div>
</footer>
