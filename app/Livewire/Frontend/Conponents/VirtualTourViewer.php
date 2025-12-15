<?php

namespace App\Livewire\Frontend\Conponents;

use Livewire\Component;

class VirtualTourViewer extends Component
{
    public $project;

    public $virtualTour;

    public function mount($project)
    {
        $this->project = $project;
        $this->virtualTour = $this->project->virtualTour;
    }

    public function render()
    {
        return view('livewire.frontend.conponents.virtual-tour-viewer');
    }
}
