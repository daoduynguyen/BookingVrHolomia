@extends('layouts.admin')

@section('title', 'Quản lý Voucher')

@section('admin_content')
<div class="container-fluid">

    {{-- TIÊU ĐỀ --}}
    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between mb-4 gap-3">
        <div class="d-flex align-items-center flex-shrink-0">
            <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2 text-nowrap">Kho Voucher</h2>
        </div>
        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary fw-bold shadow text-nowrap rounded-pill px-4 py-2">
            <i class="bi bi-plus-lg"></i> THÊM VOUCHER
        </a>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- BẢNG --}}
    <div class="bg-white rounded-4 shadow-sm border border-light overflow-hidden">

        {{-- Header --}}
        <div class="row g-0 px-4 py-3 border-bottom border-light bg-light text-secondary text-uppercase small fw-bold">
            <div class="col-3">Mã voucher</div>
            <div class="col-2 text-center text-nowrap">Loại giảm</div>
            <div class="col-1 text-center text-nowrap">Giá trị</div>
            <div class="col-2 text-center text-nowrap">Hạn dùng</div>
            <div class="col-2 text-center text-nowrap">Trạng thái</div>
            <div class="col-2 text-end text-nowrap">Hành động</div>
        </div>

        @forelse($coupons as $coupon)
            @php
                $expired   = now()->gt($coupon->expiry_date);
                $isPercent = $coupon->type == 'percent';
                $accent    = $expired ? '#adb5bd' : ($isPercent ? '#2563eb' : '#198754');
                $value     = $isPercent
                    ? number_format($coupon->value, 0) . '%'
                    : number_format($coupon->value) . 'đ';
            @endphp

            <div class="row g-0 align-items-center px-4 py-3 border-bottom border-light {{ $expired ? 'opacity-75 bg-light' : '' }}"
                 style="border-left: 4px solid {{ $accent }} !important;">

                {{-- Mã code --}}
                <div class="col-3 d-flex align-items-center gap-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width:40px; height:40px; background:{{ $accent }}18;">
                        <i class="bi {{ $isPercent ? 'bi-percent' : 'bi-cash-coin' }}"
                           style="color:{{ $accent }}; font-size:1rem;"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark text-nowrap" style="font-family:'Courier New',monospace; letter-spacing:1.5px;">
                            {{ $coupon->code }}
                        </div>
                        <div class="small text-muted text-nowrap">{{ $isPercent ? 'Phần trăm' : 'Tiền cố định' }}</div>
                    </div>
                </div>

                {{-- Loại --}}
                <div class="col-2 text-center">
                    <span class="badge rounded-pill px-3 py-2 small fw-bold text-nowrap"
                          style="background:{{ $accent }}15; color:{{ $accent }};">
                        {{ $isPercent ? '% Phần trăm' : '💵 Tiền cố định' }}
                    </span>
                </div>

                {{-- Giá trị --}}
                <div class="col-1 text-center">
                    <span class="fw-bold fs-6 text-nowrap" style="color:{{ $accent }};">{{ $value }}</span>
                </div>

                {{-- Hạn dùng --}}
                <div class="col-2 text-center">
                    <div class="fw-bold {{ $expired ? 'text-danger' : 'text-dark' }} small text-nowrap">
                        {{ \Carbon\Carbon::parse($coupon->expiry_date)->format('d/m/Y') }}
                    </div>
                    @if(!$expired)
                        <div class="text-muted text-nowrap" style="font-size:0.75rem;">
                            còn {{ (int) now()->diffInDays($coupon->expiry_date) }} ngày
                        </div>
                    @else
                        <div class="text-danger text-nowrap" style="font-size:0.75rem;">Đã hết hạn</div>
                    @endif
                </div>

                {{-- Trạng thái --}}
                <div class="col-2 text-center">
                    @if($expired)
                        <span class="badge bg-danger rounded-pill px-3 py-2 text-nowrap" style="font-size:0.8rem;">Hết hạn</span>
                    @else
                        <span class="badge bg-success rounded-pill px-3 py-2 text-nowrap" style="font-size:0.8rem;">Đang chạy</span>
                    @endif
                </div>

                {{-- Hành động --}}
                <div class="col-2 text-end d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                       class="btn btn-sm btn-outline-primary" title="Sửa">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" 
                        class="d-inline form-delete" data-label="Voucher {{ $coupon->code }}">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center py-5 text-muted">
                <i class="bi bi-ticket-perforated fs-1 d-block mb-3 opacity-50"></i>
                <p class="small">Chưa có voucher nào. Hãy bấm <strong>THÊM VOUCHER</strong> ngay!</p>
            </div>
        @endforelse

    </div>

    {{-- Phân trang --}}
    <div class="d-flex justify-content-center mt-4">
        {{ $coupons->links('pagination::bootstrap-5') }}
    </div>

</div>
@endsection