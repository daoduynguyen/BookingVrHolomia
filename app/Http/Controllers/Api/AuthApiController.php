<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    // ==========================================
    // ĐĂNG KÝ
    // ==========================================
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.unique' => 'Email này đã được đăng ký',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải từ 8 ký tự',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'customer',
        ]);

        // Tạo token Sanctum cho mobile
        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công!',
            'token' => $token,
            'user' => $this->formatUser($user),
        ], 201);
    }

    // ==========================================
    // ĐĂNG NHẬP
    // ==========================================
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Sai email hoặc mật khẩu
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đăng nhập không chính xác.',
            ], 401);
        }

        // Tài khoản bị khóa
        if ($user->is_banned) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ support@holomia.vn',
            ], 403);
        }

        // Xóa token cũ (đăng xuất thiết bị khác) nếu muốn chỉ 1 thiết bị
        // $user->tokens()->delete();

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công!',
            'token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    // ==========================================
    // ĐĂNG XUẤT
    // ==========================================
    public function logout(Request $request)
    {
        // Xóa token hiện tại đang dùng
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã đăng xuất.',
        ]);
    }

    // ==========================================
    // THÔNG TIN CÁ NHÂN
    // ==========================================
    public function profile(Request $request)
    {
        $user = $request->user()->load('orders');

        return response()->json([
            'success' => true,
            'data' => $this->formatUser($user),
        ]);
    }

    // ==========================================
    // CẬP NHẬT THÔNG TIN CÁ NHÂN
    // ==========================================
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
            'address' => 'sometimes|string|max:255',
            'gender' => 'sometimes|in:male,female,other',
            'birthday' => 'sometimes|date',
        ]);

        $user->update($request->only(['name', 'phone', 'address', 'gender', 'birthday']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công!',
            'data' => $this->formatUser($user->fresh()),
        ]);
    }

    // ==========================================
    // ĐỔI MẬT KHẨU
    // ==========================================
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không đúng.',
            ], 422);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công!',
        ]);
    }

    // ==========================================
    // HELPER — Format thông tin user trả về
    // ==========================================
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'address' => $user->address,
            'gender' => $user->gender,
            'birthday' => $user->birthday,
            'avatar' => $user->avatar
                ? asset('storage/' . $user->avatar)
                : null,
            'role' => $user->role,
            'balance' => (float) $user->balance,
            'points' => (int) $user->points,
            'tier' => $user->tier ?? 'bronze',
        ];
    }
}