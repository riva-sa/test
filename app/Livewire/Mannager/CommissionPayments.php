<?php

namespace App\Livewire\Mannager;

use App\Models\BrokerCommissionPayment;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Read-only, system-wide ledger of every commission money event (payments and
 * reversals). This is the admin's single entry point to the financial audit trail.
 */
class CommissionPayments extends Component
{
    use WithPagination;

    public string $search = '';

    public string $actionFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'actionFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        Gate::authorize('pay-broker-commissions');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActionFilter(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        $base = BrokerCommissionPayment::query()
            ->when($this->actionFilter !== '', fn ($q) => $q->where('action', $this->actionFilter))
            ->when($this->search !== '', function ($q) {
                // Group the OR so it can't break out of the action filter above.
                $q->where(function ($group) {
                    $group->whereHas('broker', function ($sub) {
                        $sub->where('name', 'like', "%{$this->search}%")
                            ->orWhere('reference_number', 'like', "%{$this->search}%");
                    })->orWhere('payment_reference', 'like', "%{$this->search}%");
                });
            });

        $payments = (clone $base)
            ->with(['broker:id,name,reference_number', 'performedBy:id,name', 'commission:id,unit_order_id'])
            ->latest()
            ->paginate(25);

        return view('livewire.mannager.commission-payments', [
            'payments' => $payments,
            'actionLabels' => BrokerCommissionPayment::ACTION_LABELS,
            'totalPaid' => BrokerCommissionPayment::where('action', BrokerCommissionPayment::ACTION_PAID)->sum('amount'),
            'totalReversed' => BrokerCommissionPayment::where('action', BrokerCommissionPayment::ACTION_REVERSED)->sum('amount'),
        ])->layout('layouts.custom');
    }
}
