<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    protected $allowedRoles = ['super_admin', 'admin', 'branch_admin'];

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $this->allowedRoles)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect('/')->with('error', 'Bạn không có quyền truy cập khu vực Admin!');
        }

        return $next($request);
    }
}