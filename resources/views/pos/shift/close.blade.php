@extends('pos.layout')

@section('title', 'Đóng ca')
@section('page-title', 'Đóng ca làm việc')

@section('styles')
<style>
    .close-layout {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 20px;
    }

    /* Summary rows */
    .payment-summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 12px;
        margin-bottom: 20px;
    }
    .pay-sum-card {
        background: rgba(255,255,255,.03);
        border: 1px solid var(--pos-card-border);
        border-radius: 10px;
        padding: 14px;
        text-align: center;
    }
    .pay-sum-card .icon { font-size: 1.5rem; margin-bottom: 6px; }
    .pay-sum-card .label { font-size: 0.68rem; color: var(--pos-text-muted); text-transform: uppercase; letter-spacing:.05em; margin-bottom: 4px; }
    .pay-sum-card .amount { font-size: 1rem; font-weight: 800; }
    .pay-sum-card.cash-card   .amount { color: #34d399; }
    .pay-sum-card.card-card   .amount { color: #818cf8; }
    .pay-sum-card.qr-card     .amount { color: #22d3ee; }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: rgba(124,58,237,.1);
        border: 1px solid rgba(124,58,237,.2);
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .total-row .label { font-size: 0.85rem; color: var(--pos-text-muted); font-weight: 600; }
    .total-row .value { font-size: 1.3rem; font-weight: 800; color: #a78bfa; }

    /* Lệch tiền */
    .diff-card {
        border-radius: 10px;
        padding: 14px 16px;
        margin-top: 14px;
        font-size: 0.85rem;
    }
    .diff-card.ok      { background: rgba(16,185,129,.1);  border: 1px solid rgba(16,185,129,.3); }
    .diff-card.over    { background: rgba(99,102,241,.1);  border: 1px solid rgba(99,102,241,.3); }
    .diff-card.short   { background: rgba(239,68,68,.12);  border: 1px solid rgba(239,68,68,.4);  }
    .diff-card .diff-amount { font-size: 1.3rem; font-weight: 800; margin-top: 4px; }
    .diff-card.ok    .diff-amount { color: #34d399; }
    .diff-card.over  .diff-amount { color: #818cf8; }
    .diff-card.short .diff-amount { color: #f87171; }

    /* Sticky summary */
    .close-sidebar { position: sticky; top: 80px; }

    .cash-input-wrap {
        position: relative;
    }
    .cash-input-wrap .currency-symbol {
        position: absolute; right: 14px; top: 50%;
        transform: translateY(-50%);
        color: #a78bfa; font-weight: 700; font-size: 0.9rem;
        pointer-events: none;
    }
    .cash-input-wrap input {
        padding-right: 40px !important;
        font-size: 1.3rem !important;
        font-weight: 700 !important;
        text-align: right !important;
    }

    .shift-meta { display: flex; flex-direction: column; gap: 6px; margin-bottom: 16px; }
    .shift-meta-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.82rem;
    }
    .shift-meta-row .key { color: var(--pos-text-muted); }
    .shift-meta-row .val { font-weight: 600; }

    .confirm-warning {
        background: rgba(245,158,11,.1);
        border: 1px solid rgba(245,158,11,.3);
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 0.78rem;
        color: #fbbf24;
        margin-bottom: 14px;
    }
</style>
@endsection

@section('content')

<div class="close-layout">

    {{-- LEFT: TỔNG KẾT CA --}}
    <div>
        <div class="pos-card mb-4">
            <div class="pos-card-title"><i class="bi bi-info-circle me-1"></i>Thông tin ca</div>
            <div class="shift-meta">
                <div class="shift-meta-row">
                    <span class="key">Nhân viên mở ca</span>
                    <span class="val">{{ $shift->user->name ?? 'N/A' }}</span>
                </div>
                <div class="shift-meta-row">
                    <span class="key">Giờ mở ca</span>
                    <span class="val">{{ \Carbon\Carbon::parse($shift->opened_at)->format('H:i — d/m/Y') }}</span>
                </div>
                <div class="shift-meta-row">
                    <span class="key">Giờ đóng ca</span>
                    <span class="val" id="close-time-display">{{ now()->format('H:i — d/m/Y') }}</span>
                </div>
                <div class="shift-meta-row">
                    <span class="key">Tổng thời gian</span>
                    <span class="val">{{ \Carbon\Carbon::parse($shift->opened_at)->diff(now())->format('%H giờ %I phút') }}</span>
                </div>
                <div class="shift-meta-row">
                    <span class="key">Chi nhánh</span>
                    <span class="val">{{ $location->name }}</span>
                </div>
            </div>
        </div>

        {{-- DOANH THU THEO PTTT --}}
        <div class="pos-card mb-4">
            <div class="pos-card-title"><i class="bi bi-bar-chart me-1"></i>Doanh thu theo phương thức thanh toán</div>

            <div class="payment-summary-grid">
                <div class="pay-sum-card cash-card">
                    <div class="icon">💵</div>
                    <div class="label">Tiền mặt</div>
                    <div class="amount">{{ number_format($summary['cash'], 0, ',', '.') }}₫</div>
                </div>
                <div class="pay-sum-card card-card">
                    <div class="icon">💳</div>
                    <div class="label">Thẻ POS</div>
                    <div class="amount">{{ number_format($summary['card'], 0, ',', '.') }}₫</div>
                </div>
                <div class="pay-sum-card qr-card">
                    <div class="icon">📱</div>
                    <div class="label">Chuyển khoản</div>
                    <div class="amount">{{ number_format($summary['transfer'], 0, ',', '.') }}₫</div>
                </div>
            </div>

            <div class="total-row">
                <div>
                    <div class="label">Tổng doanh thu ca</div>
                    <div style="font-size:0.72rem;color:var(--pos-text-muted)">{{ $summary['count'] }} vé đã bán</div>
                </div>
                <div class="value">{{ number_format($summary['total'], 0, ',', '.') }}₫</div>
            </div>

            {{-- CÔNG THỨC TIỀN MẶT --}}
            <div style="font-size:0.78rem;color:var(--pos-text-muted);background:rgba(255,255,255,.03);border-radius:8px;padding:10px 14px">
                <div style="margin-bottom:4px;font-weight:600;color:var(--pos-text)">🧮 Công thức kiểm két:</div>
                <div>Tiền đầu ca: <strong>{{ number_format($shift->opening_cash, 0, ',', '.') }}₫</strong></div>
                <div>+ Tiền mặt thu: <strong>{{ number_format($summary['cash'], 0, ',', '.') }}₫</strong></div>
                <div style="border-top:1px solid var(--pos-card-border);margin:6px 0;padding-top:6px">
                    = Két phải có: <strong style="color:#34d399">{{ number_format($expectedCash, 0, ',', '.') }}₫</strong>
                </div>
            </div>
        </div>

        {{-- DANH SÁCH VÉ ĐÃ BÁN --}}
        @php
            $recentOrders = $shift->orders()->with('slot.ticket')->where('status','paid')->latest()->limit(5)->get();
        @endphp
        @if($recentOrders->count() > 0)
        <div class="pos-card">
            <div class="pos-card-title"><i class="bi bi-receipt me-1"></i>5 vé gần nhất trong ca</div>
            <table class="pos-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Khách</th>
                        <th>Vé</th>
                        <th>Số tiền</th>
                        <th>PTTT</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOrders as $order)
                    <tr>
                        <td style="color:var(--pos-text-muted)">{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->slot->ticket->name ?? '—' }}</td>
                        <td style="color:#a78bfa;font-weight:700">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                        <td>
                            @if($order->payment_method === 'cash') 💵
                            @elseif($order->payment_method === 'card') 💳
                            @else 📱
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- RIGHT: FORM ĐÓNG CA --}}
    <div class="close-sidebar">
        <div class="pos-card">
            <div class="pos-card-title" style="font-size:0.9rem;font-weight:800;color:var(--pos-text)">
                <i class="bi bi-calculator me-1"></i>Kiểm đếm tiền mặt
            </div>
            
            <div class="confirm-warning mb-3">
                <i class="bi bi-info-circle me-1"></i>
                Nhập số tờ của mỗi mệnh giá để hệ thống tự động tính tổng.
            </div>

            <div class="denomination-list mb-3" style="display:grid;gap:8px">
                @foreach([500000, 200000, 100000, 50000, 20000, 10000, 5000, 2000, 1000] as $den)
                <div style="display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.02);padding:6px 12px;border-radius:8px">
                    <span style="flex:1;font-size:0.8rem;font-weight:600;color:var(--pos-text-muted)">{{ number_format($den, 0, ',', '.') }}₫</span>
                    <input type="number" 
                           class="den-input" 
                           data-den="{{ $den }}" 
                           placeholder="0" 
                           min="0"
                           style="width:70px;background:rgba(255,255,255,.05);border:1px solid var(--pos-card-border);border-radius:6px;padding:4px 8px;color:#fff;font-size:0.85rem;text-align:right"
                           oninput="updateClosingCash()">
                </div>
                @endforeach
            </div>

            <form action="{{ route('pos.shift.close', $subdomain) }}" method="POST" id="closeShiftForm">
                @csrf

                <div class="pos-form-group">
                    <label>Tổng tiền mặt cuối ca</label>
                    <div class="cash-input-wrap">
                        <input type="number"
                               name="closing_cash"
                               id="closing_cash"
                               min="0"
                               step="1000"
                               required
                               placeholder="0"
                               oninput="calcDiff(this.value)">
                        <span class="currency-symbol">₫</span>
                    </div>
                </div>

                {{-- HIỂN THỊ LỆCH TIỀN REALTIME --}}
                <div class="diff-card ok" id="diff-card">
                    <div style="font-size:0.75rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em">
                        <span id="diff-label">Két khớp hoàn toàn</span>
                    </div>
                    <div class="diff-amount" id="diff-amount">0₫</div>
                </div>

                <div class="pos-form-group" style="margin-top:14px">
                    <label>Ghi chú đóng ca</label>
                    <textarea name="closing_note" rows="2"
                              placeholder="VD: Nhập thêm tiền lẻ, kẹt giấy in..."></textarea>
                </div>

                <button type="submit" class="btn-danger-pos w-100" style="justify-content:center;padding:14px;font-size:1rem;margin-top:4px"
                        onclick="return confirmClose()">
                    <i class="bi bi-lock-fill me-1"></i> ĐÓNG CA & KẾT THÚC
                </button>
            </form>

            <div style="margin-top:12px">
                <a href="{{ route('pos.dashboard', $subdomain) }}" class="btn-pos-outline w-100" style="justify-content:center; font-size: 0.85rem">
                    Quay lại Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
const expectedCash = {{ $expectedCash }};

function updateClosingCash() {
    let total = 0;
    document.querySelectorAll('.den-input').forEach(input => {
        const den = parseInt(input.dataset.den);
        const qty = parseInt(input.value) || 0;
        total += den * qty;
    });
    
    const closingInput = document.getElementById('closing_cash');
    closingInput.value = total;
    calcDiff(total);
}

function calcDiff(val) {
    const closing = parseInt(val) || 0;
    const diff    = closing - expectedCash;
    const card    = document.getElementById('diff-card');
    const label   = document.getElementById('diff-label');
    const amount  = document.getElementById('diff-amount');

    amount.textContent = (diff >= 0 ? '+' : '') + diff.toLocaleString('vi-VN') + '₫';

    if (diff === 0) {
        card.className = 'diff-card ok';
        label.textContent = '✅ Két khớp hoàn toàn';
    } else if (diff > 0) {
        card.className = 'diff-card over';
        label.textContent = '📈 Dư tiền (thừa ' + Math.abs(diff).toLocaleString('vi-VN') + '₫)';
    } else {
        card.className = 'diff-card short';
        label.textContent = '⚠️ Thiếu tiền (thiếu ' + Math.abs(diff).toLocaleString('vi-VN') + '₫)';
    }
}

function confirmClose() {
    const closing = parseInt(document.getElementById('closing_cash').value) || 0;
    const diff    = closing - expectedCash;

    let msg = 'Xác nhận đóng ca?\n\n';
    msg += `Két thực tế: ${closing.toLocaleString('vi-VN')}₫\n`;
    msg += `Két lý thuyết: ${expectedCash.toLocaleString('vi-VN')}₫\n`;

    if (Math.abs(diff) > 0) {
        msg += `\n⚠️ Chênh lệch: ${(diff >= 0 ? '+' : '')}${diff.toLocaleString('vi-VN')}₫`;
    } else {
        msg += '\n✅ Két khớp hoàn toàn.';
    }

    return confirm(msg);
}

// Đồng hồ
setInterval(() => {
    const el = document.getElementById('close-time-display');
    if (el) {
        const now = new Date();
        el.textContent = String(now.getHours()).padStart(2,'0') + ':' +
                         String(now.getMinutes()).padStart(2,'0') +
                         ' — ' + String(now.getDate()).padStart(2,'0') +
                         '/' + String(now.getMonth()+1).padStart(2,'0') +
                         '/' + now.getFullYear();
    }
}, 1000);
</script>
@endsection