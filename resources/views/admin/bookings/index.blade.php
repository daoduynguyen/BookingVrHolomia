@extends('layouts.admin')
{{-- Admin quản lý đơn hàng --}}
@php
/** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Order[] $bookings */
@endphp
@section('admin_content')
    <div class="container-fluid">
        {{-- Tiêu đề --}}
        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
            <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2">Quản lý Đơn hàng</h2>
        </div>

        {{-- THANH MENU LỌC ĐƠN HÀNG NGANG SIÊU XỊN --}}
        <div class="d-flex flex-wrap align-items-center gap-2 mb-4 bg-white p-3 rounded border border-light border-opacity-50 shadow-sm">
            <div class="text-primary fw-bold text-uppercase ls-1 me-3">
                <i class="bi bi-funnel-fill fs-5 me-1"></i> Bộ lọc:
            </div>
            
            @php $currentStatus = request('status'); @endphp

            <a href="{{ route('admin.bookings.index') }}" 
               class="btn btn-sm px-3 rounded-pill {{ empty($currentStatus) ? 'btn-primary text-dark fw-bold shadow' : 'btn-outline-secondary text-dark' }}">
               <i class="bi bi-collection"></i> Tất cả
            </a>

            <a href="{{ route('admin.bookings.index', ['status' => 'paid']) }}" 
               class="btn btn-sm px-3 rounded-pill {{ $currentStatus == 'paid' ? 'btn-success fw-bold shadow' : 'btn-outline-secondary text-dark' }}">
               ✅ Đã duyệt
            </a>

            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" 
               class="btn btn-sm px-3 rounded-pill {{ $currentStatus == 'pending' ? 'btn-warning text-dark fw-bold shadow' : 'btn-outline-secondary text-dark' }}">
               ⏳ Chờ xử lý
            </a>

            <a href="{{ route('admin.bookings.index', ['status' => 'cancelled']) }}" 
               class="btn btn-sm px-3 rounded-pill {{ $currentStatus == 'cancelled' ? 'btn-danger fw-bold shadow' : 'btn-outline-secondary text-dark' }}">
               ❌ Đã hủy
            </a>

            <a href="{{ route('admin.bookings.index', ['status' => 'refunded']) }}" 
               class="btn btn-sm px-3 rounded-pill {{ $currentStatus == 'refunded' ? 'btn-light text-dark fw-bold shadow' : 'btn-outline-secondary text-dark' }}">
               ↩️ Trả hàng
            </a>

            <a href="{{ route('admin.bookings.index', ['status' => 'expired']) }}" 
               class="btn btn-sm px-3 rounded-pill {{ $currentStatus == 'expired' ? 'btn-light text-dark fw-bold shadow' : 'btn-outline-secondary text-dark' }}">
               ⏱️ Hết hạn
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="profile-content shadow bg-white text-dark border border-light rounded">
            <div class="card-body p-0">
                <div class="table-responsive">
                    {{-- Bảng --}}
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-dark">
                            <tr class="text-secondary border-bottom border-light text-uppercase small">
                                <th width="6%" class="text-center">Mã</th>
                                <th width="22%" class="text-center">Khách hàng</th>
                                <th width="27%">Chi tiết đơn</th>
                                <th width="12%" class="text-center">Tổng tiền</th>
                                <th width="10%" class="text-center">Ngày đặt</th>
                                <th width="15%" class="text-center">Trạng thái</th>
                                <th width="8%" class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                                <tr class="border-bottom border-light">
                                    <td class="fw-bold text-primary ps-4">#{{ $booking->id }}</td>
                                    <td>
                                        <div class="fw-bold text-dark">
                                            {{ $booking->user->name ?? $booking->customer_name ?? 'Khách vãng lai' }}
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-envelope me-1"></i>
                                            {{ $booking->user->email ?? $booking->customer_email ?? '---' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($booking->details && $booking->details->count() > 0)
                                            @foreach($booking->details as $detail)
                                                <div class="mb-1 small d-flex align-items-center">
                                                    <i class="bi bi-ticket-perforated-fill text-warning me-2"></i>
                                                    <span class="text-dark me-2">{{ $detail->ticket->name ?? 'Vé không tồn tại' }}</span>
                                                    <span class="badge bg-secondary border border-light">x{{ $detail->quantity }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted fst-italic">Không có chi tiết</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-success fs-6 text-center">{{ number_format($booking->total_amount) }} đ</td>
                                    <td class="small text-muted text-center">
                                        <div>{{ $booking->created_at->format('d/m/Y') }}</div>
                                        <div>{{ $booking->created_at->format('H:i') }}</div>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.bookings.updateStatus', $booking->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm fw-bold border-0 shadow-none mx-auto
                                                @if($booking->status == 'pending') bg-warning text-dark 
                                                @elseif($booking->status == 'paid') bg-success text-white 
                                                @elseif($booking->status == 'cancelled') bg-danger text-white 
                                                @elseif($booking->status == 'refunded' || $booking->status == 'expired') bg-light text-dark 
                                                @else bg-secondary text-white @endif" onchange="this.form.submit()"
                                                style="width: 145px; cursor: pointer;">

                                                <option value="pending" {{ $booking->status == 'pending' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                                                <option value="paid" {{ $booking->status == 'paid' ? 'selected' : '' }}>✅ Duyệt</option>
                                                <option value="cancelled" {{ $booking->status == 'cancelled' ? 'selected' : '' }}>❌ Đã hủy</option>
                                                <option value="refunded" {{ $booking->status == 'refunded' ? 'selected' : '' }}>↩️ Hoàn trả</option>
                                                <option value="expired" {{ $booking->status == 'expired' ? 'selected' : '' }}>⏱️ Hết hạn</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('admin.bookings.delete', $booking->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa đơn này vĩnh viễn?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="Xóa đơn hàng">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                            <p>Chưa có đơn hàng nào trong hệ thống.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Phân trang (Nếu có) --}}
                <div class="d-flex justify-content-center w-100 mt-4">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection