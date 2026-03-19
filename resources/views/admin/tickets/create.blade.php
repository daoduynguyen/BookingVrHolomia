@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex align-items-center mb-5">
        <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2">Thêm vé VR mới</h2>
    </div>

    <div class="profile-content shadow-lg p-4 bg-white rounded">
        <form action="{{ route('admin.tickets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Hiển thị lỗi --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                {{-- 1. Tên trò chơi --}}
                <div class="col-md-6 mb-4">
                    <label class="form-label text-dark">Tên trò chơi VR</label>
                    <input type="text" name="name" class="form-control" placeholder="Ví dụ: Zombie VR" required>
                </div>

                {{-- 2. Thể loại --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-dark">Thể loại</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- 3. CƠ SỞ (SỬA LẠI: Dùng Input để tự tạo mới) --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-dark">Cơ sở / Địa điểm</label>
                    {{-- QUAN TRỌNG: name="location_name" --}}
                    <input type="text" 
                           name="location_name" 
                           class="form-control bg-white text-dark border-light" 
                           list="locationOptions" 
                           placeholder="Nhập tên cơ sở..." 
                           required autocomplete="off">
                    
                    <datalist id="locationOptions">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->name }}">
                        @endforeach
                    </datalist>
                </div>
            </div>

            {{-- 4. Link Ảnh & Media --}}
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label text-dark">Ảnh bìa chính (URL)</label>
                    <textarea name="image_url" class="form-control bg-white text-dark border-light" rows="1" placeholder="https://..." required></textarea>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label text-dark">Video Trailer (Link Youtube)</label>
                    <input type="text" name="trailer_url" class="form-control bg-white text-dark border-light" placeholder="Ví dụ: https://www.youtube.com/watch?v=...">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label text-dark">Upload Các Ảnh Phụ (Chọn nhiều)</label>
                    <input type="file" name="gallery[]" class="form-control bg-white text-dark border-light" multiple accept="image/*">
                </div>
            </div>

            {{-- 5. PHÂN LOẠI VÉ (MỚI) --}}
            <div class="row">
                <div class="col-md-9 mb-4">
                    <div class="p-3 border border-primary rounded bg-light">
                        <label class="form-label text-primary fw-bold mb-3">
                            <i class="bi bi-tags"></i> Bảng giá & Loại vé (Người lớn, Trẻ em, VIP...)
                        </label>
                        
                        {{-- Vùng chứa các dòng phân loại vé --}}
                        <div id="ticket-types-container"></div>
                        
                        <button type="button" class="btn btn-sm btn-outline-info mt-3 fw-bold" onclick="addTicketTypeRow()">
                            <i class="bi bi-plus-circle me-1"></i> THÊM LOẠI VÉ MỚI
                        </button>
                    </div>
                </div>

                {{-- 6. Thời lượng (Giữ nguyên) --}}
                <div class="col-md-3 mb-4">
                    <label class="form-label text-dark">Thời lượng (Phút)</label>
                    <input type="number" name="duration" class="form-control" value="30">
                </div>
            </div>

            {{-- 7. Trạng thái --}}
            <div class="mb-4">
                <label class="form-label text-dark">Trạng thái</label>
                <select name="status" class="form-select">
                    <option value="active">Hoạt động</option>
                    <option value="maintenance">Bảo trì</option>
                </select>
            </div>

            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn btn-primary fw-bold text-dark px-5">LƯU VÉ MỚI</button>
                <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary px-5">HỦY</a>
            </div>
        </form>
    </div>
</div>

<script>
    function addTicketTypeRow(name = '', price = '') {
        const container = document.getElementById('ticket-types-container');
        const row = document.createElement('div');
        row.className = 'row mb-2 type-row align-items-center';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="type_name[]" class="form-control bg-white text-dark border-light" placeholder="Tên loại (VD: Trẻ em, Combo...)" value="${name}" required>
            </div>
            <div class="col-md-5">
                <input type="number" name="type_price[]" class="form-control text-primary bg-white border-light fw-bold" placeholder="Giá vé (VD: 150000)" value="${price}" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-danger w-100 shadow-sm" onclick="this.closest('.type-row').remove()">
                    <i class="bi bi-trash"></i> Xóa
                </button>
            </div>
        `;
        container.appendChild(row);
    }

    // Tự động load 1 dòng trống khi mới vào trang thêm vé
    document.addEventListener('DOMContentLoaded', function() {
        addTicketTypeRow('Vé tiêu chuẩn', '');
    });
</script>
@endsection