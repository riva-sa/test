<?php

namespace App\Livewire\Frontend;

use App\Models\Project;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectSingle extends Component
{
    use WithPagination;

    public Project $project;
    public $activeTab = 'units';

    public $case = 'all';
    protected $queryString = ['case'];

    public function showUnitDetails($unitId)
    {
        $this->dispatch('loadUnit', [
            'unitId' => $unitId
        ]);
    }

    public function showOrderPopup($projectId)
    {
        $this->dispatch('UnitOrderOpen', [
            'projectId' => $projectId
        ]);
    }

    public function updatingCase()
    {
        $this->resetPage();
    }

    public function getUnits()
    {
        $query = Unit::query()
            ->where('project_id', $this->project->id)
            ->where('status', 1);

        if ($this->case !== 'all') {
            $query->where('case', (int)$this->case);
        }

        return $query->latest()->get();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount($slug)
    {
        $this->project = Project::where('slug', $slug)->firstOrFail();
        $this->case = request()->query('case', 'all');
    }

    public function render()
    {
        $units = $this->getUnits();

        return view('livewire.frontend.project-single', [ 'units' => $units ]);
    }
}
