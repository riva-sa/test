<?php

namespace App\Livewire\Mannager;

use App\Models\Broker;
use App\Models\BrokerCommission;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Brokers extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount(): void
    {
        Gate::authorize('manage-brokers');
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function render(): mixed
    {
        $outstanding = [BrokerCommission::STATUS_PENDING, BrokerCommission::STATUS_APPROVED];

        $brokers = Broker::query()
            ->withCount(['orders', 'commissions as sold_deals_count' => fn ($q) => $q->whereNot('status', BrokerCommission::STATUS_VOID)])
            ->withSum(['commissions as earned_total' => fn ($q) => $q->whereNot('status', BrokerCommission::STATUS_VOID)], 'commission_amount')
            ->withSum(['commissions as paid_total' => fn ($q) => $q->where('status', BrokerCommission::STATUS_PAID)], 'commission_amount')
            ->withSum(['commissions as outstanding_total' => fn ($q) => $q->whereIn('status', $outstanding)], 'commission_amount')
            ->when($this->search !== '', function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%")
                        ->orWhere('whatsapp', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->orderByDesc('outstanding_total')
            ->paginate(20);

        return view('livewire.mannager.brokers', [
            'brokers' => $brokers,
            'statusLabels' => Broker::STATUS_LABELS,
        ])->layout('layouts.custom');
    }
}
