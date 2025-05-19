<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminaPanelMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Check if the user is authenticated and has the 'admin' role
        if (Auth::check() && Auth::user()->hasRole('Admin')) {
            return $next($request);
        }
        // Check if the user is authenticated and has the 'admin' role
        if (Auth::check() && Auth::user()->hasRole('suprt_admin')) {
            return $next($request);
        }

        // If the user does not have access, redirect them to a different page
        return abort(404);
    }
}
