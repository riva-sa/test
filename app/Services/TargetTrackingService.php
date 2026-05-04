<?php

namespace App\Services;

use App\Models\LeaderboardConfig;
use App\Models\OrderStatusTransition;
use App\Models\SalesTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TargetTrackingService
{
    /**
     * Count transitions for a specific user/type within a date range.
     * - monthly_orders / daily_orders: from_status = 0 (جديد → anything)
     * - reservations: to_status = 2 (→ معاملات بيعية)
     * - sales: to_status = 4 (→ مكتمل)
     */
    public function getProgress(int $userId, string $type, Carbon $periodStart, Carbon $periodEnd): int
    {
        $query = OrderStatusTransition::where('user_id', $userId)
            ->whereBetween('created_at', [$periodStart->startOfDay(), $periodEnd->endOfDay()]);

        return match ($type) {
            'monthly_orders', 'daily_orders' => (int) $query->where('from_status', 0)->count(),
            'reservations' => (int) $query->where('to_status', 2)->count(),
            'sales' => (int) $query->where('to_status', 4)->count(),
            default => 0,
        };
    }

    /**
     * Get all 4 target types with their current progress for a user.
     * Returns [type => ['current' => int, 'target' => int, 'label' => string, 'percentage' => float]]
     */
    public function getAllProgress(int $userId): array
    {
        $targets = SalesTarget::where('user_id', $userId)->pluck('target_value', 'type')->toArray();
        $now = Carbon::now();

        $types = [
            'monthly_orders' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'daily_orders' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'reservations' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'sales' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        ];

        $result = [];
        foreach ($types as $type => [$start, $end]) {
            $current = $this->getProgress($userId, $type, $start, $end);
            $targetValue = $targets[$type] ?? 0;
            $result[$type] = [
                'current' => $current,
                'target' => $targetValue,
                'label' => SalesTarget::TYPES[$type] ?? $type,
                'percentage' => $targetValue > 0 ? min(100, round(($current / $targetValue) * 100, 1)) : 0,
            ];
        }

        return $result;
    }

    /**
     * Build leaderboard for a specific month (defaults to current month).
     * Scores are computed as Σ (weight/100 × min(1, progress/target)) × 100
     * Returns collection sorted by composite_score desc.
     */
    public function getLeaderboard(?Carbon $month = null): Collection
    {
        $month = $month ?? Carbon::now();
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $weights = LeaderboardConfig::getWeights();

        $reps = User::role('sales')->where('is_active', true)->get();

        return $reps->map(function (User $rep) use ($start, $end, $weights) {
            $targets = SalesTarget::where('user_id', $rep->id)->pluck('target_value', 'type')->toArray();
            $progress = [];
            $score = 0.0;

            foreach (['monthly_orders', 'daily_orders', 'reservations', 'sales'] as $type) {
                $current = $this->getProgress($rep->id, $type, $start, $end);
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

    /**
     * Historical performance for a user for a past month (recomputed from transitions).
     */
    public function getHistoricalPerformance(int $userId, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        return [
            'monthly_orders' => $this->getProgress($userId, 'monthly_orders', $start, $end),
            'daily_orders' => null, // N/A for monthly history view
            'reservations' => $this->getProgress($userId, 'reservations', $start, $end),
            'sales' => $this->getProgress($userId, 'sales', $start, $end),
        ];
    }
}
