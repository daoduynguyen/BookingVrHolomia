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

    /* Print */
    @media print {
        .pos-sidebar, .pos-topbar, .no-print { display: none !important; }
        .pos-main { margin-left: 0 !important; }
        .pos-content { padding: 0 !important; }
        .report-layout { grid-template-columns: 1fr !important; }
        body { background: #fff !important; color: #000 !important; }
        .pos-card { border-color: #ddd !important; background: #fff !important; }
        .kpi-card { background: #f9f9f9 !important; border-color: #ddd !important; }
        .pos-table th, .pos-table td { color: #333 !important; }
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

<div style="margin-bottom:16px" class="no-print">
    <a href="{{ route('pos.dashboard', $subdomain) }}" class="btn-pos-outline">
        <i class="bi bi-grid-1x2"></i> Về Dashboard
    </a>
</div>

{{-- BADGE TRẠNG THÁI --}}
<span class="report-badge {{ $diffClass }}">
    {{ $diffLabel }}
</span>

<div class="report-layout" id="print-area">

    {{-- LEFT --}}
    <div>
        {{-- KPI --}}
        <div class="kpi-row">
            <div class="kpi-card">
                <div class="kpi-icon">🎫</div>
                <div class="kpi-label">Vé đã bán</div>
                <div class="kpi-value" style="color:#a78bfa">{{ $summary['count'] }}</div>
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
                            <td style="font-weight:700;color:#a78bfa">{{ number_format($order->total_amount, 0, ',', '.') }}₫</td>
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