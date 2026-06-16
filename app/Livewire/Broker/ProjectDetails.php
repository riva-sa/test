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

    public function mount($id)
    {
        $this->project = Project::with(['city', 'state', 'developer', 'projectType', 'projectMedia', 'features', 'guarantees'])
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
        $units = $this->project->units()
            // Brokers only see available units
            ->where('case', '0')
            ->when($this->unitTypeFilter, fn ($q) => $q->where('unit_type', $this->unitTypeFilter))
            ->orderBy('unit_price')
            ->paginate(12);

        return view('livewire.broker.project-details', [
            'units' => $units,
            'unitTypes' => $this->project->units()->where('case', '0')->whereNotNull('unit_type')->distinct()->pluck('unit_type'),
            'broker' => Auth::guard('broker')->user(),
        ])->layout('layouts.broker');
    }
}
