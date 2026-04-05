@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-5">
        <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2">Quản lý đơn hàng</h2>
    </div>

    <div class="profile-content shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary">
                <thead>
                    <tr class="text-secondary border-bottom border-secondary">
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="fw-bold">{{ $order->id }}</td>
                        <td>{{ $order->user->name ?? 'Khách vãng lai' }}</td>
                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="text-info fw-bold">{{ number_format($order->total_amount) }}đ</td>
                        <td>
                            @if($order->status == 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @else
                                <span class="badge bg-success">Duyệt</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($order->status == 'pending')
                                <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-info fw-bold text-dark">
                                        <i class="bi bi-check-lg"></i> DUYỆT ĐƠN
                                    </button>
                                </form>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary ms-2">Chi tiết</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection