@extends('layouts.admin')
@section('title', 'Quản lý đánh giá')
@section('admin_content')
    <div class="container-fluid">
        <h2 class="text-info fw-bold mb-4">Quản lý đánh giá</h2>

        {{-- Bộ lọc --}}
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <select name="rating" class="form-select">
                    <option value="">Tất cả số sao</option>
                    @for($i = 1; $i <= 5; $i++)
                        <option value="{{ $i }}" @selected(request('rating') == $i)>{{ $i }} sao</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <select name="ticket_id" class="form-select">
                    <option value="">Tất cả vé</option>
                    @foreach($tickets as $t)
                        <option value="{{ $t->id }}" @selected(request('ticket_id') == $t->id)>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-info">Lọc</button>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Người dùng</th>
                        <th>Vé</th>
                        <th>Số sao</th>
                        <th>Nội dung</th>
                        <th>Ngày</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td>{{ $review->user->name ?? 'Ẩn danh' }}</td>
                            <td>{{ $review->ticket->name ?? '—' }}</td>
                            <td>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : '' }}"></i>
                                @endfor
                            </td>
                            <td>{{ Str::limit($review->comment, 80) }}</td>
                            <td>{{ $review->created_at->format('d/m/Y') }}</td>
                            <td>
                                <form action="{{ route('admin.reviews.delete', $review->id) }}" method="POST"
                                    onsubmit="return confirm('Xóa đánh giá này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có đánh giá nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reviews->links() }}
    </div>
@endsection