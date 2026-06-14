<?php

namespace App\Livewire\Broker;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectDetails extends Component
{
    use WithPagination;

    public Project $project;

    public $unitCase = 'all';

    public $unitTypeFilter = '';

    public function mount($id)
    {
        $this->project = Project::with(['city', 'state', 'developer', 'projectType', 'projectMedia', 'features', 'guarantees'])
            ->where('status', true)
            ->findOrFail($id);
    }

    public function updating($name)
    {
        $this->resetPage();
    }

    public function render()
    {
        $units = $this->project->units()
            ->when($this->unitCase !== 'all', fn ($q) => $q->where('case', $this->unitCase))
            ->when($this->unitTypeFilter, fn ($q) => $q->where('unit_type', $this->unitTypeFilter))
            ->orderBy('unit_price')
            ->paginate(12);

        return view('livewire.broker.project-details', [
            'units' => $units,
            'unitTypes' => $this->project->units()->whereNotNull('unit_type')->distinct()->pluck('unit_type'),
        ])->layout('layouts.broker');
    }
}
