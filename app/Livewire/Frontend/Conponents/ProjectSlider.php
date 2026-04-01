<?php

namespace App\Livewire\Frontend\Conponents;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Computed;
use Livewire\Component;

class ProjectSlider extends Component
{
    public $type = null; // Optional parameter with default value

    public function mount($type = null)
    {
        $this->type = $type;
    }

    #[Computed]
    public function projects()
    {
        $globalVersion = Cache::get('projects_cache_version', 0);
        $cacheKey = 'home:project_slider:'.($this->type ?? 'default').':v:'.$globalVersion;

        return Cache::remember($cacheKey, 60, function () {
            return Project::query()
                ->latest()
                ->with(['projectMedia:id,project_id,media_url,media_type,main', 'developer:id,name,logo'])
                ->select(['id', 'name', 'slug', 'description', 'developer_id', 'is_featured', 'status'])
                ->where('status', 1)
                ->where('is_featured', 1)
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.frontend.conponents.project-slider', [
            'projects' => $this->projects(),
        ]);
    }
}
