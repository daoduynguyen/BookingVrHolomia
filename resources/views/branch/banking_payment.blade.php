<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán chuyển khoản - {{ $location->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --primary: {{ $location->color ?? '#0d6efd' }};
        }
    </style>
</head>
<body class="bg-light text-dark">
    @include('branch.partials.navbar')

    <div class="container py-5" style="max-width: 960px;">

        <div class="d-flex align-items-center mb-4">
            <div class="bg-primary me-3" style="width:50px;height:3px;"></div>
            <h4 class="fw-bold text-primary mb-0"><i class="bi bi-qr-code-scan me-2"></i>Thanh toán chuyển khoản</h4>
        </div>

        <div class="row g-4">

            {{-- CỘT TRÁI: Thông tin đơn --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold text-dark mb-3">Thông tin đơn hàng</h6>

                    <div id="order-status-box" class="p-3 rounded-3 mb-3 text-center
                        {{ $order->status == 'paid' ? 'bg-success bg-opacity-10 border border-success border-opacity-25' : 'bg-warning bg-opacity-10 border border-warning border-opacity-25' }}">
                        @if($order->status == 'paid')
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                            <div class="fw-bold text-success mt-1">Đã thanh toán</div>
                        @else
                            <div class="d-flex align-items-center justify-content-center gap-2">
                                <div class="spinner-border spinner-border-sm text-warning"></div>
                                <span class="fw-bold text-warning small">Chờ thanh toán...</span>
                            </div>
                        @endif
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Mã đơn:</span>
                            <span class="fw-bold text-primary">{{ $order->id }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Khách hàng:</span>
                            <span class="fw-bold">{{ $order->customer_name }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Ngày chơi:</span>
                            <span class="fw-bold">{{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</span>
                        </div>
                        @if($order->slot)
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Khung giờ:</span>
                            <span class="fw-bold">{{ substr($order->slot->start_time,0,5) }} - {{ substr($order->slot->end_time,0,5) }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Cơ sở:</span>
                            <span class="fw-bold text-end" style="max-width:55%;">{{ $location->name }}</span>
                        </div>
                    </div>

                    @foreach($order->details as $item)
                    <div class="d-flex justify-content-between align-items-center small py-2 border-bottom border-light">
                        <span>🎮 {{ $item->ticket->name ?? $item->ticket_name }} x{{ $item->quantity }}</span>
                        <span class="fw-bold text-primary">{{ number_format($item->price * $item->quantity) }}đ</span>
                    </div>
                    @endforeach

                    <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-light">
                        <span class="fw-bold text-dark">Tổng cần CK:</span>
                        <span class="fw-bold text-primary fs-5">{{ number_format($amount) }}đ</span>
                    </div>
                </div>
            </div>

            {{-- CỘT PHẢI: QR --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                    <h6 class="fw-bold text-dark mb-1">Quét mã QR để thanh toán</h6>
                    <p class="small text-muted mb-4">Mở app ngân hàng → Quét mã → Chuyển khoản đúng số tiền và nội dung</p>

                    <div class="d-flex justify-content-center mb-4">
                        <div class="p-3 border border-light rounded-3 shadow-sm bg-white" style="width: 270px; height: 270px; display: flex; align-items: center; justify-content: center;">
                            @if(empty($bankAccount))
                                <div class="text-center text-danger">
                                    <i class="bi bi-exclamation-triangle fs-1"></i>
                                    <div class="mt-2 fw-bold">Chưa cấu hình tài khoản</div>
                                    <div class="small">Vui lòng liên hệ Admin</div>
                                </div>
                            @else
                                <img src="{{ $qrUrl }}" alt="QR Thanh toán" width="240" height="240"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                <div style="display: none;" class="text-center text-danger">
                                    <i class="bi bi-qr-code-scan fs-1"></i>
                                    <div class="mt-2 fw-bold">Lỗi tải mã QR</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-light rounded-3 p-3 mb-4 text-start">
                        <div class="row g-2 small">
                            <div class="col-5 text-muted">Ngân hàng:</div>
                            <div class="col-7 fw-bold text-dark">{{ $bankName }}</div>

                            <div class="col-5 text-muted">Số tài khoản:</div>
                            <div class="col-7 d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark">{{ $bankAccount }}</span>
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyText('{{ $bankAccount }}', this)">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>

                            <div class="col-5 text-muted">Chủ tài khoản:</div>
                            <div class="col-7 fw-bold text-dark">{{ $bankOwner }}</div>

                            <div class="col-5 text-muted">Số tiền:</div>
                            <div class="col-7 fw-bold text-primary fs-6">{{ number_format($amount) }}đ</div>

                            <div class="col-5 text-muted">Nội dung CK:</div>
                            <div class="col-7 d-flex align-items-center gap-2">
                                <span class="fw-bold text-danger" id="refCode">{{ $refCode }}</span>
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyText('{{ $refCode }}', this)">
                                    <i class="bi bi-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="status-waiting" class="{{ $order->status == 'paid' ? 'd-none' : '' }} alert border-0 rounded-3 py-2 mb-3" style="background:#fff8e1;">
                        <div class="d-flex align-items-center justify-content-center gap-2 text-warning small fw-bold">
                            <div class="spinner-border spinner-border-sm"></div>
                            Đang chờ xác nhận thanh toán...
                        </div>
                    </div>

                    <div id="status-success" class="{{ $order->status == 'paid' ? '' : 'd-none' }} alert alert-success border-0 rounded-3 py-2 mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i><strong>Thanh toán thành công!</strong>
                    </div>

                    <div id="btn-view-order" class="{{ $order->status == 'paid' ? '' : 'd-none' }} mt-3">
                        <a href="{{ route('branch.profile', ['subdomain' => $subdomain]) }}" class="btn btn-success fw-bold rounded-pill px-4">
                            <i class="bi bi-check2-circle me-2"></i>Xem lịch sử đặt vé
                        </a>
                    </div>

                    <div class="small text-muted mt-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Hệ thống tự động xác nhận trong <strong>1–2 phút</strong> sau khi chuyển khoản.
                        Nhập <strong class="text-danger">đúng nội dung {{ $refCode }}</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
                setTimeout(() => btn.innerHTML = '<i class="bi bi-copy"></i>', 2000);
            });
        }

        @if($order->status !== 'paid')
        let pollCount = 0;
        const pollInterval = setInterval(() => {
            if (++pollCount > 120) { clearInterval(pollInterval); return; }
            fetch('/api/order-status/{{ $order->id }}', { headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'paid') {
                    clearInterval(pollInterval);
                    document.getElementById('status-waiting').classList.add('d-none');
                    document.getElementById('status-success').classList.remove('d-none');
                    document.getElementById('btn-view-order').classList.remove('d-none');
                    document.getElementById('order-status-box').innerHTML = '<i class="bi bi-check-circle-fill text-success fs-3"></i><div class="fw-bold text-success mt-1">Đã thanh toán</div>';

                    Swal.fire({
                        icon: 'success',
                        title: 'Thanh toán thành công! 🎉',
                        html: `<div class="text-start small">
                            <div class="mb-2"><b>Mã đơn:</b> {{ $order->id }}</div>
                            <div class="mb-2"><b>Ngày chơi:</b> {{ \Carbon\Carbon::parse($order->booking_date)->format('d/m/Y') }}</div>
                            @if($order->slot)<div class="mb-2"><b>Khung giờ:</b> {{ substr($order->slot->start_time,0,5) }} - {{ substr($order->slot->end_time,0,5) }}</div>@endif
                            <div class="mb-2"><b>Phương thức:</b> Chuyển khoản ngân hàng</div>
                            <div><b>Tổng tiền:</b> <span class="text-primary fw-bold">{{ number_format($amount) }}đ</span></div>
                        </div>`,
                        confirmButtonText: 'Xem lịch sử đặt vé',
                        confirmButtonColor: '#198754',
                    }).then(() => { window.location.href = '{{ route("branch.profile", ["subdomain" => $subdomain]) }}'; });
                }
            }).catch(()=>{});
        }, 5000);
        @endif
    </script>
</body>
</html>
