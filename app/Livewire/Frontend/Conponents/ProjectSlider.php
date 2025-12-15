<?php

namespace App\Livewire\Frontend\Conponents;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ProjectSlider extends Component
{
    public $projects;

    public $type = null; // Optional parameter with default value

    public function mount($type = null)
    {
        $this->type = $type;
        $this->loadProjects();
    }

    public function loadProjects()
    {
        $cacheKey = 'home:project_slider:'.($this->type ?? 'default');
        $this->projects = Cache::remember($cacheKey, 60, function () {
            return Project::query()
                ->latest()
                ->with(['projectMedia', 'developer'])
                ->where('status', 1)
                ->where('is_featured', 1)
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.frontend.conponents.project-slider');
    }
}
