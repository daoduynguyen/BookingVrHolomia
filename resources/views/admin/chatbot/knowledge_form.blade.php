@extends('layouts.admin')
@section('title', isset($knowledge) ? 'Chỉnh sửa Kiến thức' : 'Thêm Kiến thức Mới')

@section('admin_content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="text-primary fw-bold text-uppercase mb-0">
            <i class="bi {{ isset($knowledge) ? 'bi-pencil-square' : 'bi-plus-circle' }} me-2"></i>
            {{ isset($knowledge) ? 'Sửa Kiến thức' : 'Thêm Kiến thức mới' }}
        </h2>
        <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form action="{{ isset($knowledge) ? route('admin.chatbot.knowledge.update', $knowledge->id) : route('admin.chatbot.knowledge.store') }}" method="POST">
                @csrf
                @if(isset($knowledge))
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label class="form-label fw-bold">Chủ đề (Topic) <span class="text-danger">*</span></label>
                    <input type="text" name="topic" class="form-control" value="{{ old('topic', $knowledge->topic ?? '') }}" required placeholder="VD: Hướng dẫn đăng ký thành viên mới">
                    <small class="text-muted">Ngắn gọn, rõ ý về nội dung bài viết.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">Nội dung chi tiết (Content) <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="8" required placeholder="Nhập đầy đủ kiến thức cung cấp cho AI... Tối thiểu 20 ký tự.">{{ old('content', $knowledge->content ?? '') }}</textarea>
                    <small class="text-muted">Càng chi tiết, AI trả lời càng chính xác.</small>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            @php $cat = old('category', $knowledge->category ?? 'faq'); @endphp
                            <option value="faq" {{ $cat == 'faq' ? 'selected' : '' }}>Câu hỏi thường gặp (FAQ)</option>
                            <option value="policy" {{ $cat == 'policy' ? 'selected' : '' }}>Chính sách, Hoàn tiền</option>
                            <option value="game_guide" {{ $cat == 'game_guide' ? 'selected' : '' }}>Hướng dẫn trò chơi</option>
                            <option value="vr_knowledge" {{ $cat == 'vr_knowledge' ? 'selected' : '' }}>Kiến thức công nghệ VR</option>
                            <option value="promotion" {{ $cat == 'promotion' ? 'selected' : '' }}>Khuyến mãi & Sự kiện</option>
                            <option value="other" {{ $cat == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Độ ưu tiên (1-10) <span class="text-danger">*</span></label>
                        <input type="number" name="priority" class="form-control" min="1" max="10" value="{{ old('priority', $knowledge->priority ?? 5) }}" required>
                        <small class="text-muted">10 là ưu tiên cao nhất, báo cho bot nạp đầu tiên.</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Từ khóa nhận diện (Keywords)</label>
                    <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $knowledge->keywords ?? '') }}" placeholder="VD: dang ky, the thanh vien, dang nhap...">
                </div>

                @if(isset($knowledge))
                <div class="form-check form-switch mb-4 fs-5">
                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $knowledge->is_active) ? 'checked' : '' }}>
                    <label class="form-check-label text-success fw-bold ms-2" for="is_active">
                        Hoạt động (Cho phép bot học kiến thức này)
                    </label>
                </div>
                @endif

                <hr class="border-light opacity-50 mb-4">
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-save me-2"></i>{{ isset($knowledge) ? 'Cập nhật' : 'Thêm mới' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
