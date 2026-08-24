<?php

namespace App\Livewire\Broker;

use App\Models\Project;
use App\Services\ProjectPriceListService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectDetails extends Component
{
    use WithPagination;

    public Project $project;

    public $unitTypeFilter = '';

    public $unitStatusFilter = '';

    public $searchUnit = '';

    public function mount($id)
    {
        $this->project = Project::with(['city', 'state', 'developer', 'projectType', 'projectMedia', 'features', 'guarantees', 'landmarks'])
            ->where('status', true)
            // Brokers can only open projects that have at least one available unit
            ->whereHas('units', fn ($q) => $q->where('case', '0'))
            ->findOrFail($id);
    }

    public function updating($name)
    {
        $this->resetPage();
    }

    /**
     * Stream a freshly generated price-list PDF of the project's available units.
     */
    public function downloadPriceList(ProjectPriceListService $service)
    {
        $pdf = $service->generate($this->project);
        $fileName = $service->fileName($this->project);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf;
        }, $fileName, ['Content-Type' => 'application/pdf']);
    }

    public function render()
    {
        $driver = $this->project->units()->getConnection()->getDriverName();
        $caseField = $driver === 'mysql' ? '`case`' : '"case"';

        $units = $this->project->units()
            ->with('features')
            // Show all units (available, reserved, sold) — available first.
            ->when($this->unitTypeFilter, fn ($q) => $q->where('unit_type', $this->unitTypeFilter))
            ->when($this->unitStatusFilter !== '', fn ($q) => $q->where('case', $this->unitStatusFilter))
            ->when($this->searchUnit !== '', function ($q) {
                $search = '%' . trim($this->searchUnit) . '%';
                $q->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', $search)
                        ->orWhere('unit_number', 'like', $search)
                        ->orWhere('floor', 'like', $search);
                });
            })
            ->orderByRaw("
                CASE {$caseField}
                    WHEN 0 THEN 1
                    WHEN 1 THEN 2
                    WHEN 3 THEN 3
                    WHEN 2 THEN 4
                    ELSE 5
                END
            ")
            ->orderBy('unit_price')
            ->paginate(12);

        return view('livewire.broker.project-details', [
            'units' => $units,
            'unitTypes' => $this->project->units()->whereNotNull('unit_type')->distinct()->pluck('unit_type'),
            'broker' => Auth::guard('broker')->user(),
        ])->layout('layouts.broker');
    }
}
