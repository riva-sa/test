<?php

namespace App\Livewire;

use App\Models\State;
use Livewire\Component;

class StatesGrid extends Component
{
    public $states;

    public function mount()
    {
        // Get states that have at least one project
        $statesWithProjects = State::with('city')
            ->withCount('projects')
            // ->whereHas('projects')  // This ensures only states with projects are selected
            ->orderBy('projects_count', 'desc')  // Optional: order by most projects first
            ->take(12)
            ->get();

        // Map the final result
        $this->states = $statesWithProjects->map(function($state) {
            return [
                'id' => $state->id,
                'name' => $state->name,
                'projects_count' => $state->projects_count,
                'height' => $this->getRandomHeight(),
                'city_id' => $state->city->id ?? 1,
                'photo' => $state->photo ? asset('storage/' . $state->photo) : asset('frontend/img/riva.jpg'),
            ];
        });
    }

    private function getRandomHeight()
    {
        // Maintain the varying heights from your original design
        $heights = ['300px', '350px', '400px', '450px', '500px'];
        return $heights[array_rand($heights)];
    }

    public function render()
    {
        return view('livewire.frontend.states-grid');
    }
}
