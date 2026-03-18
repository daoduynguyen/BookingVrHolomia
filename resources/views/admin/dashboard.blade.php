@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    {{-- Tiêu đề --}}
    <div class="d-flex align-items-center mb-5">
        <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Bảng điều khiển hệ thống</h2>
    </div>

    {{-- 1. CÁC THẺ THỐNG KÊ (TOP CARDS) --}}
    <div class="row mb-5">
        {{-- Card 1: Doanh thu --}}
        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="text-white-50 text-uppercase mb-3 fw-bold">Doanh thu vé</h6>
                    <h3 class="text-info fw-bold mb-2">{{ number_format($totalRevenue) }} đ</h3>
                    <div class="small text-success"><i class="bi bi-graph-up-arrow"></i> Đã thanh toán</div>
                </div>
            </div>
        </div>

        {{-- Card 2: Đơn cần duyệt --}}
        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="text-white-50 text-uppercase mb-3 fw-bold">Đơn hàng mới</h6>
                    <h3 class="text-warning fw-bold mb-2">{{ $pendingOrders }}</h3>
                    <a href="{{ route('admin.bookings.index') }}" class="small text-warning text-decoration-none hover-underline">
                        <i class="bi bi-hourglass-split"></i> Chờ xử lý ngay
                    </a>
                </div>
            </div>
        </div>

        {{-- Card 3: Tổng vé --}}
        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="text-white-50 text-uppercase mb-3 fw-bold">Kho vé VR</h6>
                    <h3 class="text-white fw-bold mb-2">{{ $totalTickets }}</h3>
                    <div class="small text-white-50">Trò chơi hoạt động</div>
                </div>
            </div>
        </div>

        {{-- Card 4: Khách hàng --}}
        <div class="col-md-3">
            <div class="card bg-dark border-secondary shadow h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="text-white-50 text-uppercase mb-3 fw-bold">Khách hàng</h6>
                    <h3 class="text-white fw-bold mb-2">{{ $totalUsers }}</h3>
                    <div class="small text-white-50">Tài khoản thành viên</div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. BIỂU ĐỒ CỘT + TRÒN --}}
    <div class="row">
        {{-- Biểu đồ cột: Doanh thu 7 ngày --}}
        <div class="col-md-8 mb-4">
            <div class="card bg-dark border-secondary shadow h-100">
                <div class="card-header bg-transparent border-0 mt-3 ms-3">
                    <h5 class="text-white fw-bold"><i class="bi bi-bar-chart-line me-2"></i>Doanh thu 7 ngày qua</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tròn: Tỷ lệ đơn hàng --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-dark border-secondary shadow h-100">
                <div class="card-header bg-transparent border-0 mt-3 text-center">
                    <h5 class="text-white fw-bold">Tỷ lệ trạng thái đơn</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-4">
                    <small class="text-white-50">Thống kê theo trạng thái xử lý</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. BIỂU ĐỒ ĐƯỜNG: XU HƯỚNG ĐƠN HÀNG -- canvas phải nằm TRƯỚC script --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-dark border-secondary shadow h-100">
                <div class="card-header bg-transparent border-0 mt-3 ms-3">
                    <h5 class="text-white fw-bold"><i class="bi bi-graph-up text-success me-2"></i>Xu hướng lượng đơn hàng 7 ngày qua</h5>
                </div>
                <div class="card-body">
                    <canvas id="lineChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- 4. BẢNG XẾP HẠNG DOANH THU CƠ SỞ (CHỈ super_admin MỚI THẤY) --}}
    @if(Auth::check() && Auth::user()->role === 'super_admin')
    <div class="row mt-4 mb-5">
        <div class="col-12">
            <div class="card bg-dark border-secondary shadow">
                <div class="card-header bg-transparent border-bottom border-secondary mt-2 ms-2">
                    <h5 class="text-warning fw-bold"><i class="bi bi-trophy-fill me-2"></i>Bảng xếp hạng doanh thu theo chi nhánh</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle border-secondary mb-0">
                            <thead>
                                <tr class="text-secondary">
                                    <th class="text-center" width="10%">Top</th>
                                    <th>Tên cơ sở</th>
                                    <th class="text-end">Tổng doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByLocation as $index => $item)
                                <tr>
                                    <td class="text-center fw-bold text-info">#{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ $item->name }}</td>
                                    <td class="text-end text-success fw-bold">{{ number_format($item->total) }} đ</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-secondary py-4">Chưa có dữ liệu doanh thu</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>{{-- end .container-fluid --}}

{{-- SCRIPT VẼ BIỂU ĐỒ -- đặt CUỐI CÙNG sau khi tất cả canvas đã có trong DOM --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- 1. BIỂU ĐỒ CỘT (DOANH THU) ---
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'bar',
        data: {
            labels: {!! json_encode($revenueLabels) !!},
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: {!! json_encode($revenueData) !!},
                backgroundColor: '#0dcaf0',
                hoverBackgroundColor: '#0aa2c0',
                borderRadius: 5,
                barThickness: 35
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#333' },
                    ticks: { color: '#aaa' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#aaa' }
                }
            }
        }
    });

    // --- 2. BIỂU ĐỒ TRÒN (TRẠNG THÁI) ---
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['Đã thanh toán', 'Chờ xử lý', 'Đã hủy'],
            datasets: [{
                data: {!! json_encode($pieData) !!},
                backgroundColor: ['#198754', '#ffc107', '#dc3545'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#fff',
                        padding: 20,
                        font: { size: 14 }
                    }
                }
            },
            cutout: '75%'
        }
    });

    // --- 3. BIỂU ĐỒ ĐƯỜNG (SỐ LƯỢNG ĐƠN HÀNG) ---
    const ctxLine = document.getElementById('lineChart').getContext('2d');
    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: {!! json_encode($revenueLabels) !!},
            datasets: [{
                label: 'Số lượng đơn hàng',
                data: {!! json_encode($orderCountData) !!},
                borderColor: '#198754',
                backgroundColor: 'rgba(25, 135, 84, 0.15)',
                borderWidth: 3,
                pointBackgroundColor: '#198754',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: '#198754',
                pointRadius: 5,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#333' },
                    ticks: { color: '#aaa', stepSize: 1 }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#aaa' }
                }
            }
        }
    });
</script>

@endsection