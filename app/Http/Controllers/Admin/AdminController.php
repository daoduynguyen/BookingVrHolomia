<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\AdminLog;
use App\Models\Contact;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReplyContactMail;
use App\Exports\OrdersExport;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    public function index()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Khởi tạo các câu Query gốc
        $ordersQuery = Order::query();
        $ticketsQuery = Ticket::query();
        $usersQuery = User::where('role', 'customer');

        // 🔥 CHỐT CHẶN PHÂN QUYỀN
        if ($user && $user->role !== 'super_admin') {
            if ($user->location_id) {
                $ordersQuery->where('location_id', $user->location_id);
                $ticketsQuery->where('location_id', $user->location_id);
            } else {
                $ordersQuery->where('id', '<', 0);
                $ticketsQuery->where('id', '<', 0);
            }
        }

        // 1. TOP CARDS
        $totalRevenue = (clone $ordersQuery)->where('status', 'paid')->sum('total_amount');
        $pendingOrders = (clone $ordersQuery)->where('status', 'pending')->count();
        $totalTickets = (clone $ticketsQuery)->count();
        $totalUsers = $usersQuery->count();
        $completedOrders = (clone $ordersQuery)->where('status', 'completed')->count();
        $cancelledOrders = (clone $ordersQuery)->where('status', 'cancelled')->count();
        $refundedOrders = (clone $ordersQuery)->where('status', 'refunded')->count();

        // 2. DỮ LIỆU BIỂU ĐỒ CỘT & ĐƯỜNG (7 NGÀY QUA)
        $chartStats = (clone $ordersQuery)->where('status', 'paid')
            ->whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay(),
            ])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total'),
                DB::raw('COUNT(id) as order_count')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $orderCountData = $chartStats->pluck('order_count')->toArray(); // LẤY MẢNG SỐ LƯỢNG ĐƠN RA
        $revenueLabels = $chartStats->pluck('date')->toArray();
        $revenueData = $chartStats->pluck('total')->toArray();

        // 3. PIE CHART TRẠNG THÁI
        $pieData = [
            (clone $ordersQuery)->where('status', 'paid')->count(),
            (clone $ordersQuery)->where('status', 'pending')->count(),
            (clone $ordersQuery)->where('status', 'cancelled')->count(),
            (clone $ordersQuery)->where('status', 'completed')->count(),
        ];

        //  4. BẢNG XẾP HẠNG DOANH THU THEO CƠ SỞ (CHỈ SUPER ADMIN CÓ)
        $revenueByLocation = collect(); // Mặc định là rỗng (để Admin cơ sở không bị lỗi biến)
        $revenueMain = 0;
        if ($user && $user->role === 'super_admin') {
            $revenueByLocation = Order::where('status', 'paid')
                ->join('locations', 'orders.location_id', '=', 'locations.id')
                ->select('locations.name', DB::raw('SUM(orders.total_amount) as total'))
                ->groupBy('locations.id', 'locations.name')
                ->orderBy('total', 'desc') // Đứa nào doanh thu cao xếp lên đầu
                ->get();

            $revenueMain = Order::where('status', 'paid')
                ->whereNull('location_id')
                ->sum('total_amount');
        }

        return view('admin.dashboard', compact(
            'totalRevenue',
            'pendingOrders',
            'totalTickets',
            'totalUsers',
            'completedOrders',
            'cancelledOrders',
            'refundedOrders',
            'revenueLabels',
            'revenueData',
            'pieData',
            'revenueByLocation',
            'revenueMain', 
            'orderCountData'
        ));

    }

    public function manageTickets()
    {
        // Lấy toàn bộ danh sách vé từ Database
        $tickets = Ticket::orderBy('created_at', 'desc')->get();

        // Trả về view kèm dữ liệu vé
        return view('admin.tickets.index', compact('tickets'));
    }

    // Hiển thị form thêm mới
    public function createTicket()
    {
        return view('admin.tickets.create');
    }

    // Lưu dữ liệu vào Database
    public function storeTicket(Request $request)
    {
        // Xác thực dữ liệu
        $request->validate([
            'title' => 'required',
            'price' => 'required|numeric',
        ]);

        // Lưu vào bảng tickets
        Ticket::create($request->all());

        return redirect()->route('admin.tickets')->with('success', 'Đã thêm vé VR mới thành công!');
    }

    //Quản lý vé
    // Quản lý đơn hàng (Đã phân quyền theo cơ sở)
    public function manageOrders()
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        // Khởi tạo Query lấy danh sách đơn hàng
        $query = Order::with('user');

        // 🔥 CHỐT CHẶN: Nếu là Admin chi nhánh, ép chỉ lấy đơn của cơ sở đó
        if ($user && $user->role !== 'super_admin' && $user->location_id) {
            $query->where('location_id', $user->location_id);
        }

        // Thực thi truy vấn và trả về view
        $orders = $query->orderBy('created_at', 'desc')->get();
        return view('admin.orders.index', compact('orders'));
    }

    // Xuất Excel đơn hàng
    public function exportOrders(Request $request)
    {
        $filename = 'orders_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(
            new OrdersExport($request->input('start_date'), $request->input('end_date'), $request->input('location_id')),
            $filename
        );
    }

    //Quản lý người dùng
    public function manageUsers()
    {
        // Lấy tất cả user trừ chính Admin đang đăng nhập
        $users = User::where('id', '!=', auth()->id())->orderBy('id', 'desc')->get();
        // Lấy danh sách cơ sở để hiện trong form Phân quyền
        $locations = \App\Models\Location::all();

        return view('admin.users.index', compact('users', 'locations'));
    }

    // Xử lý xóa người dùng
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        AdminLog::record('delete_user', $user, ['name' => $user->name, 'email' => $user->email]);
        $user->delete();
        return back()->with('success', 'Đã xóa người dùng.');
    }

    // Khi ban user
    public function toggleBan($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->save();
        AdminLog::record($user->is_banned ? 'ban_user' : 'unban_user', $user, ['name' => $user->name]);
        return back()->with('success', $user->is_banned ? 'Đã khóa tài khoản.' : 'Đã mở khóa tài khoản.');
    }

    //Tin nhắn liên hệ 
    public function listContacts()
    {
        // Lấy danh sách tin nhắn, tin mới nhất xếp lên đầu
        $contacts = Contact::orderBy('created_at', 'desc')->get();

        // Trả về view kèm dữ liệu tin nhắn
        return view('admin.contacts.index', compact('contacts'));
    }

    // Xử lý xóa tin nhắn liên hệ
    public function deleteContact($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return back()->with('success', 'Đã xóa tin nhắn liên hệ thành công!');
    }

    //Phản hồi 
    public function replyContact(Request $request, $id)
    {
        $request->validate([
            'reply_content' => 'required|min:5'
        ]);

        $contact = Contact::findOrFail($id);

        // Gửi email
        Mail::to($contact->email)->send(new ReplyContactMail($request->reply_content, $contact->name));

        return redirect()->back()->with('success', 'Đã gửi phản hồi tới khách hàng thành công!');
    }

    // Xử lý Phân quyền Người dùng
    public function updateUserRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Cập nhật Role
        $user->role = $request->role;

        // Nếu là Admin chi nhánh thì bắt buộc phải gắn location_id, ngược lại thì xóa location_id
        if (in_array($request->role, ['branch_admin', 'staff'])) {
            $user->location_id = $request->location_id;
        } else {
            $user->location_id = null;
        }

        $user->save();

        return back()->with('success', 'Đã cập nhật phân quyền cho tài khoản: ' . $user->email);
    }

    // 1. Hiển thị Form thêm User
    public function createUser()
    {
        $locations = \App\Models\Location::all();
        return view('admin.users.create', compact('locations'));
    }

    // 2. Lưu User mới vào Database
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
           'location_id' => in_array($request->role, ['branch_admin', 'staff']) ? $request->location_id : null,
        ]);

        return redirect()->route('admin.users')->with('success', 'Đã thêm tài khoản mới thành công!');
    }

    // Hiển thị form sửa user
    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $locations = \App\Models\Location::all();
        return view('admin.users.edit', compact('user', 'locations'));
    }
 
    // Lưu thay đổi user
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
 
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8',
            'role'     => 'required',
        ]);
 
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->role  = $request->role;
        $user->location_id = in_array($request->role, ['branch_admin', 'staff']) ? $request->location_id : null;
 
        if ($request->filled('password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        }
 
        $user->save();
 
        AdminLog::record('update_user', $user, ['name' => $user->name, 'email' => $user->email]);
 
        return redirect()->route('admin.users')->with('success', 'Đã cập nhật tài khoản: ' . $user->name);
    }
}