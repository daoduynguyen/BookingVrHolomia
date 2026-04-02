@extends('layouts.admin')

@section('title', 'Chỉnh sửa Chi nhánh')

@section('admin_content')

    {{-- TIÊU ĐỀ --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-primary me-3" style="width:50px; height:3px;"></div>
            <div>
                <h2 class="text-primary fw-bold text-uppercase mb-0">Chỉnh sửa Chi nhánh</h2>
                @if($location->slug)
                    <small class="text-muted">
                        Landing page:
                        <a href="{{ $location->landingUrl() }}" target="_blank" class="text-primary fw-bold">
                            {{ $location->slug }}.{{ env('APP_DOMAIN', 'holomia.test') }} <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </small>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary rounded-pill px-4 fw-bold">
            <i class="bi bi-arrow-left me-1"></i> Quay lại
        </a>
    </div>

    <form action="{{ route('admin.locations.update', $location->id) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="row g-4">

            {{-- CỘT TRÁI --}}
            <div class="col-lg-8">

                {{-- THÔNG TIN CƠ BẢN --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-primary text-uppercase mb-4" style="letter-spacing:1px;">
                        <i class="bi bi-building me-2"></i>Thông tin cơ bản
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Tên chi nhánh <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $location->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">
                                Slug (subdomain)
                                <span class="text-muted fw-normal" style="font-size:.75rem;">– dùng cho URL chi nhánh</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text text-muted bg-light" style="font-size:13px;">slug.</span>
                                <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                       value="{{ old('slug', $location->slug) }}"
                                       placeholder="royalcity">
                                <span class="input-group-text text-muted bg-light" style="font-size:13px;">.{{ env('APP_DOMAIN', 'holomia.test') }}</span>
                            </div>
                            @error('slug')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <div class="form-text">Chỉ dùng chữ thường, số và dấu gạch ngang. VD: <code>royalcity</code></div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-uppercase text-muted">Địa chỉ <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                                   value="{{ old('address', $location->address) }}" required>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-uppercase text-muted">Hotline <span class="text-danger">*</span></label>
                            <input type="text" name="hotline" class="form-control @error('hotline') is-invalid @enderror"
                                   value="{{ old('hotline', $location->hotline) }}" required>
                            @error('hotline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Giờ mở cửa</label>
                            <input type="text" name="opening_hours" class="form-control"
                                   value="{{ old('opening_hours', $location->opening_hours) }}"
                                   placeholder="08:00 – 21:00 mỗi ngày">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-uppercase text-muted">Facebook Fanpage</label>
                            <input type="url" name="facebook_url" class="form-control"
                                   value="{{ old('facebook_url', $location->facebook_url) }}"
                                   placeholder="https://facebook.com/holomia.hanoi">
                        </div>
                    </div>
                </div>

                {{-- LANDING PAGE --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-primary text-uppercase mb-4" style="letter-spacing:1px;">
                        <i class="bi bi-images me-2"></i>Nội dung Landing Page
                    </h6>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-bold small text-uppercase text-muted">Mô tả chi nhánh</label>
                            <textarea name="description" class="form-control" rows="3"
                                      placeholder="Giới thiệu ngắn về chi nhánh...">{{ old('description', $location->description) }}</textarea>
                            <div class="form-text">Tối đa 1000 ký tự. Hiển thị trên trang landing page.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Ảnh Banner</label>
                            @if($location->banner_image)
                                <img src="{{ $location->banner_image }}" class="d-block mb-2 rounded" style="max-height:80px; object-fit:cover; max-width:100%;">
                            @endif
                            <input type="file" name="banner_image" class="form-control" accept="image/*">
                            <div class="form-text">Để trống nếu không muốn thay đổi ảnh hiện tại.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Google Maps URL</label>
                            <input type="text" name="maps_url" class="form-control"
                                   value="{{ old('maps_url', $location->maps_url) }}"
                                   placeholder="https://maps.google.com/maps?q=...">
                            <div class="form-text">Dán link Google Maps thường vào đây, hệ thống tự chuyển thành bản đồ nhúng.</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- CỘT PHẢI --}}
            <div class="col-lg-4">

                {{-- MÀU SẮC --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-primary text-uppercase mb-4" style="letter-spacing:1px;">
                        <i class="bi bi-palette me-2"></i>Màu sắc chi nhánh
                    </h6>
                    <label class="form-label fw-bold small text-uppercase text-muted">Màu chủ đạo</label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <input type="color" name="color" id="colorPicker"
                               value="{{ old('color', $location->color ?? '#2563eb') }}"
                               class="form-control form-control-color rounded-3"
                               style="width:60px; height:42px; cursor:pointer;">
                        <input type="text" id="colorHex"
                               value="{{ old('color', $location->color ?? '#2563eb') }}"
                               class="form-control fw-bold"
                               placeholder="#2563eb"
                               oninput="document.getElementById('colorPicker').value=this.value">
                    </div>
                    <div class="form-text mb-3">Màu này hiển thị trên navbar, nút bấm và các chi tiết của landing page chi nhánh.</div>

                    {{-- Preview màu --}}
                    <div class="rounded-3 p-3 text-white text-center fw-bold" id="colorPreview"
                         style="background: {{ old('color', $location->color ?? '#2563eb') }}; font-size:14px;">
                        Preview: {{ $location->name }}
                    </div>

                    {{-- Màu gợi ý --}}
                    <div class="mt-3">
                        <div class="form-text mb-2">Màu gợi ý:</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['#2563eb', '#0dcaf0', '#198754', '#dc3545', '#ff6b35', '#6f42c1', '#fd7e14', '#0d6efd'] as $c)
                                <div onclick="setColor('{{ $c }}')"
                                     style="width:28px; height:28px; border-radius:50%; background:{{ $c }}; cursor:pointer; border:2px solid #dee2e6;"
                                     title="{{ $c }}"></div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- TRẠNG THÁI --}}
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h6 class="fw-bold text-primary text-uppercase mb-4" style="letter-spacing:1px;">
                        <i class="bi bi-toggle-on me-2"></i>Trạng thái
                    </h6>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                               value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }}
                               style="width:48px; height:24px; cursor:pointer;">
                        <label class="form-check-label fw-bold ms-2" for="isActive">
                            Hiển thị landing page
                        </label>
                    </div>
                    <div class="form-text mt-2">Bật để landing page chi nhánh này công khai với khách hàng.</div>
                </div>

                {{-- LINK NHANH --}}
                @if($location->slug)
                    <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                        <h6 class="fw-bold text-primary text-uppercase mb-3" style="letter-spacing:1px;">
                            <i class="bi bi-link-45deg me-2"></i>Link chi nhánh
                        </h6>
                        <div class="bg-light rounded-3 px-3 py-2">
                            <div class="text-muted mb-1" style="font-size:11px; text-transform:uppercase;">Landing Page</div>
                            <a href="{{ $location->landingUrl() }}" target="_blank"
                               class="text-primary fw-bold text-decoration-none" style="font-size:13px; word-break:break-all;">
                                <i class="bi bi-box-arrow-up-right me-1"></i>
                                {{ $location->slug }}.{{ env('APP_DOMAIN', 'holomia.test') }}
                            </a>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- NÚT LƯU --}}
        <div class="d-flex gap-2 mt-2 mb-5">
            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow">
                <i class="bi bi-save me-1"></i> Cập nhật chi nhánh
            </button>
            @if($location->slug)
                <a href="{{ $location->landingUrl() }}" target="_blank"
                   class="btn btn-outline-info rounded-pill px-4 fw-bold">
                    <i class="bi bi-eye me-1"></i> Xem landing page
                </a>
            @endif
            <a href="{{ route('admin.locations.index') }}" class="btn btn-outline-secondary rounded-pill px-4">Huỷ</a>
        </div>

    </form>

    <script>
        document.getElementById('colorPicker').addEventListener('input', function() {
            document.getElementById('colorHex').value = this.value;
            document.getElementById('colorPreview').style.background = this.value;
        });

        function setColor(hex) {
            document.getElementById('colorPicker').value = hex;
            document.getElementById('colorHex').value = hex;
            document.getElementById('colorPreview').style.background = hex;
        }
    </script>

@endsection