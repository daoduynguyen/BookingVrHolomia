@extends('layouts.admin')
@section('title', 'Sửa Người dùng')
@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary fw-bold text-uppercase"><i class="bi bi-person-gear"></i> Sửa tài khoản</h2>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card bg-white border-light p-4 shadow-sm rounded-4">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            @php
                $selectedPermissions = old('admin_permissions', $user->admin_permissions ?? \App\Models\User::defaultAdminPermissions());
                if (!is_array($selectedPermissions)) {
                    $selectedPermissions = [];
                }
            @endphp
            <div class="row g-3">

                {{-- Họ tên --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                        class="form-control bg-light border-light" required>
                </div>

                {{-- Email --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="form-control bg-light border-light" required>
                </div>

                {{-- Số điện thoại --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="form-control bg-light border-light" placeholder="Không bắt buộc">
                </div>

                {{-- Mật khẩu mới --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">
                        Mật khẩu mới <span class="text-muted fw-normal">(để trống nếu không đổi)</span>
                    </label>
                    <input type="password" name="password" class="form-control bg-light border-light"
                        placeholder="Nhập mật khẩu mới...">
                </div>

                {{-- Vai trò --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold small text-uppercase text-muted">Vai trò</label>
                    <select name="role" id="role_select_edit" class="form-select bg-light border-light" required>
                        <option value="customer"     {{ old('role', $user->role) == 'customer'     ? 'selected' : '' }}>👤 Thành viên</option>
                        <option value="branch_admin" {{ old('role', $user->role) == 'branch_admin' ? 'selected' : '' }}>🏢 Quản lý cơ sở</option>
                        <option value="super_admin"  {{ old('role', $user->role) == 'super_admin'  ? 'selected' : '' }}>👑 Super admin</option>
                        <option value="staff" {{ old('role', $user->role) == 'staff' ? 'selected' : '' }}>🧑‍💼 Nhân viên</option>
                    </select>
                </div>

                <div class="col-12" id="permission_box_edit"
                    style="display: {{ in_array(old('role', $user->role), ['branch_admin', 'staff']) ? 'block' : 'none' }};">
                    <div class="p-3 border rounded-3 bg-light">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div>
                                <div class="fw-bold text-dark">Quyền truy cập trang admin</div>
                                <div class="small text-muted">Chỉ áp dụng cho admin chi nhánh / nhân viên. Super admin luôn có toàn quyền.</div>
                            </div>
                        </div>
                        <div class="row g-2">
                            @foreach($permissionOptions as $permissionKey => $permissionLabel)
                                <div class="col-md-4 col-sm-6">
                                    <label class="form-check border rounded-3 px-3 py-2 bg-white mb-0 w-100">
                                        <input class="form-check-input me-2" type="checkbox" name="admin_permissions[]" value="{{ $permissionKey }}"
                                            {{ in_array($permissionKey, $selectedPermissions, true) ? 'checked' : '' }}>
                                        <span class="small fw-semibold">{{ $permissionLabel }}</span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="small text-muted mt-2">Nếu không chọn gì, tài khoản sẽ không vào được các khu vực đã khóa.</div>
                    </div>
                </div>

                {{-- Cơ sở (chỉ hiện khi là branch_admin) --}}
                <div class="col-md-6" id="location_box_edit"
                    style="display: {{ in_array(old('role', $user->role), ['branch_admin', 'staff']) ? 'block' : 'none' }};">
                    <label class="form-label fw-bold small text-uppercase text-muted">Trực thuộc cơ sở</label>
                    <select name="location_id" class="form-select bg-light border-light">
                        <option value="">-- Chọn cơ sở --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}"
                                {{ old('location_id', $user->location_id) == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Thông tin thêm --}}
                <div class="col-12">
                    <hr class="my-2">
                    <p class="text-muted small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Tài khoản tạo ngày: <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong>
                        &nbsp;|&nbsp; Số dư ví: <strong>{{ number_format($user->balance ?? 0) }}đ</strong>
                        &nbsp;|&nbsp; Điểm: <strong>{{ $user->points ?? 0 }}</strong>
                        &nbsp;|&nbsp; Hạng: <strong>{{ $user->tier ?? 'Thành viên' }}</strong>
                    </p>
                </div>

            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary fw-bold px-5 rounded-pill">
                    <i class="bi bi-floppy me-1"></i> Lưu thay đổi
                </button>
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary rounded-pill px-4">Hủy</a>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('role_select_edit').addEventListener('change', function () {
        document.getElementById('location_box_edit').style.display =
            (['branch_admin', 'staff'].includes(this.value)) ? 'block' : 'none';

        document.getElementById('permission_box_edit').style.display =
            (['branch_admin', 'staff'].includes(this.value)) ? 'block' : 'none';
    });
</script>
@endsection