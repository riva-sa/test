<?php

namespace App\Livewire\Frontend\Partials;

use App\Models\Project;
use Illuminate\Support\Facades\Cache;
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
        $search = trim($this->search);

        if (strlen($search) > 2) {
            $cacheKey = 'navbar:search:'.md5(mb_strtolower($search));
            $this->results = Cache::remember($cacheKey, 30, function () use ($search) {
                return Project::query()
                    ->select(['id', 'name', 'slug'])
                    ->where('status', 1)
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%'.$search.'%')
                            ->orWhere('description', 'like', '%'.$search.'%')
                            ->orWhere('address', 'like', '%'.$search.'%');
                    })
                    ->latest('id')
                    ->take(5)
                    ->get();
            });
            $this->showDropdown = true;
        } else {
            $this->results = [];
            $this->showDropdown = false;
        }
    }

    public function render()
    {
        return view('livewire.frontend.partials.nav-bar');
    }
}
