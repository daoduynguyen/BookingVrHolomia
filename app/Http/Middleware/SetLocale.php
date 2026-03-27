<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Các ngôn ngữ được hỗ trợ.
     */
    protected array $supportedLocales = ['vi', 'en', 'ja', 'zh', 'ko', 'hi'];

    /**
     * Ngôn ngữ mặc định khi không có gì được chọn.
     */
    protected string $defaultLocale = 'vi';

    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ưu tiên: locale từ query string (?lang=en) → lưu vào session
        if ($request->has('lang')) {
            $lang = $request->get('lang');
            if (in_array($lang, $this->supportedLocales)) {
                Session::put('locale', $lang);
                // Also save to user preference if logged in
                if (Auth::check()) {
                    Auth::user()->update(['language' => $lang]);
                }
            }
        }

        // 2. Đọc từ session nếu đã chọn trước đó
        $locale = Session::get('locale');

        // 3. Nếu chưa có session, kiểm tra user preference
        if (!$locale && Auth::check() && Auth::user()->language) {
            $locale = Auth::user()->language;
            Session::put('locale', $locale);
        }

        // 4. Fallback về ngôn ngữ mặc định nếu không hợp lệ
        if (!$locale || !in_array($locale, $this->supportedLocales)) {
            $locale = $this->defaultLocale;
        }

        // 5. Áp dụng cho toàn bộ request
        App::setLocale($locale);

        return $next($request);
    }
}
