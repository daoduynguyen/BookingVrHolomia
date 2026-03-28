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
            
            // Tìm user dựa trên provider_id và provider_name
            $user = User::where('provider_name', $provider)
                        ->where('provider_id', $socialUser->getId())
                        ->first();

            if ($user) {
                // Đã từng đăng nhập bằng MXH này
                Auth::login($user);
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            }

            // Nếu user chưa tồn tại dựa theo provider_id, ta kiểm tra xem Email này đã tồn tại chưa
            $existingUser = User::where('email', $socialUser->getEmail())->first();
            
            if ($existingUser) {
                // Email đã được dùng (ví dụ đăng ký thủ công). Cập nhật thêm provider_id vào tài khoản này
                $existingUser->update([
                    'provider_name' => $provider,
                    'provider_id'   => $socialUser->getId(),
                ]);
                Auth::login($existingUser);
                return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
            }

            // Nếu hoàn toàn là người mới, tạo mới User
            $newUser = User::create([
                'name'          => $socialUser->getName(),
                'email'         => $socialUser->getEmail(),
                'provider_name' => $provider,
                'provider_id'   => $socialUser->getId(),
                // Tạo một password ngẫu nhiên và mã hoá vì cột password bắt buộc (not null)
                'password'      => Hash::make(Str::random(24)),
                'role'          => 'customer',
            ]);

            Auth::login($newUser);
            return redirect()->intended(route('home'))->with('success', 'Tài khoản mới đã được tạo và đăng nhập thành công!');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['oauth' => 'Lỗi kết nối đến tài khoản Mạng xã hội của bạn. Vui lòng thử lại.']);
        }
    }
}
