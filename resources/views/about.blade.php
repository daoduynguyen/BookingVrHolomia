@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('about.page_title') }}</title>
    <meta name="description" content="{{ __('about.meta_description') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
    </style>
</head>

<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6">
                <img src="https://holomia.com/images/contents/17194557751_16576174651-homejpgjpg.jpg"
                    class="img-fluid rounded shadow" alt="Holomia Team">
            </div>
            <div class="col-lg-6">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info me-3" style="width: 50px; height: 2px;"></div>
                    <h4 class="text-info fw-bold text-uppercase letter-spacing-2 mb-0"
                        style="font-size: 1.25rem; letter-spacing: 3px;">
                        {{ __('about.title') }}
                    </h4>
                </div>
                <h1 class="display-4 fw-bold mb-4">{{ __('about.hero_title') }} <br><span class="text-primary">{{ __('about.hero_subtitle') }}</span></h1>
                <p class="lead text-muted mb-4">{{ __('about.intro') }}</p>
                <p class="text-dark opacity-75 mb-4">{{ __('about.body') }}</p>
                <div class="d-flex gap-3">
                    <div class="text-center p-3 border border-light rounded-3 bg-white shadow-sm">
                        <h3 class="text-primary fw-bold mb-0">50+</h3>
                        <small class="text-uppercase text-muted" style="font-size: 0.7rem;">{{ __('about.stat_games') }}</small>
                    </div>
                    <div class="text-center p-3 border border-light rounded-3 bg-white shadow-sm">
                        <h3 class="text-primary fw-bold mb-0">10k+</h3>
                        <small class="text-uppercase text-muted" style="font-size: 0.7rem;">{{ __('about.stat_customers') }}</small>
                    </div>
                    <div class="text-center p-3 border border-light rounded-3 bg-white shadow-sm">
                        <h3 class="text-primary fw-bold mb-0">4.9/5</h3>
                        <small class="text-uppercase text-muted" style="font-size: 0.7rem;">{{ __('about.stat_rating') }}</small>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="container py-5"> 
        <div class="row g-4"> {{-- Thêm g-4 để tạo khoảng cách giữa 2 khối --}}

            {{-- Khối Tầm nhìn --}}
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-eye fs-2 text-primary me-3"></i>
                        <h4 class="fw-bold text-dark mb-0">{{ __('about.vision_title') }}</h4>
                    </div>
                    <p class="text-muted">{{ __('about.vision_content') }}</p>
                </div>
            </div>

            {{-- Khối Sứ mệnh --}}
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-rocket-takeoff fs-2 text-primary me-3"></i>
                        <h4 class="fw-bold text-dark mb-0">{{ __('about.mission_title') }}</h4>
                    </div>
                    <p class="text-muted">{{ __('about.mission_content') }}</p>
                </div>
            </div>

        </div>
    </div>

    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>