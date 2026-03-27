@extends('layouts.admin')

@section('title', 'Thêm Voucher')

@section('admin_content')
<div class="container text-dark">
    <div class="card shadow bg-white border-light">
        <div class="card-header bg-primary text-white border-0">
            <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Thêm Mã Giảm Giá Mới</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.coupons.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-info">Mã Code (Viết liền)</label>
                        <input type="text" name="code" class="form-control bg-light text-dark border-light" placeholder="VD: TET2026" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-info">Hạn sử dụng</label>
                        <input type="date" name="expiry_date" class="form-control bg-light text-dark border-light" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-info">Loại giảm giá</label>
                        <select name="type" class="form-select bg-light text-dark border-light">
                            <option value="fixed">Tiền mặt (VNĐ)</option>
                            <option value="percent">Phần trăm (%)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-info">Giá trị giảm</label>
                        <input type="number" name="value" class="form-control bg-light text-dark border-light" placeholder="Nhập số tiền hoặc %..." required>
                    </div>
                </div>
                
                <hr class="border-secondary my-4">
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-success px-4 fw-bold">
                        <i class="bi bi-check-lg"></i> Lưu Voucher
                    </button>
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-outline-light">Hủy bỏ</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection