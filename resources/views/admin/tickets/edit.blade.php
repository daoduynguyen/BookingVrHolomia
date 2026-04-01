@extends('layouts.admin')

@section('title', 'Sửa Vé')

@section('admin_content')
    <div class="container-fluid">
        <div class="d-flex align-items-center mb-5">
            <div class="bg-warning me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2">
                Cập nhật vé: {{ $ticket->name }}
            </h2>
        </div>

        <div class="profile-content shadow-lg p-4 bg-white rounded">
            <form action="{{ route('admin.tickets.update', $ticket->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                        <input type="text" name="name" class="form-control" value="{{ old('name', $ticket->name) }}"
                            required>
                    </div>

                    {{-- 2. Thể loại --}}
                    <div class="col-md-3 mb-4">
                        <label class="form-label text-dark">Thể loại</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $ticket->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- 3. CƠ SỞ (Checkbox) --}}
                    <div class="col-md-3 mb-4">
                        <label class="form-label text-dark fw-bold">Cơ sở</label>
                        <div class="d-flex flex-column gap-3 p-3 bg-white border border-light rounded-3" style="max-height: 160px; overflow-y: auto; padding-right: 8px;">
                            @foreach($locations as $loc)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="location_ids[]" value="{{ $loc->id }}"
                                        id="loc_edit_{{ $loc->id }}" {{ $ticket->locations->contains('id', $loc->id) ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark" for="loc_edit_{{ $loc->id }}">
                                        {{ $loc->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    {{-- 4. Link ảnh & Media --}}
                    <div class="row mb-4 border border-light rounded p-3 bg-light">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label class="form-label text-dark">Ảnh bìa chính (URL)</label>
                                <textarea name="image_url" class="form-control bg-white text-dark border-light" rows="2"
                                    required>{{ old('image_url', $ticket->image_url) }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark">Video Trailer (Link Youtube)</label>
                                <input type="text" name="trailer_url" class="form-control bg-white text-dark border-light"
                                    value="{{ old('trailer_url', $ticket->trailer_url) }}"
                                    placeholder="https://www.youtube.com/watch?v=...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label text-dark">Upload Thêm Ảnh Phụ</label>
                                <input type="file" name="gallery[]" class="form-control bg-white text-dark border-light"
                                    multiple accept="image/*">
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-dark mb-2 text-center w-100">Ảnh Bìa Hiện Tại</label>
                            <div class="text-center mb-3">
                                <img src="{{ $ticket->image_url }}" class="img-fluid rounded border border-light"
                                    style="max-height: 120px;" onerror="this.src='https://via.placeholder.com/150'">
                            </div>

                            {{-- CSS Hiệu ứng di chuột hiện nút X --}}
                            <style>
                                .gallery-item {
                                    position: relative;
                                    display: inline-block;
                                }

                                .btn-remove-img {
                                    position: absolute;
                                    top: -5px;
                                    right: -5px;
                                    background-color: #dc3545;
                                    color: white;
                                    border-radius: 50%;
                                    width: 20px;
                                    height: 20px;
                                    font-size: 12px;
                                    display: none;
                                    align-items: center;
                                    justify-content: center;
                                    cursor: pointer;
                                    border: none;
                                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
                                }

                                .gallery-item:hover .btn-remove-img {
                                    display: flex;
                                }

                                .btn-remove-img:hover {
                                    background-color: #ff0000;
                                    transform: scale(1.1);
                                }
                            </style>

                            @if($ticket->gallery)
                                <label class="form-label text-dark mb-2 text-center w-100" style="font-size: 0.85rem;">Gallery
                                    Ảnh Phụ</label>
                                <div class="d-flex flex-wrap gap-2 justify-content-center" id="gallery-container">
                                    @foreach($ticket->gallery as $index => $img)
                                        {{-- Khối bọc từng ảnh --}}
                                        <div class="gallery-item" id="img-box-{{ $index }}">
                                            <img src="{{ asset('storage/' . $img) }}"
                                                style="height: 50px; width: 50px; object-fit: cover;"
                                                class="rounded border border-light">
                                            {{-- Nút Xóa --}}
                                            <button type="button" class="btn-remove-img"
                                                onclick="removeImage('{{ $ticket->id }}', '{{ $img }}', 'img-box-{{ $index }}')"
                                                title="Xóa ảnh này">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="row">

                        {{-- 5. PHÂN LOẠI VÉ (MỚI) --}}
                        <div class="mb-4 p-4 border border-primary rounded bg-light">
                            <label class="form-label text-primary fw-bold mb-3">
                                <i class="bi bi-tags"></i> Phân loại vé & Bảng giá
                            </label>

                            {{-- Vùng chứa các dòng phân loại vé --}}
                            <div id="ticket-types-container"></div>

                            <button type="button" class="btn btn-sm btn-outline-info mt-3 fw-bold"
                                onclick="addTicketTypeRow()">
                                <i class="bi bi-plus-circle me-1"></i> THÊM LOẠI VÉ MỚI
                            </button>
                        </div>

                        {{-- 6. Thời lượng --}}
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-dark">Thời lượng (Phút)</label>
                            <input type="number" name="duration" class="form-control"
                                value="{{ old('duration', $ticket->duration) }}">
                        </div>

                        {{-- 7. Trạng thái --}}
                        <div class="col-md-3 mb-4">
                            <label class="form-label text-dark">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $ticket->status == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                <option value="maintenance" {{ $ticket->status == 'maintenance' ? 'selected' : '' }}>Bảo trì
                                </option>
                            </select>
                        </div>
                    </div>

                    {{-- 8. Mô tả --}}
                    <div class="mb-4">
                        <label class="form-label text-dark">Mô tả trò chơi</label>
                        <textarea name="description" class="form-control"
                            rows="3">{{ old('description', $ticket->description) }}</textarea>
                    </div>

                    <div class="d-flex gap-3 mt-4">
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-5">CẬP NHẬT</button>
                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary px-5">HỦY</a>
                    </div>
            </form>
        </div>
    </div>

    <script>
        // Hàm gọi API xóa ảnh
        function removeImage(ticketId, imagePath, boxId) {
            if (confirm('Bạn có chắc chắn muốn xóa ảnh này khỏi Gallery không?')) {
                // Gửi request ngầm lên server
                fetch(`/admin/tickets/${ticketId}/remove-gallery-image`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}' // Token bảo mật của Laravel
                    },
                    body: JSON.stringify({ image_path: imagePath })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Nếu server báo thành công -> Làm cho ảnh từ từ mờ đi rồi biến mất
                            let imgBox = document.getElementById(boxId);
                            imgBox.style.transition = "opacity 0.3s ease";
                            imgBox.style.opacity = "0";
                            setTimeout(() => imgBox.remove(), 300);
                        } else {
                            alert('Lỗi: ' + data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    </script>

    <script>
        // Hàm tạo 1 dòng (Tên loại vé + Giá)
        function addTicketTypeRow(name = '', price = '') {
            const container = document.getElementById('ticket-types-container');
            const row = document.createElement('div');
            row.className = 'row mb-2 type-row align-items-center';
            row.innerHTML = `
                <div class="col-md-5">
                    <input type="text" name="type_name[]" class="form-control bg-white text-dark border-light" placeholder="Tên loại (VD: Trẻ em, Combo 2 người)" value="${name}" required>
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

        // Tự động load dữ liệu cũ khi vào form Edit
        document.addEventListener('DOMContentLoaded', function () {
            const existingTypes = @json($ticket->ticket_types ?? []);

            if (existingTypes.length > 0) {
                existingTypes.forEach(type => addTicketTypeRow(type.name, type.price));
            } else {
                // Nếu vé cũ chưa có, tạo mặc định 1 dòng để nhập
                addTicketTypeRow('Vé tiêu chuẩn', '{{ $ticket->price ?? "" }}');
            }
        });
    </script>
@endsection