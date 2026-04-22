@extends('pos.layout')

@section('title', 'Báo cáo ca')
@section('page-title', 'Báo cáo ca làm việc')

@section('styles')
<style>
    .report-layout {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 20px;
    }

    .report-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        margin-bottom: 16px;
    }
    .report-badge.ok    { background: rgba(16,185,129,.15); color: #34d399; }
    .report-badge.diff  { background: rgba(239,68,68,.15);  color: #f87171; }
    .report-badge.over  { background: rgba(99,102,241,.15); color: #818cf8; }

    .kpi-row {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }
    .kpi-card {
        background: rgba(255,255,255,.03);
        border: 1px solid var(--pos-card-border);
        border-radius: 10px;
        padding: 14px;
        text-align: center;
    }
    .kpi-card .kpi-icon { font-size: 1.6rem; margin-bottom: 6px; }
    .kpi-card .kpi-label { font-size: 0.68rem; color: var(--pos-text-muted); text-transform: uppercase; letter-spacing:.05em; margin-bottom: 4px; }
    .kpi-card .kpi-value { font-size: 1.1rem; font-weight: 800; }

    /* Diff highlight */
    .diff-highlight {
        border-radius: 10px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .diff-highlight.ok    { background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.2); }
    .diff-highlight.short { background: rgba(239,68,68,.08);  border: 1px solid rgba(239,68,68,.3);  }
    .diff-highlight.over  { background: rgba(99,102,241,.08); border: 1px solid rgba(99,102,241,.2); }

    .diff-highlight .diff-val { font-size: 1.6rem; font-weight: 800; }
    .diff-highlight.ok    .diff-val { color: #34d399; }
    .diff-highlight.short .diff-val { color: #f87171; }
    .diff-highlight.over  .diff-val { color: #818cf8; }

    /* Bảng order */
    .order-table-wrap {
        max-height: 340px;
        overflow-y: auto;
        border-radius: 8px;
        border: 1px solid var(--pos-card-border);
    }

    .thermal-bill {
        display: none;
        width: 100%;
        max-width: 80mm;
        margin: 0 auto;
        background: #fff;
        color: #111827;
        font-family: 'Be Vietnam Pro', sans-serif;
        font-size: 11px;
        line-height: 1.35;
        padding: 10px;
    }
    .thermal-bill .brand {
        text-align: center;
        padding-bottom: 8px;
        margin-bottom: 8px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .thermal-bill .brand h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 900;
        letter-spacing: 1px;
    }
    .thermal-bill .brand .sub {
        font-size: 9px;
        color: #64748b;
        margin-top: 2px;
    }
    .thermal-bill .rowline {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 4px 0;
    }
    .thermal-bill .rowline .k {
        color: #64748b;
        flex: 0 0 48%;
    }
    .thermal-bill .rowline .v {
        flex: 1;
        text-align: right;
        font-weight: 700;
        word-break: break-word;
    }
    .thermal-bill .divider {
        border-top: 1px dashed #cbd5e1;
        margin: 8px 0;
    }
    .thermal-bill .section-title {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .08em;
        font-weight: 800;
        color: #64748b;
        margin-bottom: 6px;
    }
    .thermal-bill .mini-item {
        padding: 5px 0;
        border-bottom: 1px dotted #e5e7eb;
    }
    .thermal-bill .mini-item:last-child { border-bottom: 0; }
    .thermal-bill .mini-item .top {
        display: flex;
        justify-content: space-between;
        gap: 10px;
    }
    .thermal-bill .mini-item .name { font-weight: 700; }
    .thermal-bill .mini-item .meta, .thermal-bill .footnote {
        font-size: 9px;
        color: #64748b;
    }
    .thermal-bill .total-box {
        margin-top: 8px;
        padding-top: 8px;
        border-top: 2px solid #111827;
        display: flex;
        justify-content: space-between;
        font-weight: 900;
        font-size: 13px;
    }
    .thermal-bill .total-box .value {
        color: #0ea5e9;
        font-size: 15px;
    }

    /* Print */
    @media print {
        @page { size: 80mm auto; margin: 4mm; }
        body { background: #fff !important; color: #000 !important; }
        .pos-sidebar, .pos-topbar, .no-print { display: none !important; }
        .pos-main { margin-left: 0 !important; }
        .pos-content { padding: 0 !important; }
        .report-layout { grid-template-columns: 1fr !important; gap: 0 !important; }
        .kpi-row { grid-template-columns: 1fr !important; }
        .diff-highlight { flex-direction: column; align-items: flex-start; gap: 8px; }
        .diff-highlight .diff-val { font-size: 1.25rem; }
        .order-table-wrap { max-height: none !important; overflow: visible !important; }
        .pos-table th, .pos-table td { font-size: 9pt !important; padding: 4px 0 !important; color: #000 !important; }
        .pos-card { border-color: #ddd !important; background: #fff !important; }
        .kpi-card { background: #f9f9f9 !important; border-color: #ddd !important; }
        .thermal-bill { display: block !important; max-width: 72mm !important; padding: 0 !important; }
        .thermal-bill * { color: #000 !important; }
        .screen-only { display: none !important; }
    }
</style>
@endsection

@section('content')

@php
    $diff    = $shift->cash_difference ?? 0;
    $diffClass = $diff == 0 ? 'ok' : ($diff < 0 ? 'short' : 'over');
    $diffLabel = $diff == 0 ? '✅ Két khớp hoàn toàn' : ($diff < 0 ? '⚠️ Thiếu tiền' : '📈 Dư tiền');
    $allOrders = $shift->orders()->with('slot.ticket')->where('status','paid')->latest()->get();
@endphp

<div class="thermal-bill print-only" id="print-area">
    <div class="brand">
        <h3>HOLOMIA VR</h3>
        <div class="sub">BÁO CÁO CHỐT CA</div>
        <div class="sub">Ca #{{ $shift->id }} - {{ $location->name }}</div>
    </div>

    <div class="rowline"><span class="k">Nhân viên</span><span class="v">{{ $shift->user->name ?? 'N/A' }}</span></div>
    <div class="rowline"><span class="k">Mở ca</span><span class="v">{{ \Carbon\Carbon::parse($shift->opened_at)->format('H:i d/m/Y') }}</span></div>
    <div class="rowline"><span class="k">Đóng ca</span><span class="v">{{ \Carbon\Carbon::parse($shift->closed_at)->format('H:i d/m/Y') }}</span></div>
    <div class="rowline"><span class="k">Vé đã bán</span><span class="v">{{ $summary['count'] }}</span></div>
    <div class="rowline"><span class="k">Doanh thu</span><span class="v">{{ number_format($summary['total'], 0, ',', '.') }}₫</span></div>
    <div class="rowline"><span class="k">Tiền đầu ca</span><span class="v">{{ number_format($shift->opening_cash, 0, ',', '.') }}₫</span></div>
    <div class="rowline"><span class="k">Tiền mặt</span><span class="v">{{ number_format($summary['cash'], 0, ',', '.') }}₫</span></div>
    <div class="rowline"><span class="k">Tiền đếm</span><span class="v">{{ number_format($shift->closing_cash, 0, ',', '.') }}₫</span></div>

    <div class="divider"></div>
    <div class="section-title">Kiểm két</div>
    <div class="rowline"><span class="k">Két phải có</span><span class="v">{{ number_format($expectedCash, 0, ',', '.') }}₫</span></div>
    <div class="rowline"><span class="k">Chênh lệch</span><span class="v">{{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}₫</span></div>
    @if($shift->closing_note)
    <div class="divider"></div>
    <div class="section-title">Ghi chú</div>
    <div style="font-size:9px;line-height:1.4">{{ $shift->closing_note }}</div>
    @endif

    <div class="divider"></div>
    <div class="section-title">5 giao dịch gần nhất</div>
    @foreach($allOrders->take(5) as $order)
        <div class="mini-item">
            <div class="top">
                <div class="name">#{{ $order->id }} - {{ $order->customer_name }}</div>
                <div class="value">{{ number_format($order->total_amount, 0, ',', '.') }}₫</div>
            </div>
            <div class="meta">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }} | {{ $order->slot->ticket->name ?? '—' }} | SL {{ $order->quantity ?? 1 }}</div>
        </div>
    @endforeach

    <div class="footnote" style="text-align:center;margin-top:8px;">
        Vui lòng đối chiếu trước khi bàn giao ca.
    </div>
</div>

<div style="margin-bottom:16px" class="no-print">
    <a href="{{ route('pos.dashboard', $subdomain) }}" class="btn-pos-outline">
        <i class="bi bi-grid-1x2"></i> Về Dashboard
    </a>
</div>

{{-- BADGE TRẠNG THÁI --}}
<span class="report-badge {{ $diffClass }}">
    {{ $diffLabel }}
</span>

<div class="report-layout screen-only">

    {{-- LEFT --}}
    <div>
        {{-- KPI --}}
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-icon">🎫</div>
                <div class="kpi-label">Vé đã bán</div>
                <div class="kpi-value" style="color:var(--pos-primary)">{{ $summary['count'] }}</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">💰</div>
                <div class="kpi-label">Tổng doanh thu</div>
                <div class="kpi-value" style="color:#34d399">{{ number_format($summary['total'], 0, ',', '.') }}₫</div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon">⏱</div>
                <div class="kpi-label">Thời gian ca</div>
                <div class="kpi-value" style="color:#22d3ee">
                    {{ \Carbon\Carbon::parse($shift->opened_at)->diff(\Carbon\Carbon::parse($shift->closed_at))->format('%H:%I') }}h
                </div>
            </div>
        </div>

        {{-- LỆCH TIỀN --}}
        <div class="diff-highlight {{ $diffClass }}">
            <div>
                <div style="font-size:0.75rem;color:var(--pos-text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.08em;margin-bottom:6px">Tình trạng kiểm két</div>
                <div style="font-size:0.9rem;color:var(--pos-text)">
                    Thực tế: <strong class="text-white">{{ number_format($shift->closing_cash, 0, ',', '.') }}₫</strong>
                    <span style="color:var(--pos-text-muted);margin:0 8px">|</span>
                    Số liệu: <strong class="text-white">{{ number_format(($shift->opening_cash + $summary['cash']), 0, ',', '.') }}₫</strong>
                </div>
            </div>
            <div style="text-align:right">
                <div class="diff-val">
                    {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0, ',', '.') }}₫
                </div>
                <div style="font-size:0.7rem;opacity:0.8;font-weight:600">{{ $diffLabel }}</div>
            </div>
        </div>

        {{-- DOANH THU THEO PTTT --}}
        <div class="pos-card mb-4">
            <div class="pos-card-title"><i class="bi bi-pie-chart me-1"></i>Phân tích theo phương thức thanh toán</div>

            @php
                $ptttList = [
                    ['label' => 'Tiền mặt', 'icon' => '💵', 'val' => $summary['cash'],     'pct' => $summary['total'] > 0 ? round($summary['cash']/$summary['total']*100) : 0,     'color' => '#34d399'],
                    ['label' => 'Thẻ POS',  'icon' => '💳', 'val' => $summary['card'],     'pct' => $summary['total'] > 0 ? round($summary['card']/$summary['total']*100) : 0,     'color' => '#818cf8'],
                    ['label' => 'Chuyển khoản','icon' => '📱','val' => $summary['transfer'],'pct' => $summary['total'] > 0 ? round($summary['transfer']/$summary['total']*100) : 0,'color' => '#22d3ee'],
                ];
            @endphp

            @foreach($ptttList as $pttt)
            <div style="margin-bottom:12px">
                <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:5px">
                    <span>{{ $pttt['icon'] }} {{ $pttt['label'] }}</span>
                    <span style="font-weight:700;color:{{ $pttt['color'] }}">
                        {{ number_format($pttt['val'], 0, ',', '.') }}₫
                        <span style="color:var(--pos-text-muted);font-weight:400">({{ $pttt['pct'] }}%)</span>
                    </span>
                </div>
                <div style="background:rgba(255,255,255,.07);border-radius:20px;height:6px;overflow:hidden">
                    <div style="height:100%;border-radius:20px;background:{{ $pttt['color'] }};width:{{ $pttt['pct'] }}%;transition:width .5s"></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- DANH SÁCH GIAO DỊCH --}}
        <div class="pos-card">
            <div class="pos-card-title"><i class="bi bi-list-ul me-1"></i>Tất cả giao dịch trong ca ({{ $allOrders->count() }} vé)</div>
            <div class="order-table-wrap">
                <table class="pos-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Giờ</th>
                            <th>Khách hàng</th>
                            <th>Vé</th>
                            <th>SL</th>
                            <th>Số tiền</th>
                            <th>PTTT</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allOrders as $order)
                        <tr>
                            <td style="color:var(--pos-text-muted)">{{ $order->id }}</td>
                            <td style="color:var(--pos-text-muted)">{{ \Carbon\Carbon::parse($order->created_at)->format('H:i') }}</td>
                            <td>
                                <div style="font-weight:600">{{ $order->customer_name }}</div>
                                <div style="font-size:0.7rem;color:var(--pos-text-muted)">{{ $order->customer_phone }}</div>
                            </td>
                            <td>{{ $order->slot->ticket->name ?? '—' }}</td>
                            <td>{{ $order->quantity ?? 1 }}</td>
                            <td style="font-weight:700;color:var(--pos-primary)">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
                            <td>
                                @if($order->payment_method === 'cash') <span style="color:#34d399">💵</span>
                                @elseif($order->payment_method === 'card') <span style="color:#818cf8">💳</span>
                                @else <span style="color:#22d3ee">📱</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align:center;color:var(--pos-text-muted);padding:20px">Không có giao dịch nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT: TÓM TẮT + ACTIONS --}}
    <div class="no-print">

        {{-- TÓM TẮT --}}
        <div class="pos-card mb-3">
            <div class="pos-card-title"><i class="bi bi-card-checklist me-1"></i>Tóm tắt ca</div>

            @php
                $rows = [
                    ['Nhân viên', $shift->user->name ?? 'N/A'],
                    ['Chi nhánh', $location->name],
                    ['Mở ca', \Carbon\Carbon::parse($shift->opened_at)->format('H:i — d/m')],
                    ['Đóng ca', \Carbon\Carbon::parse($shift->closed_at)->format('H:i — d/m')],
                    ['Tiền đầu ca', number_format($shift->opening_cash, 0, ',', '.') . '₫'],
                    ['Tiền mặt thu', number_format($summary['cash'], 0, ',', '.') . '₫'],
                    ['Tiền đếm được', number_format($shift->closing_cash, 0, ',', '.') . '₫'],
                ];
            @endphp

            @foreach($rows as [$key, $val])
            <div style="display:flex;justify-content:space-between;font-size:0.8rem;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.04)">
                <span style="color:var(--pos-text-muted)">{{ $key }}</span>
                <span style="font-weight:600">{{ $val }}</span>
            </div>
            @endforeach

            @if($shift->closing_note)
            <div style="margin-top:10px;background:rgba(255,255,255,.03);border-radius:7px;padding:8px 12px;font-size:0.78rem;color:var(--pos-text-muted)">
                <strong>Ghi chú:</strong> {{ $shift->closing_note }}
            </div>
            @endif
        </div>

        {{-- ACTIONS --}}
        <div class="pos-card">
            <div class="pos-card-title"><i class="bi bi-download me-1"></i>Xuất báo cáo</div>

            <button onclick="window.print()" class="btn-pos w-100 mb-2" style="justify-content:center">
                <i class="bi bi-printer"></i> In báo cáo
            </button>

            <button onclick="exportCSV()" class="btn-pos-outline w-100 mb-2" style="justify-content:center">
                <i class="bi bi-filetype-csv"></i> Xuất file CSV
            </button>

            <a href="{{ route('pos.shift.open.form', $subdomain) }}" class="btn-success-pos w-100" style="justify-content:center">
                <i class="bi bi-door-open"></i> Mở ca mới
            </a>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
function exportCSV() {
    const rows = [
        ['#', 'Giờ', 'Khách hàng', 'SĐT', 'Vé', 'SL', 'Số tiền', 'PTTT'],
    ];

    // Thêm dữ liệu từ bảng
    const trs = document.querySelectorAll('.pos-table tbody tr');
    trs.forEach(tr => {
        const tds = tr.querySelectorAll('td');
        if (tds.length >= 7) {
            rows.push([
                tds[0].textContent.trim(),
                tds[1].textContent.trim(),
                tds[2].querySelector('div')?.textContent.trim() || tds[2].textContent.trim(),
                tds[2].querySelectorAll('div')[1]?.textContent.trim() || '',
                tds[3].textContent.trim(),
                tds[4].textContent.trim(),
                tds[5].textContent.trim(),
                tds[6].textContent.trim(),
            ]);
        }
    });

    // Summary rows
    rows.push(['', '', '', '', '', '', '', '']);
    rows.push(['TỔNG DOANH THU', '{{ $summary['total'] }}', '', '', '', '', '', '']);
    rows.push(['Tiền mặt', '{{ $summary['cash'] }}', 'Thẻ POS', '{{ $summary['card'] }}', 'Chuyển khoản', '{{ $summary['transfer'] }}', '', '']);
    rows.push(['Lệch tiền', '{{ $shift->cash_difference ?? 0 }}', '', '', '', '', '', '']);

    const csvContent = rows.map(r =>
        r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')
    ).join('\n');

    const BOM = '\uFEFF';
    const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `bao-cao-ca-{{ $shift->id }}-{{ \Carbon\Carbon::parse($shift->closed_at)->format('Ymd-Hi') }}.csv`;
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection