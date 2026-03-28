<div class="search-bar-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0" style="border-radius: 20px; transform: translateY(-50%); background: rgba(243, 244, 246, 0.95); backdrop-filter: blur(10px);">
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('ticket.shop') }}" method="GET" class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;"><i class="bi bi-search text-info me-1"></i> TRÒ CHƠI BẠN MUỐN TÌM?</label>
                                <input type="text" name="keyword" class="form-control form-control-lg border-0 bg-white rounded-pill px-4 fw-bold text-dark w-100" placeholder="Nhập tên trò chơi..." value="{{ request('keyword') }}" style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); height: 52px !important;">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label d-none d-md-block mb-1" style="font-size: 0.8rem;">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="height: 52px; background: linear-gradient(135deg, #2563eb, #0ea5e9); border: none;">
                                    <i class="bi bi-search me-2"></i> TÌM KIẾM
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.search-bar-wrapper .form-control {
    color: #1f2937 !important;
    background-color: #f3f4f6 !important;
    transition: all 0.2s;
}
.search-bar-wrapper .form-control:focus {
    background-color: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
    outline: none;
}
</style>