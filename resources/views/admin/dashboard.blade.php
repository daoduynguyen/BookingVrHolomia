@extends('layouts.admin')

@php
    /** @var \Illuminate\Support\Collection $revenueByLocation */
@endphp

@section('title', 'Dashboard')

@section('admin_content')
    <div class="container-fluid">
        {{-- Tiêu đề --}}
        {{-- Tiêu đề + Đơn hàng mới --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="d-flex align-items-center">
                <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
                <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Bảng điều khiển hệ thống</h2>
            </div>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-decoration-none">
                <div class="d-flex align-items-center gap-3 bg-white border-light shadow-sm rounded-3 px-4 py-2"
                    style="border-left: 4px solid #dc3545 !important;">
                    <div class="text-center">
                        <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Đơn hàng mới</div>
                        <div class="fw-bold text-danger fs-4 lh-1 my-1">{{ $pendingOrders }}</div>
                        <div class="small text-danger" style="font-size: 0.75rem;"><i class="bi bi-hourglass-split"></i> Chờ
                            xử lý</div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Hàng 1: Kho vé - Khách hàng - Hoàn thành --}}
        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <div class="card bg-white border-light shadow-sm h-100 py-3 rounded-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted text-uppercase mb-3 fw-bold small">Kho vé VR</h6>
                        <h3 class="text-dark fw-bold mb-2">{{ $totalTickets }}</h3>
                        <div class="small text-muted">Trò chơi hoạt động</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-light shadow-sm h-100 py-3 rounded-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted text-uppercase mb-3 fw-bold small">Khách hàng</h6>
                        <h3 class="text-dark fw-bold mb-2">{{ $totalUsers }}</h3>
                        <div class="small text-muted">Tài khoản thành viên</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-light shadow-sm h-100 py-3 rounded-3"
                    style="border-left: 4px solid #198754 !important;">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase mb-3 fw-bold small" style="color: #198754;">Hoàn thành</h6>
                        <h3 class="fw-bold mb-2" style="color: #198754;">{{ $completedOrders }}</h3>
                        <a href="{{ route('admin.bookings.index', ['status' => 'completed']) }}"
                            class="small text-decoration-none" style="color: #198754;">
                            <i class="bi bi-trophy-fill"></i> Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hàng 2: Doanh thu - Hoàn tiền - Hủy --}}
        <div class="row g-3 mb-5">
            <div class="col-md-4">
                <div class="card bg-white border-light shadow-sm h-100 py-3 rounded-3">
                    <div class="card-body text-center">
                        <h6 class="text-muted text-uppercase mb-3 fw-bold small">Doanh thu vé</h6>
                        <h3 class="text-primary fw-bold mb-2">{{ number_format($totalRevenue) }} đ</h3>
                        <div class="small text-success"><i class="bi bi-graph-up-arrow"></i> Đã thanh toán</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-light shadow-sm h-100 py-3 rounded-3"
                    style="border-left: 4px solid #6c757d !important;">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase mb-3 fw-bold small text-secondary">Hoàn tiền</h6>
                        <h3 class="text-secondary fw-bold mb-2">{{ $refundedOrders }}</h3>
                        <a href="{{ route('admin.bookings.index', ['status' => 'refunded']) }}"
                            class="small text-secondary text-decoration-none">
                            <i class="bi bi-arrow-counterclockwise"></i> Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white border-light shadow-sm h-100 py-3 rounded-3"
                    style="border-left: 4px solid #dc3545 !important;">
                    <div class="card-body text-center">
                        <h6 class="text-uppercase mb-3 fw-bold small text-danger">Đã hủy</h6>
                        <h3 class="text-danger fw-bold mb-2">{{ $cancelledOrders }}</h3>
                        <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}"
                            class="small text-danger text-decoration-none">
                            <i class="bi bi-x-circle-fill"></i> Xem tất cả
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. BIỂU ĐỒ CỘT + TRÒN --}}
    <div class="row">
        {{-- Biểu đồ cột: Doanh thu 7 ngày --}}
        <div class="col-md-8 mb-4">
            <div class="card bg-white border-light shadow-sm h-100">
                <div class="card-header bg-transparent border-0 mt-3 ms-3">
                    <h5 class="text-dark fw-bold"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Doanh thu 7 ngày
                        qua</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" style="max-height: 350px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Biểu đồ tròn: Tỷ lệ đơn hàng --}}
        <div class="col-md-4 mb-4">
            <div class="card bg-white border-light shadow-sm h-100">
                <div class="card-header bg-transparent border-0 mt-3 text-center">
                    <h5 class="text-dark fw-bold">Tỷ lệ trạng thái đơn</h5>
                </div>
                <div class="card-body d-flex justify-content-center align-items-center">
                    <div style="width: 280px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-4">
                    <small class="text-muted">Thống kê theo trạng thái xử lý</small>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. BIỂU ĐỒ ĐƯỜNG: XU HƯỚNG ĐƠN HÀNG -- canvas phải nằm TRƯỚC script --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-white border-light shadow-sm h-100">
                <div class="card-header bg-transparent border-0 mt-3 ms-3">
                    <h5 class="text-dark fw-bold"><i class="bi bi-graph-up text-success me-2"></i>Xu hướng lượng đơn
                        hàng 7 ngày qua</h5>
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
                <div class="card bg-white border-light shadow-sm">
                    <div class="card-header bg-transparent border-bottom border-light mt-2 ms-2">
                        <h5 class="text-primary fw-bold"><i class="bi bi-trophy-fill me-2 text-warning"></i>Bảng xếp hạng
                            doanh thu theo chi nhánh</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted">
                                        <th class="text-center" width="10%">Top</th>
                                        <th>Tên cơ sở</th>
                                        <th class="text-end">Tổng doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($revenueByLocation as $index => $item)
                                        <tr>
                                            <td class="text-center fw-bold text-primary">{{ $index + 1 }}</td>
                                            <td class="fw-bold text-dark">{{ $item->name }}</td>
                                            <td class="text-end text-success fw-bold">{{ number_format($item->total) }} đ</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-4">Chưa có dữ liệu doanh thu</td>
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
                            label: function (context) {
                                return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#e5e7eb' },
                        ticks: { color: '#4b5563' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#4b5563' }
                    }
                }
            }
        });

        // --- 2. BIỂU ĐỒ TRÒN (TRẠNG THÁI) ---
        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ['Đã thanh toán', 'Chờ xử lý', 'Đã hủy', 'Hoàn thành'],
                datasets: [{
                    data: {!! json_encode($pieData) !!},
                    backgroundColor: ['#0d6efd', '#ffc107', '#dc3545', '#198754'],
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
                            color: '#4b5563',
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
                        grid: { color: '#e5e7eb' },
                        ticks: { color: '#4b5563', stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#4b5563' }
                    }
                }
            }
        });
    </script>

@endsection