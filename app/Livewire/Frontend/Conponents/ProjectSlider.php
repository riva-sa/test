<?php

namespace App\Livewire\Frontend\Conponents;

use Livewire\Component;
use App\Models\Project;

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
        $query = Project::query();
        $this->projects = $query->latest()->with('projectMedia', 'developer')->where('status', 1)->where('is_featured', 1)->get();
    }

    public function render()
    {
        return view('livewire.frontend.conponents.project-slider');
    }
}
