<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AiApiKeyAuth
{
    /**
     * Validate the X-AI-API-KEY header against the value stored in .env
     * Add to .env:  AI_API_KEY=your-secret-key-here
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-AI-API-KEY');
        // dd($apiKey, config('services.ai_api_key'));
        if (!$apiKey || $apiKey !== config('services.ai_api_key')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: invalid or missing API key.',
            ], 401);
        }

        return $next($request);
    }
}