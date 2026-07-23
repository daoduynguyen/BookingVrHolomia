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

        // Validate — price là nullable vì có thể lấy từ ticket_types
        $request->validate([
            'name'          => 'required',
            'location_ids'  => 'nullable|array',
            'price'         => 'nullable|numeric|min:0',
            'price_weekend' => 'nullable|numeric|min:0',
            'category_id'   => 'required',
            'image_url'     => 'required',
            'rules'         => 'nullable|string',
            'notes'         => 'nullable|string',
        ]);

        // Xử lý ticket_types trước để lấy giá nếu admin không điền price riêng
        $ticket_types = [];
        if ($request->has('type_name') && $request->has('type_price')) {
            foreach ($request->type_name as $key => $name) {
                if (!empty($name) && !empty($request->type_price[$key])) {
                    $ticket_types[] = [
                        'name'  => $name,
                        'price' => (int) $request->type_price[$key],
                    ];
                }
            }
        }

        // Tính giá: ưu tiên field price, nếu trống thì lấy giá thấp nhất từ ticket_types
        $price = $request->price;
        if (empty($price) && !empty($ticket_types)) {
            $price = min(array_column($ticket_types, 'price'));
        }
        $price = (int) ($price ?? 0);

        $price_weekend = $request->price_weekend;
        if (empty($price_weekend)) {
            $price_weekend = $price; // mặc định bằng giá thường nếu không nhập
        }
        $price_weekend = (int) $price_weekend;

        // Xác định location_id chính
        $locationIds = $request->location_ids ?? [];
        if ($user->role !== 'super_admin' && $user->location_id) {
            $locationIds = [$user->location_id];
        }
        $primaryLocationId = $locationIds[0] ?? null;

        // Lưu vé
        $ticket = new Ticket();
        $ticket->name          = $request->name;
        $ticket->category_id   = $request->category_id;
        $ticket->location_id   = $primaryLocationId; // tránh lỗi NOT NULL
        $ticket->image_url     = $request->image_url;
        $ticket->duration      = $request->duration;
        $ticket->status        = $request->status ?? 'active';
        $ticket->price         = $price;
        $ticket->price_weekend = $price_weekend;
        $ticket->description   = $request->description;
        $ticket->rules         = $request->rules;
        $ticket->notes         = $request->notes;
        $ticket->trailer_url   = $request->trailer_url;
        $ticket->ticket_types  = $ticket_types;

        if ($request->hasFile('gallery')) {
            $imagePaths = [];
            foreach ($request->file('gallery') as $file) {
                $imagePaths[] = $file->store('tickets', 'public');
            }
            $ticket->gallery = $imagePaths;
        }

        $ticket->save();

        // Gán quan hệ nhiều-nhiều với locations
        $ticket->locations()->sync($locationIds);

        Cache::forget('home_tickets');

        return redirect()->route('admin.tickets.index')->with('success', 'Thêm vé thành công!');
    }

    // Form sửa
    public function edit($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

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

        if ($user->role !== 'super_admin' && $user->location_id && !$ticket->locations->contains('id', $user->location_id)) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền cập nhật vé của cơ sở khác!');
        }

        $request->validate([
            'name'         => 'required',
            'location_ids' => 'nullable|array',
            'category_id'  => 'required',
            'image_url'    => 'required',
        ]);

        $oldDuration = $ticket->duration;

        // Xử lý ticket_types
        $ticket_types = [];
        if ($request->has('type_name') && $request->has('type_price')) {
            foreach ($request->type_name as $key => $name) {
                if (!empty($name) && !empty($request->type_price[$key])) {
                    $ticket_types[] = [
                        'name'  => $name,
                        'price' => (int) $request->type_price[$key],
                    ];
                }
            }
        }

        // Tính giá
        $price = $request->price;
        if (empty($price) && !empty($ticket_types)) {
            $price = min(array_column($ticket_types, 'price'));
        }
        $price = (int) ($price ?? $ticket->price);

        $price_weekend = $request->price_weekend;
        if (empty($price_weekend)) {
            $price_weekend = $price;
        }
        $price_weekend = (int) $price_weekend;

        // Xác định location_id chính
        $locationIds = $request->location_ids ?? [];
        if ($user->role !== 'super_admin' && $user->location_id) {
            $locationIds = [$user->location_id];
        }
        $primaryLocationId = $locationIds[0] ?? $ticket->location_id;

        // Cập nhật dữ liệu
        $ticket->name          = $request->name;
        $ticket->category_id   = $request->category_id;
        $ticket->location_id   = $primaryLocationId;
        $ticket->image_url     = $request->image_url;
        $ticket->duration      = $request->duration;
        $ticket->status        = $request->status ?? $ticket->status;
        $ticket->price         = $price;
        $ticket->price_weekend = $price_weekend;
        $ticket->description   = $request->description;
        $ticket->rules         = $request->rules;
        $ticket->notes         = $request->notes;
        $ticket->trailer_url   = $request->trailer_url;
        $ticket->ticket_types  = $ticket_types;

        if ($request->hasFile('gallery')) {
            $imagePaths = $ticket->gallery ?? [];
            foreach ($request->file('gallery') as $file) {
                $imagePaths[] = $file->store('tickets', 'public');
            }
            $ticket->gallery = $imagePaths;
        }

        $ticket->save();

        // Cập nhật locations
        $ticket->locations()->sync($locationIds);

        // Tái tạo lịch nếu duration thay đổi
        $newDuration = (int) $request->duration;
        if ($newDuration > 0 && (int) $oldDuration !== $newDuration) {

            $today = \Carbon\Carbon::today()->format('Y-m-d');

            \App\Models\TimeSlot::where('ticket_id', $ticket->id)
                ->where('date', '>=', $today)
                ->where('booked_count', 0)
                ->delete();

            $duration = (int) (floor($newDuration / 5) + 1) * 5;

            $existingDates = \App\Models\TimeSlot::where('ticket_id', $ticket->id)
                ->where('date', '>=', $today)
                ->pluck('date')->unique()->toArray();

            $futureDates = [];
            for ($i = 0; $i < 7; $i++) {
                $futureDates[] = \Carbon\Carbon::today()->addDays($i)->format('Y-m-d');
            }
            $allDates    = array_unique(array_merge($futureDates, $existingDates));
            $locationId  = $ticket->locations->first()->id ?? 1;
            $dailyStart  = '08:00:00';
            $dailyEnd    = '21:00:00';

            foreach ($allDates as $date) {
                $currentTimeStr = $dailyStart;
                while (strtotime($currentTimeStr) < strtotime($dailyEnd)) {
                    $slotStart = \Carbon\Carbon::parse($currentTimeStr);
                    $slotEnd   = (clone $slotStart)->addMinutes($duration);
                    if ($slotEnd->format('H:i:s') > $dailyEnd) break;

                    $startTime = $slotStart->format('H:i:s');
                    $endTime   = $slotEnd->format('H:i:s');

                    $exists = \App\Models\TimeSlot::where('ticket_id', $ticket->id)
                        ->where('date', $date)
                        ->where('start_time', $startTime)
                        ->exists();

                    if (!$exists) {
                        \App\Models\TimeSlot::create([
                            'ticket_id'    => $ticket->id,
                            'location_id'  => $locationId,
                            'date'         => $date,
                            'start_time'   => $startTime,
                            'end_time'     => $endTime,
                            'capacity'     => 15,
                            'status'       => 'open',
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

        Cache::forget('home_tickets');

        return redirect()->route('admin.tickets.index')->with('success', 'Cập nhật vé thành công!');
    }

    // Xóa
    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $user = Auth::user();

        if ($user->role !== 'super_admin' && $user->location_id && !$ticket->locations->contains('id', $user->location_id)) {
            return redirect()->route('admin.tickets.index')->with('error', 'Bạn không có quyền xóa vé của cơ sở khác!');
        }

        $ticket->delete();

        Cache::forget('home_tickets');

        return back()->with('success', 'Đã xóa vé thành công!');
    }
}