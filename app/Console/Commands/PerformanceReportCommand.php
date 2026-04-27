<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PerformanceMetric;

class PerformanceReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'performance:report {--days=7 : The number of days to analyze}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a performance report from the collected metrics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $date = now()->subDays($days);

        $metrics = PerformanceMetric::where('created_at', '>=', $date)->get();

        if ($metrics->isEmpty()) {
            $this->info("No performance metrics found for the last {$days} days.");
            return;
        }

        $avgLoadTime = round($metrics->avg('load_time_ms'), 2);
        $avgMemory = round($metrics->avg('memory_peak_mb'), 2);
        $avgQueries = round($metrics->avg('queries_count'), 2);
        $avgPayload = round($metrics->avg('payload_size_kb'), 2);

        $this->info("Performance Report (Last {$days} days)");
        $this->line("=====================================");
        $this->line("Total Requests: " . $metrics->count());
        $this->line("Average Load Time: {$avgLoadTime} ms");
        $this->line("Average Memory Peak: {$avgMemory} MB");
        $this->line("Average Queries per Request: {$avgQueries}");
        $this->line("Average Payload Size: {$avgPayload} KB");

        // Breakdown by endpoint
        $this->line("\nSlowest Endpoints (Average Load Time):");
        $endpointStats = $metrics->groupBy('endpoint')->map(function ($group) {
            return [
                'endpoint' => '/' . $group->first()->endpoint,
                'avg_load_time' => round($group->avg('load_time_ms'), 2),
                'requests' => $group->count(),
            ];
        })->sortByDesc('avg_load_time')->take(5);

        $this->table(['Endpoint', 'Average Load Time (ms)', 'Requests'], $endpointStats);
    }
}
