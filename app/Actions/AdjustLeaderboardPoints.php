<?php

namespace App\Actions;

use App\Models\LeaderboardAdjustment;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AdjustLeaderboardPoints
{
    /**
     * Create a manual adjustment for a user's leaderboard score.
     */
    public function execute(
        User $admin,
        User $agent,
        string $metricType,
        float $adjustedValue,
        string $reason,
        string $periodType = 'daily',
        ?string $periodDate = null
    ): LeaderboardAdjustment {
        $periodDate = $periodDate ?? now()->toDateString();
        
        // Find the current value if any (or just 0)
        // In a real scenario, we might want to fetch the current snapshot value here
        // but for now we'll just track the adjustment.
        $originalValue = 0; // Placeholder or fetch from Snapshot

        $adjustment = LeaderboardAdjustment::create([
            'adjusted_by' => $admin->id,
            'user_id' => $agent->id,
            'period_type' => $periodType,
            'period_date' => $periodDate,
            'metric_type' => $metricType,
            'original_value' => $originalValue,
            'adjusted_value' => $adjustedValue,
            'reason' => $reason,
        ]);

        Log::info('leaderboard_points_manually_adjusted', [
            'admin_id' => $admin->id,
            'agent_id' => $agent->id,
            'metric' => $metricType,
            'new_value' => $adjustedValue,
            'reason' => $reason,
        ]);

        return $adjustment;
    }
}
