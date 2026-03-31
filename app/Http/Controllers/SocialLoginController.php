<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    /**
     * Chuyển hướng người dùng sang trang cấp quyền của nhà cung cấp (Google/Facebook)
     */
    public function redirect($provider)
    {
        // Các provider hợp lệ
        $validProviders = ['google', 'facebook'];

        if (!in_array($provider, $validProviders)) {
            return redirect()->route('login')->withErrors(['oauth' => 'Phương thức đăng nhập không được hỗ trợ.']);
        }

        return Socialite::driver($provider)->redirect();
    }

    /**
     * Nơi nhà cung cấp (Google/Facebook) redirect về kèm thông tin User
     */
    public function callback($provider)
    {
        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('provider_name', $provider)
                ->where('provider_id', $socialUser->getId())
                ->first();

            if ($user) {
                // ✅ Cập nhật avatar mỗi lần đăng nhập (phòng trường hợp Google đổi URL)
                $user->update([
                    'avatar' => $socialUser->getAvatar(),
                ]);

                Auth::login($user);
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            }

            $existingUser = User::where('email', $socialUser->getEmail())->first();

            if ($existingUser) {
                // ✅ Thêm avatar khi link tài khoản cũ với Google
                $existingUser->update([
                    'provider_name' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $existingUser->avatar ?: $socialUser->getAvatar(),
                    // Dùng ?: để không ghi đè nếu user đã upload ảnh thủ công
                ]);
                Auth::login($existingUser);
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            }

            // ✅ Lưu avatar khi tạo user mới
            $newUser = User::create([
                'name' => $socialUser->getName(),
                'email' => $socialUser->getEmail(),
                'provider_name' => $provider,
                'provider_id' => $socialUser->getId(),
                'password' => Hash::make(Str::random(24)),
                'role' => 'customer',
                'avatar' => $socialUser->getAvatar(),
            ]);

            Auth::login($newUser);
            return redirect()->intended(route('home'))->with('success', 'Tài khoản mới đã được tạo!');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['oauth' => 'Lỗi kết nối. Vui lòng thử lại.']);
        }
    }
}
