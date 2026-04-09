@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <title>{{ __('about.page_title') ?? 'Thông tin' }}</title>
    <meta name="description" content="{{ __('about.meta_description') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
    </style>
</head>

<body class="bg-light text-dark">
    @include('partials.navbar')

    {{-- ABOUT SECTION --}}
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-md-6" data-aos="fade-right" data-aos-duration="1000">
                <img src="https://holomia.com/images/contents/17194557751_16576174651-homejpgjpg.jpg"
                    class="img-fluid rounded shadow" alt="Holomia Team">
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
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

    <div class="container py-4"> 
        <div class="row g-4">
            <div class="col-md-6" data-aos="zoom-in" data-aos-duration="800">
                <div class="p-4 bg-white rounded-4 h-100 border border-light shadow-sm">
                        <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-eye fs-2 text-primary me-3"></i>
                        <h4 class="fw-bold text-dark mb-0">{{ __('about.vision_title') }}</h4>
                    </div>
                    <p class="text-muted">{{ __('about.vision_content') }}</p>
                </div>
            </div>

            <div class="col-md-6" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="200">
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

    {{-- CONTACT SECTION --}}
    <div class="container py-5">
        <div class="d-flex align-items-center mb-5" data-aos="fade-up">
            <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0" style="letter-spacing: 3px;">
                {{ __('contact.heading') }}
            </h2>
        </div>

        <div class="row g-4">

            {{-- INFO COLUMN --}}
            <div class="col-lg-5" data-aos="fade-up" data-aos-delay="100">
                <div class="profile-content h-100 shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">
                        {{ __('contact.info_title') }}
                    </h4>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-geo-alt-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">
                                {{ __('contact.address_label') }}
                            </h6>
                            <p class="text-muted mb-0">{{ __('contact.address_value') }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-telephone-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">
                                {{ __('contact.hotline_label') }}
                            </h6>
                            <p class="text-muted mb-0">024.6666.8888</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-envelope-at-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">
                                {{ __('contact.email_label') }}
                            </h6>
                            <p class="text-muted mb-0">info@holomia.com</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-clock-fill text-primary fs-3 me-3"></i>
                        <div>
                            <h6 class="text-primary text-uppercase fw-bold mb-1">
                                {{ __('contact.hours_label') }}
                            </h6>
                            <p class="text-muted mb-0">{{ __('contact.hours_value') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FORM COLUMN --}}
            <div class="col-lg-7" data-aos="fade-left" data-aos-delay="200">
                <div class="profile-content shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">
                        {{ __('contact.form_title') }}
                    </h4>

                    @if(session('success'))
                        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ __('contact.send_success') }}</h6>
                                    <small>{{ session('success') }}</small>
                                </div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center mb-2">
                                <i class="bi bi-exclamation-triangle-fill fs-4 me-3 text-danger"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">{{ __('contact.send_failed') }}</h6>
                                    <small>{{ __('contact.check_fields') }}</small>
                                </div>
                            </div>
                            <ul class="mb-0 small ps-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4" role="alert">
                            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label">{{ __('contact.your_name') }}</label>
                                <input type="text" name="name" class="form-control"
                                    placeholder="{{ __('contact.your_name_placeholder') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    {{ __('contact.email_address') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control"
                                    placeholder="{{ __('contact.email_placeholder') }}" required>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">
                                    {{ __('contact.phone_number') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="phone" class="form-control"
                                    placeholder="{{ __('contact.phone_placeholder') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">{{ __('contact.message_label') }}</label>
                            <textarea name="message" class="form-control" rows="5"
                                placeholder="{{ __('contact.message_placeholder') }}" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 text-uppercase shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> {{ __('contact.send_message') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- AI CHAT WIDGET --}}
    <div id="ai-icon" style="position: fixed; bottom: 30px; right: 30px; cursor: pointer; z-index: 9999;">
        <img src="https://cdn-icons-png.flaticon.com/512/4712/4712035.png" width="70" class="shadow-lg rounded-circle">
        <span class="badge rounded-pill bg-info text-dark" style="position: absolute; top: 0; right: 0;">Online</span>
    </div>

    <div id="ai-chat-box" class="bg-white shadow-lg rounded"
        style="display: none; position: fixed; bottom: 110px; right: 30px; width: 350px; height: 450px; z-index: 10000; border: 1px solid #0dcaf0; overflow: hidden;">
        <div class="bg-primary p-3 d-flex justify-content-between align-items-center">
            <strong class="text-white">{{ __('contact.ai_title') }}</strong>
            <span id="close-chat" style="cursor: pointer; color: white; font-weight: bold;">X</span>
        </div>
        <div id="chat-content" class="p-3"
            style="height: 330px; overflow-y: auto; font-size: 14px; background: #f8f9fa;">
            <div class="text-primary mb-2">AI: {{ __('contact.ai_greeting') }}</div>
        </div>
        <div class="p-2 border-top border-light bg-light">
            <div class="input-group">
                <input type="text" id="user-input" class="form-control bg-white text-dark border-1"
                    placeholder="{{ __('contact.ai_placeholder') }}">
                <button class="btn btn-primary" id="send-btn"><i class="bi bi-send-fill"></i></button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#ai-icon').click(function () { $(this).fadeOut(200); $('#ai-chat-box').fadeIn(300); });
            $('#close-chat').click(function () { $('#ai-chat-box').fadeOut(300); $('#ai-icon').fadeIn(200); });

            function sendMessage() {
                let msg = $('#user-input').val().trim();
                if (!msg) return;
                $('#chat-content').append('<div class="text-white text-end mb-3"><div class="p-2 rounded bg-info text-dark d-inline-block" style="max-width: 80%">' + msg + '</div></div>');
                $('#user-input').val('');
                $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);

                $.post("{{ route('ai.chat') }}", { _token: "{{ csrf_token() }}", message: msg }, function (data) {
                    $('#chat-content').append('<div class="mb-3 text-start"><span class="badge bg-secondary mb-1">AI</span><br><div class="p-2 rounded bg-secondary text-white d-inline-block" style="max-width: 80%">' + data.reply + '</div></div>');
                    $('#chat-content').scrollTop($('#chat-content')[0].scrollHeight);
                });
            }
            $('#send-btn').click(sendMessage);
            $('#user-input').keypress(function (e) { if (e.which == 13) sendMessage(); });
        });

        // Auto-dismiss alerts after 4s
        setTimeout(function () {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 4000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 800, offset: 50 });
    </script>

    @include('partials.footer')
</body>

</html>
