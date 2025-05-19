<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {

        if (!$request->user() && !$request->user()->hasAnyRole(['sales_manager', 'sales', 'follow_up'])) {
            abort(403, 'إجراء غير مصرح به.');
        }

        return $next($request);
    }
}
