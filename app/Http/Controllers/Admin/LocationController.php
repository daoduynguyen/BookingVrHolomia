<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Location;
use Illuminate\Support\Facades\Auth;

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
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'hotline' => 'required|string|max:20',
        ]);

        Location::create($request->all());

        return redirect()->route('admin.locations.index')->with('success', 'Thêm cơ sở thành công!');
    }

    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'hotline' => 'required|string|max:20',
        ]);

        $location->update($request->all());

        return redirect()->route('admin.locations.index')->with('success', 'Cập nhật cơ sở thành công!');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        
        // Prevent deleting location if it is still being used by users or having tickets 
        if ($location->tickets()->count() > 0) {
            return redirect()->route('admin.locations.index')->with('error', 'Không thể xoá cơ sở đang có trò chơi!');
        }
        
        $location->delete();

        return redirect()->route('admin.locations.index')->with('success', 'Đã xoá cơ sở!');
    }
}
