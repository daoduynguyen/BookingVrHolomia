@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - {{ $location->name }}</title>
    <meta name="description" content="Liên hệ với {{ $location->name }} để được hỗ trợ và giải đáp thắc mắc.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family={{ $langConfig['font'] }}&family=Orbitron:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        :root { --primary: {{ $location->color ?? '#0d6efd' }}; }
    </style>
</head>

<body class="bg-light text-dark">
    @include('branch.partials.navbar')

    <div class="container py-5">
        <div class="d-flex align-items-center mb-5">
            <div class="me-3" style="width: 50px; height: 3px; background: var(--primary);"></div>
            <h2 class="fw-bold text-uppercase mb-0" style="letter-spacing: 3px; color: var(--primary);">
                Liên hệ {{ $location->name }}
            </h2>
        </div>

        <div class="row g-4">

            {{-- INFO COLUMN --}}
            <div class="col-lg-5">
                <div class="profile-content h-100 shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">
                        Thông tin liên hệ
                    </h4>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-geo-alt-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Địa chỉ</h6>
                            <p class="text-muted mb-0">{{ $location->address ?? 'Đang cập nhật' }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-telephone-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Hotline</h6>
                            <p class="text-muted mb-0">
                                <a href="tel:{{ $location->hotline }}" class="text-muted text-decoration-none">
                                    {{ $location->hotline ?? 'Đang cập nhật' }}
                                </a>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-envelope-at-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Email</h6>
                            <p class="text-muted mb-0">{{ $location->email ?? 'info@holomia.com' }}</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-clock-fill fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Giờ mở cửa</h6>
                            <p class="text-muted mb-0">{{ $location->opening_hours ?? '9:00 - 22:00 hàng ngày' }}</p>
                        </div>
                    </div>

                    @if($location->facebook_url)
                    <div class="d-flex align-items-start mb-4">
                        <i class="bi bi-facebook fs-3 me-3" style="color: var(--primary);"></i>
                        <div>
                            <h6 class="fw-bold text-uppercase mb-1" style="color: var(--primary);">Facebook</h6>
                            <p class="text-muted mb-0">
                                <a href="{{ $location->facebook_url }}" target="_blank" class="text-muted text-decoration-none">
                                    Trang Facebook của chúng tôi
                                </a>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- FORM COLUMN --}}
            <div class="col-lg-7">
                <div class="profile-content shadow-sm bg-white border border-light p-4 rounded-4">
                    <h4 class="text-dark fw-bold mb-4 border-bottom border-light pb-3">
                        Gửi tin nhắn cho chúng tôi
                    </h4>

                    @if(session('success'))
                        <div class="alert alert-success shadow-sm border-0 rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-check-circle-fill fs-4 me-3 text-success"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Gửi thành công!</h6>
                                    <small>{{ session('success') }}</small>
                                </div>
                                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                            <ul class="mb-0 small ps-4">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('branch.contact.send', ['subdomain' => $subdomain]) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Nguyễn Văn A" value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" required>
                            </div>
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="text" name="phone" class="form-control" placeholder="0987 654 321" value="{{ old('phone', Auth::check() ? Auth::user()->phone : '') }}" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="email@example.com" value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Nội dung tin nhắn <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="5" placeholder="Nhập nội dung cần hỗ trợ..." required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-3 text-uppercase shadow-sm">
                            <i class="bi bi-send-fill me-2"></i> Gửi tin nhắn
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('branch.partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
