<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Location;

class LocationSelectorController extends Controller
{
    // ---------------------------------------------------------
    // 1. TRANG CHỌN CƠ SỞ: Hiển thị giao diện chọn ban đầu
    // ---------------------------------------------------------
    public function select()
    {
        // Nếu đã chọn rồi thì không cần vào trang này nữa
        if (Session::has('selected_location')) {
            return redirect()->route('ticket.shop');
        }

        $locations = Location::orderBy('name')->get();

        return view('location_select', compact('locations'));
    }

    // ---------------------------------------------------------
    // 2. XỬ LÝ KHI KHÁCH BẤM "XÁC NHẬN"
    // ---------------------------------------------------------
    public function store(Request $request)
    {
        $request->validate([
            'location' => 'required|string',
        ]);

        $locationId = $request->input('location');

        if ($locationId === 'all') {
            Session::put('selected_location', 'all');
        }
        else {
            // Kiểm tra tồn tại trong DB
            $exists = Location::where('id', $locationId)->exists();
            if (!$exists) {
                return back()->with('error', 'Cơ sở không hợp lệ, vui lòng chọn lại!');
            }
            Session::put('selected_location', $locationId);
        }

        // Nếu trước đó khách định vào trang nào thì redirect đúng chỗ
        $intended = Session::pull('intended_after_location', route('ticket.shop'));

        return redirect($intended);
    }

    // ---------------------------------------------------------
    // 3. ĐỔI CƠ SỞ: Xoá session rồi quay về trang chọn
    // ---------------------------------------------------------
    public function reset()
    {
        Session::forget('selected_location');
        Session::forget('intended_after_location');

        return redirect()->route('location.select');
    }
}