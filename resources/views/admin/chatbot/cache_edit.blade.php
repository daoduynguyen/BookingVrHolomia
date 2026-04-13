@extends('layouts.admin')
@section('title', 'Chỉnh sửa Câu Trả Lời Cache')

@section('admin_content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="text-info fw-bold text-uppercase mb-0">
            <i class="bi bi-pencil-square me-2"></i>Sửa Cache
        </h2>
        <a href="{{ route('admin.chatbot.cache.index') }}" class="btn btn-outline-secondary btn-sm">
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
            {{-- Câu hỏi hiển thị dạng block quote --}}
            <div class="alert alert-light border shadow-sm mb-4">
                <i class="bi bi-person-circle fs-4 text-secondary me-2"></i>
                <span class="fw-bold fw-italic text-dark fs-5">"{{ $cache->question }}"</span>
                <div class="text-muted small mt-2">
                    Lượt hỏi: {{ $cache->ask_count }} — Cache Hit: {{ $cache->hit_count }}
                </div>
            </div>

            <form action="{{ route('admin.chatbot.cache.update', $cache->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">Câu trả lời chuẩn (AI sẽ dùng để trả lời sau này) <span class="text-danger">*</span></label>
                    <textarea name="answer" class="form-control" rows="8" required placeholder="Nhập câu trả lời chính thức, tối thiểu 10 ký tự...">{{ old('answer', $cache->answer) }}</textarea>
                    <small class="text-muted">Độ dài tối thiểu 10 ký tự. Có thể dùng emoji.</small>
                </div>

                <div class="row mb-3 g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="booking" {{ old('category', $cache->category) == 'booking' ? 'selected' : '' }}>Booking & Đặt vé</option>
                            <option value="game_info" {{ old('category', $cache->category) == 'game_info' ? 'selected' : '' }}>Trò chơi & Thiết bị</option>
                            <option value="policy" {{ old('category', $cache->category) == 'policy' ? 'selected' : '' }}>Chính sách & Hoàn tiền</option>
                            <option value="general_vr" {{ old('category', $cache->category) == 'general_vr' ? 'selected' : '' }}>Kiến thức chung VR</option>
                            <option value="other" {{ old('category', $cache->category) == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Điểm chất lượng (1-5) <span class="text-danger">*</span></label>
                        <input type="number" name="quality_score" class="form-control" min="1" max="5" value="{{ old('quality_score', $cache->quality_score) }}" required>
                        <small class="text-muted">Điểm 5 có độ ưu tiên sử dụng cao nhất.</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Từ khóa nhận diện (cách nhau bởi dấu phẩy)</label>
                    <input type="text" name="keywords" class="form-control" value="{{ old('keywords', $cache->keywords) }}" placeholder="VD: gia ve, dat ve, dat cho...">
                </div>

                <div class="form-check form-switch mb-4 fs-5">
                    <input class="form-check-input" type="checkbox" name="approved" id="approved" value="1" {{ old('approved', $cache->approved) ? 'checked' : '' }}>
                    <label class="form-check-label text-success fw-bold ms-2" for="approved">
                        Đồng ý phát hành câu trả lời này vào Cache (AI sẽ trả lời ngay nếu trùng khớp)
                    </label>
                </div>

                <hr class="border-light opacity-50 mb-4">
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-save me-2"></i>Lưu Cache</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
