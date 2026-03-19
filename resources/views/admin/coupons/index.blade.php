@extends('layouts.admin') 
{{-- Admin Quản lý vocher --}}
@section('admin_content')
<div class="container-fluid text-dark">
    
    {{-- Tiêu đề và nút thêm --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-info fw-bold">🎫 Quản lý Voucher</h2>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Thêm Voucher
        </a>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success bg-success text-white border-0 shadow-sm">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Bảng danh sách (Giao diện tối) --}}
    <div class="card shadow mb-4 bg-white border-light">
        <div class="card-header py-3 bg-secondary bg-opacity-25 border-secondary">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách mã giảm giá</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover table-bordered border-secondary" width="100%" cellspacing="0">
                    <thead>
                        <tr class="text-info">
                            <th>Mã Code</th>
                            <th>Loại giảm</th>
                            <th>Giá trị</th>
                            <th>Hạn dùng</th>
                            <th>Trạng thái</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($coupons as $coupon)
                        <tr>
                            <td class="align-middle">
                                <span class="badge bg-primary fs-6">{{ $coupon->code }}</span>
                            </td>
                            <td class="align-middle">
                                @if($coupon->type == 'percent')
                                    <span class="text-warning"><i class="bi bi-percent"></i> Phần trăm</span>
                                @else
                                    <span class="text-success"><i class="bi bi-cash"></i> Tiền mặt</span>
                                @endif
                            </td>
                            <td class="align-middle fw-bold">
                                {{ $coupon->type == 'fixed' ? number_format($coupon->value).'đ' : $coupon->value.'%' }}
                            </td>
                            <td class="align-middle">
                                {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d/m/Y') }}
                            </td>
                            <td class="align-middle">
                                @if(now()->gt($coupon->expiry_date))
                                    <span class="badge bg-danger">Hết hạn</span>
                                @else
                                    <span class="badge bg-success">Đang chạy</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="btn btn-sm btn-warning me-1" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xóa voucher này?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach

                        @if($coupons->count() == 0)
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    Chưa có mã giảm giá nào. Hãy bấm "Thêm Voucher" ngay!
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                {{-- Phân trang (Sửa màu cho hợp dark mode) --}}
                <div class="d-flex justify-content-center mt-3">
                    {{ $coupons->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection