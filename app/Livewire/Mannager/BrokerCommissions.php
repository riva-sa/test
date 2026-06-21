<?php

namespace App\Livewire\Mannager;

use App\Models\BrokerCommission;
use App\Models\Project;
use App\Traits\ManagesBrokerCommissions;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BrokerCommissions extends Component
{
    use ManagesBrokerCommissions, WithFileUploads, WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $projectFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
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

    public function updatingProjectFilter(): void
    {
        $this->resetPage();
    }

    /** Refresh hook used by the shared trait after an action. */
    public function loadData(): void
    {
        // Pagination + render handle the refresh; nothing extra needed here.
    }

    public function render(): mixed
    {
        $base = BrokerCommission::query()
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->projectFilter !== '', fn ($q) => $q->where('project_id', $this->projectFilter))
            ->when($this->search !== '', function ($q) {
                $q->whereHas('broker', function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            });

        $commissions = (clone $base)
            ->with(['broker:id,name,reference_number,iban', 'project:id,name', 'unit:id,title', 'paidBy:id,name'])
            ->latest()
            ->paginate(20);

        // Totals reflect the active filters (search/project), not the status one,
        // so the manager always sees the full money breakdown for the selection.
        $totalsQuery = BrokerCommission::query()
            ->when($this->projectFilter !== '', fn ($q) => $q->where('project_id', $this->projectFilter))
            ->when($this->search !== '', function ($q) {
                $q->whereHas('broker', function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            });

        $outstanding = (clone $totalsQuery)->outstanding()->sum('commission_amount');
        $paid = (clone $totalsQuery)->where('status', BrokerCommission::STATUS_PAID)->sum('commission_amount');
        $pendingCount = (clone $totalsQuery)->where('status', BrokerCommission::STATUS_PENDING)->count();

        return view('livewire.mannager.broker-commissions', [
            'commissions' => $commissions,
            'statusLabels' => BrokerCommission::STATUS_LABELS,
            'projects' => Project::whereHas('commissions')->orderBy('name')->get(['id', 'name']),
            'totalOutstanding' => $outstanding,
            'totalPaid' => $paid,
            'pendingCount' => $pendingCount,
        ])->layout('layouts.custom');
    }
}
