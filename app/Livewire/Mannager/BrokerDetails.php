<?php

namespace App\Livewire\Mannager;

use App\Models\Broker;
use App\Models\UnitOrder;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class BrokerDetails extends Component
{
    public Broker $broker;

    public string $statusFilter = '';

    public function mount(Broker $broker): void
    {
        Gate::authorize('manage-brokers');

        $this->broker = $broker;
    }

    public function render(): mixed
    {
        $orders = $this->broker->orders()
            ->with(['project:id,name', 'unit:id,title'])
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(20);

        $totalOrders = $this->broker->orders()->count();
        $completedOrders = $this->broker->orders()->where('status', 4)->count();
        $pendingOrders = $this->broker->orders()->where('status', 0)->count();
        $activeOrders = $this->broker->orders()->whereIn('status', [1, 2])->count();

        return view('livewire.mannager.broker-details', [
            'orders' => $orders,
            'statusLabels' => UnitOrder::STATUS_LABELS,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'activeOrders' => $activeOrders,
        ])->layout('layouts.custom');
    }
}
