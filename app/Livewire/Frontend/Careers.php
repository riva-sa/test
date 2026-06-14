<?php

namespace App\Livewire\Frontend;

use App\Models\JobPosting;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Careers extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $searchTerm = '';

    #[Url]
    public $department = '';

    #[Url]
    public $location = '';

    #[Url(as: 'type')]
    public $employmentType = '';

    public $jobsPerPage = 9;

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingDepartment()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingEmploymentType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['searchTerm', 'department', 'location', 'employmentType']);
        $this->resetPage();
    }

    public function render()
    {
        $query = JobPosting::query()->published();

        if ($this->searchTerm) {
            $term = '%'.$this->searchTerm.'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('title_en', 'like', $term)
                    ->orWhere('department', 'like', $term)
                    ->orWhere('department_en', 'like', $term);
            });
        }

        if ($this->department) {
            $query->where(function ($q) {
                $q->where('department', $this->department)
                    ->orWhere('department_en', $this->department);
            });
        }

        if ($this->location) {
            $query->where(function ($q) {
                $q->where('location', $this->location)
                    ->orWhere('location_en', $this->location);
            });
        }

        if ($this->employmentType) {
            $query->where('employment_type', $this->employmentType);
        }

        $jobs = $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate($this->jobsPerPage);

        // Filter options come from the published jobs themselves. Models are
        // hydrated (get, not pluck) so HasTranslations localizes the values.
        $publishedJobs = JobPosting::published()
            ->get(['department', 'department_en', 'location', 'location_en', 'employment_type']);

        $departments = $publishedJobs->pluck('department')->filter()->unique()->sort()->values();
        $locations = $publishedJobs->pluck('location')->filter()->unique()->sort()->values();
        $employmentTypes = $publishedJobs->pluck('employment_type')->unique()->values();

        return view('livewire.frontend.careers', [
            'jobs' => $jobs,
            'departments' => $departments,
            'locations' => $locations,
            'employmentTypes' => $employmentTypes,
        ]);
    }
}
