<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    protected $allowedRoles = ['super_admin', 'admin', 'branch_admin'];

    protected array $routePermissions = [
        'admin.dashboard' => 'dashboard',
        'admin.slots' => 'slots',
        'admin.bookings' => 'bookings',
        'admin.tickets' => 'tickets',
        'admin.locations' => 'locations',
        'admin.coupons' => 'coupons',
        'admin.users' => 'users',
        'admin.contacts' => 'contacts',
        'admin.settings' => 'settings',
        'admin.chatbot' => 'chatbot',
        'admin.export.orders' => 'bookings',
        'admin.audit_log' => 'settings',
    ];

    protected function routePermissionKey(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        foreach ($this->routePermissions as $routePrefix => $permission) {
            if ($routeName === $routePrefix || str_starts_with($routeName, $routePrefix . '.')) {
                return $permission;
            }
        }

        return null;
    }

    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, $this->allowedRoles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
            return redirect('/')->with('error', 'Bạn không có quyền truy cập khu vực Admin!');
        }

        $user = Auth::user();
        if (!in_array($user->role, ['super_admin', 'admin'], true)) {
            $permissionKey = $this->routePermissionKey($request->route()?->getName());
            if (!$permissionKey || !$user->canAccessAdminSection($permissionKey)) {
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Forbidden'], 403);
                }

                return redirect()->route('admin.dashboard')->with('error', 'Bạn không có quyền truy cập chức năng này!');
            }
        }

        return $next($request);
    }
}