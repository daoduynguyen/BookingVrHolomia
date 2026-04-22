<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\PosShift;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PosShiftController extends Controller
{
    // Lấy location từ subdomain (dùng chung)
    private function getLocation(string $subdomain): Location
    {
        return Location::where('slug', $subdomain)->firstOrFail();
    }

    // Hiển thị form mở ca
    public function openForm(string $subdomain)
    {
        $location    = $this->getLocation($subdomain);
        $activeShift = PosShift::where('location_id', $location->id)
                            ->where('status', 'open')
                            ->first();

        return view('pos.shift.open', compact('location', 'subdomain', 'activeShift'));
    }

    // Xử lý mở ca
    public function open(Request $request, string $subdomain)
    {
        $location = $this->getLocation($subdomain);

        $existing = PosShift::where('location_id', $location->id)
            ->where('status', 'open')
            ->first();

        if ($existing) {
            return back()->with('error', 'Đã có ca đang mở tại chi nhánh này. Vui lòng đóng ca cũ trước.');
        }

        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
        ]);

        PosShift::create([
            'user_id'      => Auth::id(),
            'location_id'  => $location->id,
            'opening_cash' => $request->opening_cash,
            'status'       => 'open',
        ]);

        return redirect()->route('pos.dashboard', $subdomain)
            ->with('success', 'Mở ca thành công!');
    }

    // Hiển thị form đóng ca
    public function closeForm(string $subdomain)
    {
        $location = $this->getLocation($subdomain);

        $shift = PosShift::where('location_id', $location->id)
            ->where('status', 'open')
            ->firstOrFail();

        $summary = [
            'cash'     => $shift->orders()->where('payment_method', 'cash')->where('status', 'paid')->sum('total_amount'),
            'card'     => $shift->orders()->where('payment_method', 'card')->where('status', 'paid')->sum('total_amount'),
            'transfer' => $shift->orders()->where('payment_method', 'transfer')->where('status', 'paid')->sum('total_amount'),
            'total'    => $shift->orders()->where('status', 'paid')->sum('total_amount'),
            'count'    => $shift->orders()->where('status', 'paid')->count(),
        ];

        $expectedCash = $shift->opening_cash + $summary['cash'];

        return view('pos.shift.close', compact('location', 'subdomain', 'shift', 'summary', 'expectedCash'));
    }

    // Xử lý đóng ca
    public function close(Request $request, string $subdomain)
    {
        $location = $this->getLocation($subdomain);

        $shift = PosShift::where('location_id', $location->id)
            ->where('status', 'open')
            ->firstOrFail();

        $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'closing_note' => 'nullable|string|max:500',
        ]);

        $expected = $shift->opening_cash + $shift->cashRevenue();
        $diff     = (float) $request->closing_cash - $expected;

        $shift->update([
            'closing_cash'    => $request->closing_cash,
            'cash_difference' => $diff,
            'closed_at'       => now(),
            'status'          => 'closed',
            'closing_note'    => $request->closing_note,
        ]);

        return redirect()->route('pos.shift.report', [$subdomain, $shift->id])
            ->with('success', 'Đã đóng ca thành công!');
    }

    // Báo cáo ca sau khi đóng
    public function report(string $subdomain, int $shiftId)
    {
        $location = $this->getLocation($subdomain);

        $shift = PosShift::where('id', $shiftId)
            ->where('location_id', $location->id)
            ->with(['user', 'orders'])
            ->firstOrFail();

        $summary = [
            'cash'     => $shift->orders()->where('payment_method', 'cash')->where('status', 'paid')->sum('total_amount'),
            'card'     => $shift->orders()->where('payment_method', 'card')->where('status', 'paid')->sum('total_amount'),
            'transfer' => $shift->orders()->where('payment_method', 'transfer')->where('status', 'paid')->sum('total_amount'),
            'total'    => $shift->orders()->where('status', 'paid')->sum('total_amount'),
            'count'    => $shift->orders()->where('status', 'paid')->count(),
        ];

        $expectedCash = $shift->opening_cash + $summary['cash'];

        return view('pos.shift.report', compact('location', 'subdomain', 'shift', 'summary', 'expectedCash'));
    }
}