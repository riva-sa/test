<?php

namespace App\Livewire\Mannager;

use App\Models\LeaderboardConfig;
use App\Models\SalesTarget;
use App\Models\User;
use Livewire\Component;

class SalesTargets extends Component
{
    public array $targets = [];

    public array $weights = [];

    public string $defaultValue = '0';

    public function mount(): void
    {
        $this->loadTargets();
        $this->weights = LeaderboardConfig::getWeights();
    }

    public function loadTargets(): void
    {
        $reps = User::role('sales')->where('is_active', true)->orderBy('name')->get();

        $this->targets = $reps->mapWithKeys(function (User $rep) {
            $userTargets = SalesTarget::where('user_id', $rep->id)
                ->pluck('target_value', 'type')
                ->toArray();

            return [$rep->id => [
                'name' => $rep->name,
                'monthly_orders' => $userTargets['monthly_orders'] ?? 0,
                'daily_orders' => $userTargets['daily_orders'] ?? 0,
                'reservations' => $userTargets['reservations'] ?? 0,
                'sales' => $userTargets['sales'] ?? 0,
            ]];
        })->toArray();
    }

    public function saveTargets(): void
    {
        foreach ($this->targets as $userId => $types) {
            foreach (['monthly_orders', 'daily_orders', 'reservations', 'sales'] as $type) {
                SalesTarget::updateOrCreate(
                    ['user_id' => $userId, 'type' => $type],
                    ['target_value' => (int) ($types[$type] ?? 0)]
                );
            }
        }

        session()->flash('success', 'تم حفظ الأهداف بنجاح');
    }

    public function applyDefaultToAll(): void
    {
        $value = (int) $this->defaultValue;
        foreach ($this->targets as $userId => $_) {
            foreach (['monthly_orders', 'daily_orders', 'reservations', 'sales'] as $type) {
                $this->targets[$userId][$type] = $value;
            }
        }
    }

    public function saveWeights(): void
    {
        $total = array_sum($this->weights);
        if (abs($total - 100) > 0.5) {
            session()->flash('error', 'مجموع الأوزان يجب أن يساوي 100%');

            return;
        }

        LeaderboardConfig::saveWeights($this->weights);
        session()->flash('success', 'تم حفظ الأوزان بنجاح');
    }

    public function render()
    {
        return view('livewire.mannager.sales-targets')->layout('layouts.custom');
    }
}
