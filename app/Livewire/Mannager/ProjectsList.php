<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use App\Models\City;
use App\Models\ProjectType;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectsList extends Component
{
    use WithPagination;

    public $search = '';
    public $city_id = '';
    public $project_type_id = '';
    public $commission_type = '';

    public function updating($field)
    {
        if (in_array($field, ['search', 'city_id', 'project_type_id', 'commission_type'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        $query = Project::with(['units', 'projectMedia', 'features', 'guarantees', 'projectLandmarks.landmark', 'developer', 'city', 'state', 'projectType', 'salesManager']);
        
        if (auth()->user()->hasRole('sales_manager')) {
            $query->where('sales_manager_id', auth()->id());
        }

        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->city_id)) {
            $query->where('city_id', $this->city_id);
        }

        if (!empty($this->project_type_id)) {
            $query->where('project_type_id', $this->project_type_id);
        }

        if (!empty($this->commission_type)) {
            $query->where('commission_type', $this->commission_type);
        }

        $projects = $query->latest()->paginate(10);
        $cities = City::all();
        $projectTypes = ProjectType::all();

        return view('livewire.mannager.projects-list', [
            'projects' => $projects,
            'cities' => $cities,
            'projectTypes' => $projectTypes,
        ])->layout('layouts.custom');
    }
}
