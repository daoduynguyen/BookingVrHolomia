<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function adminIndex()
    {
        $surcharge = Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? '0';
        return view('admin.settings', compact('surcharge'));
    }

    public function adminUpdate(Request $request)
    {
        $request->validate([
            'weekend_surcharge_percentage' => 'required|integer|min:0|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'weekend_surcharge_percentage'],
            ['value' => $request->weekend_surcharge_percentage]
        );

        return back()->with('success', 'Đã lưu cài đặt!');
    }
}