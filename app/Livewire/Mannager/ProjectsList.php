<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use App\Models\City;
use App\Models\State;
use App\Models\Developer;
use App\Models\ProjectType;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsList extends Component
{
    use WithPagination;

    public $search = '';
    public $city_id = '';
    public $state_id = '';
    public $developer_id = '';
    public $sales_manager_id = '';
    public $project_type_id = '';
    public $commission_type = '';
    public $has_available_units = false;
    public $has_virtual_tour = false;
    public $has_ad_license = false;
    public $min_price = '';
    public $max_price = '';
    public $min_area = '';
    public $max_area = '';
    public $bedrooms = '';
    public $sort_by = 'latest';

    public $showAdvancedFilters = false;

    public function updated($propertyName)
    {
        if ($propertyName === 'city_id') {
            $this->state_id = '';
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset([
            'search',
            'city_id',
            'state_id',
            'developer_id',
            'sales_manager_id',
            'project_type_id',
            'commission_type',
            'has_available_units',
            'has_virtual_tour',
            'has_ad_license',
            'min_price',
            'max_price',
            'min_area',
            'max_area',
            'bedrooms',
            'sort_by',
        ]);
        $this->resetPage();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function render()
    {
        $query = Project::with([
            'units', 
            'projectMedia', 
            'features', 
            'guarantees', 
            'projectLandmarks.landmark', 
            'developer', 
            'city', 
            'state', 
            'projectType', 
            'salesManager'
        ])->where('status', 1)->whereHas('units', function ($uq) {
            $uq->where('case', 0);
        });

        // Text Search
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('units', function($uq) {
                      $uq->where('unit_number', 'like', '%' . $this->search . '%')
                         ->orWhere('title', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Location & Attributes Filters
        if (!empty($this->city_id)) {
            $query->where('city_id', $this->city_id);
        }

        if (!empty($this->state_id)) {
            $query->where('state_id', $this->state_id);
        }

        if (!empty($this->developer_id)) {
            $query->where('developer_id', $this->developer_id);
        }

        if (!empty($this->project_type_id)) {
            $query->where('project_type_id', $this->project_type_id);
        }

        if (!empty($this->commission_type)) {
            $query->where('commission_type', $this->commission_type);
        }

        // Feature / License Flags
        if ($this->has_virtual_tour) {
            $query->whereNotNull('virtualTour')->where('virtualTour', '!=', '');
        }

        if ($this->has_ad_license) {
            $query->whereNotNull('AdLicense')->where('AdLicense', '!=', '');
        }

        // Unit-based filtering (Price, Area, Bedrooms, Available Status)
        if ($this->has_available_units || !empty($this->min_price) || !empty($this->max_price) || !empty($this->min_area) || !empty($this->max_area) || !empty($this->bedrooms)) {
            $query->whereHas('units', function($uq) {
                if ($this->has_available_units) {
                    $uq->where('case', 0); // 0 = available
                }
                if (!empty($this->min_price)) {
                    $uq->where('unit_price', '>=', $this->min_price);
                }
                if (!empty($this->max_price)) {
                    $uq->where('unit_price', '<=', $this->max_price);
                }
                if (!empty($this->min_area)) {
                    $uq->where('unit_area', '>=', $this->min_area);
                }
                if (!empty($this->max_area)) {
                    $uq->where('unit_area', '<=', $this->max_area);
                }
                if (!empty($this->bedrooms)) {
                    if ($this->bedrooms === '5+') {
                        $uq->where('beadrooms', '>=', 5);
                    } else {
                        $uq->where('beadrooms', $this->bedrooms);
                    }
                }
            });
        }

        // Sorting
        switch ($this->sort_by) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'units_desc':
                $query->withCount('units')->orderBy('units_count', 'desc');
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        $projects = $query->paginate(12);

        $cities = City::all();
        $states = !empty($this->city_id) ? State::where('city_id', $this->city_id)->get() : collect();
        $projectTypes = ProjectType::all();
        $developers = Developer::all();
        $salesManagers = User::role('sales_manager')->get();

        return view('livewire.mannager.projects-list', [
            'projects' => $projects,
            'cities' => $cities,
            'states' => $states,
            'projectTypes' => $projectTypes,
            'developers' => $developers,
            'salesManagers' => $salesManagers,
        ])->layout('layouts.custom');
    }
}
