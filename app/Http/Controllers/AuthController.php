<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            'password' => 'required|min:6|confirmed', // Cần input password_confirmation
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email này đã được đăng ký',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải từ 6 ký tự',
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

        return redirect()->route('home')->with('success', 'Đăng ký thành công! Chào mừng ' . $user->name);
    }

    // --- 2. ĐĂNG NHẬP ---
    public function showLogin()
    {
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

            // BẮT ĐẦU KIỂM TRA QUYỀN LỰC ĐỂ CHỈ ĐƯỜNG
            $userRole = Auth::user()->role;

            // Nếu là nhóm Admin (Sếp tổng hoặc Admin chi nhánh) -> Mời vào cửa VIP
            if (in_array($userRole, ['super_admin', 'admin', 'branch_admin'])) {
                return redirect()->route('admin.dashboard')->with('success', 'Chào mừng Quản trị viên trở lại!');
            }

            // Nếu là khách hàng bình thường -> Cho ra cửa hàng mua vé
            return redirect()->route('home')->with('success', 'Đăng nhập thành công!');
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
}