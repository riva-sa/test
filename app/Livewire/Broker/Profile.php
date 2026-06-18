<?php

namespace App\Livewire\Broker;

use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    /** Order status that represents a completed (sold) deal. */
    private const STATUS_COMPLETED = 4;

    public function render()
    {
        $broker = Auth::guard('broker')->user()->load('documents');

        // Sold units = completed orders attached to a unit. Each earns commission
        // based on the rate defined for that unit's project.
        $soldOrders = UnitOrder::where('broker_id', $broker->id)
            ->where('status', self::STATUS_COMPLETED)
            ->whereNotNull('unit_id')
            ->with(['unit:id,title,unit_price,project_id', 'unit.project:id,name,commission_type,commission_value'])
            ->latest()
            ->get();

        $sales = $soldOrders->map(function (UnitOrder $order) {
            $price = (float) ($order->unit->unit_price ?? 0);
            $project = $order->unit?->project;

            return [
                'unit'       => $order->unit?->title ?? '—',
                'project'    => $project?->name ?? '—',
                'price'      => $price,
                'commission' => $project ? $project->commissionForPrice($price) : 0,
                'date'       => $order->updated_at,
            ];
        });

        return view('livewire.broker.profile', [
            'broker' => $broker,
            'sales' => $sales,
            'totalCommission' => $sales->sum('commission'),
            'soldUnitsCount' => $sales->count(),
        ])->layout('layouts.broker');
    }
}
