<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    // 1. Hiển thị trang Hồ sơ cá nhân (của USER, không phải Admin)
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Cập nhật trạng thái "hết hạn" cho đơn của user này
        $now = Carbon::now();
        Order::with('slot')
            ->where('user_id', $userId)
            ->whereIn('status', ['paid', 'pending'])
            ->get()
            ->each(function ($o) use ($now) {
                /** @var \App\Models\Order $o */
                if ($o->slot) {
                    $playTime = Carbon::parse(
                        Carbon::parse($o->booking_date)->format('Y-m-d') . ' ' . $o->slot->start_time
                    );
                    if ($now->greaterThan($playTime)) {
                        $o->status = 'expired';
                        $o->save();
                    }
                }
            });

        // Lấy đơn hàng của user, lọc theo status nếu có
        $query = Order::with('orderItems', 'slot')
            ->where('user_id', $userId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->latest()->paginate(10);

        // Lấy danh sách coupon còn hạn để hiển thị trong profile
        $coupons = Coupon::where('expiry_date', '>=', now())
            ->where('quantity', '>', 0)
            ->latest()
            ->get();

        $user = Auth::user();

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
            'language' => 'nullable|in:vi,en,ja,zh,ko,hi',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'language' => $request->language ?? 'vi',
        ]);

        return back()->with('success', 'Cập nhật hồ sơ thành công!');
    }

    // 2.5 Cập nhật đường dẫn Avatar
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('storage/avatars'), $filename);

            $user->avatar = asset('storage/avatars/' . $filename);
            $user->save();
        }

        return back()->with('success', 'Đã cập nhật ảnh đại diện thành công!');
    }

    // 3. Đổi mật khẩu
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ], [
            'new_password.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng']);
        }

        Auth::user()->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    // 4. Xem chi tiết 1 đơn hàng (AJAX popup)
    public function showOrder($id)
    {
        try {
            $order = Order::with(['orderItems.review', 'slot'])
                ->where('user_id', Auth::id())
                ->findOrFail($id);

            $html = view('profile.order_invoice', compact('order'))->render();
            return response()->json(['order' => $order, 'html' => $html]);

        } catch (\Exception $e) {
            // Log lỗi thật ra file log, KHÔNG trả về cho client
            Log::error('showOrder error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'order_id' => $id,
            ]);

            return response()->json([
                'html' => '<div class="alert alert-danger">Không thể tải thông tin đơn hàng. Vui lòng thử lại.</div>'
            ], 500);
        }
    }

    // 5. Scan vé (QR)
    public function scanTicket($id)
    {
        $order = Order::with('orderItems', 'slot')->findOrFail($id);

        // Chỉ admin và chủ đơn mới được xem
        $user = Auth::user();
        $isOwner = $user && $order->user_id === $user->id;
        $isAdmin = $user && in_array($user->role, ['admin', 'super_admin', 'branch_admin']);

        if (!$isOwner && !$isAdmin) {
            abort(403, 'Bạn không có quyền xem vé này.');
        }

        return view('profile.ticket_scan', compact('order'));
    }

    // Hàm xử lý chuyển đổi ngôn ngữ
    public function switchLanguage($locale)
    {
        // Danh sách 6 ngôn ngữ bạn muốn hỗ trợ
        $allowedLangs = ['vi', 'en', 'ko', 'zh', 'ja', 'hi'];

        if (in_array($locale, $allowedLangs)) {
            session()->put('locale', $locale);
        }

        return redirect()->back();
    }
}