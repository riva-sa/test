<?php

namespace App\Services;

use App\Models\LeaderboardAdjustment;
use App\Models\LeaderboardConfig;
use App\Models\LeaderboardSnapshot;
use App\Models\SalesTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeaderboardService
{
    public function __construct(
        protected TargetTrackingService $trackingService
    ) {}

    /**
     * Build leaderboard for a specific date, applying any admin adjustments on top.
     * - Snapshot exists → use it (+ adjustments).
     * - No snapshot, date is today → compute on-demand (+ adjustments).
     * - No snapshot, historical date → return empty (US4-SC3 "no data" state).
     */
    public function getLeaderboard(?Carbon $month = null, ?Carbon $date = null): Collection
    {
        $targetDate = $date ?? ($month ? $month->copy()->endOfMonth() : Carbon::today());

        $snapshots = LeaderboardSnapshot::forDate($targetDate)
            ->with('user')
            ->get();

        if ($snapshots->isNotEmpty()) {
            return $this->applyAdjustments($snapshots, $targetDate);
        }

        // Historical date with no snapshot → empty state (US4-SC3)
        if (!$targetDate->isToday()) {
            return collect();
        }

        // No snapshot yet for today → compute live and apply any adjustments saved so far
        $liveData = $this->computeLeaderboardOnDemand($month ?? Carbon::now());
        return $this->applyAdjustmentsToLiveData($liveData, $targetDate);
    }

    /**
     * Get leaderboard for a specific calendar month using snapshot aggregation.
     */
    public function getLeaderboardForMonth(Carbon $month): Collection
    {
        $snapshots = LeaderboardSnapshot::forMonth($month)
            ->with('user')
            ->get()
            ->groupBy('user_id');

        if ($snapshots->isEmpty()) {
            return $this->computeLeaderboardOnDemand($month);
        }

        $weights = LeaderboardConfig::getWeights();

        // Pre-load all targets in a single query to avoid N+1 inside the map.
        $userIds = $snapshots->keys()->all();
        $allTargets = SalesTarget::whereIn('user_id', $userIds)
            ->get()
            ->groupBy('user_id')
            ->map(fn($rows) => $rows->pluck('target_value', 'type')->toArray());

        return $snapshots->map(function (Collection $userSnapshots) use ($weights, $allTargets) {
            $latest = $userSnapshots->sortByDesc('snapshot_date')->first();
            $user = $latest->user;
            $targets = $allTargets->get($user->id, []);

            $progress = [
                'monthly_orders' => [
                    'current' => $userSnapshots->sum('monthly_orders'),
                    'target' => $targets['monthly_orders'] ?? 0,
                    'weight' => $weights['monthly_orders'] ?? 25.0,
                ],
                'daily_orders' => [
                    'current' => $userSnapshots->sum('daily_orders'),
                    'target' => $targets['daily_orders'] ?? 0,
                    'weight' => $weights['daily_orders'] ?? 25.0,
                ],
                'reservations' => [
                    'current' => $latest->reservations,
                    'target' => $targets['reservations'] ?? 0,
                    'weight' => $weights['reservations'] ?? 25.0,
                ],
                'sales' => [
                    'current' => $latest->sales,
                    'target' => $targets['sales'] ?? 0,
                    'weight' => $weights['sales'] ?? 25.0,
                ],
            ];

            foreach ($progress as $type => &$data) {
                $target = $data['target'];
                $data['percentage'] = $target > 0 ? min(100, round(($data['current'] / $target) * 100, 1)) : 0;
            }

            $score = 0.0;
            foreach ($progress as $type => $data) {
                $ratio = $data['target'] > 0 ? min(1.0, $data['current'] / $data['target']) : 0.0;
                $score += ($data['weight'] / 100) * $ratio * 100;
            }

            return [
                'user' => $user,
                'progress' => $progress,
                'composite_score' => round($score, 2),
            ];
        })->sortByDesc('composite_score')->values();
    }

    /**
     * Apply admin adjustments on top of snapshot data.
     */
    private function applyAdjustments(Collection $snapshots, Carbon $date): Collection
    {
        $adjustments = LeaderboardAdjustment::where('period_date', $date->toDateString())
            ->get()
            ->groupBy('user_id');

        $weights = LeaderboardConfig::getWeights();

        return $snapshots->map(function (LeaderboardSnapshot $snapshot) use ($adjustments, $weights) {
            $userAdjustments = $adjustments->get($snapshot->user_id, collect());

            $metrics = [
                'monthly_orders' => $snapshot->monthly_orders,
                'daily_orders' => $snapshot->daily_orders,
                'reservations' => $snapshot->reservations,
                'sales' => $snapshot->sales,
            ];

            foreach ($userAdjustments as $adj) {
                if (isset($metrics[$adj->metric_type])) {
                    $metrics[$adj->metric_type] = (int) $adj->adjusted_value;
                }
            }

            $targets = SalesTarget::where('user_id', $snapshot->user_id)
                ->pluck('target_value', 'type')
                ->toArray();

            $progress = [];
            $score = 0.0;

            foreach ($metrics as $type => $current) {
                $target = $targets[$type] ?? 0;
                $weight = $weights[$type] ?? 25.0;
                $ratio = $target > 0 ? min(1.0, $current / $target) : 0.0;
                $score += ($weight / 100) * $ratio * 100;

                $progress[$type] = [
                    'current' => $current,
                    'target' => $target,
                    'percentage' => $target > 0 ? min(100, round(($current / $target) * 100, 1)) : 0,
                    'weight' => $weight,
                ];
            }

            $compositeAdj = $userAdjustments->where('metric_type', 'composite_score')->last();
            $compositeScore = $compositeAdj ? (float) $compositeAdj->adjusted_value : round($score, 2);

            return [
                'user' => $snapshot->user,
                'progress' => $progress,
                'composite_score' => $compositeScore,
                'snapshot' => $snapshot,
            ];
        })->sortByDesc('composite_score')->values();
    }

    /**
     * Apply admin adjustments to live (on-demand computed) leaderboard data.
     * Re-computes the composite score after applying each metric override.
     */
    private function applyAdjustmentsToLiveData(Collection $liveData, Carbon $date): Collection
    {
        $adjustments = LeaderboardAdjustment::where('period_date', $date->toDateString())
            ->get()
            ->groupBy('user_id');

        if ($adjustments->isEmpty()) {
            return $liveData;
        }

        $weights = LeaderboardConfig::getWeights();

        return $liveData->map(function (array $entry) use ($adjustments, $weights) {
            $userAdjustments = $adjustments->get($entry['user']->id, collect());

            foreach ($userAdjustments as $adj) {
                if (isset($entry['progress'][$adj->metric_type])) {
                    $current = (int) $adj->adjusted_value;
                    $target = $entry['progress'][$adj->metric_type]['target'];
                    $entry['progress'][$adj->metric_type]['current'] = $current;
                    $entry['progress'][$adj->metric_type]['percentage'] = $target > 0
                        ? min(100, round(($current / $target) * 100, 1))
                        : 0;
                }
            }

            // Recompute composite score from the (now-adjusted) raw metrics
            $score = 0.0;
            foreach ($entry['progress'] as $type => $data) {
                $weight = $weights[$type] ?? 25.0;
                $ratio = $data['target'] > 0 ? min(1.0, $data['current'] / $data['target']) : 0.0;
                $score += ($weight / 100) * $ratio * 100;
            }
            $entry['composite_score'] = round($score, 2);

            return $entry;
        })->sortByDesc('composite_score')->values();
    }

    /**
     * On-demand computation fallback.
     */
    private function computeLeaderboardOnDemand(Carbon $month): Collection
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $weights = LeaderboardConfig::getWeights();

        $reps = User::role('sales')->where('is_active', true)->get();

        return $reps->map(function (User $rep) use ($start, $end, $weights) {
            $targets = SalesTarget::where('user_id', $rep->id)->pluck('target_value', 'type')->toArray();
            $progress = [];
            $score = 0.0;

            foreach (['monthly_orders', 'daily_orders', 'reservations', 'sales'] as $type) {
                $current = $this->trackingService->getProgress($rep->id, $type, $start, $end);
                $target = $targets[$type] ?? 0;
                $weight = $weights[$type] ?? 25.0;
                $ratio = $target > 0 ? min(1.0, $current / $target) : 0.0;
                $score += ($weight / 100) * $ratio * 100;

                $progress[$type] = [
                    'current' => $current,
                    'target' => $target,
                    'percentage' => $target > 0 ? min(100, round(($current / $target) * 100, 1)) : 0,
                    'weight' => $weight,
                ];
            }

            return [
                'user' => $rep,
                'progress' => $progress,
                'composite_score' => round($score, 2),
            ];
        })->sortByDesc('composite_score')->values();
    }
}
