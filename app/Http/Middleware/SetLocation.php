<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Location;
use Symfony\Component\HttpFoundation\Response;

class SetLocation
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has('location')) {
            $locationId = $request->get('location');

            if ($locationId === 'all') {
                Session::put('selected_location', 'all');
            }
            else {
                $exists = Location::where('id', $locationId)->exists();
                if ($exists) {
                    Session::put('selected_location', $locationId);
                }
            }
        }

        $locationId = Session::get('selected_location');

        if ($locationId && $locationId !== 'all') {
            $currentLocation = Location::find($locationId);
        }
        else {
            $currentLocation = null;
        }

        view()->share('currentLocation', $currentLocation);
        view()->share('selectedLocationId', $locationId);

        return $next($request);
    }
}