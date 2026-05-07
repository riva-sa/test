<?php

namespace App\Services;

use App\Models\LeaderboardAdjustment;
use App\Models\LeaderboardConfig;
use App\Models\LeaderboardSnapshot;
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
     * Accepts an optional $date for the daily_orders range (defaults to today).
     *
     * @return array<string, array{current: int, target: int, label: string, percentage: float}>
     */
    public function getAllProgress(int $userId, ?Carbon $date = null): array
    {
        $targets = SalesTarget::where('user_id', $userId)->pluck('target_value', 'type')->toArray();
        $now = Carbon::now();
        $day = $date ?? $now;

        $types = [
            'monthly_orders' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'daily_orders' => [$day->copy()->startOfDay(), $day->copy()->endOfDay()],
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
     * Historical performance for a user for a past month (recomputed from transitions).
     */
    public function getHistoricalPerformance(int $userId, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();

        return [
            'monthly_orders' => $this->getProgress($userId, 'monthly_orders', $start, $end),
            'daily_orders' => null,
            'reservations' => $this->getProgress($userId, 'reservations', $start, $end),
            'sales' => $this->getProgress($userId, 'sales', $start, $end),
        ];
    }
}
