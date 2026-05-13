<?php

namespace App\Livewire\Mannager;

use App\Models\LeaderboardAdjustment;
use App\Models\LeaderboardConfig;
use App\Services\LeaderboardService;
use App\Services\TargetTrackingService;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class Leaderboard extends Component
{
    public string $selectedMonth;

    public string $selectedDate;

    public array $weights = [];

    // Admin adjustment modal state
    public bool $showAdjustmentModal = false;
    public ?int $editingUserId = null;
    public string $editingUserName = '';
    public string $editingMetric = 'monthly_orders';
    public string $editingPeriodType = 'daily';
    public string $editingPeriodDate = '';
    public float|string $editingOriginalValue = 0;
    public float|string $editingAdjustedValue = 0;
    public string $editingReason = '';

    // Details modal state
    public bool $showDetailsModal = false;
    public ?int $detailsUserId = null;
    public string $detailsUserName = '';
    public string $detailsMetric = '';
    public $detailsData = [];

    // History modal state
    public bool $showHistoryModal = false;
    public $adjustmentHistory = [];

    public function mount(): void
    {
        $this->selectedMonth = Carbon::now()->format('Y-m');
        $this->selectedDate = Carbon::today()->toDateString();
        $this->weights = LeaderboardConfig::getWeights();
    }

    public function getLeaderboardProperty()
    {
        // Always use the date-based path so admin adjustments are applied regardless of
        // whether today or a historical date is selected. For today with no snapshot the
        // service falls back to live on-demand computation + adjustments.
        return app(LeaderboardService::class)->getLeaderboard(null, Carbon::parse($this->selectedDate));
    }

    public function updatedSelectedMonth(): void
    {
        // Sync the date picker to the last day of the chosen month (capped at today)
        $month = Carbon::createFromFormat('Y-m', $this->selectedMonth);
        $this->selectedDate = min(
            $month->copy()->endOfMonth()->toDateString(),
            Carbon::today()->toDateString()
        );
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
            session()->flash('error', __('leaderboard.weights_must_sum_100'));

            return;
        }
        LeaderboardConfig::saveWeights($this->weights);
        session()->flash('success', __('leaderboard.weights_saved'));
    }

    public function openAdjustmentModal(int $userId, string $date): void
    {
        $this->editingUserId = $userId;
        $this->editingPeriodDate = $date;
        $this->editingPeriodType = 'daily';
        $this->editingMetric = 'monthly_orders';
        $this->editingReason = '';

        // Resolve user name and current metric value from the live leaderboard data
        $entry = collect($this->leaderboard)->first(fn($e) => $e['user']->id === $userId);
        $this->editingUserName = $entry ? $entry['user']->name : '';
        $this->syncOriginalValue($entry);

        $this->showAdjustmentModal = true;
    }

    public function updatedEditingMetric(): void
    {
        $entry = collect($this->leaderboard)->first(fn($e) => $e['user']->id === $this->editingUserId);
        $this->syncOriginalValue($entry);
    }

    private function syncOriginalValue(?array $entry): void
    {
        $current = $entry['progress'][$this->editingMetric]['current'] ?? 0;
        $this->editingOriginalValue = $current;
        $this->editingAdjustedValue = $current;
    }

    public function saveAdjustment(): void
    {
        $adjusted = (int) $this->editingAdjustedValue;

        $allowedMetrics = ['monthly_orders', 'daily_orders', 'reservations', 'sales'];
        if (!in_array($this->editingMetric, $allowedMetrics)) {
            return;
        }

        if ($adjusted < 0) {
            session()->flash('error', __('leaderboard.no_negative_values'));
            return;
        }

        if (empty(trim($this->editingReason))) {
            session()->flash('error', __('leaderboard.reason_required'));
            return;
        }

        $original = (int) $intOriginalValue = (int) $this->editingOriginalValue;

        LeaderboardAdjustment::create([
            'adjusted_by' => auth()->id(),
            'user_id' => $this->editingUserId,
            'period_type' => $this->editingPeriodType,
            'period_date' => $this->editingPeriodDate,
            'metric_type' => $this->editingMetric,
            'original_value' => $intOriginalValue,
            'adjusted_value' => $adjusted,
            'reason' => trim($this->editingReason),
        ]);

        Log::info('leaderboard_adjustment', [
            'adjusted_by' => auth()->id(),
            'user_id' => $this->editingUserId,
            'period_date' => $this->editingPeriodDate,
            'period_type' => $this->editingPeriodType,
            'metric_type' => $this->editingMetric,
            'original_value' => $intOriginalValue,
            'adjusted_value' => $adjusted,
            'reason' => trim($this->editingReason),
        ]);

        $this->showAdjustmentModal = false;
        $this->reset(['editingUserId', 'editingUserName', 'editingReason', 'editingAdjustedValue', 'editingOriginalValue']);
        session()->flash('success', __('leaderboard.adjustment_saved'));
    }

    public function closeAdjustmentModal(): void
    {
        $this->showAdjustmentModal = false;
        $this->reset(['editingUserId', 'editingUserName', 'editingReason', 'editingAdjustedValue', 'editingOriginalValue']);
    }

    public function openDetailsModal(int $userId, string $metric): void
    {
        $this->detailsUserId = $userId;
        $this->detailsMetric = $metric;

        $entry = collect($this->leaderboard)->first(fn($e) => $e['user']->id === $userId);
        $this->detailsUserName = $entry ? $entry['user']->name : '';

        $date = Carbon::parse($this->selectedDate);
        $start = ($metric === 'daily_orders') ? $date->copy()->startOfDay() : $date->copy()->startOfMonth();
        $end = ($metric === 'daily_orders') ? $date->copy()->endOfDay() : $date->copy()->endOfMonth();

        $this->detailsData = app(TargetTrackingService::class)->getTransitionsDetail($userId, $metric, $start, $end);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal(): void
    {
        $this->showDetailsModal = false;
        $this->reset(['detailsUserId', 'detailsUserName', 'detailsMetric', 'detailsData']);
    }

    public function openHistoryModal(): void
    {
        $this->adjustmentHistory = LeaderboardAdjustment::with(['admin', 'agent'])
            ->latest()
            ->take(50)
            ->get();
        $this->showHistoryModal = true;
    }

    public function closeHistoryModal(): void
    {
        $this->showHistoryModal = false;
        $this->reset(['adjustmentHistory']);
    }

    public function refreshLeaderboard(): void
    {
        if (!auth()->user()->hasRole('Admin') && !auth()->user()->hasRole('sales_manager')) {
            return;
        }

        Artisan::call('leaderboard:refresh', [
            '--date' => $this->selectedDate,
        ]);

        session()->flash('success', __('leaderboard.refresh_success') ?? 'تم تحديث البيانات بنجاح');
    }

    public function render()
    {
        return view('livewire.mannager.leaderboard', [
            'leaderboard' => $this->leaderboard,
            'availableMonths' => $this->availableMonths,
            'isAdmin' => auth()->user()?->hasRole('Admin'),
        ])->layout('layouts.custom');
    }
}
