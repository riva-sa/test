<?php

namespace App\Livewire\Broker;

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
            'projects' => Project::where('status', true)->count(),
            'units' => Unit::where('case', '0')->count(),
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
