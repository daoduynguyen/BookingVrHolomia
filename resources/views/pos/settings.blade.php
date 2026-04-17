@extends('pos.layout')

@section('title', 'Cài đặt giao diện — Holomia POS')
@section('page-title', 'Cài đặt giao diện')

@section('styles')
<style>
    .settings-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 24px; }
    
    .theme-card {
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid transparent;
        position: relative;
    }
    .theme-card:hover { transform: translateY(-3px); }
    .theme-card.active { border-color: var(--pos-primary); background: rgba(30, 136, 229, 0.05); }

    .theme-preview {
        height: 120px;
        border-radius: 8px;
        margin-bottom: 12px;
        display: flex;
        overflow: hidden;
        border: 1px solid var(--pos-card-border);
    }
    .theme-preview .prev-sidebar { width: 30px; }
    .theme-preview .prev-content { flex: 1; padding: 10px; }

    /* Preset Colors */
    .color-presets { display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px; margin-top: 10px; }
    .color-btn {
        width: 100%; height: 40px; border-radius: 8px; border: 2px solid transparent;
        transition: all 0.2s; cursor: pointer;
    }
    .color-btn:hover { transform: scale(1.1); }
    .color-btn.active { border-color: #fff; box-shadow: 0 0 10px rgba(255,255,255,0.3); }

    .check-mark {
        position: absolute; top: 10px; right: 10px;
        background: var(--pos-primary); color: #fff;
        width: 22px; height: 22px; border-radius: 50%;
        display: none; align-items: center; justify-content: center;
        font-size: 0.8rem;
    }
    .theme-card.active .check-mark { display: flex; }
</style>
@endsection

@section('content')

<form action="{{ route('pos.settings.update', $subdomain) }}" method="POST">
    @csrf
    
    <div class="settings-grid">
        
        {{-- CHẾ ĐỘ GIAO DIỆN --}}
        <div class="pos-card shadow-sm">
            <h3 class="pos-card-title"><i class="bi bi-palette me-2"></i>Chế độ hiển thị</h3>
            
            <input type="hidden" name="pos_theme" id="input-theme" value="{{ $uiSettings['pos_theme'] ?? 'dark' }}">
            
            <div class="row g-3">
                <div class="col-6">
                    <div class="theme-card pos-card p-3 {{ ($uiSettings['pos_theme'] ?? 'dark') == 'dark' ? 'active' : '' }}" 
                         onclick="selectTheme('dark')">
                        <div class="check-mark"><i class="bi bi-check-lg"></i></div>
                        <div class="theme-preview">
                            <div class="prev-sidebar" style="background:#152342; border-right:1px solid #1e2d5a"></div>
                            <div class="prev-content" style="background:#0d1b3e">
                                <div style="height:6px;width:60%;background:#1e2d5a;margin-bottom:4px"></div>
                                <div style="height:6px;width:40%;background:#1e2d5a"></div>
                            </div>
                        </div>
                        <div class="text-center fw-bold small text-uppercase">Tối (Dark)</div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="theme-card pos-card p-3 {{ ($uiSettings['pos_theme'] ?? 'dark') == 'light' ? 'active' : '' }}" 
                         onclick="selectTheme('light')">
                        <div class="check-mark"><i class="bi bi-check-lg"></i></div>
                        <div class="theme-preview">
                            <div class="prev-sidebar" style="background:#ffffff; border-right:1px solid #d1d5db"></div>
                            <div class="prev-content" style="background:#f0f4f8">
                                <div style="height:6px;width:60%;background:#d1d5db;margin-bottom:4px"></div>
                                <div style="height:6px;width:40%;background:#d1d5db"></div>
                            </div>
                        </div>
                        <div class="text-center fw-bold small text-uppercase">Sáng (Light)</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MÀU CHỦ ĐẠO --}}
        <div class="pos-card shadow-sm">
            <h3 class="pos-card-title"><i class="bi bi-droplet-fill me-2"></i>Màu sắc chủ đạo</h3>
            
            <div class="pos-form-group">
                <label>Chọn màu tùy chỉnh</label>
                <div class="d-flex gap-3 align-items-center">
                    <input type="color" name="pos_primary_color" id="input-color" 
                           value="{{ $uiSettings['pos_primary_color'] ?? '#1e88e5' }}" 
                           class="form-control form-control-color p-1" 
                           style="width: 60px; height: 45px;"
                           oninput="applyLiveColor(this.value)">
                    <div class="flex-grow-1">
                        <input type="text" id="color-hex" class="form-control text-uppercase" 
                               value="{{ $uiSettings['pos_primary_color'] ?? '#1E88E5' }}" 
                               readonly style="background:rgba(255,255,255,0.05); border-color:var(--pos-card-border)">
                    </div>
                </div>
            </div>

            <label class="mt-2 text-muted small">Màu gợi ý chuyên nghiệp</label>
            <div class="color-presets">
                @php
                    $presets = [
                        '#1e88e5' => 'Holomia Blue',
                        '#7c3aed' => 'Purple',
                        '#06b6d4' => 'Cyan',
                        '#10b981' => 'Emerald',
                        '#ef4444' => 'Ruby',
                        '#f59e0b' => 'Amber',
                        '#ec4899' => 'Pink',
                        '#6366f1' => 'Indigo',
                        '#475569' => 'Slate',
                        '#0f172a' => 'Navy'
                    ];
                @endphp
                @foreach($presets as $hex => $name)
                    <div class="color-btn" style="background:{{ $hex }}" 
                         title="{{ $name }}"
                         onclick="selectPreset('{{ $hex }}')"></div>
                @endforeach
            </div>
        </div>
        
    </div>

    <div class="pos-card mt-4 d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            <i class="bi bi-info-circle me-1"></i>
            Thay đổi sẽ được áp dụng cho toàn bộ các trang POS trên tài khoản này.
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn-pos-outline" onclick="resetToDefault()">Mặc định Holomia</button>
            <button type="submit" class="btn-pos px-5">
                <i class="bi bi-save me-1"></i> Lưu cài đặt
            </button>
        </div>
    </div>
</form>

@endsection

@section('scripts')
<script>
    function selectTheme(theme) {
        document.getElementById('input-theme').value = theme;
        document.querySelectorAll('.theme-card').forEach(c => c.classList.remove('active'));
        event.currentTarget.classList.add('active');
        
        // Gợi ý cho người dùng reload để áp dụng theme hoàn chỉnh
        // Hoặc có thể thay đổi body class nếu muốn live preview theme
    }

    function selectPreset(hex) {
        document.getElementById('input-color').value = hex;
        document.getElementById('color-hex').value = hex.toUpperCase();
        applyLiveColor(hex);
    }

    function applyLiveColor(hex) {
        document.getElementById('color-hex').value = hex.toUpperCase();
        document.documentElement.style.setProperty('--pos-primary', hex);
        document.querySelectorAll('.logo span').forEach(el => el.style.background = 'none');
        document.querySelectorAll('.logo span').forEach(el => el.style.webkitTextFillColor = hex);
    }

    function resetToDefault() {
        if(confirm('Bạn có muốn khôi phục về giao diện mặc định (Holomia Blue / Dark)?')) {
            selectTheme('dark');
            selectPreset('#1e88e5');
        }
    }
</script>
@endsection
