<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentApiKeyAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-AGENT-API-KEY') ?? $request->query('api_key');
        
        if (! $apiKey || $apiKey !== config('services.agent_api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: invalid or missing Agent API key.',
            ], 401);
        }

        return $next($request);
    }
}
