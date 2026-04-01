@extends('layouts.admin')
@section('title', 'Cài đặt hệ thống')
@section('admin_content')

<div class="d-flex align-items-center mb-4">
    <div class="bg-primary me-3" style="width:50px; height:3px;"></div>
    <h2 class="text-primary fw-bold text-uppercase mb-0">Cài đặt hệ thống</h2>
</div>

@if(session('success'))
    <div class="alert alert-success rounded-3">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="max-width:600px;">
        <h6 class="fw-bold text-primary text-uppercase mb-4" style="letter-spacing:1px;">
            <i class="bi bi-calendar-week me-2"></i>Phụ phí cuối tuần
        </h6>
        <div class="mb-3">
            <label class="form-label fw-bold">Tỉ lệ phụ phí (%) áp dụng T7 & CN</label>
            <div class="input-group" style="max-width:200px;">
                <input type="number" name="weekend_surcharge_percentage"
                       class="form-control form-control-lg fw-bold text-center"
                       value="{{ $surcharge }}" min="0" max="100">
                <span class="input-group-text fw-bold">%</span>
            </div>
            <div class="form-text mt-2">Nhập 0 để tắt phụ phí. Ví dụ: nhập 20 thì giá T7-CN tăng thêm 20%.</div>
        </div>
        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
            <i class="bi bi-save me-1"></i> Lưu cài đặt
        </button>
    </div>

</form>
@endsection