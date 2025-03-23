<?php

namespace App\Livewire\Frontend\Conponents;

use Livewire\Component;
use App\Models\Project;
use App\Models\ProjectType;

class ProjectsTab extends Component
{
    public $activeTab = 'all';
    public $projectTypes;
    public $projects;

    public function mount()
    {
        $this->projectTypes = ProjectType::all();
        $this->loadProjects();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadProjects();
    }

    public function loadProjects()
    {
        if ($this->activeTab === 'all') {
            $this->projects = Project::with(['projectType', 'developer', 'units'])
                ->where('status', 1)
                ->whereHas('units', function($query) {
                    $query->where('case', 0);
                })
                ->get();
        } else {
            $this->projects = Project::with(['developer', 'units'])
                ->where('status', 1)
                ->whereHas('projectType', function($query) {
                    $query->where('slug', $this->activeTab);
                })
                ->whereHas('units', function($query) {
                    $query->where('case', 0);
                })
                ->get();
        }
    }

    public function render()
    {
        return view('livewire.frontend.conponents.projects-tab');
    }
}
