<?php

namespace App\Livewire\Frontend;

use App\Models\City;
use App\Models\Developer;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\State;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

// #[Lazy]
#[Title('المشاريع')]
class ProjectsMap extends Component
{
    use LivewireAlert;

    public bool $showSideSheet = false;

    public ?Project $selectedProject = null;

    #[Url]
    public $selected_projectTypes = '';

    #[Url]
    public $selected_developer = '';

    #[Url]
    public $price_range = 0;

    #[Url]
    public $selected_cities = null;

    #[Url]
    public $selected_states = null;

    public function mount()
    {
        // Load initial data
    }

    #[On('showProject')]
    public function showProject($projectId)
    {
        $this->selectedProject = Project::query()
            ->with([
                'developer:id,name,logo',
                'projectMedia:id,project_id,media_url,media_type,main',
                'units:id,project_id,unit_price,unit_area,beadrooms,bathrooms,kitchen,case',
            ])
            ->select(['id', 'name', 'slug', 'developer_id', 'address', 'description', 'AdLicense', 'show_price'])
            ->find($projectId);
        $this->showSideSheet = true;
        $this->dispatch('side-sheet-updated');
    }

    public function closeSideSheet()
    {
        $this->showSideSheet = false;
        $this->selectedProject = null;
        $this->dispatch('side-sheet-updated');
    }

    public function updatedSelectedCities($value)
    {
        $this->selected_states = null;
    }

    protected function applyFilters($query)
    {
        // Apply project type filter
        if ($this->selected_projectTypes) {
            $query->where('project_type_id', $this->selected_projectTypes);
        }

        // Apply developer filter
        if ($this->selected_developer) {
            $query->where('developer_id', $this->selected_developer);
        }

        // Apply city filter
        if ($this->selected_cities) {
            $query->where('city_id', $this->selected_cities);
        }

        // Apply state filter
        if ($this->selected_states) {
            $query->where('state_id', $this->selected_states);
        }

        // Apply price range filter
        if ($this->price_range) {
            $query->whereHas('units', function ($query) {
                $query->where('unit_price', '<=', $this->price_range);
            });
        }

        return $query;
    }

    #[Computed]
    public function cities()
    {
        return Cache::remember('all_cities', 3600, function () {
            return City::all();
        });
    }

    #[Computed]
    public function states()
    {
        if ($this->selected_cities) {
            return Cache::remember('states_by_city:'.$this->selected_cities, 3600, function () {
                return State::where('city_id', $this->selected_cities)->get();
            });
        }

        return collect();
    }

    public function render()
    {
        $query = Project::query()
            ->with(['projectType:id,name,slug', 'developer:id,name,logo'])
            ->select(['id', 'name', 'slug', 'developer_id', 'project_type_id', 'latitude', 'longitude', 'address', 'status'])
            ->where('status', 1);

        $query = $this->applyFilters($query);

        $filters = [
            'selected_projectTypes' => $this->selected_projectTypes,
            'selected_developer' => $this->selected_developer,
            'price_range' => $this->price_range,
            'selected_cities' => $this->selected_cities,
            'selected_states' => $this->selected_states,
        ];
        $globalVersion = Cache::get('projects_cache_version', 0);
        $cacheKey = 'projects_map:'.md5(json_encode($filters)).':v:'.$globalVersion;

        $projects = Cache::remember($cacheKey, 60, function () use ($query) {
            return $query->get();
        });

        $this->dispatch('projectsUpdated', ['projects' => $projects]);

        $developers = Cache::remember('developers_all', 3600, function () {
            return Developer::select(['id', 'name', 'logo'])->get();
        });

        $projectTypes = Cache::remember('project_types_active', 3600, function () {
            return ProjectType::where('status', 1)->select(['id', 'name', 'slug'])->get();
        });

        return view('livewire.frontend.projects-map', [
            'developers' => $developers,
            'projectTypes' => $projectTypes,
            'projects' => $projects,
            'cities' => $this->cities(),
            'states' => $this->states(),
        ]);
    }
}
