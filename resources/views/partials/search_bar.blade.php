<div class="search-bar-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-lg border-0" style="border-radius: 20px; transform: translateY(-50%); background: rgba(243, 244, 246, 0.95); backdrop-filter: blur(10px);">
                    <div class="card-body p-3 p-md-4">
                        <form action="{{ route('ticket.shop') }}" method="GET" class="row g-3 align-items-center">
                            
                            {{-- Chọn Cơ sở --}}
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;"><i class="bi bi-geo-alt-fill text-danger me-1"></i> TRUNG TÂM VR</label>
                                <select name="location_id" class="form-select form-select-lg border-0 bg-white rounded-pill px-4 fw-bold text-dark w-100" style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                    <option value="">Tất cả cơ sở</option>
                                    @foreach($globalLocations ?? [] as $loc)
                                     @php /** @var \App\Models\Location $loc */ @endphp

                                        <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>
                                            {{ $loc->name }}
                                        </option>
                                    @endforeach
                                    </select>
                                    </div>

                            {{-- Chọn Thể loại --}}
                            <div class="col-md-4">
                                <label class="form-label text-dark fw-bold mb-1" style="font-size: 0.8rem;"><i class="bi bi-controller text-info me-1"></i> THỂ LOẠI GAME</label>
                                <select name="category_id" class="form-select form-select-lg border-0 bg-white rounded-pill px-4 fw-bold text-dark w-100" style="box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);">
                                    <option value="">Tất cả thể loại</option>
                                    {{-- For category we can hit DB or omit dynamically if not passed globally, but let's assume standard passing or leave it broad --}}
                                    @php
$categories = \App\Models\Category::all() ?? collect([]);
                                    @endphp
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Nút Tìm kiếm --}}
                            <div class="col-md-4 text-center text-md-end mt-4 mt-md-0 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill w-100 fw-bold shadow-sm d-flex align-items-center justify-content-center" style="height: 52px; background: linear-gradient(135deg, #2563eb, #0ea5e9); border: none;">
                                    <i class="bi bi-search me-2"></i> TÌM GAME NGAY
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
/* Đảm bảo Select box trong form tìm kiếm hiển thị đẹp ở Light Mode*/
.search-bar-wrapper .form-select {
    color: #1f2937 !important;
    background-color: #f3f4f6 !important;
    transition: all 0.2s;
}
.search-bar-wrapper .form-select:focus {
    background-color: #ffffff !important;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1) !important;
}
</style>
