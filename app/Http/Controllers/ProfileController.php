<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\Coupon;

//Hồ sơ cá nhân
class ProfileController extends Controller
{
    // 1. Hiển thị trang Hồ sơ chung
    public function index()
    {
        $user = Auth::user();

        // Lấy đơn hàng (Code cũ của bạn)
        $orders = Order::where('user_id', $user->id)->with('slot')->orderBy('created_at', 'desc')->get();

        // 2. Lấy Voucher đang hoạt động từ Admin
        $coupons = Coupon::where('expiry_date', '>=', now()) // Chỉ lấy mã còn hạn
            ->orderBy('id', 'desc')
            ->get();

        // 3. Truyền biến $coupons sang View
        return view('profile.index', compact('user', 'orders', 'coupons'));
    }

    // 2. Cập nhật thông tin cá nhân
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
        ]);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');

    }

    // 3. Đổi mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'Mật khẩu nhập lại không khớp.'
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    public function showOrder($id)
    {
        try {
            // 1. Lấy thông tin đơn hàng
            $order = Order::with('orderItems','slot')
                ->where('user_id', operator: Auth::id())
                ->findOrFail($id);
           

            // 2. Render HTML
            // KIỂM TRA KỸ ĐƯỜNG DẪN FILE VIEW
            $html = view('profile.order_invoice', compact('order'))->render();  

            return response()->json(['order' => $order,'html' => $html ]);

        } catch (\Exception $e) {
            // Trả về lỗi chi tiết để hiện lên Popup
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}