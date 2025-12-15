<?php

namespace App\Livewire\Frontend\Conponents;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ProjectsTab extends Component
{
    public $activeTab = 'all';

    public $projectTypes;

    public $projects;

    public function mount()
    {
        $this->projectTypes = Cache::remember('home:project_types', 300, function () {
            return ProjectType::all();
        });
        $this->loadProjects();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadProjects();
    }

    public function loadProjects()
    {
        $key = 'home:projects_tab:'.$this->activeTab;
        $this->projects = Cache::remember($key, 60, function () {
            if ($this->activeTab === 'all') {
                return Project::with(['projectType', 'developer', 'units'])
                    ->where('status', 1)
                    ->whereHas('units', function ($query) {
                        $query->where('case', 0);
                    })
                    ->get();
            }

            return Project::with(['developer', 'units'])
                ->where('status', 1)
                ->whereHas('projectType', function ($query) {
                    $query->where('slug', $this->activeTab);
                })
                ->whereHas('units', function ($query) {
                    $query->where('case', 0);
                })
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.frontend.conponents.projects-tab');
    }
}
