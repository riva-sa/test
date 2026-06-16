<?php

namespace App\Livewire\Broker;

use App\Models\City;
use App\Models\Developer;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Projects extends Component
{
    use WithPagination;

    public $search = '';

    public $cityFilter = '';

    public $developerFilter = '';

    public $unitTypeFilter = '';

    public $minPrice = '';

    public $maxPrice = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'cityFilter' => ['except' => ''],
        'developerFilter' => ['except' => ''],
        'unitTypeFilter' => ['except' => ''],
        'minPrice' => ['except' => ''],
        'maxPrice' => ['except' => ''],
    ];

    public function updating($name)
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'cityFilter', 'developerFilter', 'unitTypeFilter', 'minPrice', 'maxPrice']);
        $this->resetPage();
    }

    public function render()
    {
        $projects = Project::with(['city', 'developer', 'projectType', 'projectMedia'])
            ->withCount(['units', 'units as available_units_count' => fn ($q) => $q->where('case', '0')])
            ->where('status', true)
            // Brokers only see projects that have at least one available unit
            ->whereHas('units', fn ($sub) => $sub->where('case', '0'))
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->when($this->cityFilter, fn ($q) => $q->where('city_id', $this->cityFilter))
            ->when($this->developerFilter, fn ($q) => $q->where('developer_id', $this->developerFilter))
            ->when($this->unitTypeFilter, function ($q) {
                $q->whereHas('units', fn ($sub) => $sub->where('case', '0')->where('unit_type', $this->unitTypeFilter));
            })
            ->when($this->minPrice, function ($q) {
                $q->whereHas('units', fn ($sub) => $sub->where('case', '0')->where('unit_price', '>=', (float) $this->minPrice));
            })
            ->when($this->maxPrice, function ($q) {
                $q->whereHas('units', fn ($sub) => $sub->where('case', '0')->where('unit_price', '<=', (float) $this->maxPrice));
            })
            ->latest()
            ->paginate(12);

        return view('livewire.broker.projects', [
            'projects' => $projects,
            'cities' => City::orderBy('name')->get(),
            'developers' => Developer::orderBy('name')->get(),
            'unitTypes' => Unit::where('case', '0')->whereNotNull('unit_type')->distinct()->pluck('unit_type'),
            'broker' => Auth::guard('broker')->user(),
        ])->layout('layouts.broker');
    }
}
