<?php

namespace App\Livewire\Broker;

use App\Models\BrokerCommission;
use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $broker = Auth::guard('broker')->user();

        $base = UnitOrder::forBroker($broker);

        $stats = [
            'total' => (clone $base)->count(),
            'processing' => (clone $base)->whereIn('status', [0, 1, 2, 5])->count(),
            'completed' => (clone $base)->where('status', 4)->count(),
            'not_interested' => (clone $base)->where('status', 3)->count(),
            'projects' => Project::where('status', true)
                ->whereHas('units', fn ($q) => $q->where('case', '0'))
                ->count(),
            'units' => Unit::where('case', '0')->count(),
            // Brokers only see money the admin has approved (approved + paid).
            'earned' => (float) $broker->commissions()->whereIn('status', [BrokerCommission::STATUS_APPROVED, BrokerCommission::STATUS_PAID])->sum('commission_amount'),
            'paid' => (float) $broker->commissions()->where('status', BrokerCommission::STATUS_PAID)->sum('commission_amount'),
            'outstanding' => (float) $broker->commissions()->where('status', BrokerCommission::STATUS_APPROVED)->sum('commission_amount'),
            // Deals still awaiting admin approval — shown as a count only, no amount.
            'under_review' => $broker->commissions()->where('status', BrokerCommission::STATUS_PENDING)->count(),
        ];

        $latestLeads = (clone $base)
            ->with(['project', 'unit'])
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.broker.dashboard', [
            'broker' => $broker,
            'stats' => $stats,
            'latestLeads' => $latestLeads,
        ])->layout('layouts.broker');
    }
}
