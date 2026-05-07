<?php

namespace App\Livewire\Mannager\Widgets;

use App\Services\LeaderboardService;
use Carbon\Carbon;
use Livewire\Component;

class TopPerformersWidget extends Component
{
    public function render()
    {
        try {
            // Reuse the same service as the leaderboard page: snapshot when available,
            // live on-demand computation as fallback (so the widget works before the
            // nightly leaderboard:refresh job has run for the first time).
            $leaderboard = app(LeaderboardService::class)->getLeaderboard(null, Carbon::today());
            $topPerformers = $leaderboard->take(5);
        } catch (\Throwable) {
            $topPerformers = collect();
        }

        return view('livewire.mannager.widgets.top-performers-widget', [
            'topPerformers' => $topPerformers,
        ]);
    }
}
