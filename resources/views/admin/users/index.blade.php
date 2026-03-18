@extends('layouts.admin')

@section('admin_content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="text-info fw-bold text-uppercase border-start border-info border-4 ps-3">Quản lý người dùng</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-info fw-bold">
        <i class="bi bi-plus-lg"></i> THÊM TÀI KHOẢN
    </a>
</div>
    <div class="profile-content shadow-lg">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle border-secondary">
                <thead>
                    <tr class="text-secondary border-bottom border-secondary">
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tham gia</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-secondary">#{{ $user->id }}</td>
                        <td class="fw-bold">{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if($user->role == 'admin')
                                <span class="badge bg-info text-dark">Admin</span>
                            @else
                                <span class="badge bg-secondary">Thành viên</span>
                            @endif
                        </td>
                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-warning me-2" title="Chặn tài khoản">
                                <i class="bi bi-slash-circle"></i>
                            </button>

                            <button type="button" class="btn btn-outline-info btn-sm me-1" data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}">
                               <i class="bi bi-shield-lock"></i> Phân quyền
                            </button>

                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Nguyên có chắc chắn muốn xóa người dùng này không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash3"></i> XÓA
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- VÒNG LẶP TẠO POPUP CHO TỪNG USER --}}
@foreach($users as $user)
<div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark border-info">
            <div class="modal-header border-bottom border-secondary">
                <h5 class="modal-title text-info"><i class="bi bi-person-gear me-2"></i>Phân quyền tài khoản</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="text-white-50">Tài khoản:</label>
                        <div class="fw-bold text-white fs-5">{{ $user->email }}</div>
                    </div>

                    {{-- Chọn Vai trò --}}
                    <div class="mb-3">
                        <label class="form-label text-warning fw-bold">Vai trò hệ thống</label>
                        <select name="role" class="form-select bg-black text-white border-secondary role-select" data-userid="{{ $user->id }}" required>
                            <option value="customer" {{ $user->role == 'customer' ? 'selected' : '' }}>Khách hàng (Thành viên)</option>
                            <option value="branch_admin" {{ $user->role == 'branch_admin' ? 'selected' : '' }}>Quản lý Cơ sở (Admin chi nhánh)</option>
                            <option value="super_admin" {{ $user->role == 'super_admin' ? 'selected' : '' }}>Sếp tổng (Super Admin)</option>
                        </select>
                    </div>

                    {{-- Chọn Cơ sở (Mặc định ẩn, chỉ hiện khi chọn Quản lý Cơ sở) --}}
                    <div class="mb-3 location-group" id="locationGroup{{ $user->id }}" style="display: {{ $user->role == 'branch_admin' ? 'block' : 'none' }};">
                        <label class="form-label text-info fw-bold">Trực thuộc Cơ sở</label>
                        <select name="location_id" class="form-select bg-black text-white border-secondary">
                            <option value="">-- Chọn cơ sở quản lý --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $user->location_id == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-info fw-bold">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- SCRIPT HIỆU ỨNG TỰ ĐỘNG BẬT/TẮT Ô CHỌN CƠ SỞ --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelects = document.querySelectorAll('.role-select');
        roleSelects.forEach(select => {
            select.addEventListener('change', function() {
                const userId = this.getAttribute('data-userid');
                const locationGroup = document.getElementById('locationGroup' + userId);
                
                // Nếu chọn "branch_admin" thì mới hiện ô chọn cơ sở
                if (this.value === 'branch_admin') {
                    locationGroup.style.display = 'block';
                    locationGroup.querySelector('select').setAttribute('required', 'required');
                } else {
                    locationGroup.style.display = 'none';
                    locationGroup.querySelector('select').removeAttribute('required');
                }
            });
        });
    });
</script>

@endsection