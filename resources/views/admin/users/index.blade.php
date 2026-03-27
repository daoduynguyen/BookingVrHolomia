@extends('layouts.admin')
@section('title', 'Quản lý Người dùng')
@section('admin_content')
<div class="container-fluid">

    {{-- TIÊU ĐỀ --}}
    <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between mb-4 gap-3">
        <div class="d-flex align-items-center flex-shrink-0">
            <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2 text-nowrap">Quản lý người dùng</h2>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary fw-bold shadow text-nowrap rounded-pill px-4 py-2">
            <i class="bi bi-plus-lg"></i> THÊM TÀI KHOẢN
        </a>
    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 rounded-3" role="alert">
            <i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- BẢNG --}}
    <div class="profile-content shadow-sm bg-white rounded-4 border border-light overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="text-secondary text-uppercase small fw-bold border-bottom border-light bg-light">
                        <th class="ps-4" width="6%">ID</th>
                        <th width="16%">Họ tên</th>
                        <th width="22%">Email</th>
                        <th width="18%">Vai trò</th>
                        <th width="14%">Ngày tham gia</th>
                        <th width="8%" class="text-center">Trạng thái</th>
                        <th class="text-end pe-4" width="16%">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr class="border-bottom border-light {{ $user->is_banned ? 'opacity-60' : '' }}"
                        style="{{ $user->is_banned ? 'background:#fff8f8;' : '' }}">

                        {{-- ID --}}
                        <td class="ps-4 fw-bold text-primary">#{{ $user->id }}</td>

                        {{-- Tên --}}
                        <td>
                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                            @if($user->phone)
                                <div class="small text-muted">{{ $user->phone }}</div>
                            @endif
                        </td>

                        {{-- Email --}}
                        <td class="text-muted small">{{ $user->email }}</td>

                        {{-- Vai trò --}}
                        <td>
                            @if($user->role === 'super_admin')
                                <span class="badge bg-danger rounded-pill px-3 py-2">👑 Sếp tổng</span>
                            @elseif($user->role === 'admin')
                                <span class="badge bg-primary rounded-pill px-3 py-2">🛡️ Admin</span>
                            @elseif($user->role === 'branch_admin')
                                <div>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">🏢 Quản lý cơ sở</span>
                                    @if($user->location)
                                        <div class="small text-muted mt-1"><i class="bi bi-geo-alt-fill me-1"></i>{{ $user->location->name }}</div>
                                    @endif
                                </div>
                            @else
                                <span class="badge bg-secondary rounded-pill px-3 py-2">👤 Thành viên</span>
                            @endif
                        </td>

                        {{-- Ngày tham gia --}}
                        <td class="small text-muted">{{ $user->created_at->format('d/m/Y') }}</td>

                        {{-- Trạng thái --}}
                        <td class="text-center">
                            @if($user->is_banned)
                                <span class="badge bg-danger rounded-pill px-2 py-1" style="font-size:0.75rem;">Đã chặn</span>
                            @else
                                <span class="badge bg-success rounded-pill px-2 py-1" style="font-size:0.75rem;">Hoạt động</span>
                            @endif
                        </td>

                        {{-- Thao tác --}}
                        <td class="text-end pe-4">
                            {{-- Nút Chặn / Mở khóa --}}
                            <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @if($user->is_banned)
                                    <button type="submit" class="btn btn-sm btn-outline-success me-1" title="Mở khóa tài khoản">
                                        <i class="bi bi-unlock"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-warning me-1" title="Chặn tài khoản"
                                        onclick="return confirm('Chặn tài khoản {{ $user->name }}?')">
                                        <i class="bi bi-slash-circle"></i>
                                    </button>
                                @endif
                            </form>

                            {{-- Phân quyền --}}
                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                data-bs-toggle="modal" data-bs-target="#roleModal{{ $user->id }}" title="Phân quyền">
                                <i class="bi bi-shield-lock"></i>
                            </button>

                            {{-- Xóa --}}
                            <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" 
                                class="d-inline form-delete" data-label="{{ $user->name }}">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->isEmpty())
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people fs-1 d-block mb-3 opacity-50"></i>
                <p>Chưa có người dùng nào.</p>
            </div>
        @endif
    </div>
</div>

{{-- MODAL PHÂN QUYỀN --}}
@foreach($users as $user)
<div class="modal fade" id="roleModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white border-light shadow-lg">
            <div class="modal-header border-bottom border-light">
                <h5 class="modal-title text-primary fw-bold"><i class="bi bi-person-gear me-2"></i>Phân quyền tài khoản</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3 p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Tài khoản</div>
                        <div class="fw-bold text-dark">{{ $user->name }}</div>
                        <div class="small text-muted">{{ $user->email }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase text-dark">Vai trò hệ thống</label>
                        <select name="role" class="form-select border-light role-select" data-userid="{{ $user->id }}" required>
                            <option value="customer"     {{ $user->role == 'customer'     ? 'selected' : '' }}>👤 Thành viên</option>
                            <option value="branch_admin" {{ $user->role == 'branch_admin' ? 'selected' : '' }}>🏢 Quản lý cơ sở</option>
                            <option value="super_admin"  {{ $user->role == 'super_admin'  ? 'selected' : '' }}>👑 Sếp tổng</option>
                        </select>
                    </div>

                    <div class="mb-3 location-group" id="locationGroup{{ $user->id }}"
                         style="display: {{ $user->role == 'branch_admin' ? 'block' : 'none' }};">
                        <label class="form-label fw-bold small text-uppercase text-dark">Trực thuộc cơ sở</label>
                        <select name="location_id" class="form-select border-light">
                            <option value="">-- Chọn cơ sở --</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc->id }}" {{ $user->location_id == $loc->id ? 'selected' : '' }}>
                                    {{ $loc->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top border-light">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary fw-bold rounded-pill px-4">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.role-select').forEach(select => {
            select.addEventListener('change', function () {
                const group = document.getElementById('locationGroup' + this.dataset.userid);
                if (this.value === 'branch_admin') {
                    group.style.display = 'block';
                    group.querySelector('select').setAttribute('required', 'required');
                } else {
                    group.style.display = 'none';
                    group.querySelector('select').removeAttribute('required');
                }
            });
        });
    });
</script>
@endsection