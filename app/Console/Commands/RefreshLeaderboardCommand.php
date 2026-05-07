<?php

namespace App\Console\Commands;

use App\Models\LeaderboardConfig;
use App\Models\LeaderboardSnapshot;
use App\Models\SalesTarget;
use App\Models\UnitOrder;
use App\Models\User;
use App\Services\TargetTrackingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshLeaderboardCommand extends Command
{
    protected $signature = 'leaderboard:refresh {--date= : Snapshot date in Y-m-d format, defaults to today}';

    protected $description = 'Calculate and store daily leaderboard snapshots for all active sales reps';

    public function handle(TargetTrackingService $service): int
    {
        $startedAt = microtime(true);

        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))
            : Carbon::today();

        $weights = LeaderboardConfig::getWeights();

        if (! \Spatie\Permission\Models\Role::where('name', 'sales')->exists()) {
            $this->warn('No "sales" role found. Skipping leaderboard refresh.');
            return self::SUCCESS;
        }

        $reps = User::role('sales')->where('is_active', true)->get();
        $count = 0;

        foreach ($reps as $rep) {
            $targets = SalesTarget::where('user_id', $rep->id)
                ->pluck('target_value', 'type')
                ->toArray();

            // monthly_orders / daily_orders: count transitions from status 0 (picking up new leads)
            $monthlyOrders = $service->getProgress(
                $rep->id,
                'monthly_orders',
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            );

            $dailyOrders = $service->getProgress(
                $rep->id,
                'daily_orders',
                $date->copy()->startOfDay(),
                $date->copy()->endOfDay()
            );

            // reservations / sales: count transitions to these statuses within the current month
            $reservations = $service->getProgress(
                $rep->id,
                'reservations',
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            );

            $sales = $service->getProgress(
                $rep->id,
                'sales',
                $date->copy()->startOfMonth(),
                $date->copy()->endOfMonth()
            );

            $score = 0.0;
            $metrics = [
                'monthly_orders' => $monthlyOrders,
                'daily_orders' => $dailyOrders,
                'reservations' => $reservations,
                'sales' => $sales,
            ];

            foreach ($metrics as $type => $current) {
                $target = $targets[$type] ?? 0;
                $weight = $weights[$type] ?? 25.0;
                $ratio = $target > 0 ? min(1.0, $current / $target) : 0.0;
                $score += ($weight / 100) * $ratio * 100;
            }

            LeaderboardSnapshot::updateOrCreate(
                ['user_id' => $rep->id, 'snapshot_date' => $date->toDateString()],
                [
                    'monthly_orders' => $monthlyOrders,
                    'daily_orders' => $dailyOrders,
                    'reservations' => $reservations,
                    'sales' => $sales,
                    'composite_score' => round($score, 2),
                ]
            );

            $count++;
        }

        $durationMs = round((microtime(true) - $startedAt) * 1000);

        Log::info('leaderboard_refresh', [
            'date' => $date->toDateString(),
            'users_processed' => $count,
            'duration_ms' => $durationMs,
        ]);

        $this->info("Leaderboard refreshed for {$date->toDateString()} — {$count} reps processed in {$durationMs}ms.");

        return self::SUCCESS;
    }
}
