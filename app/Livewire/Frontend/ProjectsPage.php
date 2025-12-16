<?php

namespace App\Livewire\Frontend;

use App\Models\City;
use App\Models\Developer;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\State;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('المشاريع')]
class ProjectsPage extends Component
{
    use LivewireAlert;
    use WithPagination;

    // Pagination theme
    protected $paginationTheme = 'bootstrap';

    #[Url]
    public $view_type = 'projects'; // Default view

    #[Url]
    public $projects_page = 1; // Pagination for projects

    #[Url]
    public $units_page = 1; // Pagination for units

    #[Url]
    public $selected_projectTypes = [];

    #[Url]
    public $selected_developer = [];

    #[Url]
    public $is_featured = false;

    #[Url]
    public $price_range = 0;

    #[Url]
    public $space_range = 0;

    #[URL]
    public $price_min = 0;

    #[URL]
    public $price_max = 10000000;

    #[URL]
    public $space_min = 0;

    #[URL]
    public $space_max = 5000;

    #[Url]
    public $selected_bedrooms = [];

    #[Url]
    public $selected_bathrooms = [];

    #[Url]
    public $selected_kitchens = [];

    #[Url]
    public $selected_cities = null;

    #[Url]
    public $selected_states = null;

    #[Url]
    public $projectCaseAvilable = true;

    public $sort_by = 'id';

    public $sort_direction = 'asc';

    public $states = [];

    public $cities = [];

    public function sortBy($field)
    {
        if ($this->sort_by === $field) {
            $this->sort_direction = $this->sort_direction === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $field;
            $this->sort_direction = 'asc';
        }
    }

    public function mount()
    {
        // Get all URL parameters
        $urlParams = request()->query();

        // Handle arrays in URL parameters
        $this->selected_projectTypes = ! empty($urlParams['selected_projectTypes']) ?
            (is_array($urlParams['selected_projectTypes']) ? $urlParams['selected_projectTypes'] : [$urlParams['selected_projectTypes']]) :
            [];

        $this->selected_developer = ! empty($urlParams['selected_developer']) ?
            (is_array($urlParams['selected_developer']) ? $urlParams['selected_developer'] : [$urlParams['selected_developer']]) :
            [];

        $this->selected_bedrooms = ! empty($urlParams['selected_bedrooms']) ?
            (is_array($urlParams['selected_bedrooms']) ? $urlParams['selected_bedrooms'] : [$urlParams['selected_bedrooms']]) :
            [];

        $this->selected_bathrooms = ! empty($urlParams['selected_bathrooms']) ?
            (is_array($urlParams['selected_bathrooms']) ? $urlParams['selected_bathrooms'] : [$urlParams['selected_bathrooms']]) :
            [];

        $this->selected_kitchens = ! empty($urlParams['selected_kitchens']) ?
            (is_array($urlParams['selected_kitchens']) ? $urlParams['selected_kitchens'] : [$urlParams['selected_kitchens']]) :
            [];

        // Handle boolean values
        $this->is_featured = isset($urlParams['is_featured']) ?
            filter_var($urlParams['is_featured'], FILTER_VALIDATE_BOOLEAN) :
            false;

        $this->projectCaseAvilable = isset($urlParams['projectCaseAvilable']) ? true : false;

        // Handle numeric values
        $this->price_range = $urlParams['price_range'] ?? 0;
        $this->space_range = $urlParams['space_range'] ?? null;
        $this->price_min = $urlParams['price_min'] ?? 0;
        $this->price_max = $urlParams['price_max'] ?? 10000000;
        $this->space_min = $urlParams['space_min'] ?? 0;
        $this->space_max = $urlParams['space_max'] ?? 5000;

        // Handle view type
        $this->view_type = $urlParams['view_type'] ?? 'projects';
        // $this->projectCaseAvilable = $urlParams['projectCaseAvilable'] ?? true;

        // Handle city and state
        $this->selected_cities = $urlParams['selected_cities'] ?? null;
        $this->selected_states = $urlParams['selected_states'] ?? null;

        // Load cities
        $this->cities = City::all();

        // Load states if city is selected
        if ($this->selected_cities) {
            $this->states = State::where('city_id', $this->selected_cities)->get();
        } else {
            $this->states = collect();
        }

        // Handle sorting
        $this->sort_by = $urlParams['sort_by'] ?? 'id';
        $this->sort_direction = $urlParams['sort_direction'] ?? 'asc';
    }

    protected $queryString = [
        'view_type' => ['except' => 'projects'],
        'projects_page' => ['except' => 1],
        'units_page' => ['except' => 1],
        'selected_projectTypes' => ['except' => []],
        'selected_developer' => ['except' => []],
        'is_featured' => ['except' => false],
        'projectCaseAvilable' => ['except' => true],
        'price_range' => ['except' => 0],
        'space_range' => ['except' => null],
        'price_min' => ['except' => 0],
        'price_max' => ['except' => 10000000],
        'space_min' => ['except' => 0],
        'space_max' => ['except' => 5000],
        'selected_bedrooms' => ['except' => []],
        'selected_bathrooms' => ['except' => []],
        'selected_kitchens' => ['except' => []],
        'selected_cities' => ['except' => null],
        'selected_states' => ['except' => null],
        'sort_by' => ['except' => 'id'],
        'sort_direction' => ['except' => 'asc'],
    ];

