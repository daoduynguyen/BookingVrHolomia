@extends('layouts.admin')

@section('title', 'Quản lý Cơ sở')

@section('admin_content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Quản lý Cơ sở</h2>
    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
        <i class="bi bi-plus-lg"></i> Thêm Cơ Sở
    </a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-secondary">
                <tr>
                    <th scope="col" class="ps-4">ID</th>
                    <th scope="col">Tên Cơ Sở</th>
                    <th scope="col">Địa Chỉ</th>
                    <th scope="col">Hotline</th>
                    <th scope="col" class="text-end pe-4">Hành Động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($locations as $location)
                <tr>
                    <td class="ps-4 fw-medium">#{{ $location->id }}</td>
                    <td><span class="fw-bold text-dark">{{ $location->name }}</span></td>
                    <td>{{ $location->address }}</td>
                    <td>{{ $location->hotline }}</td>
                    <td class="text-end pe-4">
                        <div class="d-flex gap-2 justify-content-end">
                            <a href="{{ route('admin.locations.edit', $location->id) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('admin.locations.destroy', $location->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xoá cơ sở này không?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Xoá">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $locations->links('pagination::bootstrap-5') }}
</div>
@endsection
