@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <title>{{ $ticket->name }} - {{ __('detail.page_title_suffix') }}</title>
    <meta name="description" content="{{ __('detail.meta_description', ['name' => $ticket->name, 'desc' => Str::limit(strip_tags($ticket->description), 120)]) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
    </style>
</head>

<body class="bg-light text-dark ticket-detail-page">

    @include('branch.partials.navbar')

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}"
                            class="text-secondary text-decoration-none">{{ __('detail.breadcrumb_home') }}</a></li>
                    <li class="breadcrumb-item active text-info" aria-current="page">{{ $ticket->name }}</li>
                </ol>
            </nav>
            <a href="{{ route('branch.home', ['subdomain' => $subdomain]) }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> {{ __('detail.back_btn') }}
            </a>
        </div>

        <div class="row gx-lg-5">
            <div class="col-lg-7 mb-4">
                <div class="custom-scroll-container">

                    @if($ticket->trailer_url)
                        @php $embedUrl = str_replace('watch?v=', 'embed/', $ticket->trailer_url); @endphp
                        <div class="ratio ratio-16x9 mb-4 shadow-sm rounded-4 overflow-hidden border border-light">
                            <iframe src="{{ $embedUrl }}" title="YouTube video" allowfullscreen></iframe>
                        </div>
                    @endif

                    @if($ticket->gallery && count($ticket->gallery) > 0)
                        <div id="gameGallery" class="carousel slide mb-4 shadow-sm rounded-4 overflow-hidden border border-light" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{ $ticket->image_url }}" class="d-block w-100" style="object-fit: cover; height: 450px;" alt="{{ $ticket->name }}">
                                </div>
                                @foreach($ticket->gallery as $img)
                                    <div class="carousel-item">
                                        <img src="{{ asset('storage/' . $img) }}" class="d-block w-100" style="object-fit: cover; height: 450px;" alt="{{ $ticket->name }}">
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#gameGallery" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color:rgba(0,0,0,0.5);border-radius:50%;padding:20px;"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#gameGallery" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true" style="background-color:rgba(0,0,0,0.5);border-radius:50%;padding:20px;"></span>
                            </button>
                        </div>
                    @endif

                    @if(!$ticket->trailer_url && empty($ticket->gallery))
                        <img src="{{ $ticket->image_url }}" class="img-fluid rounded-4 shadow w-100 mb-4" style="object-fit:cover;max-height:450px;">
                    @endif

                    <div class="content-box">
                        <h5 class="section-title text-primary"><i class="bi bi-file-text-fill me-2"></i> {{ __('detail.description_title') }}</h5>
                        <p class="text-muted" style="text-align:justify;line-height:1.7;">{{ $ticket->description }}</p>

                        <div class="mt-5">
                            <h5 class="section-title text-primary"><i class="bi bi-controller me-2"></i> {{ __('detail.rules_title') }}</h5>
                            <div class="bg-white p-3 rounded border border-light shadow-sm">
                                <ul class="mb-0 ps-3 mt-2 text-dark">
                                    <li>{{ __('detail.rule_1') }}</li>
                                    <li>{{ __('detail.rule_2') }}</li>
                                    <li>{{ __('detail.rule_3') }}</li>
                                    <li>{{ __('detail.rule_4') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div class="mt-5">
                            <h5 class="section-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> {{ __('detail.notes_title') }}</h5>
                            <div class="bg-danger bg-opacity-10 p-3 rounded border border-danger border-opacity-25 text-dark">
                                <ul class="mb-0 ps-3">
                                    <li class="mb-2">{!! __('detail.note_1') !!}</li>
                                    <li class="mb-2">{{ __('detail.note_2') }}</li>
                                    <li class="mb-2">{{ __('detail.note_3') }}</li>
                                    <li>{{ __('detail.note_4') }}</li>
                                </ul>
                            </div>
                        </div>

                        <div style="height:50px;"></div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="h-100 d-flex flex-column justify-content-start">

                    <div class="text-center mb-4">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <h1 class="fw-bold text-dark mb-0" style="font-size:2.5rem;">{{ $ticket->name }}</h1>
                            <button id="btn-share" class="btn btn-light border rounded-circle shadow-sm"
                                style="width:40px;height:40px;padding:0;flex-shrink:0;" title="{{ __('detail.share_title') }}">
                                <i class="bi bi-share-fill text-primary" style="font-size:16px;"></i>
                            </button>
                        </div>

                        <div id="share-panel" class="d-none shadow rounded-4 border bg-white p-3 mx-auto" style="max-width:320px;z-index:100;">
                            <p class="fw-bold text-dark mb-3 text-start small">{{ __('detail.share_title') }}</p>
                            <div class="d-flex justify-content-around mb-2">
                                <a href="#" class="share-btn text-center text-decoration-none" data-platform="facebook">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width:50px;height:50px;background:#1877F2;">
                                        <i class="bi bi-facebook text-white fs-5"></i>
                                    </div>
                                    <small class="text-muted" style="font-size:11px;">Facebook</small>
                                </a>
                                <a href="#" class="share-btn text-center text-decoration-none" data-platform="messenger">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width:50px;height:50px;background:linear-gradient(45deg,#0084FF,#B44FE8);">
                                        <i class="bi bi-messenger text-white fs-5"></i>
                                    </div>
                                    <small class="text-muted" style="font-size:11px;">Messenger</small>
                                </a>
                                <a href="#" class="share-btn text-center text-decoration-none" data-platform="zalo">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width:50px;height:50px;background:#0068FF;">
                                        <span class="text-white fw-bold" style="font-size:14px;">Z</span>
                                    </div>
                                    <small class="text-muted" style="font-size:11px;">Zalo</small>
                                </a>
                                <a href="#" class="share-btn text-center text-decoration-none" data-platform="twitter">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1" style="width:50px;height:50px;background:#000;">
                                        <i class="bi bi-twitter-x text-white fs-5"></i>
                                    </div>
                                    <small class="text-muted" style="font-size:11px;">Twitter</small>
                                </a>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex align-items-center gap-2">
                                <input type="text" id="share-link-input" class="form-control form-control-sm bg-light border-light"
                                    value="{{ url()->current() }}" readonly style="font-size:12px;">
                                <button id="btn-copy-link" class="btn btn-primary btn-sm px-3 fw-bold" style="white-space:nowrap;">
                                    <i class="bi bi-clipboard me-1"></i>{{ __('detail.copy_btn') }}
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-center gap-3 text-muted">
                            <span class="d-flex align-items-center gap-1">
                                <i class="bi bi-geo-alt-fill text-danger"></i> {{ $location->name }}
                            </span>
                            <span class="opacity-25">|</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-3 py-2 rounded-pill">
                                {{ $ticket->category->name }}
                            </span>
                        </div>
                    </div>

                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="p-3 rounded-4 bg-white shadow-sm border border-light text-center h-100">
                                <h5 class="fw-bold mb-1 text-dark">{{ $ticket->duration }}p</h5>
                                <small class="text-muted" style="font-size:0.75rem;text-transform:uppercase;">{{ __('detail.stat_duration') }}</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-4 bg-white shadow-sm border border-light text-center h-100">
                                <h5 class="fw-bold mb-1 text-primary">{{ $ticket->avg_rating }} <i class="bi bi-star-fill fs-6"></i></h5>
                                <small class="text-muted" style="font-size:0.75rem;text-transform:uppercase;">{{ __('detail.stat_rating') }}</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-4 bg-white shadow-sm border border-light text-center h-100">
                                <h5 class="fw-bold mb-1 text-dark">{{ number_format($ticket->play_count) }}</h5>
                                <small class="text-muted" style="font-size:0.75rem;text-transform:uppercase;">{{ __('detail.stat_play_count') }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden" style="background-color:var(--bg-card);">
                        <div class="card-body p-4">
                            <h5 class="text-center text-uppercase fw-bold text-primary mb-4 ls-1 border-bottom border-light pb-3">
                                <i class="bi bi-ticket-perforated me-2"></i> {{ __('detail.select_ticket_title') }}
                            </h5>

                            @if($ticket->ticket_types && count($ticket->ticket_types) > 0)
                                @foreach($ticket->ticket_types as $index => $type)
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-3 rounded-3 bg-white shadow-sm border border-light hover-scale"
                                        style="cursor:pointer;" onclick="document.getElementById('type_{{ $index }}').checked=true;">
                                        <div class="form-check m-0">
                                            <input class="form-check-input border-primary" type="radio" name="selected_ticket_type"
                                                id="type_{{ $index }}" value="{{ $type['name'] }}" {{ $index == 0 ? 'checked' : '' }}>
                                            <label class="form-check-label text-dark ms-2 fw-bold" for="type_{{ $index }}">{{ $type['name'] }}</label>
                                        </div>
                                        <span class="fw-bold fs-5 text-primary">{{ number_format($type['price']) }}đ</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-muted text-center fst-italic mb-3">{{ __('detail.price_updating') }}</div>
                            @endif

                            @if($ticket->status == 'maintenance')
                                <button class="btn btn-secondary w-100 btn-lg fw-bold text-uppercase py-3 rounded-pill opacity-75" disabled>
                                    <i class="bi bi-cone-striped me-2"></i> {{ __('detail.maintenance_btn') }}
                                </button>
                            @else
                                <form action="{{ route('branch.booking.form', ['subdomain' => $subdomain, 'id' => $ticket->id]) }}" method="GET" id="booking-form">
                                    <input type="hidden" name="selected_ticket_type" id="hidden_selected_type"
                                        value="{{ isset($ticket->ticket_types[0]['name']) ? $ticket->ticket_types[0]['name'] : '' }}">
                                    <button type="submit" class="btn btn-primary w-100 btn-lg fw-bold text-uppercase py-3 shadow-sm rounded-pill"
                                        style="border:none;color:#fff;letter-spacing:1px;">
                                        {{ __('detail.book_now_btn') }} <i class="bi bi-arrow-right-circle-fill ms-2"></i>
                                    </button>
                                </form>
                            @endif

                            <div class="mt-3 text-center small text-muted fst-italic">
                                <i class="bi bi-shield-check me-1 text-success"></i> {{ __('detail.refund_note') }}
                            </div>
                        </div>
                    </div>

                    <div style="height:50px;"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.querySelectorAll('input[name="selected_ticket_type"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            document.getElementById('hidden_selected_type').value = this.value;
        });
    });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (function() {
        const btnShare  = document.getElementById('btn-share');
        const panel     = document.getElementById('share-panel');
        const btnCopy   = document.getElementById('btn-copy-link');
        const linkInput = document.getElementById('share-link-input');
        const pageUrl   = encodeURIComponent(window.location.href);
        const pageTitle = encodeURIComponent(document.title);

        btnShare.addEventListener('click', function(e) {
            e.stopPropagation();
            panel.classList.toggle('d-none');
        });
        document.addEventListener('click', function(e) {
            if (!panel.contains(e.target) && e.target !== btnShare) panel.classList.add('d-none');
        });
        document.querySelectorAll('.share-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const platform = this.dataset.platform;
                let url = '';
                if (platform === 'facebook') url = 'https://www.facebook.com/sharer/sharer.php?u=' + pageUrl;
                else if (platform === 'messenger') url = 'https://www.facebook.com/dialog/send?link=' + pageUrl + '&app_id=291494419107518&redirect_uri=' + pageUrl;
                else if (platform === 'zalo') url = 'https://zalo.me/share?url=' + pageUrl + '&title=' + pageTitle;
                else if (platform === 'twitter') url = 'https://twitter.com/intent/tweet?url=' + pageUrl + '&text=' + pageTitle;
                if (url) window.open(url, '_blank', 'width=600,height=500');
                panel.classList.add('d-none');
            });
        });
        btnCopy.addEventListener('click', function() {
            navigator.clipboard.writeText(linkInput.value).then(function() {
                btnCopy.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __("detail.copied_btn") }}';
                btnCopy.classList.replace('btn-primary', 'btn-success');
                setTimeout(function() {
                    btnCopy.innerHTML = '<i class="bi bi-clipboard me-1"></i>{{ __("detail.copy_btn") }}';
                    btnCopy.classList.replace('btn-success', 'btn-primary');
                    panel.classList.add('d-none');
                }, 1500);
            });
        });
    })();
    </script>

@include('branch.partials.footer')

</body>
</html>
