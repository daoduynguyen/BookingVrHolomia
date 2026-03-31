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
 *   slug, description, banner_image, opening_hours, maps_url, facebook_url, is_active
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
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:100|unique:locations,slug|regex:/^[a-z0-9\-]+$/',
            'address'       => 'required|string|max:255',
            'hotline'       => 'required|string|max:20',
            'description'   => 'nullable|string|max:1000',
            'banner_image'  => 'nullable|url|max:500',
            'opening_hours' => 'nullable|string|max:100',
            'maps_url'      => 'nullable|string|max:1000',
            'facebook_url'  => 'nullable|url|max:500',
            'is_active'     => 'nullable|boolean',
        ]);

        // Nếu slug rỗng thì tự tạo từ name (model cũng làm nhưng đề phòng)
        $data['slug']      = $data['slug'] ?? Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);

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
            'name'          => 'required|string|max:255',
            'slug'          => "nullable|string|max:100|unique:locations,slug,{$id}|regex:/^[a-z0-9\-]+$/",
            'address'       => 'required|string|max:255',
            'hotline'       => 'required|string|max:20',
            'description'   => 'nullable|string|max:1000',
            'banner_image'  => 'nullable|url|max:500',
            'opening_hours' => 'nullable|string|max:100',
            'maps_url'      => 'nullable|string|max:1000',
            'facebook_url'  => 'nullable|url|max:500',
            'is_active'     => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

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
