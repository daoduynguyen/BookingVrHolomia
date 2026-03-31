<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifySepayIp
{
    // Danh sách IP của SePay (cập nhật theo docs của SePay)
    protected $allowedIps = [
        '103.124.92.0/24',
        '103.124.93.0/24',
    ];

    public function handle(Request $request, Closure $next)
    {
        $clientIp = $request->ip();

        // Trong môi trường development thì bỏ qua
        if (app()->environment('local')) {
            return $next($request);
        }

        foreach ($this->allowedIps as $allowedIp) {
            if ($this->ipInRange($clientIp, $allowedIp)) {
                return $next($request);
            }
        }

        \Log::warning("SePay webhook từ IP không hợp lệ: {$clientIp}");
        return response()->json(['error' => 'Forbidden'], 403);
    }

    private function ipInRange($ip, $cidr): bool
    {
        [$subnet, $mask] = explode('/', $cidr);
        return (ip2long($ip) & ~((1 << (32 - $mask)) - 1)) === ip2long($subnet);
    }
}