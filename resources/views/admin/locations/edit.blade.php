@extends('layouts.admin')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Chỉnh Sửa Cơ Sở</h2>
    <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary d-flex align-items-center gap-2">
        <i class="bi bi-arrow-left"></i> Quay Lại
    </a>
</div>

<div class="card border-0 shadow-sm rounded-4 p-4">
    <form action="{{ route('admin.locations.update', $location->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label fw-bold">Tên Cơ Sở</label>
            <input type="text" name="name" class="form-control" value="{{ $location->name }}" required placeholder="Nhập tên cơ sở...">
        </div>
        
        <div class="mb-3">
            <label class="form-label fw-bold">Địa Chỉ</label>
            <input type="text" name="address" class="form-control" value="{{ $location->address }}" required placeholder="Nhập địa chỉ cơ sở...">
        </div>
        
        <div class="mb-4">
            <label class="form-label fw-bold">Hotline</label>
            <input type="text" name="hotline" class="form-control" value="{{ $location->hotline }}" required placeholder="Nhập đường dây nóng...">
        </div>
        
        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="bi bi-save"></i> Cập Nhật Cơ Sở
        </button>
    </form>
</div>
@endsection
