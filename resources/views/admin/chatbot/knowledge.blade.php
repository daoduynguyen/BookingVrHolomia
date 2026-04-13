@extends('layouts.admin')
@section('title', 'Dữ liệu huấn luyện của Chatbot')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="text-primary fw-bold text-uppercase mb-0">
            <i class="bi bi-book me-2"></i>Dữ liệu huấn luyện
        </h2>
        <div>
            <a href="{{ route('admin.chatbot.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Dashboard
            </a>
            <a href="{{ route('admin.chatbot.knowledge.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Thêm dữ liệu huấn luyện
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.chatbot.knowledge.index') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm Topic hoặc nội dung..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <select name="category" class="form-select">
                        <option value="">-- Tất cả danh mục --</option>
                        <option value="faq" {{ request('category') == 'faq' ? 'selected' : '' }}>Câu hỏi thường gặp (FAQ)</option>
                        <option value="policy" {{ request('category') == 'policy' ? 'selected' : '' }}>Chính sách</option>
                        <option value="game_guide" {{ request('category') == 'game_guide' ? 'selected' : '' }}>Hướng dẫn game</option>
                        <option value="vr_knowledge" {{ request('category') == 'vr_knowledge' ? 'selected' : '' }}>Kiến thức VR</option>
                        <option value="promotion" {{ request('category') == 'promotion' ? 'selected' : '' }}>Khuyến mãi</option>
                        <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Lọc dữ liệu</button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px;">STT</th>
                            <th>Chủ đề (Topic)</th>
                            <th>Trích dẫn nội dung</th>
                            <th>Danh mục</th>
                            <th>Độ ưu tiên</th>
                            <th>Lượt dùng</th>
                            <th>Trạng thái</th>
                            <th style="width: 140px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($knowledges as $k)
                        <tr>
                            <td>{{ $knowledges->firstItem() + $loop->index }}</td>
                            <td class="fw-bold text-dark">{{ Str::limit($k->topic, 60) }}</td>
                            <td class="text-muted small">{{ Str::limit($k->content, 100) }}</td>
                            <td><span class="badge bg-secondary">{{ $k->category }}</span></td>
                            <td class="text-center">{{ $k->priority }}</td>
                            <td class="text-center"><span class="badge bg-light text-dark border">{{ $k->use_count }}</span></td>
                            <td>
                                <form action="{{ route('admin.chatbot.knowledge.toggle', $k->id) }}" method="POST">
                                    @csrf @method('PATCH')
                                    @if($k->is_active)
                                        <button class="btn btn-sm btn-success border-0 px-2 py-0" title="Tắt">Active</button>
                                    @else
                                        <button class="btn btn-sm btn-secondary border-0 px-2 py-0" title="Bật">Inactive</button>
                                    @endif
                                </form>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('admin.chatbot.knowledge.edit', $k->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.chatbot.knowledge.destroy', $k->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xoá kiến thức này? Hành động không thể hoàn tác!');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="bi bi-box fs-2 d-block mb-2"></i>Chưa có kiến thức nào
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3 d-flex justify-content-center">
                {{ $knowledges->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
