<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TicketController extends Controller
{
    // Danh sách vé
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 🔥 CHỐT CHẶN 1: Nếu là Admin chi nhánh, chỉ lấy vé của chi nhánh đó
        if ($user->role !== 'super_admin' && $user->location_id) {
            $query->where('location_id', $user->location_id);
        }

        $tickets = $query->orderBy('id', 'desc')->paginate(5);
        $search = $request->input('search', '');

        return view('admin.tickets.index', compact('tickets', 'search'));
    }

    // Form thêm vé
    public function create()
    {
        $categories = Category::all();
        $locations = Location::all();
        return view('admin.tickets.create', compact('categories', 'locations'));
    }

    // --- HÀM LƯU DỮ LIỆU ---
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validate: 
        $request->validate([
            'name' => 'required',
            'location_ids' => 'nullable|array',
            'price' => 'required|numeric|min:0',
            'price_weekend' => 'nullable|numeric|min:0',
            'category_id' => 'required',
            'image_url' => 'required',
        ]);

        // 3. Lưu vé
        $ticket = new Ticket();
        $ticket->name = $request->name;
        $ticket->category_id = $request->category_id;
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

        if ($user->role !== 'super_admin' && $user->location_id) {
            $ticket->locations()->sync([$user->location_id]);
        } else {
            $ticket->locations()->sync($request->location_ids ?? []);
        }

        Cache::forget('home_tickets');

        return redirect()->route('admin.tickets.index')->with('success', 'Thêm vé thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        //  CHỐT CHẶN 3: Không cho phép Admin cơ sở khác chui vào Form Sửa
        if ($user->role !== 'super_admin' && $user->location_id && !$ticket->locations->contains('id', $user->location_id)) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền sửa vé của cơ sở khác!');
        }

        $categories = Category::all();
        $locations = Location::all();

        return view('admin.tickets.edit', compact('ticket', 'categories', 'locations'));
    }


    // Cập nhật
    public function update(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        //  CHỐT CHẶN 4: Chống hack qua URL khi bấm Update
        if ($user->role !== 'super_admin' && $user->location_id && !$ticket->locations->contains('id', $user->location_id)) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền cập nhật vé của cơ sở khác!');
        }

        // 1. Validate
        $request->validate([
            'name' => 'required',
            'location_ids' => 'nullable|array',
            'category_id' => 'required',
            'image_url' => 'required',
        ]);

        $oldDuration = $ticket->duration;

        // 3. Cập nhật dữ liệu
        $ticket->name = $request->name;
        $ticket->category_id = $request->category_id;
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

        // TỰ ĐỘNG TÁI TẠO LỊCH KHI DURATION THAY ĐỔI
        $newDuration = (int) $request->duration;
        if ($newDuration > 0 && (int) $oldDuration !== $newDuration) {

            $today = \Carbon\Carbon::today()->format('Y-m-d');

            // Xóa slot tương lai chưa có ai đặt
            \App\Models\TimeSlot::where('ticket_id', $ticket->id)
                ->where('date', '>=', $today)
                ->where('booked_count', 0)
                ->delete();

            // Làm tròn duration lên bội 5 tiếp theo
            $duration = (int) (floor($newDuration / 5) + 1) * 5;

            // Lấy ngày còn slot khách đặt để tái tạo đúng ngày đó
            $existingDates = \App\Models\TimeSlot::where('ticket_id', $ticket->id)
                ->where('date', '>=', $today)
                ->pluck('date')->unique()->toArray();

            // Tái tạo 7 ngày tới + các ngày đang có khách đặt
            $futureDates = [];
            for ($i = 0; $i < 7; $i++) {
                $futureDates[] = \Carbon\Carbon::today()->addDays($i)->format('Y-m-d');
            }
            $allDates = array_unique(array_merge($futureDates, $existingDates));
            $locationId = $ticket->locations->first()->id ?? 1;
            $dailyStart = '08:00:00';
            $dailyEnd = '21:00:00';

            foreach ($allDates as $date) {
                $currentTimeStr = $dailyStart;
                while (strtotime($currentTimeStr) < strtotime($dailyEnd)) {
                    $slotStart = \Carbon\Carbon::parse($currentTimeStr);
                    $slotEnd = (clone $slotStart)->addMinutes($duration);
                    if ($slotEnd->format('H:i:s') > $dailyEnd)
                        break;

                    $startTime = $slotStart->format('H:i:s');
                    $endTime = $slotEnd->format('H:i:s');

                    $exists = \App\Models\TimeSlot::where('ticket_id', $ticket->id)
                        ->where('date', $date)
                        ->where('start_time', $startTime)
                        ->exists();

                    if (!$exists) {
                        \App\Models\TimeSlot::create([
                            'ticket_id' => $ticket->id,
                            'location_id' => $locationId,
                            'date' => $date,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'capacity' => 15,
                            'status' => 'open',
                            'booked_count' => 0,
                        ]);
                    }
                    $currentTimeStr = $endTime;
                }
            }

            Cache::forget('home_tickets');

            return redirect()->route('admin.tickets.index')
                ->with('success', 'Cập nhật vé thành công! Đã tái tạo lịch theo thời lượng mới (' . $newDuration . ' phút → ca ' . $duration . ' phút).');
        }

        // Cập nhật locations
        if ($user->role !== 'super_admin' && $user->location_id) {
            $ticket->locations()->sync([$user->location_id]);
        } else {
            $ticket->locations()->sync($request->location_ids ?? []);
        }

        Cache::forget('home_tickets');

        return redirect()->route('admin.tickets.index')->with('success', 'Cập nhật vé thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        //  CHỐT CHẶN 5: Chống xóa trộm
        if ($user->role !== 'super_admin' && $user->location_id && !$ticket->locations->contains('id', $user->location_id)) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền xóa vé của cơ sở khác!');
        }

        $ticket->delete();

        Cache::forget('home_tickets');

        return back()->with('success', 'Đã xóa vé thành công!');
    }
}