<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function adminIndex()
    {
        $surcharge   = Setting::where('key', 'weekend_surcharge_percentage')->value('value') ?? '0';
        $bankName    = Setting::where('key', 'bank_name')->value('value') ?? '';
        $bankBin     = Setting::where('key', 'bank_bin')->value('value') ?? '';
        $bankAccount = Setting::where('key', 'bank_account')->value('value') ?? '';
        $bankOwner   = Setting::where('key', 'bank_owner')->value('value') ?? '';

        return view('admin.settings', compact('surcharge', 'bankName', 'bankBin', 'bankAccount', 'bankOwner'));
    }

    public function adminUpdate(Request $request)
    {
        // Form ngân hàng
        if ($request->input('form_type') === 'bank') {
            $request->validate([
                'bank_name'    => 'required|string|max:100',
                'bank_bin'     => 'required|string|max:20',
                'bank_account' => 'required|string|max:50',
                'bank_owner'   => 'required|string|max:100',
            ]);

            foreach (['bank_name', 'bank_bin', 'bank_account', 'bank_owner'] as $key) {
                Setting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
            }

            return back()->with('success', '✅ Đã lưu thông tin ngân hàng!');
        }

        // Form phụ phí cuối tuần
        $request->validate([
            'weekend_surcharge_percentage' => 'required|integer|min:0|max:100',
        ]);

        Setting::updateOrCreate(
            ['key' => 'weekend_surcharge_percentage'],
            ['value' => $request->weekend_surcharge_percentage]
        );

        return back()->with('success', '✅ Đã lưu cài đặt!');
    }
}
