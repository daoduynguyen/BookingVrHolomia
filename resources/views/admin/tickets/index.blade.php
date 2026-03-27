@extends('layouts.admin')

@section('title', 'Quản lý Vé')

@section('admin_content')
<style>
    .price-scroll-box::-webkit-scrollbar { width: 4px; }
    .price-scroll-box::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .price-scroll-box::-webkit-scrollbar-thumb { background: #adb5bd; border-radius: 10px; }
    .price-scroll-box::-webkit-scrollbar-thumb:hover { background: #6c757d; }
</style>

    <div class="container-fluid">
       {{-- TIÊU ĐỀ + NÚT THÊM --}}
<div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between mb-4 gap-3">
    <div class="d-flex align-items-center flex-shrink-0">
        <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2 text-nowrap">Quản lý kho vé VR</h2>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary fw-bold shadow text-nowrap rounded-pill px-4 py-2">
            <i class="bi bi-plus-lg"></i> THÊM VÉ MỚI
        </a>
    </div>
</div>

{{-- Thanh tìm kiếm --}}
<div class="row justify-content-center mb-5">
    <div class="col-md-10 col-lg-6">
        <form action="{{ route('admin.tickets.index') }}" method="GET" class="d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center bg-white rounded-pill shadow-sm border border-light w-100" style="height: 50px;">
                <span class="bg-transparent border-0 text-primary ps-4 pe-2 fs-5">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none px-1 h-100" placeholder="Tìm tên trò chơi..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-light rounded-circle text-danger d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

        {{-- Hiển thị thông báo thành công --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="profile-content shadow-sm p-4 bg-white rounded-4 border border-light">
            <div class="table-responsive">
                <table class="table table-hover align-middle border-light">
                    <thead>
                        <tr class="text-muted border-bottom border-light">
                            <th width="10%">Ảnh</th>
                            <th width="25%">Tên trò chơi</th>
                            <th width="15%">Thể loại</th>
                            <th width="25%">Bảng giá vé</th> 
                            <th width="10%">Trạng thái</th>
                            <th width="15%" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                {{-- 1. Ảnh --}}
                                <td>
                                    <img src="{{ $ticket->image_url }}" alt="{{ $ticket->name }}" class="rounded shadow-sm"
                                        style="width: 80px; height: 50px; object-fit: cover;"
                                        onerror="this.src='https://via.placeholder.com/80x50?text=No+Image'">
                                </td>
                                
                                {{-- 2. Tên --}}
                                <td>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $ticket->name }}</h6>
                                    {{-- Hiển thị thêm tên cơ sở cho dễ quản lý --}}
                                    @if($ticket->locations->count() > 0)
                                        <small class="text-muted"><i class="bi bi-geo-alt-fill me-1 text-muted"></i>{{ $ticket->locations->pluck('name')->join(', ') }}</small>
                                    @endif
                                </td>

                                {{-- 3. Thể loại --}}
                                <td>
                                    <span class="badge bg-primary text-white">
                                        {{ $ticket->category->name ?? 'Chưa phân loại' }}
                                    </span>
                                </td>

                                {{-- 4. GIÁ VÉ (ĐÃ NÂNG CẤP JSON) --}}
                                <td>
                                    @if($ticket->ticket_types && count($ticket->ticket_types) > 0)
                                        {{-- Nếu đã có mảng JSON thì lặp ra --}}
                                       <div class="d-flex flex-column gap-1 price-scroll-box" style="max-width: 250px; max-height: 80px; overflow-y: auto; padding-right: 6px;">
                                            @foreach($ticket->ticket_types as $type)
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-light text-dark fw-normal border border-light" style="min-width: 100px; text-align: left;">
                                                        {{ $type['name'] }}
                                                    </span>
                                                    <span class="fw-bold text-success ms-2" style="font-size: 0.95rem;">
                                                        {{ number_format($type['price']) }} đ
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        {{-- Fallback: Dành cho các vé cũ chưa được cập nhật JSON --}}
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-light text-dark fw-normal border border-light" style="min-width: 100px; text-align: left;">Vé cơ bản</span>
                                            <span class="fw-bold text-success ms-2" style="font-size: 0.95rem;">{{ number_format($ticket->price) }} đ</span>
                                        </div>
                                    @endif
                                </td>

                                {{-- 5. Trạng thái --}}
                                <td>
                                    @if($ticket->status == 'active')
                                        <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Hoạt động</span>
                                    @else
                                        <span class="badge bg-danger"><i class="bi bi-exclamation-octagon me-1"></i>Bảo trì</span>
                                    @endif
                                </td>
                                
                                {{-- 6. Thao tác --}}
                                <td class="text-end">
                                    <a href="{{ route('admin.tickets.edit', $ticket->id) }}" class="btn btn-sm btn-outline-primary me-2" title="Sửa vé">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.tickets.delete', $ticket->id) }}" method="POST" 
                                             class="d-inline form-delete" data-label="{{ $ticket->name }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa vé">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($tickets->isEmpty())
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1"></i>
                    <p class="mt-2">Chưa có vé nào trong hệ thống.</p>
                </div>
            @endif
            
            <div class="mt-3 pb-4 d-flex justify-content-center">
        <div style="width: auto;">
            {{ $tickets->links() }}
        </div>
    </div>
        </div>
    </div>
@endsection