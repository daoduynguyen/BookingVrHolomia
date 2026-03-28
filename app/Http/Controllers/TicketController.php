<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\BookingDetail;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;

use Illuminate\Support\Facades\Session;

class TicketController extends Controller
{
    // ---------------------------------------------------------
    // 1. TRANG CHỦ: Hiển thị 6 vé HOT nhất (Cả Active & Maintenance)
    // ---------------------------------------------------------
    public function index()
    {
        $tickets = Ticket::with(['category', 'locations'])
            ->whereIn('status', ['active', 'maintenance'])
            ->orderBy('play_count', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('tickets'));
    }

    // ---------------------------------------------------------
    // 2. TRANG CỬA HÀNG: Hiển thị TẤT CẢ (Có phân trang)
    // ---------------------------------------------------------
    public function shop(Request $request)    {
        $query = Ticket::with(['category', 'locations'])
            ->whereIn('status', ['active', 'maintenance']);

        $locationName = "Tất cả vé";

        // ✅ ƯU TIÊN 1: Đọc cơ sở từ session (do khách đã chọn ở màn hình chọn cơ sở)
        $sessionLocation = Session::get('selected_location');

        if ($sessionLocation && $sessionLocation !== 'all') {
            $query->whereHas('locations', function ($q) use ($sessionLocation) {
                $q->where('locations.id', $sessionLocation);
            });

            $loc = Location::find($sessionLocation);
            if ($loc) {
                $locationName = "Vé tại " . $loc->name;
            }
        }

        // ✅ ƯU TIÊN 2: Nếu có ?location_id trên URL thì lọc thêm (dùng cho admin preview hoặc link trực tiếp)
        // Chỉ áp dụng khi session đang là 'all' hoặc chưa chọn
        elseif ($request->filled('location_id')) {
            $query->whereHas('locations', function ($q) use ($request) {
                $q->where('locations.id', $request->location_id);
            });

            $loc = Location::find($request->location_id);
            if ($loc) {
                $locationName = "Vé tại " . $loc->name;
            }
        }

        // Lọc tìm kiếm theo tên (keyword)
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $tickets = $query->orderBy('play_count', 'desc')
            ->paginate(9)
            ->appends($request->query());

        return view('shop', compact('tickets', 'locationName'));    }


    // ---------------------------------------------------------
    // 3. TRANG CHI TIẾT SẢN PHẨM
    // ---------------------------------------------------------
    public function show($id)
    {
        $ticket = Ticket::with(['category', 'locations'])->findOrFail($id);

        $related = Ticket::where('category_id', $ticket->category_id)
            ->where('id', '!=', $id)
            ->whereIn('status', ['active', 'maintenance'])
            ->limit(3)
            ->get();

        return view('detail', compact('ticket', 'related'));
    }

    // ---------------------------------------------------------
    // 4. XỬ LÝ ĐẶT VÉ (Đẩy vào giỏ hàng) - CÓ NHẬN LOẠI VÉ
    // ---------------------------------------------------------
    public function create(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status == 'maintenance') {
            return redirect()->route('ticket.show', $id)
                ->with('error', 'Trò chơi đang bảo trì, không thể đặt vé lúc này!');
        }

        // 🔥 Lấy tên loại vé khách vừa chọn (Từ nút Radio)
        $selectedType = $request->input('selected_ticket_type', 'Vé mặc định');

        // Chuyển hướng sang Giỏ hàng, kèm theo loại vé
        return redirect()->route('cart.add', [
            'id' => $id,
            'type' => $selectedType
        ]);
    }

    // ---------------------------------------------------------
    // 5. XỬ LÝ LƯU ĐƠN HÀNG (Dành cho mua trực tiếp không qua Giỏ hàng)
    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:15',
            'quantity' => 'required|integer|min:1|max:50',
            'selected_ticket_type' => 'nullable|string' // 🔥 Thêm validate loại vé
        ]);

        DB::beginTransaction();
        try {
            $ticket = Ticket::findOrFail($request->ticket_id);

            if ($ticket->status == 'maintenance') {
                return back()->with('error', 'Trò chơi này đang bảo trì!');
            }

            // 🔥 LOGIC TÍNH TIỀN MỚI: TÌM GIÁ THEO LOẠI VÉ (JSON)
            $pricePerTicket = $ticket->price; // Mặc định lấy giá gốc nếu có lỗi
            $selectedTypeName = $request->selected_ticket_type ?? 'Vé tiêu chuẩn';

            // Lục tìm trong mảng JSON xem khách chọn loại nào thì lấy giá loại đó
            if ($ticket->ticket_types && is_array($ticket->ticket_types)) {
                foreach ($ticket->ticket_types as $type) {
                    if ($type['name'] === $request->selected_ticket_type) {
                        $pricePerTicket = $type['price']; // Tìm thấy giá chuẩn!
                        $selectedTypeName = $type['name'];
                        break;
                    }
                }
            }

            $totalAmount = $pricePerTicket * $request->quantity;

            // Lưu Booking
            $booking = Booking::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id() ?? null,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'payment_method' => 'cod',
                'booking_date' => now(),
            ]);

            // Lưu Booking Detail (Có lưu kèm Tên loại vé)
            BookingDetail::create([
                'booking_id' => $booking->id,
                'ticket_id' => $ticket->id,
                'ticket_type' => $selectedTypeName, // 🔥 LƯU LOẠI VÉ ĐỂ SAU NÀY IN VÉ (Cần thêm cột ticket_type vào bảng booking_details nếu chưa có)
                'quantity' => $request->quantity,
                'price' => $pricePerTicket,
            ]);

            DB::commit();
            return redirect()->route('home')->with('success', 'Đặt vé thành công! Mã đơn: #' . $booking->id . ' - Tổng tiền: ' . number_format($totalAmount) . 'đ');

        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    // ---------------------------------------------------------
    // 6. CÁC TRANG TĨNH & AJAX
    // ---------------------------------------------------------
    public function about()
    {
        return view('about');
    }

    public function removeGalleryImage(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $imageToRemove = $request->input('image_path');
        $gallery = $ticket->gallery ?? [];

        if (($key = array_search($imageToRemove, $gallery)) !== false) {
            unset($gallery[$key]);

            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($imageToRemove)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($imageToRemove);
            }

            $ticket->gallery = array_values($gallery);
            $ticket->save();

            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Không tìm thấy ảnh']);
    }
}