<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use App\Models\PerformanceMetric;
use Illuminate\Support\Facades\Log;

class PerformanceMonitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $request->attributes->set('start_time', microtime(true));
        
        // Start query profiling if needed
        DB::enableQueryLog();

        return $next($request);
    }

    /**
     * Handle tasks after the response has been sent to the browser.
     */
    public function terminate(Request $request, Response $response): void
    {
        $startTime = $request->attributes->get('start_time');
        
        if (!$startTime) {
            return;
        }

        $endTime = microtime(true);
        $durationMs = round(($endTime - $startTime) * 1000, 2);
        
        $queries = DB::getQueryLog();
        $queryCount = count($queries);
        
        // Disable after capturing
        DB::disableQueryLog();
        DB::flushQueryLog();

        $memoryPeakBytes = memory_get_peak_usage(true);
        $memoryPeakMb = round($memoryPeakBytes / 1024 / 1024, 2);
        
        $payloadSizeBytes = strlen($response->getContent());
        $payloadSizeKb = round($payloadSizeBytes / 1024, 2);

        // Save asynchronously if queue is available, or just save normally since terminate is after response
        try {
            PerformanceMetric::create([
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'load_time_ms' => $durationMs,
                'memory_peak_mb' => $memoryPeakMb,
                'queries_count' => $queryCount,
                'payload_size_kb' => $payloadSizeKb,
                'status_code' => $response->getStatusCode(),
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            // Silently ignore to not affect anything
            Log::error('Performance metric tracking failed: ' . $e->getMessage());
        }
    }
}
