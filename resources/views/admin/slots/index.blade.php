@extends('layouts.admin')

@section('admin_content')
    <style>
        /* Style đặc trị cho các ô Khung giờ để hiển thị dạng lưới đẹp mắt */
        .slot-box {
            border: 1px solid #444;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        .slot-box:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 10px 20px rgba(0,0,0,0.5); 
            border-color: #0dcaf0;
        }

        /* Màu sắc theo Tone Admin Dark của Holomia */
        .slot-empty { background-color: #1a1d20; color: #ffffff; border-color: #333; }
        .slot-booking { background-color: #3d3511; color: #ffc107; border-color: #ffc107; }
        .slot-full { background-color: #411418; color: #dc3545; border-color: #dc3545; }

        .time-label { font-size: 1.3rem; font-weight: bold; display: block; margin-bottom: 5px; }
        .capacity-label { font-size: 0.85rem; opacity: 0.8; }
        .letter-spacing-2 { letter-spacing: 2px; }

        /* CSS MỚI: Ẩn hiện nút Sửa/Xóa khi hover */
        .slot-item-container:hover .slot-action-overlay {
            display: block !important;
        }
        .slot-action-overlay .btn:hover {
            transform: scale(1.1);
        }
    </style>

    <div class="container-fluid">
        {{-- THÔNG BÁO THÀNH CÔNG/LỖI --}}
        @if(session('success'))
            <div class="alert alert-success bg-success text-white border-0">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger bg-danger text-white border-0">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger bg-danger text-white border-0">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- 1. TIÊU ĐỀ ĐỒNG BỘ VỚI DASHBOARD (ĐÃ FIX LỖI ÉP KHUNG) --}}
        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between mb-5 gap-3">
            
            {{-- Bên Trái: Tiêu đề --}}
            <div class="d-flex align-items-center flex-shrink-0">
                <div class="bg-info me-3" style="width: 50px; height: 3px;"></div>
                <h2 class="text-info fw-bold text-uppercase mb-0 letter-spacing-2 text-nowrap">Quản lý lịch đặt vé</h2>
            </div>

            {{-- Bên Phải: Bộ lọc và Nút Tạo --}}
            <div class="d-flex align-items-center gap-3 flex-wrap flex-md-nowrap">
                
                {{-- Form chọn ngày xem lịch --}}
                <form action="{{ route('admin.slots.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0 border-end border-secondary pe-3">
                    <label class="text-white-50 fw-bold text-nowrap mb-0">Xem ngày:</label>
                    <input type="date" name="date" class="form-control bg-dark text-white border-secondary" 
                           value="{{ $selectedDate }}" onchange="this.form.submit()" style="width: auto;">
                </form>

                {{-- NÚT TẠO LỊCH TẤT CẢ TRÒ CHƠI --}}
                <form action="{{ route('admin.slots.generate') }}" method="POST" class="m-0" onsubmit="return confirm('Bạn muốn khởi tạo 14 ca cho TẤT CẢ trò chơi trong ngày {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}?');">
                    @csrf
                    <input type="hidden" name="date" value="{{ $selectedDate }}">
                    {{-- NÚT TẠO LỊCH NHIỀU NGÀY --}}
                <button type="button" class="btn btn-warning fw-bold text-dark shadow-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                    <i class="bi bi-calendar-range"></i> TẠO LỊCH HÀNG LOẠT
                </button>
                </form>
            </div>
        </div>

        {{-- 2. CHÚ THÍCH TRẠNG THÁI --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-dark border-secondary shadow-sm">
                    <div class="card-body d-flex justify-content-center gap-4 py-2">
                        <small class="text-white"><i class="bi bi-circle-fill me-1"></i> Trống (Sẵn sàng)</small>
                        <small class="text-warning"><i class="bi bi-circle-fill text-warning me-1"></i> Đang nhận (Còn chỗ)</small>
                        <small class="text-danger"><i class="bi bi-circle-fill text-danger me-1"></i> Đã đầy (Hết chỗ)</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. DANH SÁCH LỊCH THEO TỪNG TRÒ CHƠI --}}
        @foreach($tickets as $ticket)
            <div class="card bg-dark border-secondary shadow mb-5">
                <div class="card-header bg-transparent border-secondary py-3 d-flex justify-content-between align-items-center">
                    <h5 class="text-info fw-bold mb-0">
                        <i class="bi bi-controller me-2"></i> {{ $ticket->name }}
                    </h5>
                    
                    <div class="d-flex gap-2">
                        {{-- NÚT THÊM CA LẺ --}}
                        <button class="btn btn-sm btn-outline-success text-nowrap" data-bs-toggle="modal" data-bs-target="#addSlotModal-{{ $ticket->id }}">
                            <i class="bi bi-plus-lg"></i> Thêm ca lẻ
                        </button>
                        
                        {{-- NÚT TẠO LỊCH TỰ ĐỘNG CHO TỪNG GAME --}}
                        {{-- NÚT TẠO LỊCH TỰ ĐỘNG CHO TỪNG GAME --}}
                        <form action="{{ route('admin.slots.generate') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            {{-- Truyền cùng 1 ngày vào cả start và end để tạo cho 1 ngày --}}
                            <input type="hidden" name="start_date" value="{{ $selectedDate }}">
                            <input type="hidden" name="end_date" value="{{ $selectedDate }}">
                            <button type="submit" class="btn btn-outline-info btn-sm rounded-pill px-3 text-nowrap">
                                <i class="bi bi-magic"></i> Tạo tự động (14 ca)
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    @php 
                        $ticketSlots = isset($slots[$ticket->id]) ? $slots[$ticket->id] : []; 
                    @endphp

                    @if(count($ticketSlots) > 0)
                        <div class="row g-3">
                            @foreach($ticketSlots as $slot)
                                @php
                                    $statusClass = 'slot-empty';
                                    if ($slot->booked_count > 0 && $slot->booked_count < $slot->capacity) {
                                        $statusClass = 'slot-booking';
                                    } elseif ($slot->booked_count >= $slot->capacity || $slot->status == 'full') {
                                        $statusClass = 'slot-full';
                                    }
                                @endphp
                            
                               {{-- 🌟 CỤC GIỜ ĐÃ ĐƯỢC NÂNG CẤP (HIỆN NÚT SỬA/XÓA) --}}
<div class="col-6 col-md-3 col-lg-2 position-relative slot-item-container">
    {{-- THÊM onclick VÀO ĐÂY --}}
    <div class="slot-box {{ $statusClass }} h-100 d-flex flex-column justify-content-center" 
     style="cursor: pointer; position: relative; z-index: 1;" 
     onclick="showCustomerList({{ $slot->id }})">
        
        <span class="time-label">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</span>
        <span class="capacity-label">
            <i class="bi bi-people-fill"></i> {{ $slot->booked_count }} / {{ $slot->capacity }}
        </span>

        @if($statusClass == 'slot-full')
            <div class="small mt-1 fw-bold">FULL</div>
        @endif

        {{-- Nút Sửa/Xóa (Cần chặn sự kiện nổi bọt để không trigger showCustomerList) --}}
        <div class="slot-action-overlay position-absolute top-0 end-0 p-1 mt-1 me-1" 
             style="display: none; background: rgba(0,0,0,0.8); border-radius: 6px; z-index: 10;"
             onclick="event.stopPropagation();"> {{-- THÊM DÒNG NÀY ĐỂ CHẶN CLICK NHẦM --}}
            
            {{-- Nút Sửa --}}
            <button type="button" class="btn btn-sm text-info p-1" data-bs-toggle="modal" data-bs-target="#editSlotModal-{{ $slot->id }}">
                <i class="bi bi-pencil-square fs-6"></i>
            </button>
            
            {{-- Nút Xóa --}}
            <form action="{{ route('admin.slots.destroy', $slot->id) }}" method="POST" class="d-inline" 
                  onsubmit="event.stopPropagation(); return confirm('Xác nhận xóa ca này?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm text-danger p-1">
                    <i class="bi bi-trash fs-6"></i>
                </button>
            </form>
        </div>
    </div>
</div>
                            @endforeach
                        </div>

                        {{-- ⚠️ CHUYỂN TOÀN BỘ MODAL SỬA RA NGOÀI THẺ DIV ROW ĐỂ TRÁNH LỖI MẤT CA --}}
                        @foreach($ticketSlots as $slot)
                            {{-- 🌟 MODAL SỬA CA CHƠI NÀY --}}
                            <div class="modal fade" id="editSlotModal-{{ $slot->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark border-secondary">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title text-info"><i class="bi bi-pencil-square"></i> Sửa ca chơi</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.slots.update', $slot->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body text-white">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label text-secondary small">Giờ bắt đầu</label>
                                                        {{-- Thay input time bằng input text dùng Flatpickr --}}
                                                        <input type="text" name="start_time" class="form-control bg-black text-white border-secondary time-picker" required value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label text-secondary small">Giờ kết thúc</label>
                                                        <input type="text" name="end_time" class="form-control bg-black text-white border-secondary time-picker" required value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label text-secondary small">Sức chứa tối đa</label>
                                                        <input type="number" name="capacity" class="form-control bg-black text-white border-secondary" required min="{{ $slot->booked_count > 0 ? $slot->booked_count : 1 }}" value="{{ $slot->capacity }}">
                                                        @if($slot->booked_count > 0)
                                                            <small class="text-warning">Đã có {{ $slot->booked_count }} người đặt. Sức chứa không được nhỏ hơn số này.</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="submit" class="btn btn-info fw-bold">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    @else
                        {{-- TRƯỜNG HỢP CHƯA CÓ DỮ LIỆU --}}
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-white-50 display-4 opacity-25"></i>
                            <p class="text-white-50 mt-3">Chưa có khung giờ nào cho ngày {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
                            <form action="{{ route('admin.slots.generate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <input type="hidden" name="date" value="{{ $selectedDate }}">
                                <button type="submit" class="btn btn-info rounded-pill px-4 py-2 fw-bold">
                                    <i class="bi bi-plus-circle-fill me-2"></i> KHỞI TẠO 14 CA MẶC ĐỊNH
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 🌟 MODAL THÊM CA LẺ CHO TRÒ CHƠI NÀY --}}
            <div class="modal fade" id="addSlotModal-{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content bg-dark border-secondary">
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title text-success"><i class="bi bi-plus-circle"></i> Thêm ca lẻ - {{ $ticket->name }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.slots.store-single') }}" method="POST">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <input type="hidden" name="date" value="{{ $selectedDate }}">
                            <div class="modal-body text-white">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary small">Giờ bắt đầu</label>
                                        {{-- Thay input time bằng input text dùng Flatpickr --}}
                                        <input type="text" name="start_time" class="form-control bg-black text-white border-secondary time-picker" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-secondary small">Giờ kết thúc</label>
                                        <input type="text" name="end_time" class="form-control bg-black text-white border-secondary time-picker" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-secondary small">Sức chứa tối đa</label>
                                        <input type="number" name="capacity" class="form-control bg-black text-white border-secondary" required min="1" value="15">
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer border-secondary">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                <button type="submit" class="btn btn-success fw-bold">Tạo ca</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            {{-- 🌟 MODAL TẠO LỊCH HÀNG LOẠT (NHIỀU NGÀY) --}}
    <div class="modal fade" id="bulkGenerateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary shadow-lg">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning fw-bold">
                        <i class="bi bi-calendar-range me-2"></i> Tạo lịch hàng loạt
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.slots.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body text-white">
                        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-info small mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Hệ thống sẽ tự động tạo 14 ca chơi (08:00 - 21:00) cho <b>TẤT CẢ TRÒ CHƠI</b> trong khoảng thời gian bạn chọn. Ca nào đã có sẽ tự động bỏ qua để tránh trùng lặp.
                        </div>
                        
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Từ ngày</label>
                                <input type="date" name="start_date" class="form-control bg-black text-white border-secondary" required value="{{ $selectedDate }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Đến ngày</label>
                                <input type="date" name="end_date" class="form-control bg-black text-white border-secondary" required value="{{ \Carbon\Carbon::parse($selectedDate)->addDays(2)->format('Y-m-d') }}">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-4">
                            <i class="bi bi-lightning-charge-fill me-1"></i> BẮT ĐẦU TẠO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
        @endforeach
    </div>

    {{-- 1. Chèn CSS của Flatpickr (Giao diện Dark Mode) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">

    {{-- 2. Chèn JS của Flatpickr --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    {{-- 3. Kích hoạt định dạng 24h --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr(".time-picker", {
                enableTime: true,        // Cho phép chọn giờ
                noCalendar: true,        // Ẩn lịch ngày đi
                dateFormat: "H:i",       // Định dạng giờ phút (24h)
                time_24hr: true,         // Bắt buộc dùng 24h (Không AM/PM)
                minuteIncrement: 15      // Mỗi lần cuộn tăng 15 phút cho tiện
            });
        });
    </script>

    </div> {{-- Kết thúc container-fluid --}}

    <div class="modal fade" id="customerListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark border-secondary shadow-lg">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-info">
                        <i class="bi bi-people-fill me-2"></i> Danh sách khách hàng ca <span id="modal-slot-time"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3 text-white-50 small">
                        <span>Ngày: <b id="modal-slot-date" class="text-white"></b></span>
                        <span>Sức chứa: <b id="modal-slot-usage" class="text-white"></b></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-dark table-hover border-secondary">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th class="text-center">Số vé</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="customer-table-body">
                                {{-- Dữ liệu AJAX sẽ đổ vào đây --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DÒNG NÀY ĐỂ TRÌNH DUYỆT HIỂU ĐƯỢC JQUERY ($) --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
   // ĐẶT HÀM NÀY RA NGOÀI TẤT CẢ CÁC CẶP $(document).ready ĐỂ TRỞ THÀNH GLOBAL
   function showCustomerList(slotId) {
    if(!slotId) return;

    // 1. Khởi tạo Modal bằng Bootstrap API (Lấy instance cũ hoặc tạo mới)
    var modalElement = document.getElementById('customerListModal');
    var customerModal = bootstrap.Modal.getOrCreateInstance(modalElement);
    
    // 2. Hiện loading trong khi chờ AJAX
    $('#customer-table-body').html('<tr><td colspan="5" class="text-center text-info py-4"><div class="spinner-border spinner-border-sm me-2"></div> Đang tải dữ liệu...</td></tr>');
    customerModal.show();

    // 3. Gọi AJAX lấy dữ liệu từ Controller
    $.ajax({
        url: '/admin/slots/' + slotId + '/customers', 
        method: 'GET',
        success: function(response) {
            // Đổ thông tin vào Header của Modal
            $('#modal-slot-time').text(response.time);
            $('#modal-slot-date').text(response.date);
            $('#modal-slot-usage').text(response.booked + ' / ' + response.capacity);

            var html = '';
            if (!response.customers || response.customers.length === 0) {
                html = '<tr><td colspan="5" class="text-center text-white-50 py-4">Chưa có khách nào đặt ca này.</td></tr>';
            } else {
                response.customers.forEach(function(cust) {
                    // Tự động đổi màu badge theo trạng thái đơn hàng
                    let statusClass = 'bg-warning text-dark';
                    if(cust.status === 'completed' || cust.status === 'paid' || cust.status === 'success') {
                        statusClass = 'bg-success text-white';
                    }
                    
                    html += `
                        <tr>
                            <td>
                                <div class="fw-bold text-white">${cust.name}</div>
                                <small class="text-info">Đơn: #${cust.order_id}</small>
                            </td>
                            <td>${cust.phone}</td>
                            <td class="text-center">${cust.qty}</td>
                            <td class="text-uppercase small text-white-50">${cust.payment || 'N/A'}</td>
                            <td><span class="badge ${statusClass}">${cust.status}</span></td>
                        </tr>`;
                });
            }
            $('#customer-table-body').html(html);
        },
        error: function(xhr) {
            console.error("Lỗi AJAX:", xhr.responseText);
            $('#customer-table-body').html('<tr><td colspan="5" class="text-center text-danger py-4">Lỗi tải dữ liệu. Hãy kiểm tra Route và Controller!</td></tr>');
        }
    });
}
    </script>


@endsection