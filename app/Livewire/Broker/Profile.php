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

        // Sold units = completed orders attached to a unit. Each earns commission.
        $soldOrders = UnitOrder::where('broker_id', $broker->id)
            ->where('status', self::STATUS_COMPLETED)
            ->whereNotNull('unit_id')
            ->with(['unit:id,title,unit_price,project_id', 'unit.project:id,name'])
            ->latest()
            ->get();

        $sales = $soldOrders->map(function (UnitOrder $order) use ($broker) {
            $price = (float) ($order->unit->unit_price ?? 0);

            return [
                'unit'       => $order->unit?->title ?? '—',
                'project'    => $order->unit?->project?->name ?? '—',
                'price'      => $price,
                'commission' => $broker->commissionForPrice($price),
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
