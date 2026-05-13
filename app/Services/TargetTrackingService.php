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
     * Count net transitions for a specific user/type within a date range.
     * Logic:
     * - monthly_orders / daily_orders: (transitions FROM 0) - (transitions TO 0)
     * - reservations: (transitions TO 2) - (transitions FROM 2)
     * - sales: (transitions TO 4) - (transitions FROM 4)
     * 
     * This ensures that if a status is reverted (e.g. 4 -> 3), the point is subtracted.
     */
    public function getProgress(int $userId, string $type, Carbon $periodStart, Carbon $periodEnd): int
    {
        $baseQuery = OrderStatusTransition::where('attributed_user_id', $userId)
            ->whereBetween('created_at', [$periodStart->startOfDay(), $periodEnd->endOfDay()]);

        return match ($type) {
            'monthly_orders', 'daily_orders' => (int) (
                (clone $baseQuery)->where('from_status', 0)->count() - 
                (clone $baseQuery)->where('to_status', 0)->count()
            ),
            'reservations' => (int) (
                (clone $baseQuery)->where('to_status', 2)->count() - 
                (clone $baseQuery)->where('from_status', 2)->count()
            ),
            'sales' => (int) (
                (clone $baseQuery)->where('to_status', 4)->count() - 
                (clone $baseQuery)->where('from_status', 4)->count()
            ),
            default => 0,
        };
    }

    /**
     * Get actual transition records for a specific user/type within a date range.
     * For details view, we show the "Forward" transitions that added the points.
     * Note: In a real "Net" view, we might want to show subtractions too, 
     * but for now we show the positive actions.
     */
    public function getTransitionsDetail(int $userId, string $type, Carbon $periodStart, Carbon $periodEnd): Collection
    {
        $query = OrderStatusTransition::with(['order', 'user'])
            ->where('attributed_user_id', $userId)
            ->whereBetween('created_at', [$periodStart->startOfDay(), $periodEnd->endOfDay()]);

        switch ($type) {
            case 'monthly_orders':
            case 'daily_orders':
                $query->where('from_status', 0);
                break;
            case 'reservations':
                $query->where('to_status', 2);
                break;
            case 'sales':
                $query->where('to_status', 4);
                break;
        }

        return $query->latest()->get();
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
