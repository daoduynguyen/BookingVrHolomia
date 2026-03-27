<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nạp tiền Ví Holomia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5" style="max-width: 900px;">
        <div class="d-flex align-items-center mb-4">
            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="bi bi-arrow-left"></i> Quay lại
            </a>
            <h4 class="fw-bold text-primary mb-0"><i class="bi bi-wallet2 me-2"></i>Nạp tiền Ví Holomia</h4>
        </div>

        <div class="row g-4">
            {{-- CỘT TRÁI: Chọn số tiền --}}
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h6 class="fw-bold text-dark mb-3">Số dư hiện tại</h6>
                    <div class="p-3 rounded-3 mb-4 text-center" style="background: linear-gradient(135deg,#2563eb,#0dcaf0);">
                        <div class="text-white small opacity-75 mb-1">Ví Holomia</div>
                        <div class="text-white fw-bold fs-3">{{ number_format($user->balance) }}đ</div>
                    </div>

                    <h6 class="fw-bold text-dark mb-3">Chọn số tiền nạp</h6>
                    <div class="row g-2 mb-3">
                        @foreach([50000, 100000, 200000, 500000] as $amt)
                            <div class="col-6">
                                <a href="{{ route('wallet.topup', ['amount' => $amt]) }}"
                                   class="btn w-100 fw-bold rounded-3 {{ $amount == $amt ? 'btn-primary' : 'btn-outline-secondary' }}">
                                    {{ number_format($amt) }}đ
                                </a>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('wallet.topup') }}" method="GET" class="d-flex gap-2">
                        <input type="number" name="amount" class="form-control border-light"
                               placeholder="Nhập số tiền khác..." min="10000" step="10000" value="{{ $amount }}">
                        <button type="submit" class="btn btn-primary fw-bold px-3">OK</button>
                    </form>

                    {{-- Lịch sử nạp tiền --}}
                    @if($transactions->count() > 0)
                        <hr class="my-4 border-light">
                        <h6 class="fw-bold text-dark mb-3">Lịch sử giao dịch</h6>
                        @foreach($transactions as $tx)
                            <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-light">
                                <div>
                                    <div class="small fw-bold {{ $tx->type == 'topup' ? 'text-success' : 'text-danger' }}">
                                        {{ $tx->type == 'topup' ? '+' : '-' }}{{ number_format($tx->amount) }}đ
                                    </div>
                                    <div class="small text-muted">{{ $tx->description }}</div>
                                </div>
                                <span class="badge rounded-pill {{ $tx->status == 'completed' ? 'bg-success' : ($tx->status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                    {{ $tx->status == 'completed' ? 'Thành công' : ($tx->status == 'pending' ? 'Chờ xác nhận' : 'Thất bại') }}
                                </span>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- CỘT PHẢI: QR Chuyển khoản --}}
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                    <h6 class="fw-bold text-dark mb-1">Quét mã QR để nạp tiền</h6>
                    <p class="small text-muted mb-4">Mở app ngân hàng → Quét mã → Chuyển khoản</p>

                    {{-- QR Code --}}
                    <div class="d-flex justify-content-center mb-3">
                        <div class="p-3 border border-light rounded-3 shadow-sm bg-white" style="display:inline-block;">
                            <img src="{{ $qrUrl }}" alt="QR Nạp tiền" width="220" height="220"
                                 onerror="this.src='https://via.placeholder.com/220?text=QR+Error'">
                        </div>
                    </div>

                    {{-- Thông tin CK --}}
                    <div class="bg-light rounded-3 p-3 mb-4 text-start">
                        <div class="row g-2 small">
                            <div class="col-5 text-muted">Ngân hàng:</div>
                            <div class="col-7 fw-bold text-dark">{{ $bankName }}</div>

                            <div class="col-5 text-muted">Số tài khoản:</div>
                            <div class="col-7 d-flex align-items-center gap-2">
                                <span class="fw-bold text-dark" id="bankAcc">{{ $bankAccount }}</span>
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

                    {{-- Trạng thái chờ --}}
                    <div id="status-waiting" class="alert border-0 rounded-3 py-2 mb-3"
                         style="background:#fff8e1;">
                        <div class="d-flex align-items-center justify-content-center gap-2 text-warning small fw-bold">
                            <div class="spinner-border spinner-border-sm" role="status"></div>
                            Đang chờ xác nhận chuyển khoản...
                        </div>
                    </div>

                    <div id="status-success" class="alert alert-success border-0 rounded-3 py-2 mb-3 d-none">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Nạp tiền thành công!</strong> Ví đã được cộng <span id="credited-amount"></span>đ
                    </div>

                    <div class="small text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Hệ thống tự động xác nhận trong vòng <strong>1–2 phút</strong> sau khi chuyển khoản thành công.
                        <br>Vui lòng nhập <strong class="text-danger">đúng nội dung chuyển khoản</strong>.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Copy text
        function copyText(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                btn.innerHTML = '<i class="bi bi-check-lg text-success"></i>';
                setTimeout(() => btn.innerHTML = '<i class="bi bi-copy"></i>', 2000);
            });
        }

        // Polling kiểm tra trạng thái nạp tiền mỗi 5 giây
        const refCode = '{{ $refCode }}';
        let pollInterval = setInterval(checkStatus, 5000);
        let pollCount = 0;

        function checkStatus() {
            pollCount++;
            if (pollCount > 60) { // Dừng sau 5 phút
                clearInterval(pollInterval);
                return;
            }

            fetch('{{ route("wallet.check") }}?ref_code=' + refCode, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'completed') {
                    clearInterval(pollInterval);
                    document.getElementById('status-waiting').classList.add('d-none');
                    document.getElementById('status-success').classList.remove('d-none');
                    document.getElementById('credited-amount').textContent =
                        new Intl.NumberFormat('vi-VN').format(data.amount);
                    // Cập nhật số dư hiển thị
                    setTimeout(() => window.location.href = '{{ route("wallet.topup") }}', 3000);
                }
            })
            .catch(() => {});
        }
    </script>
    @include('partials.footer')
</body>
</html>