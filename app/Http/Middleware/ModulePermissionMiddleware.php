<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ModulePermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission)
    {

        if (!auth()->user() || !auth()->user()->can($permission)) {
            return response()->json(['message' => 'Forbidden: Missing permission '.$permission], 403);
        }

        return $next($request);
    }
}
