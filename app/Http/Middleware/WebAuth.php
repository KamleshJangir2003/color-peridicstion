<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class WebAuth
{
    public function handle(Request $request, Closure $next)
    {
        // Pages are protected client-side via JS token check
        // Server just serves the blade views
        return $next($request);
    }
}
