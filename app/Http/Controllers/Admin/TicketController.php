<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    // Danh sách vé
    public function index()
    {
        $user = Auth::user();
        $query = Ticket::query();

        // 🔥 CHỐT CHẶN 1: Nếu là Admin chi nhánh, chỉ lấy vé của chi nhánh đó
        if ($user->role !== 'super_admin' && $user->location_id) {
            $query->where('location_id', $user->location_id);
        }

        $tickets = $query->orderBy('id', 'desc')->paginate(5); 

        return view('admin.tickets.index', compact('tickets'));
    }

    // Form thêm vé
    public function create()
    {
        $categories = Category::all();
        $locations  = Location::all();
        return view('admin.tickets.create', compact('categories', 'locations'));
    }

    // --- HÀM LƯU DỮ LIỆU ---
    public function store(Request $request) {
        $user = Auth::user();

        // 1. Validate: 
        $request->validate([
            'name' => 'required',
            'location_name' => 'nullable|string', // Sửa thành nullable để Admin chi nhánh ko bị lỗi nếu ẩn trường này
            'price' => 'required|numeric|min:0',  
            'price_weekend' => 'nullable|numeric|min:0', 
            'category_id' => 'required',
            'image_url' => 'required',
        ]);

        // 🔥 CHỐT CHẶN 2: Ép Cơ sở cho Admin chi nhánh
        if ($user->role !== 'super_admin' && $user->location_id) {
            $assigned_location_id = $user->location_id;
        } else {
            // Logic Tự động tạo cơ sở (Dành cho Sếp tổng)
            $location = Location::firstOrCreate(
                ['name' => $request->location_name], 
                [
                    'address' => 'Đang cập nhật',    
                    'hotline' => '0909000000',       
                ]
            );
            $assigned_location_id = $location->id;
        }

        // 3. Lưu vé
        $ticket = new Ticket();
        $ticket->name = $request->name;
        $ticket->category_id = $request->category_id;
        $ticket->location_id = $assigned_location_id; // 🔥 Sử dụng biến đã xử lý
        $ticket->image_url = $request->image_url;
        $ticket->duration = $request->duration;
        $ticket->status = $request->status;

        // Lưu 2 loại giá
        $ticket->price = $request->price; 
        $ticket->price_weekend = $request->price_weekend; 
        
        $ticket->trailer_url = $request->trailer_url;
        if ($request->hasFile('gallery')) {
            $imagePaths = [];
            foreach ($request->file('gallery') as $file) {
                $imagePaths[] = $file->store('tickets', 'public');
            }
            $ticket->gallery = $imagePaths; // Sẽ tự động cast sang JSON nhờ Model
        }
        
        $ticket_types = [];
        if ($request->has('type_name') && $request->has('type_price')) {
            foreach ($request->type_name as $key => $name) {
                // Chỉ lưu nếu Admin có nhập cả Tên và Giá
                if (!empty($name) && !empty($request->type_price[$key])) {
                    $ticket_types[] = [
                        'name' => $name,
                        'price' => $request->type_price[$key]
                    ];
                }
            }
        }
        $ticket->ticket_types = $ticket_types;

        $ticket->save();

        return redirect()->route('admin.tickets.index')->with('success', 'Thêm vé thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $ticket     = Ticket::findOrFail($id);
        $user = Auth::user();

        // 🔥 CHỐT CHẶN 3: Không cho phép Admin cơ sở khác chui vào Form Sửa
        if ($user->role !== 'super_admin' && $user->location_id && $ticket->location_id !== $user->location_id) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền sửa vé của cơ sở khác!');
        }

        $categories = Category::all();
        $locations  = Location::all();

        return view('admin.tickets.edit', compact('ticket', 'categories', 'locations'));
    }

    
    // Cập nhật
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // 🔥 CHỐT CHẶN 4: Chống hack qua URL khi bấm Update
        if ($user->role !== 'super_admin' && $user->location_id && $ticket->location_id !== $user->location_id) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền cập nhật vé của cơ sở khác!');
        }

        // 1. Validate
        $request->validate([
            'name' => 'required',
            'location_name' => 'nullable|string', 
            'category_id' => 'required',
            'image_url' => 'required',
        ]);

        // 🔥 Xử lý ép Cơ sở (Tương tự hàm Store)
        if ($user->role !== 'super_admin' && $user->location_id) {
            $assigned_location_id = $user->location_id;
        } else {
            $location = Location::firstOrCreate(
                ['name' => $request->location_name],
                [
                    'address' => 'Đang cập nhật',
                    'hotline' => '0909000000' 
                ]
            );
            $assigned_location_id = $location->id;
        }

        // 3. Cập nhật dữ liệu
        $ticket->name = $request->name;
        $ticket->category_id = $request->category_id;
        $ticket->location_id = $assigned_location_id; // 🔥 Gắn ID mới
        $ticket->image_url = $request->image_url;
        $ticket->duration = $request->duration;
        $ticket->status = $request->status;
        $ticket->description = $request->description; 
        
        // 🔥 LOGIC MỚI: XỬ LÝ MẢNG JSON VÀ TỰ ĐỘNG GÁN GIÁ GỐC
        $ticket_types = [];
        $base_price = 0; // Khởi tạo giá gốc mặc định
        
        $ticket->trailer_url = $request->trailer_url;

        if ($request->hasFile('gallery')) {
            $imagePaths = $ticket->gallery ?? []; // Giữ lại ảnh cũ
            foreach ($request->file('gallery') as $file) {
                $imagePaths[] = $file->store('tickets', 'public');
            }
            $ticket->gallery = $imagePaths;
        }
        
        $ticket_types = [];
        if ($request->has('type_name') && $request->has('type_price')) {
            foreach ($request->type_name as $key => $name) {
                // Chỉ lưu nếu Admin có nhập cả Tên và Giá
                if (!empty($name) && !empty($request->type_price[$key])) {
                    $ticket_types[] = [
                        'name' => $name,
                        'price' => $request->type_price[$key]
                    ];
                }
            }
        }
        $ticket->ticket_types = $ticket_types;

        $ticket->save();

        return redirect()->route('admin.tickets.index')->with('success', 'Cập nhật vé thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        // 🔥 CHỐT CHẶN 5: Chống xóa trộm
        if ($user->role !== 'super_admin' && $user->location_id && $ticket->location_id !== $user->location_id) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền xóa vé của cơ sở khác!');
        }

        $ticket->delete();
        
        return back()->with('success', 'Đã xóa vé thành công!');
    }
}