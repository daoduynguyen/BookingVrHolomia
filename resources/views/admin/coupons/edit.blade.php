@extends('layouts.admin')

@section('title', 'Sửa Voucher')

@section('admin_content')
<div class="container-fluid">

    {{-- TIÊU ĐỀ --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-warning me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2">Chỉnh sửa Voucher: {{ $coupon->code }}</h2>
    </div>

    <div class="bg-white rounded-4 shadow-sm border border-light p-4" style="max-width: 700px;">
        <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST">
            @csrf @method('PUT')

            <div class="row g-4">
                {{-- Mã code --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase">Mã Code</label>
                    <input type="text" name="code"
                           class="form-control bg-light border-light text-muted fw-bold"
                           value="{{ $coupon->code }}" readonly
                           style="font-family:'Courier New',monospace; letter-spacing:1.5px;">
                    <div class="small text-muted mt-1"><i class="bi bi-lock me-1"></i>Không thể thay đổi mã</div>
                </div>

                {{-- Hạn sử dụng --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase">Hạn sử dụng</label>
                    <input type="date" name="expiry_date"
                           class="form-control border-light"
                           value="{{ \Carbon\Carbon::parse($coupon->expiry_date)->format('Y-m-d') }}" required>
                </div>

                {{-- Loại giảm --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase">Loại giảm giá</label>
                    <select name="type" class="form-select border-light">
                        <option value="fixed"   {{ $coupon->type == 'fixed'   ? 'selected' : '' }}>💵 Tiền mặt (VNĐ)</option>
                        <option value="percent" {{ $coupon->type == 'percent' ? 'selected' : '' }}>% Phần trăm</option>
                    </select>
                </div>

                {{-- Giá trị --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark small text-uppercase">Giá trị giảm</label>
                    <input type="number" name="value"
                           class="form-control border-light fw-bold"
                           value="{{ intval($coupon->value) }}" required>
                </div>
            </div>

            <hr class="my-4 border-light">

            <div class="d-flex gap-3">
                <button type="submit" class="btn btn-warning fw-bold text-dark px-5 rounded-pill">
                    <i class="bi bi-save me-1"></i> Cập nhật
                </button>
                <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-secondary px-5 rounded-pill">
                    <i class="bi bi-x-lg me-1"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection