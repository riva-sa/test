<?php

namespace App\Livewire\Frontend;

use App\Models\Project;
use App\Models\Unit;
use App\Services\TrackingService;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectSingle extends Component
{
    use WithPagination;

    public Project $project;

    public $activeTab = 'units';

    public $case = 'all';

    protected $queryString = ['case'];

    protected $trackingService;

    public function boot(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function showUnitDetails($unitId)
    {
        $unit = Unit::find($unitId);
        if ($unit) {
            $this->trackingService->trackUnitShow($unit);
        }
        $this->dispatch('loadUnit', [
            'unitId' => $unitId,
        ]);
    }

    public function showOrderPopup($projectId)
    {
        // Track project order popup show
        $project = Project::find($projectId);
        if ($project) {
            $this->trackingService->trackProjectOrderShow($project);
        }
        $this->dispatch('UnitOrderOpen', [
            'projectId' => $projectId,
        ]);
    }

    public function updatingCase()
    {
        $this->resetPage();
    }

    protected $paginationTheme = 'bootstrap';

    public function getUnits()
    {
        $query = Unit::query()
            ->where('project_id', $this->project->id)
            ->where('status', 1)
            ->select(['id', 'title', 'unit_type', 'unit_price', 'unit_area', 'beadrooms', 'bathrooms', 'floor_plan', 'case', 'show_price']);

        if ($this->case !== 'all') {
            $query->where('case', (int) $this->case);
        }

        $page = (int) request()->query('page', 1);
        $version = Cache::get('project_cache_version:'.$this->project->id, optional($this->project->updated_at)->getTimestamp() ?? 0);
        $key = 'project_units:'.$this->project->id.':'.$this->case.':'.$page.':v:'.$version;

        return Cache::remember($key, 60, function () use ($query) {
            return $query->orderBy('id', 'desc')->simplePaginate(12);
        });
    }

    public function setActiveTab($tab)
    {
        // Track tab view as additional tracking
        $this->trackingService->trackProjectVisit($this->project);
        $this->activeTab = $tab;
    }

    public function mount($slug)
    {

        $this->project = Project::where('slug', $slug)->firstOrFail();
        $this->case = request()->query('case', 'all');
        // Track project visit
        app(TrackingService::class)->trackProjectVisit($this->project);
    }

    public function render()
    {
        $units = $this->getUnits();

        return view('livewire.frontend.project-single', ['units' => $units]);
    }
}
