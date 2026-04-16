<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PosRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Phải đăng nhập
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('branch.login', $request->route('subdomain'));
        }

        $user      = \Illuminate\Support\Facades\Auth::user();
        $subdomain = $request->route('subdomain');
        $location  = \App\Models\Location::where('slug', $subdomain)->firstOrFail();

        // Admin tổng: được phép vào mọi chi nhánh
        if (in_array($user->role, ['super_admin', 'admin'])) {
            $request->merge(['_pos_location_id' => $location->id]);
            return $next($request);
        }

        // Branch admin / staff: phải đúng chi nhánh
        if (in_array($user->role, ['branch_admin', 'staff'])
            && (int) $user->location_id === (int) $location->id
        ) {
            $request->merge(['_pos_location_id' => $location->id]);
            return $next($request);
        }

        abort(403, 'Bạn không có quyền truy cập POS của chi nhánh này.');
    }
}