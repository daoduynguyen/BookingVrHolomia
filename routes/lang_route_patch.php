<?php
// ============================================================
// THÊM VÀO routes/web.php — Route chuyển ngôn ngữ (tùy chọn)
// ============================================================
//
// Paste đoạn route dưới đây vào TRÊN CÙNG của web.php,
// sau phần use statements.
//
// Route này xử lý việc chuyển ngôn ngữ qua URL /lang/{locale}
// (ngoài cơ chế ?lang=xx đã có trong middleware)
// ============================================================

// Chuyển ngôn ngữ — ví dụ: /lang/en, /lang/ja
Route::get('/lang/{locale}', function (string $locale) {
    $supported = array_keys(config('i18n.supported', []));

    if (in_array($locale, $supported)) {
        session(['locale' => $locale]);
    }

    return redirect()->back()->withInput();
})->name('lang.switch');
