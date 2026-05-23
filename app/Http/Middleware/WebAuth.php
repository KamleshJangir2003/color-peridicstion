<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Server-side: just serve the blade view.
        // Real auth is enforced on every API call via auth:sanctum + admin middleware.
        // The JS layer redirects to login if no token is found in localStorage.
        return $next($request);
    }
}
