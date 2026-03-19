@extends('layouts.admin')

@section('admin_content')
<div class="container text-dark">
    <div class="card shadow bg-white border-light">
        {{-- Header màu vàng cam để phân biệt với trang Thêm mới --}}
        <div class="card-header bg-warning text-dark border-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa Voucher: {{ $coupon->code }}</h5>
        </div>
        
        <div class="card-body">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-warning">Mã Code</label>
                        {{-- Mã code thường không cho sửa để tránh lỗi lịch sử đơn hàng, nên mình để readonly --}}
                        <input type="text" name="code" class="form-control bg-light text-dark border-light opacity-50" value="{{ $coupon->code }}" readonly>
                        <small class="text-muted fst-italic">Không thể thay đổi mã.</small>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-warning">Hạn sử dụng</label>
                        {{-- Định dạng ngày tháng năm cho input date --}}
                        <input type="date" name="expiry_date" class="form-control bg-light text-dark border-light" 
                               value="{{ \Carbon\Carbon::parse($coupon->expiry_date)->format('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-warning">Loại giảm giá</label>
                        <select name="type" class="form-select bg-light text-dark border-light">
                            <option value="fixed" {{ $coupon->type == 'fixed' ? 'selected' : '' }}>Tiền mặt (VNĐ)</option>
                            <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-warning">Giá trị giảm</label>
                        <input type="number" name="value" class="form-control bg-light text-dark border-light" 
                               value="{{ intval($coupon->value) }}" required>
                    </div>
                </div>
                
                <hr class="border-secondary my-4">
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-warning fw-bold text-dark px-4">
                        <i class="bi bi-save"></i> Cập nhật
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-light">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection