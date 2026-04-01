<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Location;

/**
 * Admin\LocationController (Phase 1 – cập nhật)
 *
 * Bổ sung validation và xử lý các field mới:
 * slug, description, banner_image, opening_hours, maps_url, facebook_url, is_active
 */
class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::orderBy('id', 'desc')->paginate(10);
        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:locations,slug|regex:/^[a-z0-9\-]+$/',
            'address' => 'required|string|max:255',
            'hotline' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
            // 🔥 Đã sửa: Cho phép tải file ảnh (jpeg, png, jpg, gif, webp) tối đa 5MB thay vì bắt nhập URL
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'opening_hours' => 'nullable|string|max:100',
            // 🔥 Đã sửa: Bỏ giới hạn max:1000 vì chuỗi iframe bản đồ có thể rất dài
            'maps_url' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
            'color' => 'nullable|string|max:20',
        ]);

        // Nếu slug rỗng thì tự tạo từ name (model cũng làm nhưng đề phòng)
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

        // --- BẮT ĐẦU ĐOẠN XỬ LÝ ẢNH & BẢN ĐỒ DÀNH CHO FORM TẠO MỚI ---

        // Xử lý upload banner
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/banners'), $filename);
            $data['banner_image'] = asset('storage/banners/' . $filename);
        }

        // Xử lý convert Maps URL sang iframe
        if ($request->maps_url && !str_contains($request->maps_url, '<iframe')) {
            $url = htmlspecialchars($request->maps_url);
            $data['maps_url'] = '<iframe src="' . $url . '" width="100%" height="400" style="border:0;" allowfullscreen loading="lazy"></iframe>';
        }

        // --- KẾT THÚC ĐOẠN XỬ LÝ ---

        Location::create($data);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Thêm cơ sở thành công!');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => "nullable|string|max:100|unique:locations,slug,{$id}|regex:/^[a-z0-9\-]+$/",
            'address' => 'required|string|max:255',
            'hotline' => 'required|string|max:20',
            'description' => 'nullable|string|max:1000',
            // 🔥 Đã sửa: Cho phép tải file ảnh (jpeg, png, jpg, gif, webp) tối đa 5MB
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'logo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'opening_hours' => 'nullable|string|max:100',
            // 🔥 Đã sửa: Bỏ giới hạn max:1000
            'maps_url' => 'nullable|string',
            'facebook_url' => 'nullable|url|max:500',
            'is_active' => 'nullable|boolean',
            'color' => 'nullable|string|max:20',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        // --- BẮT ĐẦU ĐOẠN XỬ LÝ ẢNH & BẢN ĐỒ DÀNH CHO FORM CHỈNH SỬA ---

        // Xử lý upload banner mới
        if ($request->hasFile('banner_image')) {
            $file = $request->file('banner_image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/banners'), $filename);
            $data['banner_image'] = asset('storage/banners/' . $filename);
        }

        // Xử lý convert Maps URL sang iframe
        if ($request->maps_url && !str_contains($request->maps_url, '<iframe')) {
            $url = htmlspecialchars($request->maps_url);
            $data['maps_url'] = '<iframe src="' . $url . '" width="100%" height="400" style="border:0;" allowfullscreen loading="lazy"></iframe>';
        }

        if ($request->hasFile('logo_url')) {
    $file = $request->file('logo_url');
    $filename = time() . '_logo_' . $file->getClientOriginalName();
    $file->move(public_path('storage/logos'), $filename);
    $data['logo_url'] = asset('storage/logos/' . $filename);
}
        // --- KẾT THÚC ĐOẠN XỬ LÝ ---

        $location->update($data);

        return redirect()->route('admin.locations.index')
            ->with('success', 'Cập nhật cơ sở thành công!');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        if ($location->tickets()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Không thể xoá cơ sở đang có trò chơi!');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Đã xoá cơ sở!');
    }
}