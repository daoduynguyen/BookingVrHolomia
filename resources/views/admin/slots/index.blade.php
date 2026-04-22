@extends('layouts.admin')

@section('title', 'Quản lý Khung giờ')

@section('admin_content')
    <style>
        .slot-compact-box {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
            transition: all 0.2s ease;
            cursor: pointer;
            position: relative;
            width: 100px;
            background-color: #fff;
        }
        .slot-compact-box:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1); 
            border-color: #0dcaf0;
        }
        .slot-empty   { background-color: #f3f4f6; border-color: #d1d5db; }
        .slot-booking { background-color: #fffbeb; border-color: #fde68a; }
        .slot-full    { background-color: #fef2f2; border-color: #fecaca; opacity: 0.8; }

        .time-label     { font-size: 1.1rem; font-weight: bold; display: block; line-height: 1.2; color: #1f2937; }
        .capacity-label { font-size: 0.75rem; font-weight: 600; display: block; margin-top: 4px; }
        .letter-spacing-2 { letter-spacing: 2px; }

        .slot-compact-box:hover .slot-action-overlay { display: flex !important; }
        .slot-action-overlay {
            display: none;
            position: absolute;
            top: -8px; right: -8px;
            background: rgba(0,0,0,0.8);
            border-radius: 6px;
            padding: 2px;
            z-index: 10;
        }
        .slot-action-overlay .btn { padding: 2px 4px; line-height: 1; margin: 0 2px; }
        .slot-action-overlay .btn:hover { transform: scale(1.1); color: #fff !important; }
    </style>

    <div class="container-fluid">
        @if(session('success'))
            <div class="alert alert-success bg-success text-dark border-0">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger bg-danger text-dark border-0">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger bg-danger text-dark border-0">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between mb-4 gap-3">
            <div class="d-flex align-items-center flex-shrink-0">
                <div class="bg-primary me-3" style="width: 50px; height: 3px;"></div>
                <h2 class="text-primary fw-bold text-uppercase mb-0 letter-spacing-2 text-nowrap">Quản lý lịch đặt vé</h2>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="btn btn-warning fw-bold text-dark shadow text-nowrap rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal">
                    <i class="bi bi-calendar-range"></i> TẠO LỊCH HÀNG LOẠT
                </button>
            </div>
        </div>

        <div class="row justify-content-center mb-5">
            <div class="col-md-10 col-lg-8">
                <form action="{{ route('admin.slots.index') }}" method="GET" class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3">
                    <div class="d-flex align-items-center bg-white rounded-pill shadow-sm border border-light flex-grow-1" style="height: 50px;">
                        <span class="bg-transparent border-0 text-primary ps-4 pe-2 fs-5"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none px-1 h-100" placeholder="Tìm tên trò chơi..." value="{{ request('search') }}">
                        @if(request('search'))
                            <a href="{{ route('admin.slots.index', ['date' => $selectedDate]) }}" class="btn btn-sm btn-light rounded-circle text-danger d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                    <div class="d-flex align-items-center bg-white rounded-pill shadow-sm border border-light px-4" style="width: 300px; height: 50px;">
                        <label class="text-muted fw-bold text-nowrap mb-0 me-2">Ngày xem:</label>
                        <input type="date" name="date" class="form-control border-0 bg-light rounded-pill shadow-none fw-bold text-primary px-3 py-1 w-100" value="{{ $selectedDate }}" onchange="this.form.submit()" style="cursor: pointer;">
                    </div>
                    <button type="submit" class="d-none"></button>
                </form>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-white border-light shadow-sm">
                    <div class="card-body d-flex justify-content-center gap-4 py-2">
                        <small class="text-dark"><i class="bi bi-circle-fill me-1" style="color: #374151 !important;"></i> Trống (Sẵn sàng)</small>
                        <small class="text-warning"><i class="bi bi-circle-fill me-1" style="color: #b45309 !important;"></i> Đang nhận (Còn máy)</small>
                        <small class="text-danger"><i class="bi bi-circle-fill me-1" style="color: #b91c1c !important;"></i> Đã đầy (Hết máy)</small>
                    </div>
                </div>
            </div>
        </div>

        @foreach($tickets as $ticket)
            @php $ticketSlots = isset($slots[$ticket->id]) ? $slots[$ticket->id] : []; @endphp
            <div class="card bg-white border-light shadow mb-5">
                <div class="card-header bg-transparent border-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="text-primary fw-bold mb-0">
                        <i class="bi bi-controller me-2"></i> {{ $ticket->name }}
                    </h5>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-success text-nowrap" data-bs-toggle="modal" data-bs-target="#addSlotModal-{{ $ticket->id }}">
                            <i class="bi bi-plus-lg"></i> Thêm ca lẻ
                        </button>
                        <form action="{{ route('admin.slots.generate') }}" method="POST" class="m-0">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <input type="hidden" name="start_date" value="{{ $selectedDate }}">
                            <input type="hidden" name="end_date" value="{{ $selectedDate }}">
                            <input type="hidden" name="gap_time" value="10">
                            <button type="submit" class="btn btn-outline-info btn-sm rounded-pill px-3 text-nowrap">
                                <i class="bi bi-magic"></i> Tạo nhanh (08:00 - 21:00)
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($ticketSlots) > 0)
                        @php
                            $groups = [
                                'morning' => ['title' => 'Buổi Sáng (08:00 - 12:00)', 'icon' => 'bi-sunrise', 'color' => 'success', 'slots' => []],
                                'afternoon' => ['title' => 'Buổi Chiều (12:00 - 18:00)', 'icon' => 'bi-sun', 'color' => 'warning', 'slots' => []],
                                'evening' => ['title' => 'Buổi Tối (18:00 - 23:00)', 'icon' => 'bi-moon-stars', 'color' => 'primary', 'slots' => []],
                            ];
                            foreach($ticketSlots as $slot) {
                                $hour = (int)\Carbon\Carbon::parse($slot->start_time)->format('H');
                                if($hour < 12) $groups['morning']['slots'][] = $slot;
                                elseif($hour < 18) $groups['afternoon']['slots'][] = $slot;
                                else $groups['evening']['slots'][] = $slot;
                            }
                            $accordionId = 'accordion-' . $ticket->id;
                        @endphp

                        <div class="accordion" id="{{ $accordionId }}">
                            @foreach($groups as $key => $group)
                                @if(count($group['slots']) > 0)
                                    <div class="accordion-item border-light mb-3 shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                        <h2 class="accordion-header" id="heading-{{ $ticket->id }}-{{ $key }}">
                                            <button class="accordion-button bg-light fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $ticket->id }}-{{ $key }}" aria-expanded="true" aria-controls="collapse-{{ $ticket->id }}-{{ $key }}">
                                                <i class="bi {{ $group['icon'] }} text-{{ $group['color'] }} fs-5 me-2"></i> {{ $group['title'] }} 
                                                <span class="badge bg-secondary ms-2 rounded-pill">{{ count($group['slots']) }} ca</span>
                                            </button>
                                        </h2>
                                        <div id="collapse-{{ $ticket->id }}-{{ $key }}" class="accordion-collapse collapse show" aria-labelledby="heading-{{ $ticket->id }}-{{ $key }}">
                                            <div class="accordion-body">
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($group['slots'] as $slot)
                                                        @php
                                                            $statusClass = 'slot-empty';
                                                            $textColor = 'text-secondary';
                                                            if ($slot->booked_count > 0 && $slot->booked_count < $slot->capacity) {
                                                                $statusClass = 'slot-booking';
                                                                $textColor = 'text-warning';
                                                            } elseif ($slot->booked_count >= $slot->capacity || $slot->status == 'full') {
                                                                $statusClass = 'slot-full';
                                                                $textColor = 'text-danger';
                                                            }
                                                        @endphp
                                                        <div class="slot-compact-box {{ $statusClass }}" onclick="showCustomerList({{ $slot->id }})">
                                                            <span class="time-label">{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}</span>
                                                            <span class="capacity-label {{ $textColor }}">
                                                                @if($statusClass != 'slot-full')
                                                                    {{ $slot->booked_count }}/{{ $slot->capacity }} máy
                                                                @else
                                                                    FULL
                                                                @endif
                                                            </span>
                                                            <div class="slot-action-overlay" onclick="event.stopPropagation();">
                                                                <button type="button" class="btn btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#editSlotModal-{{ $slot->id }}" onclick="event.stopPropagation();" title="Sửa ca">
                                                                    <i class="bi bi-pencil-square"></i>
                                                                </button>
                                                                <form action="{{ route('admin.slots.destroy', $slot->id) }}" method="POST" class="d-inline" onsubmit="event.stopPropagation(); return confirm('Xác nhận xóa ca này?');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-sm text-danger" title="Xóa ca">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted display-4 opacity-25"></i>
                            <p class="text-muted mt-3">Chưa có khung giờ nào cho ngày {{ \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') }}</p>
                            <form action="{{ route('admin.slots.generate') }}" method="POST">
                                @csrf
                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                                <input type="hidden" name="start_date" value="{{ $selectedDate }}">
                                <input type="hidden" name="end_date" value="{{ $selectedDate }}">
                                <input type="hidden" name="gap_time" value="10">
                                <button type="submit" class="btn btn-info rounded-pill px-4 py-2 fw-bold">
                                    <i class="bi bi-plus-circle-fill me-2"></i> KHỞI TẠO CA NHANH (08:00 - 21:00)
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

    </div>{{-- /container-fluid --}}


    {{-- ================================================================ --}}
    {{-- TẤT CẢ MODAL NẰM Ở ĐÂY — NGOÀI container-fluid, NGOÀI mọi card --}}
    {{-- Bootstrap yêu cầu modal phải là con trực tiếp của <body>         --}}
    {{-- Để modal lồng trong card/container gây ra lỗi nháy / 2 lớp modal --}}
    {{-- ================================================================ --}}

    @foreach($tickets as $ticket)
        @php $ticketSlots = isset($slots[$ticket->id]) ? $slots[$ticket->id] : []; @endphp

        {{-- Modal Sửa từng ca --}}
        @foreach($ticketSlots as $slot)
        <div class="modal fade" id="editSlotModal-{{ $slot->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white border-light">
                    <div class="modal-header border-light">
                        <h5 class="modal-title text-primary"><i class="bi bi-pencil-square"></i> Sửa ca chơi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.slots.update', $slot->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body text-dark">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Giờ bắt đầu</label>
                                    <input type="text" name="start_time" class="form-control bg-light text-dark border-light time-picker" required value="{{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Giờ kết thúc</label>
                                    <input type="text" name="end_time" class="form-control bg-light text-dark border-light time-picker" required value="{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-secondary small">Sức chứa tối đa</label>
                                    <input type="number" name="capacity" class="form-control bg-light text-dark border-light" required min="{{ $slot->booked_count > 0 ? $slot->booked_count : 1 }}" value="{{ $slot->capacity }}">
                                    @if($slot->booked_count > 0)
                                        <small class="text-warning">Đã có {{ $slot->booked_count }} người đặt. Sức chứa không được nhỏ hơn số này.</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-info fw-bold">Lưu thay đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Modal Thêm ca lẻ --}}
        <div class="modal fade" id="addSlotModal-{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content bg-white border-light">
                    <div class="modal-header border-light">
                        <h5 class="modal-title text-success"><i class="bi bi-plus-circle"></i> Thêm ca lẻ - {{ $ticket->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.slots.store-single') }}" method="POST">
                        @csrf
                        <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                        <input type="hidden" name="date" value="{{ $selectedDate }}">
                        <div class="modal-body text-dark">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Giờ bắt đầu</label>
                                    <input type="text" name="start_time" class="form-control bg-light text-dark border-light time-picker" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-secondary small">Giờ kết thúc</label>
                                    <input type="text" name="end_time" class="form-control bg-light text-dark border-light time-picker" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label text-secondary small">Sức chứa tối đa</label>
                                    <input type="number" name="capacity" class="form-control bg-light text-dark border-light" required min="1" value="15">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                            <button type="submit" class="btn btn-success fw-bold">Tạo ca</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Modal Tạo lịch hàng loạt (chỉ 1 cái) --}}
    <div class="modal fade" id="bulkGenerateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-white border-light shadow-lg">
                <div class="modal-header border-light">
                    <h5 class="modal-title text-warning fw-bold">
                        <i class="bi bi-calendar-range me-2"></i> Tạo lịch hàng loạt
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.slots.generate') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="alert alert-info bg-primary bg-opacity-10 border-primary border-opacity-25 text-primary small mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            Hệ thống sẽ tự động tạo ca chơi cho <b>TẤT CẢ TRÒ CHƠI</b>. Ca nào đã có sẽ bỏ qua để tránh trùng lặp.
                        </div>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Từ ngày</label>
                                <input type="date" name="start_date" class="form-control bg-light text-dark border-light" required value="{{ $selectedDate }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Đến ngày</label>
                                <input type="date" name="end_date" class="form-control bg-light text-dark border-light" required value="{{ \Carbon\Carbon::parse($selectedDate)->addDays(2)->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Mỗi ngày từ giờ</label>
                                <input type="text" name="start_time" class="form-control bg-light text-dark border-light time-picker" required value="08:00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Đến giờ</label>
                                <input type="text" name="end_time" class="form-control bg-light text-dark border-light time-picker" required value="21:00">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label text-secondary small fw-bold text-uppercase">Khoảng nghỉ giữa các ca (Giấy nghỉ chờ 1-20 phút)</label>
                                <input type="number" name="gap_time" class="form-control bg-light text-dark border-light" required value="10" min="0" max="30">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy bỏ</button>
                        <button type="submit" class="btn btn-warning fw-bold text-dark px-4">
                            <i class="bi bi-lightning-charge-fill me-1"></i> BẮT ĐẦU TẠO
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal xem danh sách khách hàng --}}
    <div class="modal fade" id="customerListModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-white border-light shadow-lg">
                <div class="modal-header border-light">
                    <h5 class="modal-title text-primary">
                        <i class="bi bi-people-fill me-2"></i> Danh sách khách hàng ca <span id="modal-slot-time"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex justify-content-between mb-3 text-muted small">
                        <span>Ngày: <b id="modal-slot-date" class="text-dark"></b></span>
                        <span>Sức chứa: <b id="modal-slot-usage" class="text-dark"></b></span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover border-light">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Số điện thoại</th>
                                    <th class="text-center">Số vé</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody id="customer-table-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Flatpickr --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll('.modal').forEach(function (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function () {
                    modalEl.querySelectorAll('.time-picker').forEach(function (el) {
                        if (!el._flatpickr) {
                            flatpickr(el, {
                                enableTime: true,
                                noCalendar: true,
                                dateFormat: "H:i",
                                time_24hr: true,
                                minuteIncrement: 15,
                                static: true
                            });
                        }
                    });
                });
            });
        });
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function showCustomerList(slotId) {
            if (!slotId) return;
            var modalElement = document.getElementById('customerListModal');
            var customerModal = bootstrap.Modal.getOrCreateInstance(modalElement);
            $('#customer-table-body').html('<tr><td colspan="5" class="text-center text-primary py-4"><div class="spinner-border spinner-border-sm me-2"></div> Đang tải dữ liệu...</td></tr>');
            customerModal.show();
            $.ajax({
                url: '/admin/slots/' + slotId + '/customers',
                method: 'GET',
                success: function (response) {
                    $('#modal-slot-time').text(response.time);
                    $('#modal-slot-date').text(response.date);
                    $('#modal-slot-usage').text(response.booked + ' / ' + response.capacity);
                    var html = '';
                    if (!response.customers || response.customers.length === 0) {
                        html = '<tr><td colspan="5" class="text-center text-muted py-4">Chưa có khách nào đặt ca này.</td></tr>';
                    } else {
                        response.customers.forEach(function (cust) {
                            let statusClass = 'bg-warning text-dark';
                            if (cust.status === 'completed' || cust.status === 'paid' || cust.status === 'success') {
                                statusClass = 'bg-success text-dark';
                            }
                            html += '<tr>'
                                + '<td><div class="fw-bold text-dark">' + cust.name + '</div><small class="text-primary">Đơn: #' + cust.order_id + '</small></td>'
                                + '<td>' + cust.phone + '</td>'
                                + '<td class="text-center">' + cust.qty + '</td>'
                                + '<td class="text-uppercase small text-muted">' + (cust.payment || 'N/A') + '</td>'
                                + '<td><span class="badge ' + statusClass + '">' + cust.status + '</span></td>'
                                + '</tr>';
                        });
                    }
                    $('#customer-table-body').html(html);
                },
                error: function (xhr) {
                    console.error("Lỗi AJAX:", xhr.responseText);
                    $('#customer-table-body').html('<tr><td colspan="5" class="text-center text-danger py-4">Lỗi tải dữ liệu.</td></tr>');
                }
            });
        }
    </script>

@endsection