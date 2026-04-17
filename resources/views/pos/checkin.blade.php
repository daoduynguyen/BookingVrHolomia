@extends('pos.layout')

@section('title', 'Check-in QR')
@section('page-title', 'Quét mã QR Check-in')
@section('styles')
<style>
    .checkin-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; max-width: 1000px; margin: 0 auto; }

    .scanner-box {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        overflow: hidden;
    }
    .scanner-box .scan-area {
        width: 100%;
        max-width: 400px;
        margin: 0 auto 16px;
        border: 2px dashed var(--pos-primary);
        border-radius: 12px;
        background: rgba(var(--pos-primary-rgb),.05);
        position: relative;
        overflow: hidden;
    }
    
    #reader {
        width: 100%;
        border: none !important;
    }

    .manual-input { display: flex; gap: 8px; margin-top: 16px; }
    .manual-input input { flex: 1; }

    .result-card {
        background: var(--pos-card);
        border: 1px solid var(--pos-card-border);
        border-radius: 12px;
        padding: 24px;
        display: none;
        animation: slideUp 0.3s ease-out;
    }
    @keyframes slideUp { from {opacity:0; transform:translateY(10px)} to {opacity:1; transform:translateY(0)} }
    
    .result-card.show { display: block; }
    .result-card.success-result { border-color: rgba(16,185,129,.4); box-shadow: 0 0 20px rgba(16,185,129,0.1); }
    .result-card.error-result   { border-color: rgba(239,68,68,.4); box-shadow: 0 0 20px rgba(239,68,68,0.1); }

    .result-icon { font-size: 3.5rem; text-align: center; margin-bottom: 12px; }

    .info-table { width: 100%; }
    .info-table tr td:first-child {
        color: var(--pos-text-muted);
        font-size: 0.78rem;
        width: 40%;
        padding: 6px 0;
    }
    .info-table tr td:last-child {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .recent-checkins { margin-top: 24px; }
    .checkin-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        background: rgba(16,185,129,.06);
        border: 1px solid rgba(16,185,129,.15);
        border-radius: 10px;
        margin-bottom: 10px;
        transition: transform 0.2s;
    }
    .checkin-item:hover { transform: translateX(5px); }
    .checkin-item .ci-icon { font-size: 1.2rem; }
    .checkin-item .ci-name { font-size: 0.88rem; font-weight: 700; color: #34d399; }
    .checkin-item .ci-meta { font-size: 0.75rem; color: var(--pos-text-muted); }
    .checkin-item .ci-time { margin-left: auto; font-size: 0.75rem; color: var(--pos-text-muted); }
    
    /* HTML5 QR Code Overrides */
    #reader__status_span { font-size: 0.7rem !important; color: var(--pos-text-muted) !important; }
    #reader__dashboard_section_csr button {
        background: var(--pos-primary) !important;
        border: none !important;
        color: white !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
    }
</style>
@endsection

@section('content')

<div class="checkin-layout">

    {{-- SCANNER --}}
    <div class="scanner-box">
        <h3 class="fs-6 fw-bold mb-3">Quét QR hoặc nhập mã</h3>
        <div class="scan-area">
            <div id="reader"></div>
        </div>
        
        <div class="manual-input">
            <input type="text" id="qr-input" class="pos-form-group" placeholder="Nhập mã QR thủ công..."
                   value=""
                   autofocus
                   onkeydown="if(event.key==='Enter') processQR(this.value)">
            <button onclick="processQR(document.getElementById('qr-input').value)" class="btn-pos" id="manual-btn">
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <div style="margin-top:16px;font-size:0.75rem;color:var(--pos-text-muted)">
            <i class="bi bi-camera me-1"></i>
            Hệ thống hỗ trợ cả camera và súng scan USB
        </div>
    </div>

    {{-- RESULT --}}
    <div>
        <div class="result-card" id="result-box">
            <div class="result-icon" id="result-icon"></div>
            <div id="result-message" style="text-align:center;font-weight:800;font-size:1.1rem;margin-bottom:16px"></div>
            <table class="info-table" id="result-info"></table>
            <div id="result-actions" style="margin-top:20px;display:flex;gap:10px;justify-content:center"></div>
        </div>

        {{-- RECENT CHECKINS --}}
        <div class="recent-checkins">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div style="font-size:0.75rem;font-weight:700;color:var(--pos-text-muted);text-transform:uppercase;letter-spacing:.05em">
                    <i class="bi bi-clock-history me-1"></i>Check-in gần đây
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill" style="font-size:0.65rem" id="recent-count">0 trong ca</span>
            </div>
            <div id="recent-list">
                <div style="color:var(--pos-text-muted);font-size:0.8rem;text-align:center;padding:20px;border:1px dashed var(--pos-card-border);border-radius:10px">
                    Chưa có check-in nào
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let html5QrCode;
const recentCheckins = [];

