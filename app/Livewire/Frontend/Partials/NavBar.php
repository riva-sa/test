<?php

namespace App\Livewire\Frontend\Partials;

use App\Models\Project;
use Livewire\Component;

class NavBar extends Component
{
    public $search = '';
    public $results = [];
    public $showDropdown = false;

    public function closeDropdown()
    {
        $this->showDropdown = false;
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->results = [];
        $this->showDropdown = false;
    }
    public function selectProperty($slug)
    {
        return redirect()->route('frontend.projects', $slug);
    }

    protected $listeners = ['clickedAway' => 'closeDropdown'];

    // Method that runs when the search input changes
    public function updatedSearch()
    {
        // Perform the search when more than 2 characters are entered
        if (strlen($this->search) > 2) {
            $this->results = Project::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhere('address', 'like', '%' . $this->search . '%')
                ->take(5) // Limit the results to 5
                ->get();
        } else {
            $this->results = []; // Clear results if search input is less than 3 characters
        }
    }
    public function render()
    {
        return view('livewire.frontend.partials.nav-bar');
    }
}
