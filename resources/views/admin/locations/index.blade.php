@extends('layouts.admin')

@section('title', 'Quản lý Cơ sở')

@section('admin_content')

    {{-- TIÊU ĐỀ --}}
    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between mb-4 gap-3">
        <div class="d-flex align-items-center">
            <div class="bg-primary me-3" style="width:50px; height:3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0">Quản lý Chi nhánh</h2>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary fw-bold rounded-pill px-4 py-2 shadow">
            <i class="bi bi-plus-lg me-1"></i> THÊM CHI NHÁNH
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($locations as $location)
            <div class="col-md-6 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">

                    {{-- Thanh màu trên đầu card --}}
                    <div style="height:6px; background: {{ $location->color ?? '#2563eb' }};"></div>

                    <div class="card-body p-4">
                        {{-- Tên + trạng thái --}}
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div>
                                <h5 class="fw-bold mb-1">{{ $location->name }}</h5>
                                <code class="text-muted" style="font-size:12px;">{{ $location->slug }}</code>
                            </div>
                            @if($location->is_active)
                                <span class="badge bg-success rounded-pill px-3">Hoạt động</span>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3">Ẩn</span>
                            @endif
                        </div>

                        {{-- Thông tin --}}
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-geo-alt-fill text-primary mt-1" style="font-size:13px;"></i>
                                <span class="text-muted small">{{ $location->address }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-primary" style="font-size:13px;"></i>
                                <span class="text-muted small">{{ $location->hotline }}</span>
                            </div>
                            @if($location->opening_hours)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-fill text-primary" style="font-size:13px;"></i>
                                    <span class="text-muted small">{{ $location->opening_hours }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- Link subdomain --}}
                        @if($location->slug)
                            <div class="bg-light rounded-3 px-3 py-2 mb-3">
                                <div class="text-muted" style="font-size:11px; text-transform:uppercase; letter-spacing:1px;">
                                    Landing page</div>
                                <a href="{{ $location->landingUrl() }}" target="_blank"
                                    class="text-primary fw-bold text-decoration-none" style="font-size:13px;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i>
                                    {{ $location->slug }}.{{ env('APP_DOMAIN', 'holomia.test') }}
                                </a>
                            </div>
                        @endif

                        {{-- Màu sắc preview --}}
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div
                                style="width:24px; height:24px; border-radius:50%; background:{{ $location->color ?? '#2563eb' }}; border:2px solid #dee2e6;">
                            </div>
                            <span class="text-muted small">Màu chi nhánh: {{ $location->color ?? '#2563eb' }}</span>
                        </div>

                        {{-- Nút hành động --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.locations.edit', $location->id) }}"
                                class="btn btn-primary btn-sm rounded-pill px-3 fw-bold flex-grow-1">
                                <i class="bi bi-pencil me-1"></i> Chỉnh sửa
                            </a>
                            <a href="{{ $location->landingUrl() }}" target="_blank"
                                class="btn btn-outline-info btn-sm rounded-pill px-3">
                                <i class="bi bi-eye"></i>
                            </a>
                            <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="d-inline"
                                onsubmit="return confirm('Xoá chi nhánh {{ $location->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $locations->links('pagination::bootstrap-5') }}</div>

@endsection