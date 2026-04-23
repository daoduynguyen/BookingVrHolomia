@php
    $locale = app()->getLocale();
    $langConfig = config('i18n.supported.' . $locale, config('i18n.supported.vi'));
@endphp
<!DOCTYPE html>
<html lang="{{ $langConfig['html_lang'] }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Đánh giá') }} - {{ $ticket->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: {{ $langConfig['font_family'] }}; }
        .star { cursor: pointer; font-size: 1.5rem; color: #e0e0e0; transition: 0.2s; }
        .star.active, .star:hover { color: #ffc107; }
        .rating-item { padding: 12px 8px; text-align: center; }
        .review-card { border-left: 4px solid #0dcaf0; }
        .review-card .user-avatar { width: 48px; height: 48px; border-radius: 50%; object-fit: cover; }
        .review-images { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .review-images img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; cursor: pointer; }
        .rating-bar { background: #e9ecef; border-radius: 4px; overflow: hidden; }
        .rating-fill { background: linear-gradient(90deg, #ffc107 0%, #ff9800 100%); height: 8px; }
    </style>
</head>
<body class="bg-light text-dark">
    @include('partials.navbar')

    <div class="container py-5">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">{{ __('Trang chủ') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('ticket.shop') }}" class="text-decoration-none">{{ __('Tất cả vé') }}</a></li>
                <li class="breadcrumb-item active">{{ $ticket->name }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Left Column: Rating Summary -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 p-4 shadow-sm sticky-top" style="top: 100px;">
                    <h5 class="fw-bold mb-4 text-center">ĐÁNH GIÁ SẢN PHẨM</h5>

                    <!-- Overall Rating -->
                    <div class="text-center mb-4">
                        <div class="display-6 fw-bold text-dark mb-1">{{ $ticket->avg_rating ?? 0 }}</div>
                        <div class="mb-2">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($ticket->avg_rating ?? 0))
                                    <i class="bi bi-star-fill text-warning"></i>
                                @elseif($i - 0.5 <= ($ticket->avg_rating ?? 0))
                                    <i class="bi bi-star-half text-warning"></i>
                                @else
                                    <i class="bi bi-star text-warning"></i>
                                @endif
                            @endfor
                        </div>
                        <small class="text-muted">{{ __('Trên 5 sao') }}</small>
                    </div>

                    <hr class="my-3">

                    <!-- Rating Distribution -->
                    <div class="mb-4">
                        @for($rating = 5; $rating >= 1; $rating--)
                            @php
                                $count = $ratingDistribution[$rating] ?? 0;
                                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div style="width: 50px;">
                                    <small class="text-muted">{{ $rating }} <i class="bi bi-star-fill text-warning" style="font-size: 0.7rem;"></i></small>
                                </div>
                                <div class="rating-bar flex-grow-1" style="height: 6px;">
                                    <div class="rating-fill" style="width: {{ $percentage }}%;"></div>
                                </div>
                                <small class="text-muted" style="width: 40px;">{{ $count }}</small>
                            </div>
                        @endfor
                    </div>

                    <hr class="my-3">

                    <!-- Total Reviews -->
                    <div class="text-center">
                        <small class="text-muted d-block mb-1">Tổng số đánh giá</small>
                        <div class="h4 fw-bold text-primary">{{ $totalReviews }}</div>
                    </div>

                    <!-- Write Review Button -->
                    @auth
                        <button class="btn btn-primary w-100 mt-4 fw-bold rounded-pill py-2" onclick="scrollToReviewForm()">
                            <i class="bi bi-pencil-square me-2"></i>Viết đánh giá
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100 mt-4 fw-bold rounded-pill py-2">
                            <i class="bi bi-pencil-square me-2"></i>Viết đánh giá
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Right Column: Reviews List -->
            <div class="col-lg-8">
                <!-- Ticket Info Header -->
                <div class="card border-0 rounded-4 p-4 shadow-sm mb-4">
                    <div class="d-flex gap-4">
                        <img src="{{ $ticket->image_url ?? 'https://via.placeholder.com/150' }}" 
                             alt="{{ $ticket->name }}" 
                             class="rounded-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <div>
                            <h4 class="fw-bold mb-2">{{ $ticket->name }}</h4>
                            <div class="mb-2">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($ticket->avg_rating ?? 0))
                                        <i class="bi bi-star-fill text-warning"></i>
                                    @elseif($i - 0.5 <= ($ticket->avg_rating ?? 0))
                                        <i class="bi bi-star-half text-warning"></i>
                                    @else
                                        <i class="bi bi-star text-warning"></i>
                                    @endif
                                @endfor
                                <span class="text-muted ms-2">{{ $totalReviews }} {{ __('đánh giá') }}</span>
                            </div>
                            <small class="text-muted d-block">
                                <i class="bi bi-geo-alt-fill text-danger"></i>
                                {{ $ticket->locations->pluck('name')->join(', ') }}
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Review Form (if user is logged in) -->
                @auth
                    <div class="card border-0 rounded-4 p-4 shadow-sm mb-4" id="review-form-section">
                        <h5 class="fw-bold mb-4">Chia sẻ đánh giá của bạn</h5>
                        <form id="review-form-submit" method="POST" action="{{ route('reviews.store') }}">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            <input type="hidden" name="rating" id="hidden-rating" value="5">

                            <!-- Rating Stars -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Đánh giá của bạn</label>
                                <div id="star-rating">
                                    @for($i = 5; $i >= 1; $i--)
                                        <i class="bi bi-star-fill star" data-rating="{{ $i }}" style="margin-right: 8px;"></i>
                                    @endfor
                                </div>
                                <small class="text-muted d-block mt-2"><span id="rating-text">Rất tuyệt vời</span></small>
                            </div>

                            <!-- Comment -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Bình luận (Không bắt buộc)</label>
                                <textarea name="comment" class="form-control rounded-3" rows="4" 
                                          placeholder="Chia sẻ trải nghiệm của bạn..." 
                                          maxlength="500"></textarea>
                                <small class="text-muted d-block mt-1"><span id="char-count">0</span>/500 ký tự</small>
                            </div>

                            <button type="submit" class="btn btn-primary fw-bold rounded-pill px-5 py-2">
                                <i class="bi bi-send me-2"></i>Gửi đánh giá
                            </button>
                        </form>
                    </div>
                @endauth

                <!-- Reviews List -->
                <div id="reviews-list">
                    @if($reviews->count() > 0)
                        @foreach($reviews as $review)
                            <div class="card border-0 rounded-4 p-4 shadow-sm mb-3 review-card">
                                <!-- User Info -->
                                <div class="d-flex align-items-start gap-3 mb-3">
                                    <img src="{{ $review->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($review->user->name) }}" 
                                         alt="{{ $review->user->name }}" 
                                         class="user-avatar">
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">{{ $review->user->name }}</h6>
                                        <small class="text-muted d-block">{{ $review->created_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    <div class="text-end">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="bi bi-star-fill text-warning"></i>
                                            @else
                                                <i class="bi bi-star text-warning"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>

                                <!-- Review Comment -->
                                @if($review->comment)
                                    <p class="text-dark mb-3">{{ $review->comment }}</p>
                                @endif

                                <!-- Like/Helpful -->
                                <div class="d-flex gap-3 mt-3">
                                    <small class="text-muted">
                                        <i class="bi bi-hand-thumbs-up"></i> <span class="helpful-count">0</span>
                                    </small>
                                </div>
                            </div>
                        @endforeach

                        <!-- Pagination -->
                        <nav aria-label="Page navigation" class="mt-4">
                            {{ $reviews->links('pagination::bootstrap-5') }}
                        </nav>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-chat-left-quote display-1 text-muted opacity-25"></i>
                            <h5 class="mt-3 fw-bold text-dark">Chưa có đánh giá nào</h5>
                            <p class="text-muted">Hãy là người đầu tiên chia sẻ trải nghiệm của bạn!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Star Rating Selection
            let selectedRating = 5;
            const ratingTexts = {
                5: '🤩 Rất tuyệt vời',
                4: '😊 Tốt',
                3: '😐 Bình thường',
                2: '😟 Không tốt',
                1: '😢 Tệ'
            };

            $('#star-rating').on('click', '.star', function() {
                selectedRating = $(this).data('rating');
                $('#hidden-rating').val(selectedRating);
                updateStars(selectedRating);
                $('#rating-text').text(ratingTexts[selectedRating]);
            });

            $('#star-rating').on('mouseover', '.star', function() {
                const hoverRating = $(this).data('rating');
                updateStars(hoverRating);
            });

            $('#star-rating').on('mouseout', function() {
                updateStars(selectedRating);
            });

            function updateStars(rating) {
                $('#star-rating .star').each(function() {
                    const starRating = $(this).data('rating');
                    if (starRating <= rating) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            }

            // Initialize stars
            updateStars(selectedRating);

            // Character counter
            $('textarea[name="comment"]').on('input', function() {
                $('#char-count').text($(this).val().length);
            });

            // Submit review form
            $('#review-form-submit').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const comment = form.find('textarea[name="comment"]').val();

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: {
                        _token: $('[name="_token"]').val(),
                        ticket_id: form.find('input[name="ticket_id"]').val(),
                        rating: $('#hidden-rating').val(),
                        comment: comment
                    },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công!',
                                text: res.message,
                                background: '#1a1d20',
                                color: '#fff',
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Có lỗi xảy ra!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: message,
                            background: '#1a1d20',
                            color: '#fff',
                        });
                    }
                });
            });
        });

        function scrollToReviewForm() {
            document.getElementById('review-form-section').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
