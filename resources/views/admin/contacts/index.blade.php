@extends('layouts.admin')

@section('title', 'Quản lý Liên hệ')

@section('admin_content')
<div class="container-fluid">
    {{-- 1. Tiêu đề chuẩn Dark Mode --}}
    <div class="d-flex align-items-center mb-4">
        <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
        <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2">Quản lý Liên hệ</h2>
    </div>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 2. Khung chứa bảng (Nền tối) --}}
    <div class="profile-content shadow bg-white text-dark">
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- 3. Bảng (Table Dark) --}}
                <table class="table table-hover align-middle mb-0 border-light">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th width="5%">STT</th>
                            <th width="20%">Khách hàng</th>
                            <th width="20%">Email / SĐT</th>
                            <th width="30%">Nội dung</th>
                            <th width="10%">Ngày gửi</th>
                            <th width="15%" class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contacts as $key => $contact)
                        <tr>
                            {{-- STT --}}
                            <td class="text-center text-dark">{{ $key + 1 }}</td>
                            
                            {{-- Tên khách --}}
                            <td class="fw-bold text-primary">
                                {{ $contact->name ?? 'Khách vãng lai' }}
                            </td>

                            {{-- Email/SĐT --}}
                            <td>
                                <div><i class="bi bi-envelope me-2"></i>{{ $contact->email }}</div>
                                @if($contact->phone)
                                    <div class="small text-dark mt-1"><i class="bi bi-telephone me-2"></i>{{ $contact->phone }}</div>
                                @endif
                            </td>

                            {{-- Nội dung --}}
                            <td>
                                <div class="text-muted fst-italic">
                                    "{{ Str::limit($contact->message, 80) }}"
                                </div>
                            </td>

                            {{-- Ngày gửi --}}
                            <td class="small text-dark">
                                {{ $contact->created_at->format('d/m/Y') }}<br>
                                {{ $contact->created_at->format('H:i') }}
                            </td>

                            {{-- Thao tác --}}
                            <td class="text-end">
                                {{-- 1. NÚT PHẢN HỒI --}}
                                <button type="button" class="btn btn-sm btn-outline-primary me-1 btn-reply" 
                                        data-id="{{ $contact->id }}" 
                                        data-email="{{ $contact->email }}"
                                        data-name="{{ $contact->name }}"
                                        title="Trả lời khách hàng">
                                    <i class="bi bi-reply-fill"></i>
                                </button>

                                {{-- 2. NÚT XÓA --}}
                                <form action="{{ route('admin.contacts.delete', $contact->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa tin nhắn này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        {{-- Trạng thái trống --}}
                        <tr>
                            <td colspan="6" class="text-center py-5 text-dark">
                                <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                Hiện chưa có tin nhắn liên hệ nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- --- MODAL PHẢN HỒI (POPUP) --- --}}
{{-- Phần này ẩn đi, chỉ hiện khi bấm nút --}}
<div class="modal fade" id="replyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-white text-dark border-light shadow-lg">
            <form id="replyForm" method="POST" action="">
                @csrf
                <div class="modal-header border-light">
                    <h5 class="modal-title text-primary fw-bold">
                        <i class="bi bi-send-fill me-2"></i>Gửi phản hồi
                    </h5>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    {{-- Thông tin người nhận --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small text-uppercase fw-bold">Người nhận</label>
                        <input type="text" class="form-control bg-secondary bg-opacity-10 text-dark border-0" id="replyToInfo" readonly>
                    </div>

                    {{-- Nhập nội dung --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small text-uppercase fw-bold">Nội dung trả lời <span class="text-danger">*</span></label>
                        <textarea name="reply_content" class="form-control bg-light text-dark border-light" rows="6" required placeholder="Nhập nội dung phản hồi của bạn tại đây..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary fw-bold text-dark px-4">
                        <i class="bi bi-cursor-fill me-1"></i> Gửi Email
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- --- JAVASCRIPT XỬ LÝ --- --}}
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Lấy các phần tử cần thiết
        const replyButtons = document.querySelectorAll('.btn-reply');
        const replyModalElement = document.getElementById('replyModal');
        
        // Kiểm tra nếu Modal tồn tại (tránh lỗi JS)
        if(replyModalElement) {
            const replyModal = new bootstrap.Modal(replyModalElement);
            const replyForm = document.getElementById('replyForm');
            const replyToInfo = document.getElementById('replyToInfo');

            // Gán sự kiện click cho từng nút Phản hồi
            replyButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const email = this.getAttribute('data-email');
                    const name = this.getAttribute('data-name');

                    // 1. Cập nhật Action của Form để gửi đúng Route (admin/contacts/reply/{id})
                    replyForm.action = `/admin/contacts/reply/${id}`;
                    
                    // 2. Điền thông tin vào ô Người nhận (để Admin biết đang gửi cho ai)
                    replyToInfo.value = `${name} (${email})`;

                    // 3. Hiển thị Modal
                    replyModal.show();
                });
            });
        }
    });
</script>

@endsection