document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo scanner
    const config = { fps: 10, qrbox: { width: 250, height: 250 } };
    
    // Nếu có camera, tự động bật (cho phép chọn cam)
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length > 0) {
            html5QrCode = new Html5Qrcode("reader");
            html5QrCode.start(
                { facingMode: "environment" }, 
                config,
                onScanSuccess
            ).catch(err => {
                console.error("Lỗi khởi động camera", err);
                document.getElementById('reader').innerHTML = '<div class="p-4" style="color:var(--pos-text-muted)">Không thể bật camera, hãy sử dụng súng scan hoặc nhập tay.</div>';
            });
        }
    });
});

function onScanSuccess(decodedText, decodedResult) {
    processQR(decodedText);
}

function processQR(token) {
    token = token.trim();
    if (!token) return;

    // Hiển thị loading state
    const manualBtn = document.getElementById('manual-btn');
    if (manualBtn) manualBtn.disabled = true;

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
        
        if (data.success) {
            const infoRows = [
                ['Khách hàng', data.customer_name],
                ['Vé', data.ticket_name],
                ['Giờ chơi', data.slot_time],
                ['Ngày', data.slot_date],
                ['Số lượng', data.quantity + ' người'],
            ];

            const actions = [
                `<a href="/chi-nhanh/{{ $subdomain }}/pos/slot/${data.slot_id}" class="btn-pos">
                    <i class="bi bi-eye"></i> Chi tiết Slot
                </a>`,
                `<button onclick="resetUI()" class="btn-pos-outline">Quét tiếp</button>`
            ];

            showResult('✅', 'XÁC NHẬN THÀNH CÔNG', infoRows, actions, 'success');
            addRecentCheckin(data.customer_name, data.ticket_name, data.slot_time);
            playBeep(true);
        } else {
            showResult('❌', data.message || 'Mã QR không hợp lệ', [], [`<button onclick="resetUI()" class="btn-pos">Thử lại</button>`], 'error');
            playBeep(false);
        }
    })
    .catch(err => {
        showResult('⚠️', 'Lỗi hệ thống hoặc kết nối!', [], [], 'error');
        playBeep(false);
    })
    .finally(() => {
        if (manualBtn) manualBtn.disabled = false;
    });
}

function resetUI() {
    document.getElementById('result-box').classList.remove('show');
    document.getElementById('qr-input').value = '';
    document.getElementById('qr-input').focus();
}

function showResult(icon, message, infoRows, actions, type) {
    const box = document.getElementById('result-box');
    box.className = 'result-card show ' + (type === 'success' ? 'success-result' : type === 'error' ? 'error-result' : '');

    document.getElementById('result-icon').innerHTML = icon;
    document.getElementById('result-message').textContent = message;

    const infoHtml = infoRows.map(([l,v]) => `<tr><td>${l}</td><td>${v}</td></tr>`).join('');
    document.getElementById('result-info').innerHTML = infoHtml;

    document.getElementById('result-actions').innerHTML = actions.join('');
}

function addRecentCheckin(name, ticket, time) {
    const now = new Date();
    const timeStr = now.getHours().toString().padStart(2,'0') + ':' + now.getMinutes().toString().padStart(2,'0');

    recentCheckins.unshift({ name, ticket, time, timeStr });
    if (recentCheckins.length > 5) recentCheckins.pop();

    const html = recentCheckins.map(c => `
        <div class="checkin-item">
            <div class="ci-icon">✅</div>
            <div style="flex:1">
                <div class="ci-name">${c.name}</div>
                <div class="ci-meta">${c.ticket} · ${c.time}</div>
            </div>
            <div class="ci-time">${c.timeStr}</div>
        </div>
    `).join('');
    
    document.getElementById('recent-list').innerHTML = html;
    document.getElementById('recent-count').textContent = recentCheckins.length + ' trong ca';
}

function playBeep(success) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = success ? 880 : 330;
        gain.gain.setValueAtTime(0.1, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
        osc.start();
        osc.stop(ctx.currentTime + 0.3);
    } catch(e) {}
}
</script>
@endsection