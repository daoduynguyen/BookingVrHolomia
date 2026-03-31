<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Location;

/**
 * LocationLandingController
 *
 * Hiển thị landing page riêng cho từng cơ sở theo slug.
 * Khi khách vào /co-so/{slug}:
 *   1. Lấy thông tin cơ sở
 *   2. Lấy danh sách game của cơ sở đó
 *   3. Set session selected_location để flow hiện tại hoạt động bình thường
 */
class LocationLandingController extends Controller
{
    /**
     * Hiển thị landing page của một cơ sở.
     */
    public function show(string $slug)
    {
        $location = Location::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Tự động set session — khách không cần qua trang chọn cơ sở nữa
        Session::put('selected_location', $location->id);

        // Lấy tối đa 6 game nổi bật của cơ sở này
        $tickets = $location->tickets()
            ->where('status', '!=', 'deleted')
            ->withAvg('reviews', 'rating')
            ->orderByDesc('play_count')
            ->take(6)
            ->get()
            ->map(function ($ticket) {
                $ticket->avg_rating = round($ticket->reviews_avg_rating ?? 0, 1);
                return $ticket;
            });

        return view('location_landing', compact('location', 'tickets'));
    }

    /**
     * Trang tổng hợp tất cả cơ sở (sitemap chi nhánh).
     * Route: GET /he-thong-co-so
     */
    public function index()
    {
        $locations = Location::active()
            ->withCount('tickets')
            ->orderBy('name')
            ->get();

        return view('location_index', compact('locations'));
    }
}
