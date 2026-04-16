@extends('pos.layout')

@section('title', 'Check-in QR')
@section('page-title', 'Quét mã QR Check-in')

@section('styles')
<style>
    .checkin-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 900px; }

    .scanner-box {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
    }
    .scanner-box .scan-area {
        width: 200px; height: 200px;
        margin: 0 auto 16px;
        border: 2px dashed var(--pos-primary);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(124,58,237,.05);
        position: relative;
        overflow: hidden;
    }
    .scanner-box .scan-area::before {
        content: '';
        position: absolute;
        left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--pos-primary), transparent);
        animation: scan 2s linear infinite;
    }
    @keyframes scan {
        0%  { top: 0; }
        100%{ top: 200px; }
    }

    .scan-area i { font-size: 4rem; color: rgba(124,58,237,.4); }

    .manual-input { display: flex; gap: 8px; margin-top: 16px; }
    .manual-input input { flex: 1; }

    .result-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 24px;
        display: none;
    }
    .result-card.show { display: block; }
    .result-card.success-result { border-color: rgba(16,185,129,.4); }
    .result-card.error-result   { border-color: rgba(239,68,68,.4); }

    .result-icon { font-size: 3rem; text-align: center; margin-bottom: 12px; }

    .info-table { width: 100%; }
    .info-table tr td:first-child {
        color: var(--pos-text-muted);
        font-size: 0.78rem;
        width: 40%;
        padding: 5px 0;
    }
    .info-table tr td:last-child {
        font-size: 0.85rem;
        font-weight: 600;
    }

    .recent-checkins { margin-top: 20px; }
    .checkin-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: rgba(16,185,129,.06);
        border: 1px solid rgba(16,185,129,.15);
        border-radius: 8px;
        margin-bottom: 8px;
    }
    .checkin-item .ci-icon { font-size: 1.2rem; color: #34d399; }
    .checkin-item .ci-name { font-size: 0.85rem; font-weight: 700; }
    .checkin-item .ci-meta { font-size: 0.72rem; color: var(--pos-text-muted); }
    .checkin-item .ci-time { margin-left: auto; font-size: 0.72rem; color: var(--pos-text-muted); }
</style>
@endsection

@section('content')

<div class="checkin-layout">

    {{-- SCANNER --}}
    <div class="scanner-box">
        <div class="scan-area">
            <i class="bi bi-qr-code"></i>
        </div>
        <p style="color:var(--pos-text-muted);font-size:0.82rem;margin:0 0 4px">
            Đưa mã QR vào vùng quét hoặc nhập thủ công:
        </p>

        <div class="manual-input">
            <input type="text" id="qr-input" class="pos-form-group" placeholder="Nhập / quét mã QR..."
                   style="background:rgba(255,255,255,.05);border:1px solid var(--pos-card-border);border-radius:8px;padding:10px 14px;color:var(--pos-text);font-size:0.88rem;flex:1"
                   autofocus
                   onkeydown="if(event.key==='Enter') processQR()">
            <button onclick="processQR()" class="btn-pos" style="padding:0 16px">
                <i class="bi bi-check2"></i>
            </button>
        </div>

        <div style="margin-top:16px;font-size:0.72rem;color:var(--pos-text-muted)">
            <i class="bi bi-info-circle me-1"></i>
            Súng scan USB sẽ tự động nhập và xác nhận
        </div>
    </div>

    {{-- RESULT --}}
    <div>
        <div class="result-card" id="result-box">
            <div class="result-icon" id="result-icon"></div>
            <div id="result-message" style="text-align:center;font-weight:700;margin-bottom:14px"></div>
            <table class="info-table" id="result-info"></table>
            <div id="result-actions" style="margin-top:16px;display:flex;gap:8px;flex-wrap:wrap"></div>
        </div>

        {{-- RECENT CHECKINS --}}
        <div class="recent-checkins">
            <div style="font-size:0.75rem;font-weight:700;color:var(--pos-text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">
                <i class="bi bi-clock-history me-1"></i>Check-in gần đây
            </div>
            <div id="recent-list">
                <div style="color:var(--pos-text-muted);font-size:0.78rem">Chưa có check-in trong ca này</div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const recentCheckins = [];

// Xử lý khi súng scan hoặc nhập tay
let autoScanBuffer = '';
let autoScanTimer;

document.getElementById('qr-input').addEventListener('input', function(e) {
    // Nếu súng scan, sẽ nhập nhanh và kết thúc
    clearTimeout(autoScanTimer);
    autoScanTimer = setTimeout(() => {
        if (this.value.length > 10) {
            processQR();
        }
    }, 200);
});

function processQR() {
    const token = document.getElementById('qr-input').value.trim();
    if (!token) return;

    // Hiển thị loading
    showResult('⏳', 'Đang xác thực...', [], [], 'info');

    fetch('{{ route('pos.checkin.process', $subdomain) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ qr_token: token })
    })
    .then(r => r.json())
    .then(data => {
        document.getElementById('qr-input').value = '';
        document.getElementById('qr-input').focus();

        if (data.success) {
            const infoRows = [
                ['Khách hàng', data.customer_name],
                ['Số điện thoại', data.customer_phone],
                ['Loại vé', data.ticket_name],
                ['Khung giờ', data.slot_time],
                ['Ngày', data.slot_date],
                ['Số lượng', data.quantity + ' vé'],
            ];

            const actions = [
                `<a href="/chi-nhanh/{{ $subdomain }}/pos/slot/${data.slot_id ?? ''}" class="btn-pos" style="font-size:0.8rem">
                    <i class="bi bi-controller"></i> Xem slot
                </a>`
            ];

            showResult('✅', 'Check-in thành công!', infoRows, actions, 'success');

            // Thêm vào recent
            addRecentCheckin(data.customer_name, data.ticket_name, data.slot_time);

            // Âm thanh thành công (optional)
            playBeep(true);
        } else {
            showResult('❌', data.message || 'Mã QR không hợp lệ', [], [], 'error');
            playBeep(false);
        }
    })
    .catch(() => {
        showResult('⚠️', 'Lỗi kết nối, vui lòng thử lại', [], [], 'error');
        document.getElementById('qr-input').value = '';
        document.getElementById('qr-input').focus();
    });
}

function showResult(icon, message, infoRows, actions, type) {
    const box = document.getElementById('result-box');
    box.className = 'result-card show ' + (type === 'success' ? 'success-result' : type === 'error' ? 'error-result' : '');

    document.getElementById('result-icon').textContent = icon;
    document.getElementById('result-message').textContent = message;

    const infoHtml = infoRows.map(([l,v]) => `<tr><td>${l}</td><td>${v}</td></tr>`).join('');
    document.getElementById('result-info').innerHTML = infoHtml;

    document.getElementById('result-actions').innerHTML = actions.join('');
}

function addRecentCheckin(name, ticket, time) {
    const now = new Date();
    const timeStr = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0');

    recentCheckins.unshift({ name, ticket, time, timeStr });
    if (recentCheckins.length > 5) recentCheckins.pop();

    const html = recentCheckins.map(c => `
        <div class="checkin-item">
            <div class="ci-icon">✅</div>
            <div>
                <div class="ci-name">${c.name}</div>
                <div class="ci-meta">${c.ticket} · ${c.time}</div>
            </div>
            <div class="ci-time">${c.timeStr}</div>
        </div>
    `).join('');
    document.getElementById('recent-list').innerHTML = html;
}

function playBeep(success) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.value = success ? 880 : 220;
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.3);
    } catch(e) {}
}
</script>
@endsection