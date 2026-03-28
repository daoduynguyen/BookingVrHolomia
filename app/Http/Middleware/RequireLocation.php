<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class RequireLocation
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('selected_location')) {
            Session::put('intended_after_location', $request->fullUrl());
            return redirect()->route('location.select');
        }

        return $next($request);
    }
}