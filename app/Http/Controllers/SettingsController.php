<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\App;

class SettingsController extends Controller
{
    // Hiển thị trang Settings
    public function index()
    {
        $user = Auth::user();

        // Chuyển đổi font_size cũ (small/medium/large) sang px nếu cần
        $ui = $user->ui_settings ?? [];
        if (isset($ui['font_size']) && !is_numeric($ui['font_size'])) {
            $legacyMap = ['small' => 13, 'medium' => 15, 'large' => 17];
            $ui['font_size'] = $legacyMap[$ui['font_size']] ?? 15;
            $user->update(['ui_settings' => $ui]);
        }

        return view('settings.index', compact('user'));
    }

    // Tab 1: Lưu giao diện (theme, font, màu)
    public function saveUI(Request $request)
    {
        $request->validate([
            'theme'         => 'required|in:light,dark,auto',
            'font'          => 'required|string|max:60',
            'font_size'     => 'required|integer|min:12|max:32',
            'primary_color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        Auth::user()->update([
            'ui_settings' => array_merge(Auth::user()->ui_settings ?? [], [
                'theme'         => $request->theme,
                'font'          => $request->font,
                'font_size'     => (int) $request->font_size,
                'primary_color' => $request->primary_color,
            ])
        ]);

        return back()->with('success', 'Đã lưu cài đặt giao diện!');
    }

    // Tab 2 (Bảo mật): Đổi mật khẩu
    public function savePassword(Request $request)
    {
        $user = Auth::user();

        // Nếu user đăng nhập bằng Google, không cho đổi mật khẩu
        if ($user->provider_name) {
            return back()->withErrors(['current_password' => 'Tài khoản của bạn sử dụng đăng nhập qua mạng xã hội, không thể đổi mật khẩu tại đây.']);
        }

        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required',
        ], [
            'new_password.min'       => 'Mật khẩu mới phải có ít nhất 8 ký tự.',
            'new_password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    // Tab 3: Lưu ngôn ngữ & tiền tệ
    public function saveLanguage(Request $request)
    {
        $request->validate([
            'language' => 'required|in:vi,en,ja,zh,ko,hi',
            'currency' => 'required|in:VND,USD,JPY,KRW',
        ]);

        Auth::user()->update([
            'language' => $request->language,
            'currency' => $request->currency,
        ]);

        // Override session locale ngay lập tức
        session()->forget('locale');
        session()->put('locale', $request->language);
        App::setLocale($request->language);

        return back()->with('success', 'Đã lưu ngôn ngữ và đơn vị tiền tệ!');
    }

    // Tab 4: Lưu thông báo
    public function saveNotifications(Request $request)
    {
        Auth::user()->update([
            'notification_prefs' => [
                'email_booking'   => $request->boolean('email_booking'),
                'email_promotion' => $request->boolean('email_promotion'),
                'remind_schedule' => $request->boolean('remind_schedule'),
                'inapp_alerts'    => $request->boolean('inapp_alerts'),
            ]
        ]);

        return back()->with('success', 'Đã lưu cài đặt thông báo!');
    }

    // Tab 5: Lưu quyền riêng tư
    public function savePrivacy(Request $request)
    {
        Auth::user()->update([
            'privacy_settings' => [
                'hide_balance' => $request->boolean('hide_balance'),
                'hide_history' => $request->boolean('hide_history'),
            ]
        ]);

        return back()->with('success', 'Đã lưu cài đặt quyền riêng tư!');
    }

    // Tab 6: Lưu thanh toán mặc định
    public function savePayment(Request $request)
    {
        $request->validate([
            'default_payment' => 'required|in:wallet,banking,cod',
        ]);

        Auth::user()->update(['default_payment' => $request->default_payment]);

        return back()->with('success', 'Đã lưu phương thức thanh toán mặc định!');
    }

    // Tab 7: Lưu trợ năng (gộp vào ui_settings)
    public function saveAccessibility(Request $request)
    {
        $user = Auth::user();
        $ui = $user->ui_settings ?? [];

        $user->update([
            'ui_settings' => array_merge($ui, [
                'high_contrast' => $request->boolean('high_contrast'),
                'reduce_motion' => $request->boolean('reduce_motion'),
                'large_text'    => $request->boolean('large_text'),
            ])
        ]);

        return back()->with('success', 'Đã lưu cài đặt trợ năng!');
    }

    // Tab 8 (Tài khoản): Hủy liên kết mạng xã hội
    public function unlinkSocial(Request $request)
    {
        $user = Auth::user();

        // Bắt buộc phải có mật khẩu riêng trước khi hủy liên kết
        if (!$user->password) {
            return back()->withErrors(['unlink' => 'Vui lòng thiết lập mật khẩu trước khi hủy liên kết.']);
        }

        $provider = $user->provider_name;
        $user->update([
            'provider_name' => null,
            'provider_id'   => null,
        ]);

        return back()->with('success', 'Đã hủy liên kết tài khoản ' . ucfirst($provider) . '.');
    }

    // Tab 2 (Bảo mật): Đăng xuất tất cả thiết bị
    public function logoutAllDevices(Request $request)
    {
        if (method_exists(Auth::user(), 'tokens')) {
            Auth::user()->tokens()->delete();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Đã đăng xuất khỏi tất cả thiết bị!');
    }

    // Tab 8 (Tài khoản): Xóa tài khoản
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirm_delete' => 'required|in:XOA_TAI_KHOAN',
        ], [
            'confirm_delete.in' => 'Vui lòng nhập chính xác "XOA_TAI_KHOAN" để xác nhận.',
        ]);

        $user = Auth::user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Tài khoản của bạn đã được xóa vĩnh viễn.');
    }
}