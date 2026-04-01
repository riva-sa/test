<?php

namespace App\Livewire\Frontend\Conponents;

use App\Models\Project;
use App\Models\ProjectType;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProjectsTab extends Component
{
    public $activeTab = 'all';

    public function mount() {}

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    #[Computed]
    public function projectTypes()
    {
        return Cache::remember('home:project_types', 300, function () {
            return ProjectType::all();
        });
    }

    #[Computed]
    public function projects()
    {
        $globalVersion = Cache::get('projects_cache_version', 0);
        $key = 'home:projects_tab:'.$this->activeTab.':v:'.$globalVersion;

        return Cache::remember($key, 60, function () {
            if ($this->activeTab === 'all') {
                return Project::query()
                    ->with([
                        'projectType:id,name,slug',
                        'developer:id,name,logo',
                        'projectMedia:id,project_id,media_url,media_type,main',
                        'units:id,project_id,unit_price,unit_area,beadrooms,bathrooms,kitchen,case',
                    ])
                    ->select(['id', 'name', 'slug', 'project_type_id', 'developer_id', 'status', 'show_price'])
                    ->where('status', 1)
                    ->whereHas('units', function ($query) {
                        $query->where('case', 0);
                    })
                    ->get();
            }

            return Project::query()
                ->with([
                    'projectType:id,name,slug',
                    'developer:id,name,logo',
                    'projectMedia:id,project_id,media_url,media_type,main',
                    'units:id,project_id,unit_price,unit_area,beadrooms,bathrooms,kitchen,case',
                ])
                ->select(['id', 'name', 'slug', 'project_type_id', 'developer_id', 'status', 'show_price'])
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
        return view('livewire.frontend.conponents.projects-tab', [
            'projects' => $this->projects(),
            'projectTypes' => $this->projectTypes(),
        ]);
    }
}
