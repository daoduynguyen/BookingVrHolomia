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

{{-- FORM PHỤ PHÍ CUỐI TUẦN --}}
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

{{-- FORM THÔNG TIN NGÂN HÀNG --}}
<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    <input type="hidden" name="form_type" value="bank">

    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4" style="max-width:600px;">
        <h6 class="fw-bold text-primary text-uppercase mb-4" style="letter-spacing:1px;">
            <i class="bi bi-bank me-2"></i>Thông tin ngân hàng (QR chuyển khoản)
        </h6>

        <div class="mb-3">
            <label class="form-label fw-bold">Tên ngân hàng</label>
            <input type="text" name="bank_name" class="form-control"
                   value="{{ $bankName }}" placeholder="Ví dụ: BIDV, Vietcombank...">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Mã BIN ngân hàng</label>
            <input type="text" name="bank_bin" class="form-control"
                   value="{{ $bankBin }}" placeholder="Ví dụ: 970418">
            <div class="form-text">
                970418 = BIDV &nbsp;|&nbsp; 970436 = Vietcombank &nbsp;|&nbsp;
                970422 = MB Bank &nbsp;|&nbsp; 970432 = VPBank &nbsp;|&nbsp; 970423 = TPBank
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Số tài khoản</label>
            <input type="text" name="bank_account" class="form-control"
                   value="{{ $bankAccount }}" placeholder="Ví dụ: 8860075445">
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Tên chủ tài khoản</label>
            <input type="text" name="bank_owner" class="form-control"
                   value="{{ $bankOwner }}" placeholder="Ví dụ: NGUYEN VAN A">
        </div>

        {{-- Preview QR --}}
        @if($bankAccount && $bankBin)
        <div class="mb-3 text-center">
            <p class="text-muted small mb-2">Preview QR hiện tại:</p>
            <img src="https://img.vietqr.io/image/{{ $bankBin }}-{{ $bankAccount }}-compact2.png?amount=100000&addInfo=PREVIEW"
                 width="160" height="160" class="rounded-3 border shadow-sm"
                 onerror="this.style.display='none'">
        </div>
        @endif

        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">
            <i class="bi bi-save me-1"></i> Lưu thông tin ngân hàng
        </button>
    </div>

</form>
@endsection
