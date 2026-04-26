<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  string  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! $request->user()) {
            abort(401);
        }

        // If specific roles are required, check them
        if (! empty($roles)) {
            if (! $request->user()->hasAnyRole($roles)) {
                abort(403, 'إجراء غير مصرح به.');
            }
        }

        return $next($request);
    }
}
