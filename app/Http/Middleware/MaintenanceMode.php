<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        if (setting('maintenance_mode') !== 'on') {
            return $next($request);
        }

        $user = Auth::user();

        if ($user && $user->hasRole('Super Admin')) {
            return $next($request);
        }

        if ($request->routeIs('maintenance') || $request->is('login*') || $request->is('admin/*')) {
            return $next($request);
        }

        return redirect()->route('maintenance');
    }
}
