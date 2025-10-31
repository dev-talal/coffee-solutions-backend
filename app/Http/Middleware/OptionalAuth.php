<?php

// app/Http/Middleware/OptionalAuth.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class OptionalAuth
{
    public function handle($request, Closure $next)
    {
        // Force Laravel to check for API token if provided
        Auth::shouldUse('api');

        return $next($request);
    }
}


?>