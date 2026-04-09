<footer class="bg-black border-top border-secondary mt-5">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-md-4">
                <h4 class="fw-bold mb-3" style="color: {{ $location->color ?? '#38bdf8' }};">
                    <i class="bi bi-badge-vr-fill me-2"></i>{{ $location->name }}
                </h4>
                <p class="text-secondary small mb-3">
                    {{ $location->description ?? __('common.footer_desc') }}
                </p>
                <div class="d-flex gap-3">
                    @if($location->facebook_url)
                    <a href="{{ $location->facebook_url }}" target="_blank" class="text-secondary fs-5 hover-text-info" title="Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">
                    {{ __('branch.quick_links') }}
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('branch.home') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('branch.info', ['subdomain' => $subdomain]) }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('branch.info') }}
                        </a>
                    </li>
                    @auth
                    <li class="mb-2">
                        <a href="{{ route('branch.profile', ['subdomain' => $subdomain]) }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('branch.my_account') }}
                        </a>
                    </li>
                    @else
                    <li class="mb-2">
                        <a href="{{ route('branch.login', ['subdomain' => $subdomain]) }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('branch.login') }}
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('branch.register', ['subdomain' => $subdomain]) }}" class="text-secondary text-decoration-none small">
                            <i class="bi bi-chevron-right me-1"></i>{{ __('branch.register') }}
                        </a>
                    </li>
                    @endauth
                    <li class="mb-2 mt-3">
                        <a href="{{ route('home') }}" target="_blank" class="text-secondary text-decoration-none small">
                            <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('branch.main_page') }}
                        </a>
                    </li>
                </ul>
            </div>

            <div class="col-md-4">
                <h6 class="text-white fw-bold mb-3 text-uppercase" style="letter-spacing: 1px;">
                    {{ __('branch.contact_info') }}
                </h6>
                <ul class="list-unstyled">
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-geo-alt-fill me-2" style="color: {{ $location->color ?? '#f87171' }};"></i>
                        {{ $location->address }}
                    </li>
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-telephone-fill me-2" style="color: {{ $location->color ?? '#38bdf8' }};"></i>
                        <a href="tel:{{ $location->hotline }}" class="text-secondary text-decoration-none">{{ $location->hotline }}</a>
                    </li>
                    <li class="mb-2 text-secondary small">
                        <i class="bi bi-clock-fill me-2" style="color: {{ $location->color ?? '#38bdf8' }};"></i>
                        {{ $location->opening_hours ?? __('common.open_hours') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-top py-3" style="border-color: rgba(255,255,255,0.1) !important;">
        <div class="container d-flex flex-wrap justify-content-between align-items-center">
            <small class="text-muted">
                &copy; {{ date('Y') }} {{ $location->name }} - By Holomia VR
            </small>
            <small class="text-muted">
                <i class="bi bi-shield-check text-success me-1"></i>{{ __('common.footer_secure') }}
            </small>
        </div>
    </div>
</footer>