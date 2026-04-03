<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'webhook/sepay',
        ]);

        // Tự động check banned trên mọi request web có auth
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckBanned::class);

        // Set locale middleware for language switching
        $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);


        // Đăng ký alias để dùng trong route nếu cần
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdminRole::class,
            'sepay.ip' => \App\Http\Middleware\VerifySepayIp::class,
            'check.banned' => \App\Http\Middleware\CheckBanned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Đã có lỗi xảy ra. Vui lòng thử lại.'
                ], 500);
            }
            // Không return null để Laravel tự xử lý view lỗi
        });
    })->create();



