@extends('layouts.admin')
@section('title', 'Quản lý Cache Q&A')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="text-info fw-bold text-uppercase mb-0">
            <i class="bi bi-database me-2"></i>Quản lý Cache Q&A
        </h2>
        <a href="{{ route('admin.chatbot.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại Dashboard
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.chatbot.cache.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm câu hỏi/trả lời..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="approved" class="form-select">
                        <option value="">-- Trạng thái duyệt --</option>
                        <option value="1" {{ request('approved') == '1' ? 'selected' : '' }}>Đã duyệt</option>
                        <option value="0" {{ request('approved') == '0' ? 'selected' : '' }}>Chờ duyệt</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">-- Danh mục --</option>
                        <option value="booking" {{ request('category') == 'booking' ? 'selected' : '' }}>Booking & Đặt vé</option>
                        <option value="game_info" {{ request('category') == 'game_info' ? 'selected' : '' }}>Trò chơi & Thiết bị</option>
                        <option value="policy" {{ request('category') == 'policy' ? 'selected' : '' }}>Chính sách & Hoàn tiền</option>
                        <option value="general_vr" {{ request('category') == 'general_vr' ? 'selected' : '' }}>Kiến thức chung</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Lọc</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Câu hỏi (User)</th>
                            <th>Câu trả lời của AI</th>
                            <th style="width: 120px;">Danh mục</th>
                            <th style="width: 130px;">Hỏi / Hit</th>
                            <th style="width: 120px;">Trạng thái</th>
                            <th style="width: 130px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($caches as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td class="fw-bold text-dark">{{ Str::limit($item->question, 60) }}</td>
                            <td class="text-muted small">{{ Str::limit($item->answer, 100) }}</td>
                            <td><span class="badge bg-secondary">{{ $item->category }}</span></td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $item->ask_count }} asked</span>
                                <span class="badge bg-warning text-dark border">{{ $item->hit_count }} hits</span>
                            </td>
                            <td>
                                @if($item->approved)
                                    <span class="badge bg-success">Đã duyệt</span>
                                @else
                                    <span class="badge bg-danger">Chờ duyệt</span>
                                    <form action="{{ route('admin.chatbot.cache.approve', $item->id) }}" method="POST" class="d-inline mt-1">
                                        @csrf @method('PATCH')
                                        <button class="btn btn-sm btn-outline-success border-0 py-0 px-1 d-block" title="Duyệt">
                                            <i class="bi bi-check-circle"></i> Phát hành
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.chatbot.cache.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.chatbot.cache.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá cache này?');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>Không có dữ liệu cache nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $caches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
