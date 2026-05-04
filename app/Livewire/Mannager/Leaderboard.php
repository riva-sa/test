<?php

namespace App\Livewire\Mannager;

use App\Models\LeaderboardConfig;
use App\Services\TargetTrackingService;
use Carbon\Carbon;
use Livewire\Component;

class Leaderboard extends Component
{
    public string $selectedMonth;

    public array $weights = [];

    public function mount(): void
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->weights = LeaderboardConfig::getWeights();
    }

    public function getLeaderboardProperty()
    {
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);

        return app(TargetTrackingService::class)->getLeaderboard($month);
    }

    public function getAvailableMonthsProperty(): array
    {
        $months = [];
        for ($i = 0; $i < 12; $i++) {
            $m = Carbon::now()->subMonths($i);
            $months[$m->format('Y-m')] = $m->translatedFormat('F Y');
        }

        return $months;
    }

    public function saveWeights(): void
    {
        $total = array_sum($this->weights);
        if (abs($total - 100) > 0.5) {
            session()->flash('error', 'مجموع الأوزان يجب أن يساوي 100%');

            return;
        }
        LeaderboardConfig::saveWeights($this->weights);
        session()->flash('success', 'تم حفظ الأوزان وتحديث الترتيب');
    }

    public function render()
    {
        return view('livewire.mannager.leaderboard', [
            'leaderboard' => $this->leaderboard,
            'availableMonths' => $this->availableMonths,
        ])->layout('layouts.custom');
    }
}
