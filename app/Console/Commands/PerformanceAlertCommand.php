<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PerformanceMetric;
use Illuminate\Support\Facades\Log;

class PerformanceAlertCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check performance metrics for regressions and alert administrators';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $minutes = 5;
        $date = now()->subMinutes($minutes);

        $metrics = PerformanceMetric::where('created_at', '>=', $date)->get();

        if ($metrics->isEmpty()) {
            $this->info("No performance metrics found for the last {$minutes} minutes.");
            return;
        }

        // Group by endpoint and check if average load time > 2000ms (2 seconds)
        $endpointStats = $metrics->groupBy('endpoint')->map(function ($group) {
            return [
                'endpoint' => '/' . $group->first()->endpoint,
                'avg_load_time' => round($group->avg('load_time_ms'), 2),
                'requests' => $group->count(),
            ];
        });

        $regressions = $endpointStats->filter(function ($stat) {
            return $stat['avg_load_time'] > 2000;
        });

        if ($regressions->isNotEmpty()) {
            foreach ($regressions as $regression) {
                // Log critical alert for administrators
                Log::critical('Performance Regression Alert', [
                    'endpoint' => $regression['endpoint'],
                    'avg_load_time_ms' => $regression['avg_load_time'],
                    'requests_in_last_5_mins' => $regression['requests'],
                    'threshold_ms' => 2000,
                ]);
            }
            $this->error("Found {$regressions->count()} performance regressions.");
        } else {
            $this->info("All endpoints are performing within the 2000ms target.");
        }
    }
}