    // Update state based on selected city
    public function updatedSelectedCities($value)
    {
        if ($value) {
            $this->states = State::where('city_id', $value)->get();
            // Reset state when city changes
            $this->selected_states = null;
        } else {
            $this->states = collect();
            $this->selected_states = null;
        }
    }

    // Reset pagination when switching tabs
    public function updatedViewType($value)
    {
        if ($value === 'projects') {
            $this->resetPage('units_page'); // Reset units pagination
        } else {
            $this->resetPage('projects_page'); // Reset projects pagination
        }
    }

    public function showUnitDetails($unitId)
    {
        $this->dispatch('loadUnit', [
            'unitId' => $unitId,
        ]);
    }

    public function render()
    {
        $query = null;

        if ($this->view_type === 'projects') {
            $page = (int) request()->query('projects_page', 1);
            $filters = [
                'selected_projectTypes' => $this->selected_projectTypes,
                'selected_developer' => $this->selected_developer,
                'is_featured' => $this->is_featured,
                'selected_cities' => $this->selected_cities,
                'selected_states' => $this->selected_states,
                'price_min' => $this->price_min,
                'price_max' => $this->price_max,
                'space_min' => $this->space_min,
                'space_max' => $this->space_max,
                'sort_by' => $this->sort_by,
                'sort_direction' => $this->sort_direction,
                'projectCaseAvilable' => $this->projectCaseAvilable,
            ];
            $globalVersion = Cache::get('projects_cache_version', 0);
            $cacheKey = 'projects_page:projects:'.md5(json_encode($filters)).':page:'.$page.':v:'.$globalVersion;

            // Prepare subquery for unit counts
            $unitCounts = Unit::toBase()
                ->select('project_id')
                ->selectRaw('count(case when "case" = 0 then 1 end) as available_units_count')
                ->selectRaw('count(case when "case" = 1 then 1 end) as reserved_units_count')
                ->selectRaw('count(case when "case" = 2 then 1 end) as sold_units_count')
                ->groupBy('project_id');

            $query = Project::with(['projectType', 'developer', 'units'])
                ->leftJoinSub($unitCounts, 'unit_counts', 'projects.id', '=', 'unit_counts.project_id')
                ->select('projects.*', 'unit_counts.available_units_count', 'unit_counts.reserved_units_count', 'unit_counts.sold_units_count')
                ->where('status', 1)
                ->where('unit_counts.available_units_count', '>', 0) // Hide sold out or empty projects
                ->orderByRaw('
                CASE
                    WHEN unit_counts.available_units_count > 0 THEN 1
                    WHEN unit_counts.reserved_units_count > 0 THEN 2
                    ELSE 3
                END
            ');

            if (! empty($this->selected_projectTypes)) {
                $query->whereIn('project_type_id', $this->selected_projectTypes);
            }

            if (! empty($this->selected_developer)) {
                $query->whereIn('developer_id', $this->selected_developer);
            }

            if ($this->is_featured) {
                $query->where('is_featured', 1);
            }

            if ($this->selected_cities) {
                $query->where('city_id', $this->selected_cities);
            }

            if ($this->selected_states) {
                $query->where('state_id', $this->selected_states);
            }

            if (! empty($this->selected_bedrooms)) {
                $query->whereHas('units', function ($q) {
                    $q->whereIn('beadrooms', $this->selected_bedrooms);
                });
            }

            if ($this->projectCaseAvilable == true) {
                $query->whereHas('units', function ($q) {
                    $q->where('case', 0);
                });
            }

            if (! empty($this->selected_bathrooms)) {
                $query->whereHas('units', function ($q) {
                    $q->whereIn('bathrooms', $this->selected_bathrooms);
                });
            }

            if (! empty($this->selected_kitchens)) {
                $query->whereHas('units', function ($q) {
                    $q->whereIn('kitchen', $this->selected_kitchens);
                });
            }

            // Add price range filter
            if ($this->price_min > 0 || $this->price_max < 10000000) {
                $query->whereHas('units', function ($q) {
                    $q->whereBetween('unit_price', [$this->price_min, $this->price_max]);
                });
            }

            // Add space range filter
            if ($this->space_min > 0 || $this->space_max < 10000) {
                $query->whereHas('units', function ($q) {
                    $q->whereBetween('unit_area', [$this->space_min, $this->space_max]);
                });
            }

            // Custom ordering by unit case priority
            // if ($this->sort_by === 'unit_case_priority') {
            //     $query->orderByRaw('case_0_count DESC, case_1_count DESC, case_2_count DESC');
            // } elseif ($this->sort_by === 'unit_price') {
            //     $query->addSelect([
            //         'min_unit_price' => Unit::selectRaw('MIN(unit_price)')
            //             ->whereColumn('units.project_id', 'projects.id')
            //             ->limit(1)
            //     ])->orderBy('min_unit_price', $this->sort_direction);
            // } else {
            //     $query->orderBy($this->sort_by, $this->sort_direction);
            // }

            // Use projects_page for pagination with cache
            $items = Cache::remember($cacheKey, 60, function () use ($query) {
                return $query->paginate(18, ['*'], 'projects_page');
            });
        } else {
            $page = (int) request()->query('units_page', 1);
            $filters = [
                'selected_projectTypes' => $this->selected_projectTypes,
                'selected_developer' => $this->selected_developer,
                'is_featured' => $this->is_featured,
                'selected_cities' => $this->selected_cities,
                'selected_states' => $this->selected_states,
                'price_min' => $this->price_min,
                'price_max' => $this->price_max,
                'space_min' => $this->space_min,
                'space_max' => $this->space_max,
                'sort_by' => $this->sort_by,
                'sort_direction' => $this->sort_direction,
                'projectCaseAvilable' => $this->projectCaseAvilable,
            ];
            $globalVersion = Cache::get('projects_cache_version', 0);
            $cacheKey = 'projects_page:units:'.md5(json_encode($filters)).':page:'.$page.':v:'.$globalVersion;
            $query = Unit::with(['project', 'project.developer'])
                ->whereHas('project', function ($q) {
                    $q->where('status', 1);
                })
                ->where('status', 1)
                ->where('case', 0)
                ->OrWhere('case', 1)
                ->select(['id', 'title', 'unit_type', 'unit_price', 'unit_area', 'beadrooms', 'bathrooms', 'floor_plan', 'case', 'show_price', 'project_id'])
                ->addSelect([
                    'available_units_count' => Unit::selectRaw('count(*)')
                        ->whereColumn('project_id', 'units.project_id')
                        ->where('case', '0')
                        ->limit(1),
                    'reserved_units_count' => Unit::selectRaw('count(*)')
                        ->whereColumn('project_id', 'units.project_id')
                        ->where('case', '1')
                        ->limit(1),
                ])
                ->orderByRaw("
                    CASE
                        WHEN (select count(*) from units u where u.project_id = units.project_id and u.case = '0') > 0 THEN 1
                        WHEN (select count(*) from units u where u.project_id = units.project_id and u.case = '1') > 0 THEN 2
                        ELSE 3
                    END
                ");

            if (! empty($this->selected_projectTypes)) {
                $query->whereHas('project', function ($q) {
                    $q->whereIn('project_type_id', $this->selected_projectTypes);
                });
            }

            if (! empty($this->selected_developer)) {
                $query->whereHas('project', function ($q) {
                    $q->whereIn('developer_id', $this->selected_developer);
                });
            }

            if ($this->is_featured) {
                $query->whereHas('project', function ($q) {
                    $q->where('is_featured', 1);
                });
            }
            if ($this->projectCaseAvilable == true) {
                $query->where('case', 0);
            }

            if ($this->selected_cities) {
                $query->whereHas('project', function ($q) {
                    $q->where('city_id', $this->selected_cities);
                });
            }

            if ($this->selected_states) {
                $query->whereHas('project', function ($q) {
                    $q->where('state_id', $this->selected_states);
                });
            }

            if ($this->price_range > 0) {
                $query->where('unit_price', '<=', $this->price_range);
            }

            // Add price range filter
            if ($this->price_min > 0 || $this->price_max < 10000000) {
                $query->whereBetween('unit_price', [$this->price_min, $this->price_max]);
            }

            // Add space range filter
            if ($this->space_min > 0 || $this->space_max < 10000) {
                $query->whereBetween('unit_area', [$this->space_min, $this->space_max]);
            }

            if (! empty($this->selected_bedrooms)) {
                $query->where('beadrooms', $this->selected_bedrooms);
            }

            if (! empty($this->selected_bathrooms)) {
                $query->where('bathrooms', $this->selected_bathrooms);
            }

            if (! empty($this->selected_kitchens)) {
                $query->where('kitchen', $this->selected_kitchens);
            }

            $query->orderBy($this->sort_by, $this->sort_direction);

            // Use units_page for pagination with cache
            $items = Cache::remember($cacheKey, 60, function () use ($query) {
                return $query->paginate(12, ['*'], 'units_page');
            });
        }
        
        // Cache static data for 1 hour
        $developers = Cache::remember('developers_all', 3600, function () {
            return Developer::all();
        });
        
        $projectTypes = Cache::remember('project_types_active', 3600, function () {
            return ProjectType::where('status', 1)->get();
        });

        return view('livewire.frontend.projects-page', [
            'items' => $items,
            'developers' => $developers,
            'projectTypes' => $projectTypes,
        ]);
    }
}
