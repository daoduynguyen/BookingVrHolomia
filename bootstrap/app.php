<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
   ->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'webhook/sepay',
    ]);

    // Tự động check banned trên mọi request web có auth
    $middleware->appendToGroup('web', \App\Http\Middleware\CheckBanned::class);

    // Set locale middleware for language switching
    $middleware->appendToGroup('web', \App\Http\Middleware\SetLocale::class);

    // Load location info check for shop if needed
    $middleware->appendToGroup('web', \App\Http\Middleware\SetLocation::class);

    // Đăng ký alias để dùng trong route nếu cần
    $middleware->alias([
        'check.banned' => \App\Http\Middleware\CheckBanned::class,
        'require.location' => \App\Http\Middleware\RequireLocation::class,
        'set.location' => \App\Http\Middleware\SetLocation::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();