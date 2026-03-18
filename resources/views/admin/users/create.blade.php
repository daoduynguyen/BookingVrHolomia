@extends('layouts.admin')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-info fw-bold text-uppercase"><i class="bi bi-person-plus-fill"></i> Thêm tài khoản mới</h2>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card bg-dark border-secondary p-4 shadow">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="text-white-50">Họ và tên</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control bg-black text-white border-secondary" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-white-50">Email (Tên đăng nhập)</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control bg-black text-white border-secondary" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-white-50">Mật khẩu khởi tạo</label>
                    <input type="password" name="password" class="form-control bg-black text-white border-secondary" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="text-warning fw-bold">Vai trò</label>
                    <select name="role" id="role_select" class="form-select bg-black text-white border-secondary" required>
                        <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Khách hàng</option>
                        <option value="branch_admin" {{ old('role') == 'branch_admin' ? 'selected' : '' }}>Quản lý Cơ sở</option>
                        <option value="super_admin" {{ old('role') == 'super_admin' ? 'selected' : '' }}>Sếp tổng (Super Admin)</option>
                    </select>
                </div>

                <div class="col-md-12 mb-4" id="location_box" style="display: {{ old('role') == 'branch_admin' ? 'block' : 'none' }};">
                    <label class="text-info fw-bold">Gắn vào cơ sở quản lý</label>
                    <select name="location_id" class="form-select bg-black text-white border-secondary">
                        <option value="">-- Chọn cơ sở --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>
                                {{ $loc->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-info px-5 fw-bold text-uppercase">Xác nhận thêm</button>
        </form>
    </div>
</div>

<script>
    document.getElementById('role_select').addEventListener('change', function() {
        document.getElementById('location_box').style.display = (this.value === 'branch_admin') ? 'block' : 'none';
    });
</script>
@endsection