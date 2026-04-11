
@extends('layouts.admin')
@section('title', 'Quản lý Chatbot AI')

@section('admin_content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="bg-info me-3" style="width:50px;height:3px;"></div>
            <h2 class="text-info fw-bold text-uppercase mb-0">Trung tâm học máy Chatbot</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.chatbot.knowledge.create') }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Thêm kiến thức
            </a>
            <a href="{{ route('admin.chatbot.cache') }}" class="btn btn-outline-info btn-sm">
                <i class="bi bi-database me-1"></i> Quản lý Cache
            </a>
            <a href="{{ route('admin.chatbot.knowledge') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-book me-1"></i> Knowledge Base
            </a>
        </div>
    </div>

    {{-- Thống kê --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-info">{{ $stats['total_cache'] }}</div>
                <div class="small text-muted">Tổng Q&A đã học</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center py-3" style="border-left:4px solid #dc3545!important;">
                <div class="fs-2 fw-bold text-danger">{{ $stats['pending_approval'] }}</div>
                <div class="small text-muted">Chờ duyệt</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-success">{{ $stats['approved_cache'] }}</div>
                <div class="small text-muted">Đã duyệt</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-warning">{{ number_format($stats['total_hits']) }}</div>
                <div class="small text-muted">Lượt cache hit</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-primary">{{ $stats['active_knowledge'] }}</div>
                <div class="small text-muted">Kiến thức active</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="fs-2 fw-bold text-secondary">{{ $stats['total_knowledge'] }}</div>
                <div class="small text-muted">Tổng knowledge</div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Chờ duyệt --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h6 class="fw-bold mb-0 text-danger">
                        <i class="bi bi-clock-history me-2"></i>Câu hỏi chờ duyệt
                        @if($stats['pending_approval'] > 0)
                            <span class="badge bg-danger ms-1">{{ $stats['pending_approval'] }}</span>
                        @endif
                    </h6>
                    <a href="{{ route('admin.chatbot.cache', ['approved' => 0]) }}" class="small text-muted">Xem tất cả →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($pendingCache as $item)
                    <div class="border-bottom px-4 py-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-light text-dark me-1">{{ $item->category }}</span>
                                <span class="small text-muted">Hỏi {{ $item->ask_count }} lần</span>
                            </div>
                            <div class="d-flex gap-2">
                                {{-- Duyệt nhanh --}}
                                <form action="{{ route('admin.chatbot.cache.approve', $item->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-success btn-sm py-0 px-2" title="Duyệt">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                                <a href="{{ route('admin.chatbot.cache.edit', $item->id) }}" class="btn btn-outline-primary btn-sm py-0 px-2" title="Chỉnh sửa">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('admin.chatbot.cache.destroy', $item->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xoá câu trả lời này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger btn-sm py-0 px-2" title="Xoá">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="fw-semibold small mb-1 text-dark">❓ {{ Str::limit($item->question, 100) }}</div>
                        <div class="small text-muted">🤖 {{ Str::limit($item->answer, 150) }}</div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-check-circle fs-2 text-success d-block mb-2"></i>
                        Không có câu hỏi nào chờ duyệt 🎉
                    </div>
                    @endforelse
                </div>
                @if($pendingCache->hasPages())
                <div class="card-footer bg-white border-0">
                    {{ $pendingCache->links() }}
                </div>
                @endif
            </div>
        </div>

        {{-- Top hỏi nhiều + cache hit --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-fire me-2 text-danger"></i>Câu hỏi phổ biến nhất</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($topAsked as $i => $item)
                    <div class="list-group-item border-0 px-4 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-danger rounded-pill mt-1">{{ $i+1 }}</span>
                            <div>
                                <div class="small fw-semibold">{{ Str::limit($item->question, 60) }}</div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    Hỏi {{ $item->ask_count }}x · Cache {{ $item->hit_count }}x
                                    @if($item->approved)
                                        <span class="text-success">✓ Đã duyệt</span>
                                    @else
                                        <span class="text-danger">⏳ Chờ duyệt</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-lightning me-2 text-warning"></i>Cache hiệu quả nhất</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($topHit as $i => $item)
                    <div class="list-group-item border-0 px-4 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-warning text-dark rounded-pill mt-1">{{ $item->hit_count }}</span>
                            <div class="small">{{ Str::limit($item->question, 65) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

</div>
@endsection