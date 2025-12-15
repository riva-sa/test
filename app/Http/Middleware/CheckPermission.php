<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$permissions
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (! $request->user() || ! $request->user()->hasAnyPermission($permissions)) {
            abort(403, 'إجراء غير مصرح به.');
        }

        return $next($request);
    }
}
