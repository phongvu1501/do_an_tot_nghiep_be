<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
