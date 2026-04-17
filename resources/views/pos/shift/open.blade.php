@extends('pos.layout')

@section('title', 'Mở ca làm việc')
@section('page-title', 'Mở ca làm việc')

@section('styles')
<style>
    .open-shift-wrapper {
        max-width: 480px;
        margin: 0 auto;
    }

    .shift-hero {
        text-align: center;
        padding: 32px 24px 24px;
        background: linear-gradient(135deg, rgba(var(--pos-primary-rgb),.15), rgba(var(--pos-secondary-rgb, 56, 189, 248),.08));
        border: 1px solid rgba(var(--pos-primary-rgb),.25);
        border-radius: 14px;
        margin-bottom: 20px;
    }
    .shift-hero-icon {
        font-size: 3.5rem;
        display: block;
        margin-bottom: 12px;
    }
    .shift-hero h2 {
        font-size: 1.25rem;
        font-weight: 800;
        margin: 0 0 6px;
    }
    .shift-hero p {
        color: var(--pos-text-muted);
        font-size: 0.85rem;
        margin: 0;
    }
 
    .cash-input-wrap {
        position: relative;
    }
    .cash-input-wrap .currency-symbol {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pos-primary);
        font-weight: 700;
        font-size: 0.9rem;
        pointer-events: none;
    }
    .cash-input-wrap input {
        padding-right: 40px !important;
        font-size: 1.2rem !important;
        font-weight: 700 !important;
        text-align: right !important;
    }
 
    .quick-amounts {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-top: 10px;
    }
    .quick-amount-btn {
        padding: 8px;
        border-radius: 8px;
        border: 1px solid var(--pos-card-border);
        background: rgba(255,255,255,.04);
        color: var(--pos-text-muted);
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        text-align: center;
        transition: all .15s;
    }
    .quick-amount-btn:hover {
        border-color: var(--pos-primary);
        color: var(--pos-primary);
        background: rgba(var(--pos-primary-rgb),.1);
    }

    .shift-info-box {
        background: rgba(6,182,212,.07);
        border: 1px solid rgba(6,182,212,.2);
        border-radius: 10px;
        padding: 14px 16px;
        margin-bottom: 16px;
    }
    .shift-info-box .info-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.82rem;
        color: var(--pos-text-muted);
        margin-bottom: 5px;
    }
    .shift-info-box .info-row:last-child { margin-bottom: 0; }
    .shift-info-box .info-row i { color: #22d3ee; width: 16px; }

    /* Nếu ca đang mở (đã có ca) */
    .active-shift-card {
        background: rgba(245,158,11,.08);
        border: 1px solid rgba(245,158,11,.3);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }
    .active-shift-card h3 { font-size: 1rem; font-weight: 700; color: #fbbf24; margin-bottom: 8px; }
    .active-shift-card p { font-size: 0.83rem; color: var(--pos-text-muted); margin-bottom: 16px; }
</style>
@endsection

@section('content')
<div class="open-shift-wrapper">

    @if($activeShift)
    {{-- ĐÃ CÓ CA MỞ --}}
    <div class="shift-hero">
        <span class="shift-hero-icon">⚠️</span>
        <h2>Đã có ca đang mở</h2>
        <p>Chi nhánh {{ $location->name }} hiện đang có ca làm việc.</p>
    </div>

    <div class="active-shift-card">
        <h3><i class="bi bi-door-open me-2"></i>Ca đang mở</h3>
        <p>
            Mở lúc <strong>{{ \Carbon\Carbon::parse($activeShift->opened_at)->format('H:i d/m/Y') }}</strong><br>
            Nhân viên: <strong>{{ $activeShift->user->name ?? 'N/A' }}</strong><br>
            Tiền đầu ca: <strong>{{ number_format($activeShift->opening_cash, 0, ',', '.') }}₫</strong>
        </p>
        <a href="{{ route('pos.dashboard', $subdomain) }}" class="btn-pos w-100" style="justify-content:center">
            <i class="bi bi-grid-1x2"></i> Vào Dashboard
        </a>
        <div style="margin-top:10px">
            <a href="{{ route('pos.shift.close.form', $subdomain) }}" class="btn-danger-pos w-100" style="justify-content:center">
                <i class="bi bi-door-closed"></i> Đóng ca
            </a>
        </div>
    </div>

    @else
    {{-- MỞ CA MỚI --}}
    <div class="shift-hero">
        <span class="shift-hero-icon">🌅</span>
        <h2>Bắt đầu ca làm việc</h2>
        <p>Nhập số tiền mặt có trong két để hệ thống tính lệch tiền chính xác khi đóng ca.</p>
    </div>

    <div class="shift-info-box">
        <div class="info-row"><i class="bi bi-building"></i>Chi nhánh: <strong>{{ $location->name }}</strong></div>
        <div class="info-row"><i class="bi bi-person"></i>Nhân viên: <strong>{{ Auth::user()->name }}</strong></div>
        <div class="info-row"><i class="bi bi-calendar3"></i>Ngày: <strong>{{ now()->format('d/m/Y') }}</strong></div>
        <div class="info-row"><i class="bi bi-clock"></i>Giờ mở ca: <strong id="open-time">{{ now()->format('H:i:s') }}</strong></div>
    </div>

    <form action="{{ route('pos.shift.open', $subdomain) }}" method="POST">
        @csrf

        <div class="pos-card">
            <div class="pos-form-group mb-0">
                <label><i class="bi bi-cash-coin me-1"></i>Tiền mặt đầu ca (tiền lẻ trong két)</label>
                <div class="cash-input-wrap">
                    <input type="number"
                           name="opening_cash"
                           id="opening_cash"
                           value="0"
                           min="0"
                           step="1000"
                           required
                           placeholder="0"
                           oninput="formatPreview(this.value)">
                    <span class="currency-symbol">₫</span>
                </div>

                <div style="margin-top:8px;font-size:0.82rem;color:var(--pos-primary);text-align:right" id="cash-preview">
                    Không có tiền lẻ
                </div>

                <div class="quick-amounts">
                    <button type="button" class="quick-amount-btn" onclick="setAmount(200000)">200k</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(300000)">300k</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(500000)">500k</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(1000000)">1 triệu</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(2000000)">2 triệu</button>
                    <button type="button" class="quick-amount-btn" onclick="setAmount(0)">Xoá</button>
                </div>
            </div>
        </div>

        <div style="margin-top:20px">
            <button type="submit" class="btn-pos w-100" style="justify-content:center;padding:14px;font-size:1rem">
                <i class="bi bi-door-open"></i> Mở ca ngay
            </button>
        </div>
    </form>
    @endif

</div>
@endsection

@section('scripts')
<script>
// Đồng hồ cập nhật giờ mở ca
setInterval(() => {
    const el = document.getElementById('open-time');
    if (el) {
        const now = new Date();
        el.textContent = String(now.getHours()).padStart(2,'0') + ':' +
                         String(now.getMinutes()).padStart(2,'0') + ':' +
                         String(now.getSeconds()).padStart(2,'0');
    }
}, 1000);

function setAmount(val) {
    document.getElementById('opening_cash').value = val;
    formatPreview(val);
}

function formatPreview(val) {
    const n = parseInt(val) || 0;
    const el = document.getElementById('cash-preview');
    if (!el) return;
    el.textContent = n === 0 ? 'Không có tiền lẻ' : n.toLocaleString('vi-VN') + ' đồng';
}
</script>
@endsection