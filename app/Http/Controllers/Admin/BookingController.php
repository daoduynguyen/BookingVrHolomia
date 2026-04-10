<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth; // Thêm thư viện Auth để check User
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderCompletedMail;

//Quản lý đơn hàng
class BookingController extends Controller
{
    // 1. Danh sách đơn hàng
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. TỰ ĐỘNG CẬP NHẬT TRẠNG THÁI "HẾT HẠN" CHO CÁC VÉ ĐÃ QUA GIỜ
        $now = \Carbon\Carbon::now();
        
        // Tối ưu: Chỉ check những đơn mà Admin này có quyền quản lý
        $ordersToCheckQuery = Order::with('slot')->whereIn('status', ['paid', 'pending']);
        if ($user->role !== 'super_admin' && $user->location_id) {
            $ordersToCheckQuery->where('location_id', $user->location_id);
        }
        $ordersToCheck = $ordersToCheckQuery->get();
        
        foreach($ordersToCheck as $o) {
            /** @var \App\Models\Order $o */
            if ($o->slot) {
                $playDate = \Carbon\Carbon::parse($o->booking_date)->format('Y-m-d');
                $playTime = \Carbon\Carbon::parse($playDate . ' ' . $o->slot->start_time);
                
                if ($now->greaterThan($playTime)) {
                    $o->status = 'expired';
                    $o->save();
                }
            }
        }

        // 2. KHỞI TẠO QUERY LẤY DỮ LIỆU VÀ LỌC
        $query = Order::with('orderItems');

        //  LOGIC PHÂN QUYỀN CƠ SỞ: 
        // Nếu không phải Sếp tổng (super_admin) VÀ có gắn location_id -> Chỉ lấy đơn của cơ sở đó
        if ($user->role !== 'super_admin' && $user->location_id) {
            $query->where('location_id', $user->location_id);
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $bookings = $query->latest()->paginate(10);

        return view('admin.bookings.index', compact('bookings'));
    }

    // 2. Cập nhật trạng thái
public function updateStatus(Request $request, $id) {
    $order = Order::with(['details.ticket', 'slot', 'location'])->findOrFail($id);
    $user = Auth::user();

    // BẢO MẬT: Chặn Admin chi nhánh sửa đơn của chi nhánh khác
    if ($user->role !== 'super_admin' && $user->location_id && $order->location_id !== $user->location_id) {
        return redirect()->back()->with('error', 'CẢNH BÁO: Bạn không có quyền thao tác trên đơn hàng của cơ sở khác!');
    }

    $oldStatus = $order->status;
    $newStatus = $request->status;

    $order->status = $newStatus;
    $order->save();

    // Lấy email an toàn cho cả user lẫn guest
    $recipientEmail = $order->customer_email ?? ($order->user ? $order->user->email : null);

    // ✅ THÊM MỚI: Email khi admin duyệt đơn (pending → paid)
    if ($newStatus === 'paid' && $oldStatus !== 'paid') {
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new \App\Mail\OrderStatusMail(
                    $order,
                    'Đã xác nhận',
                    'Đơn vé #' . $order->id . ' của bạn đã được xác nhận. Vui lòng xuất trình mã QR khi đến trải nghiệm.'
                ));
            } catch (\Exception $e) {
                \Log::error('Gửi email duyệt đơn thất bại (Order #' . $id . '): ' . $e->getMessage());
            }
        }
    }

    // ✅ THÊM MỚI: Email khi admin hủy đơn
    if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new \App\Mail\OrderStatusMail(
                    $order,
                    'Đã hủy',
                    'Đơn vé #' . $order->id . ' của bạn đã bị hủy. Nếu có thắc mắc, vui lòng liên hệ Holomia VR để được hỗ trợ.'
                ));
            } catch (\Exception $e) {
                \Log::error('Gửi email hủy đơn thất bại (Order #' . $id . '): ' . $e->getMessage());
            }
        }
    }

    // Gửi email + tăng play_count khi chuyển sang "Hoàn thành"
    if ($newStatus === 'completed' && $oldStatus !== 'completed') {
        foreach ($order->details as $item) {
            $ticket = \App\Models\Ticket::find($item->ticket_id);
            if ($ticket) {
                $ticket->increment('play_count', $item->quantity ?? 1);
            }
        }

        // ✅ SỬA: dùng $recipientEmail thay vì $order->user->email để tránh lỗi null
        if ($recipientEmail) {
            try {
                Mail::to($recipientEmail)->send(new OrderCompletedMail($order));
            } catch (\Exception $e) {
                \Log::error('Gửi email hoàn thành thất bại (Order #' . $id . '): ' . $e->getMessage());
            }
        }
    }

    return redirect()->back()->with('success', 'Đã cập nhật trạng thái đơn hàng #' . $id);
}

    // 3. Xóa đơn hàng
    public function destroy($id) {
        $order = Order::findOrFail($id);
        $user = Auth::user();

        // BẢO MẬT: Chặn Admin chi nhánh xóa đơn của chi nhánh khác
        if ($user->role !== 'super_admin' && $user->location_id && $order->location_id !== $user->location_id) {
            return redirect()->back()->with('error', 'CẢNH BÁO: Bạn không có quyền xóa đơn hàng của cơ sở khác!');
        }
        
        // Xóa chi tiết đơn hàng trước
        if ($order->details) {
            $order->details()->delete();
        }
        
        $order->delete();

        return redirect()->back()->with('success', 'Đã xóa đơn hàng!');
    }
}   