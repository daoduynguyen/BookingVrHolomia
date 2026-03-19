<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Holomia VR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
</head>
<body class="bg-light text-dark">
    
    @include('partials.navbar')

    <div class="container-fluid p-0 mb-5">
        <div class="position-relative d-flex align-items-center justify-content-center" 
             style="height: 600px; margin-bottom: 60px;
                    background: url('https://images.unsplash.com/photo-1614726365723-49cfae9686ae?q=80&w=2000&auto=format&fit=crop') center/cover no-repeat fixed;">
            
            <div class="position-absolute top-0 start-0 w-100 h-100 bg-black opacity-50"></div>
            
            <div class="container position-relative text-center text-white" style="z-index: 1;">
                <div class="mb-3 animate-bounce">
                    <i class="bi bi-headset-vr display-1 text-info text-shadow"></i>
                </div>

                <h1 class="display-2 fw-bold text-uppercase mb-3 text-shadow" style="letter-spacing: 3px;">
                    Holomia <span class="text-info">VR World</span>
                </h1>
                
                <p class="lead fs-3 mb-5 text-light opacity-75">
                    Khám phá thế giới ảo - Trải nghiệm cảm xúc thật
                </p>

                <a href="#featured-games" class="text-white text-decoration-none opacity-75 hover-opacity-100">
                    <small>Khám phá ngay</small><br>
                    <i class="bi bi-chevron-down fs-4 animate-down"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- THANH TÌM KIẾM NỔI -->
    @include('partials.search_bar')

    <div id="featured-games"></div>
    
    <div class="container pb-5" id="list-games">
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow mb-5" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> <strong>Thành công!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="text-center mb-5">
            <h2 class="text-info fw-bold text-uppercase display-6">
                <i class="bi bi-controller"></i> TRÒ CHƠI NỔI BẬT
            </h2>
            <div style="width: 60px; height: 3px; background-color: #0dcaf0; margin: 10px auto;"></div>
        </div>
        
        <div class="row g-4"> 
            @if($tickets->isEmpty())
                <div class="col-12 text-center text-muted py-5">
                    <h3>Hiện chưa có vé nào được mở bán.</h3>
                </div>
            @else
                @foreach($tickets as $ticket)
                    <div class="col-md-4">
                        <div class="card h-100 card-game overflow-hidden rounded-4 border-0 {{ $ticket->status == 'maintenance' ? 'card-maintenance' : '' }}">
                            
                            @if($ticket->status == 'maintenance')
                                <div class="badge-maintenance">BẢO TRÌ</div>
                            @endif
                            
                            <div class="position-relative">
                                <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/640x480' }}" 
                                     class="card-img-top" 
                                     alt="{{ $ticket->name }}" 
                                     style="height: 240px; object-fit: cover;">
                                
                                <div class="position-absolute top-0 end-0 m-3 badge bg-warning text-dark shadow rounded-pill px-3 py-2">
                                    <i class="bi bi-star-fill text-dark"></i> {{ $ticket->avg_rating }}
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">{{ $ticket->category->name }}</span>
                                    <small class="text-muted fw-bold" style="font-size: 0.8rem;">
                                        <i class="bi bi-person-check-fill text-secondary"></i> {{ number_format($ticket->play_count) }} lượt
                                    </small>
                                </div>
                                
                                <h5 class="card-title fw-bold mb-2 text-truncate" title="{{ $ticket->name }}" style="font-size: 1.2rem;">
                                    {{ $ticket->name }}
                                </h5>
                                
                                <p class="small text-muted mb-4 fw-medium">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $ticket->location->name }}
                                </p>
                                
                                <div class="mt-auto pt-3 border-top border-light mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted fw-medium">Giá vé từ:</span>
                                        <span class="fw-bold text-primary fs-5">{{ number_format($ticket->price) }}đ</span>
                                    </div>
                                </div>

                                <a href="{{ route('ticket.show', $ticket->id) }}" class="btn btn-primary w-100 fw-bold py-2 text-uppercase shadow-sm">
                                    {{ $ticket->status == 'maintenance' ? 'Xem thông tin' : 'ĐẶT VÉ NGAY' }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif </div> 
    </div>

    <footer class="bg-black text-center py-5 border-top border-secondary mt-5">
        <div class="container">
            <h4 class="text-info fw-bold mb-3">HOLOMIA VR</h4>
            <p class="text-secondary mb-1">© 2026 Holomia VR Booking System. All rights reserved.</p>
            <small class="text-muted">Designed for University Project</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>