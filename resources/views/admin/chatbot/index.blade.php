
@extends('layouts.admin')
@section('title', 'Quản lý Chatbot AI')

@section('admin_content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <div class="me-3" style="width:6px;height:40px;background:linear-gradient(180deg,#0d6efd,#3b82f6);border-radius:4px;"></div>
            <div>
                <h2 class="mb-0 fw-bold h4">Trung tâm học máy — Chatbot AI</h2>
                <div class="small text-muted">Quản lý dữ liệu huấn luyện, cache và knowledge base</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.chatbot.knowledge.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i> Thêm kiến thức
            </a>
            <a href="{{ route('admin.chatbot.cache.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-database me-1"></i> Quản lý Cache
            </a>
            <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-book me-1"></i> Knowledge Base
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['value' => $stats['total_cache'], 'label' => 'Tổng Q&A đã học', 'color' => 'info'],
                ['value' => $stats['pending_approval'], 'label' => 'Chờ duyệt', 'color' => 'danger'],
                ['value' => $stats['approved_cache'], 'label' => 'Đã duyệt', 'color' => 'success'],
                ['value' => number_format($stats['total_hits']), 'label' => 'Lượt cache hit', 'color' => 'warning'],
                ['value' => $stats['active_knowledge'], 'label' => 'Kiến thức active', 'color' => 'primary'],
                ['value' => $stats['total_knowledge'], 'label' => 'Tổng knowledge', 'color' => 'secondary'],
            ];
        @endphp
        @foreach($cards as $c)
        <div class="col-6 col-md-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-3">
                    <div class="fs-3 fw-bold text-{{ $c['color'] }}">{{ $c['value'] }}</div>
                    <div class="small text-muted">{{ $c['label'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row gy-4">
        {{-- Main column: pending & actions --}}
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
                    <div>
                        <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-clock-history me-2"></i> Câu hỏi chờ duyệt</h6>
                        <div class="small text-muted">Quản lý, duyệt nhanh hoặc chỉnh sửa trước khi đưa vào training</div>
                    </div>
                    <a href="{{ route('admin.chatbot.cache.index', ['approved' => 0]) }}" class="small">Xem tất cả →</a>
                </div>
                <div class="card-body p-0">
                    @forelse($pendingCache as $item)
                    <div class="px-4 py-3 border-bottom d-flex justify-content-between">
                        <div class="me-3" style="flex:1">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <div>
                                    <span class="badge bg-light text-dark me-2">{{ $item->category }}</span>
                                    <small class="text-muted">Hỏi {{ $item->ask_count }} lần</small>
                                </div>
                                <div class="text-end small text-muted">{{ $item->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="fw-semibold small mb-1">❓ {{ Str::limit($item->question, 160) }}</div>
                            <div class="small text-muted">🤖 {{ Str::limit($item->answer, 300) }}</div>
                        </div>
                        <div class="ms-3 d-flex flex-column align-items-end gap-2">
                            <form action="{{ route('admin.chatbot.cache.approve', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm btn-success" title="Duyệt">Duyệt</button>
                            </form>
                            <a href="{{ route('admin.chatbot.cache.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">Chỉnh sửa</a>
                            <form action="{{ route('admin.chatbot.cache.destroy', $item->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xoá câu trả lời này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xoá</button>
                            </form>
                        </div>
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

            {{-- Quick stats / actions row --}}
            <div class="d-flex gap-3 mb-4">
                <a href="{{ route('admin.chatbot.knowledge.create') }}" class="btn btn-outline-primary flex-grow-1">Thêm knowledge mới</a>
                <a href="{{ route('admin.chatbot.knowledge.index') }}" class="btn btn-outline-secondary">Quản lý knowledge</a>
                <a href="{{ route('admin.chatbot.cache.index') }}" class="btn btn-outline-secondary">Quản lý cache</a>
            </div>
        </div>

        {{-- Right column: insights + training data --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-fire me-2 text-danger"></i> Câu hỏi phổ biến</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($topAsked as $i => $item)
                    <div class="list-group-item border-0 px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-danger rounded-pill mt-1">{{ $i+1 }}</span>
                            <div>
                                <div class="small fw-semibold">{{ Str::limit($item->question, 70) }}</div>
                                <div class="text-muted" style="font-size:.75rem;">Hỏi {{ $item->ask_count }}x · Cache {{ $item->hit_count }}x
                                    @if($item->approved)
                                        <span class="text-success">✓</span>
                                    @else
                                        <span class="text-danger">⏳</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-lightning me-2 text-warning"></i> Cache hiệu quả</h6>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($topHit as $i => $item)
                    <div class="list-group-item border-0 px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <span class="badge bg-warning text-dark rounded-pill mt-1">{{ $item->hit_count }}</span>
                            <div class="small">{{ Str::limit($item->question, 65) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Training data panel --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="bi bi-gear-wide-connected me-2"></i> Dữ liệu huấn luyện</h6>
                        <div class="small text-muted">Mẫu dữ liệu QA để import vào hệ thống hoặc copy nhanh</div>
                    </div>
                    <div class="text-end">
                        <button id="copySampleBtn" class="btn btn-sm btn-outline-primary">Sao chép</button>
                    </div>
                </div>
                <div class="card-body p-3">
                    <pre id="sampleJson" class="small bg-light p-2 rounded" style="max-height:260px;overflow:auto;font-size:0.85rem;">[
  {
    "question": "Holomia VR mở cửa mấy giờ?",
    "answer": "Chúng tôi hoạt động từ 9:00 đến 21:00 hàng ngày tại các cơ sở.",
    "category": "Giờ mở cửa"
  },
  {
    "question": "Làm sao để đổi vé?",
    "answer": "Bạn có thể đổi vé tại quầy trong vòng 24 giờ trước giờ chơi, mang theo mã đơn.",
    "category": "Chính sách"
  }
]</pre>
                    <div class="d-flex gap-2 mt-2">
                        <a id="downloadSample" href="#" class="btn btn-sm btn-outline-secondary">Tải JSON</a>
                        <a href="{{ route('admin.chatbot.knowledge.create') }}" class="btn btn-sm btn-primary">Tạo knowledge từ mẫu</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function(){
        const copyBtn = document.getElementById('copySampleBtn');
        const samplePre = document.getElementById('sampleJson');
        const downloadLink = document.getElementById('downloadSample');

        if(copyBtn && samplePre){
            copyBtn.addEventListener('click', function(){
                const text = samplePre.textContent.trim();
                navigator.clipboard.writeText(text).then(() => {
                    copyBtn.innerText = 'Đã sao chép';
                    setTimeout(()=> copyBtn.innerText = 'Sao chép', 1800);
                }).catch(()=> alert('Không thể sao chép — vui lòng thử thủ công.'));
            });
        }

        if(downloadLink && samplePre){
            downloadLink.addEventListener('click', function(e){
                e.preventDefault();
                const data = samplePre.textContent.trim();
                const blob = new Blob([data], { type: 'application/json' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'holomia_chatbot_sample.json';
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(url);
            });
        }
    });
</script>
@endpush