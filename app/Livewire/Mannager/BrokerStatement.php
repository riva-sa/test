<?php

namespace App\Livewire\Mannager;

use App\Models\Broker;
use App\Models\BrokerCommission;
use App\Traits\ManagesBrokerCommissions;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class BrokerStatement extends Component
{
    use ManagesBrokerCommissions, WithFileUploads;

    public Broker $broker;

    public string $statusFilter = '';

    public function mount(Broker $broker): void
    {
        Gate::authorize('manage-brokers');

        $this->broker = $broker;
    }

    public function render(): mixed
    {
        $commissions = $this->broker->commissions()
            ->with(['project:id,name', 'unit:id,title', 'paidBy:id,name'])
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->get();

        $active = $this->broker->commissions()->whereNot('status', BrokerCommission::STATUS_VOID);

        // Immutable money-event audit trail (payments + reversals) for this broker.
        $payments = \App\Models\BrokerCommissionPayment::where('broker_id', $this->broker->id)
            ->with(['performedBy:id,name', 'commission:id,unit_order_id'])
            ->latest()
            ->get();

        return view('livewire.mannager.broker-statement', [
            'commissions' => $commissions,
            'statusLabels' => BrokerCommission::STATUS_LABELS,
            'payments' => $payments,
            'earnedTotal' => (clone $active)->sum('commission_amount'),
            'paidTotal' => $this->broker->commissions()->where('status', BrokerCommission::STATUS_PAID)->sum('commission_amount'),
            'outstandingTotal' => $this->broker->commissions()->outstanding()->sum('commission_amount'),
            'soldCount' => (clone $active)->count(),
        ])->layout('layouts.custom');
    }
}
