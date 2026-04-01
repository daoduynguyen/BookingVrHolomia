@extends('layouts.admin')

@section('title', 'Nhật ký hoạt động Admin')

@section('admin_content')
    <div class="container-fluid">

        {{-- Tiêu đề --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0">Nhật ký hoạt động</h2>
            <span class="badge bg-secondary ms-3">Super Admin only</span>
        </div>

        {{-- Bảng log --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:50px">#</th>
                                <th>Thời gian</th>
                                <th>Admin</th>
                                <th>Hành động</th>
                                <th>Đối tượng</th>
                                <th>Chi tiết</th>
                                <th>IP</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td class="text-muted small">{{ $log->id }}</td>
                                    <td class="small text-nowrap">
                                        <span title="{{ $log->created_at }}">
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($log->admin)
                                            <span class="fw-semibold">{{ $log->admin->name }}</span>
                                            <br><small class="text-muted">{{ $log->admin->email }}</small>
                                        @else
                                            <span class="text-muted fst-italic">Không rõ</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $actionColors = [
                                                'create' => 'success',
                                                'update' => 'warning',
                                                'delete' => 'danger',
                                                'login' => 'info',
                                                'logout' => 'secondary',
                                            ];
                                            $actionKey = strtolower(explode(' ', $log->action)[0] ?? '');
                                            $color = $actionColors[$actionKey] ?? 'primary';
                                        @endphp
                                        <span class="badge bg-{{ $color }} text-uppercase" style="font-size:11px;">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        @if($log->model_type)
                                            <span class="text-primary fw-semibold">{{ $log->model_type }}</span>
                                            @if($log->model_id)
                                                <span class="text-muted"> #{{ $log->model_id }}</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="small" style="max-width:260px;">
                                        @if($log->details)
                                            <code class="text-dark" style="font-size:11px;word-break:break-all;">
                                                    {{ json_encode($log->details, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}
                                                </code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted text-nowrap">{{ $log->ip_address ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-journal-x fs-2 d-block mb-2"></i>
                                        Chưa có nhật ký nào.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Phân trang --}}
            @if($logs->hasPages())
                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center px-4 py-3">
                    <small class="text-muted">
                        Hiển thị {{ $logs->firstItem() }}–{{ $logs->lastItem() }} / {{ $logs->total() }} bản ghi
                    </small>
                    {{ $logs->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection