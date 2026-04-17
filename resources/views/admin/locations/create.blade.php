{{--
resources/views/admin/locations/create.blade.php (Phase 1 – cập nhật)
Thêm các field: slug, description, banner_image, opening_hours, maps_url, facebook_url, is_active
--}}
@extends('layouts.admin')

@section('admin_content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Thêm Cơ Sở Mới</h2>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
            <i class="bi bi-arrow-left"></i> Quay Lại
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4">
        <form action="{{ route('admin.locations.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- ── THÔNG TIN CƠ BẢN ── --}}
            <h6 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing:1px;">
                <i class="bi bi-building me-2"></i>Thông tin cơ bản
            </h6>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Tên Cơ Sở <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required placeholder="Vd: Holomia VR Hà Nội">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        Slug (URL)
                        <span class="text-muted fw-normal" style="font-size:.8rem;">– tự tạo nếu để trống</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text text-muted">/co-so/</span>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                            value="{{ old('slug') }}" placeholder="ha-noi">
                    </div>
                    @error('slug') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    <div class="form-text">Chỉ dùng chữ thường, số và dấu gạch ngang. Vd: <code>ha-noi</code></div>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-bold">Địa Chỉ <span class="text-danger">*</span></label>
                    <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                        value="{{ old('address') }}" required placeholder="Số nhà, đường, quận, thành phố...">
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Hotline <span class="text-danger">*</span></label>
                    <input type="text" name="hotline" class="form-control @error('hotline') is-invalid @enderror"
                        value="{{ old('hotline') }}" required placeholder="0901 234 567">
                    @error('hotline') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Giờ Mở Cửa</label>
                    <input type="text" name="opening_hours" class="form-control" value="{{ old('opening_hours') }}"
                        placeholder="09:00 – 22:00 mỗi ngày">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Facebook Fanpage</label>
                    <input type="url" name="facebook_url" class="form-control" value="{{ old('facebook_url') }}"
                        placeholder="https://facebook.com/holomia.hanoi">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tổng Số Kính VR <span class="text-danger">*</span></label>
                    <input type="number" name="total_devices" class="form-control @error('total_devices') is-invalid @enderror"
                        value="{{ old('total_devices', 20) }}" required min="1">
                    @error('total_devices') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">Dùng để giới hạn số khách cùng lúc.</div>
                </div>
            </div>

            {{-- ── LANDING PAGE CONTENT ── --}}
            <hr class="my-4">
            <h6 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing:1px;">
                <i class="bi bi-images me-2"></i>Nội dung Landing Page
            </h6>

            <div class="row g-3 mb-4">
                <div class="col-12">
                    <label class="form-label fw-bold">Mô Tả Cơ Sở</label>
                    <textarea name="description" class="form-control" rows="3"
                        placeholder="Giới thiệu ngắn về cơ sở, hiển thị trên landing page...">{{ old('description') }}</textarea>
                    <div class="form-text">Tối đa 1000 ký tự. Hiển thị ở phần giới thiệu trên trang cơ sở.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Ảnh Banner</label>
                    <input type="file" name="banner_image" class="form-control @error('banner_image') is-invalid @enderror"
                        accept="image/*">
                    @error('banner_image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <div class="form-text">Kích thước khuyến nghị: 1920×600px. Định dạng: JPG, PNG, WEBP.</div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Google Maps URL</label>
                    <input type="text" name="maps_url" class="form-control" value="{{ old('maps_url') }}"
                        placeholder="https://maps.google.com/maps?q=...">
                    <div class="form-text">Dán link Google Maps thường vào đây, hệ thống tự chuyển thành bản đồ nhúng.</div>
                </div>
            </div>

            {{-- ── TRẠNG THÁI ── --}}
            <hr class="my-4">
            <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="isActive">
                    Hiển thị landing page cơ sở này (công khai)
                </label>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-save"></i> Lưu Cơ Sở
                </button>
                <a href="{{ route('admin.locations.index') }}" class="btn btn-light">Huỷ</a>
            </div>
        </form>
    </div>
@endsection
