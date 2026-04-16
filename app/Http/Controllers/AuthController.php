<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AuthController extends Controller
{
    // --- 1. ĐĂNG KÝ ---
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed', // Cần input password_confirmation
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email này đã được đăng ký',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải từ 8 ký tự',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp'
        ]);

        // Tạo user mới
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'customer' // Mặc định là khách hàng
        ]);

        // Đăng nhập luôn sau khi đăng ký
        Auth::login($user);

        return redirect()->route('home')
    ->with('success', 'Đăng ký thành công!');
    }


    // --- 2. ĐĂNG NHẬP ---
    public function showLogin()
    {
        // Lưu lại trang trước đó để sau khi login nhảy về đúng chỗ (VD: đang ở trang thanh toán)
        if (!session()->has('url.intended')) {
            $prevUrl = url()->previous();
            // Khách có thể bị đá từ trang khác sang login, ta lưu lại URL đó trừ chính trang login
            if (!str_contains($prevUrl, '/login') && !str_contains($prevUrl, '/register')) {
                session(['url.intended' => $prevUrl]);
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Nếu đăng nhập thành công (Đoạn này thường là if (Auth::attempt(...)) )
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (Auth::user()->is_banned) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ hỗ trợ: support@holomia.vn',
                ]);
            }

            // BẮT ĐẦU KIỂM TRA QUYỀN LỰC ĐỂ CHỈ ĐƯỜNG
            $userRole = Auth::user()->role;

            // Nếu là nhóm Admin (Sếp tổng hoặc Admin chi nhánh) -> Mời vào cửa VIP
            if (in_array($userRole, ['super_admin', 'admin', 'branch_admin'])) {
                return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Quản trị viên trở lại!');
            }

            // Nếu là nhân viên -> Chuyển hướng tới trang POS của chi nhánh
            if ($userRole === 'staff') {
                if (Auth::user()->location_id) {
                    $location = \App\Models\Location::find(Auth::user()->location_id);
                    if ($location && $location->slug) {
                        return redirect()->route('pos.dashboard', ['subdomain' => $location->slug])->with('success', 'Đăng nhập ca làm việc!');
                    }
                }
                
                // Nếu nhân viên chưa được gán chi nhánh
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'email' => 'Tài khoản nhân viên của bạn chưa được gán chi nhánh. Vui lòng liên hệ Admin.',
                ]);
            }

            // Nếu là khách hàng bình thường -> Quay lại trang trước đó hoặc ra trang chủ
            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'email' => 'Thông tin đăng nhập không chính xác.',
        ]);
    }
    // --- 3. ĐĂNG XUẤT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Đã đăng xuất.');
    }

    // --- 4. QUÊN MẬT KHẨU ---
    public function showForgotPassword()
    {
        return view('auth.forgot_password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.exists' => 'Email này chưa được đăng ký trong hệ thống',
        ]);

        // Tạo token ngẫu nhiên
        $token = Str::random(64);

        // Xóa token cũ nếu có, lưu token mới
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Gửi email chứa link reset
        $resetLink = url('/dat-lai-mat-khau/' . $token . '?email=' . urlencode($request->email));

        Mail::send('emails.reset_password', ['resetLink' => $resetLink], function ($m) use ($request) {
            $m->to($request->email)
                ->subject('Đặt lại mật khẩu - Holomia VR');
        });

        return back()->with('success', 'Đã gửi link đặt lại mật khẩu vào email của bạn!');
    }

    // --- 5. FORM ĐẶT LẠI MẬT KHẨU ---
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset_password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
            'token' => 'required',
        ], [
            'password.min' => 'Mật khẩu phải từ 8 ký tự',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp',
        ]);

        // Tìm token trong DB
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.']);
        }

        // Token hết hạn sau 60 phút
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Link đã hết hạn. Vui lòng gửi lại yêu cầu mới.']);
        }

        // Cập nhật mật khẩu mới
        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        // Xóa token sau khi dùng
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Mật khẩu đã được đặt lại! Vui lòng đăng nhập.');
    }
}