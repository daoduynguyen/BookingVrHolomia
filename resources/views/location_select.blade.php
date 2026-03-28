@php
    $locale     = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn cơ sở – Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700;900&family={{ $langConfig['font'] }}&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: {{ $langConfig['font_family'] }};
            background: linear-gradient(135deg, #0d1b4b 0%, #0a2a6e 40%, #0055c4 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            width: 500px; height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,120,255,0.25) 0%, transparent 70%);
            top: -100px; right: -100px;
            pointer-events: none;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,200,255,0.15) 0%, transparent 70%);
            bottom: -80px; left: -80px;
            pointer-events: none;
        }

        .location-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            font-family: 'Orbitron', monospace;
            font-size: 2.2rem;
            font-weight: 900;
            color: #0d1b4b;
            letter-spacing: 2px;
            text-align: center;
            margin-bottom: 5px;
        }

        .brand-logo span { color: #00c8ff; }

        .brand-tagline {
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .form-header { text-align: center; margin-bottom: 25px; }
        .form-header p { color: #6b7280; font-size: 0.95rem; margin-top: 10px; }

        .location-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-bottom: 20px;
            max-height: 380px;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        /* Custom Scrollbar for Grid */
        .location-grid::-webkit-scrollbar { width: 6px; }
        .location-grid::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
        .location-grid::-webkit-scrollbar-thumb { background: #0055c4; border-radius: 10px; }
        .location-grid::-webkit-scrollbar-thumb:hover { background: #003da0; }

        .loc-card {
            background: #f8fafc;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            position: relative;
            user-select: none;
        }

        .loc-card:hover {
            border-color: #0055c4;
            background: #eff6ff;
        }

        .loc-card.selected {
            border-color: #0055c4;
            background: #eff6ff;
            box-shadow: 0 0 0 3px rgba(0,85,196,0.12);
        }

        .check-badge {
            display: none;
            position: absolute;
            top: 8px; right: 8px;
            width: 20px; height: 20px;
            background: #0055c4;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
        }
        .loc-card.selected .check-badge { display: flex; }
        .check-badge i { color: #fff; font-size: 11px; }

        .loc-icon-wrap {
            width: 44px; height: 44px;
            border-radius: 12px;
            background: rgba(0,85,196,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .loc-name { font-size: 0.9rem; font-weight: 600; color: #0d1b4b; }
        .loc-address { font-size: 0.75rem; color: #6b7280; line-height: 1.3; margin-bottom: 4px; }
        
        .loc-badge {
            font-size: 0.7rem; padding: 2px 8px; border-radius: 20px;
            background: #dbeafe; color: #1e40af; font-weight: 500;
        }

        .loc-card-all { border-style: dashed; border-color: #d1d5db; }
        .loc-card-all:hover, .loc-card-all.selected { border-style: dashed; border-color: #0055c4; }

        .btn-confirm {
            background: linear-gradient(135deg, #0055c4, #0078ff);
            border: none; color: #fff; font-weight: 600; font-size: 1rem;
            padding: 14px; border-radius: 10px; width: 100%;
            transition: all 0.2s; letter-spacing: 0.5px;
            opacity: 0.45; cursor: not-allowed; margin-top: 10px;
        }

        .btn-confirm.ready { opacity: 1; cursor: pointer; }
        .btn-confirm.ready:hover {
            background: linear-gradient(135deg, #003da0, #0060d4);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,85,196,0.35);
        }

        .back-link {
            color: #6b7280; font-size: 0.85rem; text-decoration: none;
            display: inline-flex; align-items: center; gap: 4px; margin-top: 20px;
        }
        .back-link:hover { color: #374151; }

        @media (max-width: 500px) {
            .location-grid { grid-template-columns: 1fr; }
            .location-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

    <div class="location-card">
        <div class="form-header">
            <div class="brand-logo">HOLOMIA <span>VR</span></div>
            <div class="brand-tagline">{{ __('auth.tagline') }}</div>
            <p class="small text-muted mt-2" style="font-size: 0.85rem;">Chọn cơ sở gần bạn để xem dịch vụ tốt nhất</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger" style="font-size: 0.85rem; padding: 10px; border-radius: 8px;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form action="{{ route('location.store') }}" method="POST" id="locationForm">
            @csrf
            <input type="hidden" name="location" id="locationInput" value="">

            <div class="location-grid">
                @foreach($locations as $loc)
                <div class="loc-card" onclick="selectLocation('{{ $loc->id }}', this)">
                    <div class="check-badge"><i class="bi bi-check"></i></div>
                    <div class="loc-icon-wrap">
                        <i class="bi bi-building" style="color:#0055c4;"></i>
                    </div>
                    <div class="loc-name">{{ $loc->name }}</div>
                    <div class="loc-address">{{ $loc->address }}</div>
                    @if($loc->hotline)
                        <span class="loc-badge"><i class="bi bi-telephone-fill me-1"></i>{{ $loc->hotline }}</span>
                    @endif
                </div>
                @endforeach

                <div class="loc-card loc-card-all" onclick="selectLocation('all', this)">
                    <div class="check-badge"><i class="bi bi-check"></i></div>
                    <div class="loc-icon-wrap" style="background: rgba(107,114,128,0.1);">
                        <i class="bi bi-globe" style="color:#6b7280;"></i>
                    </div>
                    <div class="loc-name">Tất cả cơ sở</div>
                    <div class="loc-address">Xem toàn bộ trò chơi hệ thống</div>
                    <span class="loc-badge" style="background:#f3f4f6; color:#374151;">Tham khảo</span>
                </div>
            </div>

            <button type="submit" class="btn-confirm" id="confirmBtn" disabled>
                <i class="bi bi-arrow-right-circle-fill me-2"></i>Xem dịch vụ
            </button>
        </form>

        <div class="text-center">
            <a href="{{ route('home') }}" class="back-link">
                <i class="bi bi-arrow-left"></i> Về trang chủ
            </a>
        </div>
    </div>

    <script>
    function selectLocation(id, el) {
        document.querySelectorAll('.loc-card').forEach(c => c.classList.remove('selected'));
        el.classList.add('selected');
        document.getElementById('locationInput').value = id;
        const btn = document.getElementById('confirmBtn');
        btn.disabled = false;
        btn.classList.add('ready');
    }
    </script>

</body>
</html>