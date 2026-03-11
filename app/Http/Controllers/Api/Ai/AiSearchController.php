<?php

namespace App\Http\Controllers\Api\Ai;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Developer;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\State;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AiSearchController extends Controller
{

    // =========================================================================
    //  NAME → ID RESOLVERS
    //  The AI sends human-readable names (Arabic or English).
    //  These helpers do a case-insensitive LIKE lookup and return the ID,
    //  or null if nothing matched (so the filter is simply skipped).
    // =========================================================================

    private function resolveCityId(?string $name): ?int
    {
        if (!$name) return null;
        return City::where('name', 'like', "%{$name}%")->first()?->id;
    }

    private function resolveStateId(?string $name, ?int $cityId = null): ?int
    {
        if (!$name) return null;
        $query = State::where('name', 'like', "%{$name}%");
        if ($cityId) $query->where('city_id', $cityId);
        return $query->first()?->id;
    }

    private function resolveDeveloperId(?string $name): ?int
    {
        if (!$name) return null;
        return Developer::where('name', 'like', "%{$name}%")->first()?->id;
    }

    private function resolveProjectTypeId(?string $name): ?int
    {
        if (!$name) return null;
        return ProjectType::where('name', 'like', "%{$name}%")
            ->orWhere('slug', 'like', "%{$name}%")
            ->first()?->id;
    }

    private function resolveProjectId(?string $name): ?int
    {
        if (!$name) return null;
        return Project::where('name', 'like', "%{$name}%")->first()?->id;
    }

    /**
     * Generate full URL for files stored in Laravel Cloud / S3 bucket.
     * Uses Storage::url() which respects the configured filesystem disk.
     * If the path is already a full URL it is returned as-is.
     */
    private function storageUrl(?string $path): ?string
    {
        if (!$path) return null;
        if (str_starts_with($path, 'http')) return $path;
        return Storage::url($path);
    }

    // =========================================================================
    //  SEARCH PROJECTS   GET /api/ai/search/projects
    //
    //  AI sends names (not IDs):
    //   city           e.g. "الرياض"
    //   state          e.g. "النرجس"
    //   developer      e.g. "دار الأركان"
    //   project_type   e.g. "فيلا" | "شقة" | "villa"
    // =========================================================================

    public function searchProjects(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q'            => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|max:100',
            'developer'    => 'nullable|string|max:100',
            'project_type' => 'nullable|string|max:100',
            'min_price'    => 'nullable|numeric|min:0',
            'max_price'    => 'nullable|numeric|min:0',
            'is_featured'  => 'nullable|boolean',
            'bedrooms'     => 'nullable|integer|min:0',
            'bathrooms'    => 'nullable|integer|min:0',
            'min_area'     => 'nullable|numeric|min:0',
            'max_area'     => 'nullable|numeric|min:0',
            'sale_type'    => 'nullable|string|max:100',
            'sort'         => 'nullable|in:popular,newest,price_asc,price_desc',
            'per_page'     => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── Resolve names → IDs ───────────────────────────────────────────────
        $cityId        = $this->resolveCityId($request->city);
        $stateId       = $this->resolveStateId($request->state, $cityId);
        $developerId   = $this->resolveDeveloperId($request->developer);
        $projectTypeId = $this->resolveProjectTypeId($request->project_type);

        // Track anything the AI sent that had no DB match
        $unresolved = [];
        if ($request->filled('city')         && !$cityId)        $unresolved[] = "city: \"{$request->city}\"";
        if ($request->filled('state')        && !$stateId)       $unresolved[] = "state: \"{$request->state}\"";
        if ($request->filled('developer')    && !$developerId)   $unresolved[] = "developer: \"{$request->developer}\"";
        if ($request->filled('project_type') && !$projectTypeId) $unresolved[] = "project_type: \"{$request->project_type}\"";

        // ── Build query ───────────────────────────────────────────────────────
        $query = Project::with([
            'developer:id,name,logo',
            'projectType:id,name,slug',
            'city:id,name',
            'state:id,name',
            'features:id,name,icon',
            'guarantees:id,name,icon',
        ])->where('status', true);

        if ($q = $request->q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name',         'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address',     'like', "%{$q}%");
            });
        }

        if ($cityId)        $query->where('city_id',         $cityId);
        if ($stateId)       $query->where('state_id',        $stateId);
        if ($developerId)   $query->where('developer_id',    $developerId);
        if ($projectTypeId) $query->where('project_type_id', $projectTypeId);

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $hasUnitFilter = $request->hasAny([
            'min_price','max_price','min_area','max_area','bedrooms','bathrooms','sale_type',
        ]);

        if ($hasUnitFilter) {
            $query->whereHas('units', function ($sub) use ($request) {
                if ($request->filled('min_price'))  $sub->where('unit_price', '>=', $request->min_price);
                if ($request->filled('max_price'))  $sub->where('unit_price', '<=', $request->max_price);
                if ($request->filled('min_area'))   $sub->where('unit_area',  '>=', $request->min_area);
                if ($request->filled('max_area'))   $sub->where('unit_area',  '<=', $request->max_area);
                if ($request->filled('bedrooms'))   $sub->where('beadrooms',  '>=', $request->bedrooms);
                if ($request->filled('bathrooms'))  $sub->where('bathrooms',  '>=', $request->bathrooms);
                if ($request->filled('sale_type'))  $sub->where('sale_type',       $request->sale_type);
            });
        }

        match ($request->sort ?? 'newest') {
            'popular'    => $query->orderByRaw('(visits_count + views_count*2 + shows_count*3 + orders_count*10) DESC'),
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->latest(),
        };

        $perPage  = min((int)($request->per_page ?? 15), 50);
        $projects = $query->paginate($perPage);

        $projects->getCollection()->transform(function (Project $p) {
            return [
                'id'             => $p->id,
                'name'           => $p->name,
                'slug'           => $p->slug,
                'description'    => $p->description,
                'address'        => $p->address,
                'city'           => $p->city?->name,
                'state'          => $p->state?->name,
                'developer'      => $p->developer
                    ? ['id' => $p->developer->id, 'name' => $p->developer->name, 'logo' => $this->storageUrl($p->developer->logo)]
                    : null,
                'project_type'   => $p->projectType?->name,
                'price_range'    => $p->show_price ? $p->price_range : null,
                'space_range'    => $p->space_range,
                'bedroom_range'  => $p->bedroom_range,
                'bathroom_range' => $p->bathroom_range,
                'is_featured'    => $p->is_featured,
                'status_summary' => $p->dynamic_project_status,
                'status_details' => $p->project_status_details,
                'features'       => $p->features->pluck('name'),
                'guarantees'     => $p->guarantees->pluck('name'),
                'main_image'     => $this->storageUrl($p->getMainImage()?->media_url),
                'latitude'       => $p->latitude,
                'longitude'      => $p->longitude,
                'virtual_tour'   => $p->virtualTour,
                'ad_license'     => $p->AdLicense,
            ];
        });

        $response = ['success' => true, 'data' => $projects];

        if (!empty($unresolved)) {
            $response['warnings'] = [
                'message'    => 'Some filters were ignored because no matching record was found.',
                'unresolved' => $unresolved,
            ];
        }

        return response()->json($response);
    }

    // =========================================================================
    //  SEARCH UNITS   GET /api/ai/search/units
    //
    //  Same name-based params plus:
    //   project    – project name  e.g. "مشروع النخيل"
    //   unit_type  – string
    //   floor      – integer
    //   case       – 0=available | 1=reserved | 2=sold | 3=under_construction
    // =========================================================================

    public function searchUnits(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'q'            => 'nullable|string|max:255',
            'project'      => 'nullable|string|max:255',
            'city'         => 'nullable|string|max:100',
            'state'        => 'nullable|string|max:100',
            'developer'    => 'nullable|string|max:100',
            'project_type' => 'nullable|string|max:100',
            'unit_type'    => 'nullable|string|max:100',
            'sale_type'    => 'nullable|string|max:100',
            'min_price'    => 'nullable|numeric|min:0',
            'max_price'    => 'nullable|numeric|min:0',
            'min_area'     => 'nullable|numeric|min:0',
            'max_area'     => 'nullable|numeric|min:0',
            'bedrooms'     => 'nullable|integer|min:0',
            'bathrooms'    => 'nullable|integer|min:0',
            'floor'        => 'nullable|integer',
            'case'         => 'nullable|in:0,1,2,3',
            'sort'         => 'nullable|in:popular,newest,price_asc,price_desc,area_asc,area_desc',
            'per_page'     => 'nullable|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors'  => $validator->errors(),
            ], 422);
        }

        // ── Resolve all names → IDs ───────────────────────────────────────────
        $cityId        = $this->resolveCityId($request->city);
        $stateId       = $this->resolveStateId($request->state, $cityId);
        $developerId   = $this->resolveDeveloperId($request->developer);
        $projectTypeId = $this->resolveProjectTypeId($request->project_type);
        $projectId     = $this->resolveProjectId($request->project);

        $unresolved = [];
        if ($request->filled('city')         && !$cityId)        $unresolved[] = "city: \"{$request->city}\"";
        if ($request->filled('state')        && !$stateId)       $unresolved[] = "state: \"{$request->state}\"";
        if ($request->filled('developer')    && !$developerId)   $unresolved[] = "developer: \"{$request->developer}\"";
        if ($request->filled('project_type') && !$projectTypeId) $unresolved[] = "project_type: \"{$request->project_type}\"";
        if ($request->filled('project')      && !$projectId)     $unresolved[] = "project: \"{$request->project}\"";

        // ── Build query ───────────────────────────────────────────────────────
        $query = Unit::with([
            'project:id,name,slug,city_id,state_id,developer_id',
            'project.city:id,name',
            'project.state:id,name',
            'project.developer:id,name',
            'features:id,name,icon',
        ])->where('status', true);

        if ($q = $request->q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title',        'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        if ($projectId)              $query->where('project_id', $projectId);
        if ($request->filled('unit_type'))  $query->where('unit_type',  $request->unit_type);
        if ($request->filled('sale_type'))  $query->where('sale_type',  $request->sale_type);
        if ($request->filled('floor'))      $query->where('floor',      $request->floor);
        if ($request->filled('case'))       $query->where('case',       $request->case);
        if ($request->filled('bedrooms'))   $query->where('beadrooms',  '>=', $request->bedrooms);
        if ($request->filled('bathrooms'))  $query->where('bathrooms',  '>=', $request->bathrooms);
        if ($request->filled('min_price'))  $query->where('unit_price', '>=', $request->min_price);
        if ($request->filled('max_price'))  $query->where('unit_price', '<=', $request->max_price);
        if ($request->filled('min_area'))   $query->where('unit_area',  '>=', $request->min_area);
        if ($request->filled('max_area'))   $query->where('unit_area',  '<=', $request->max_area);

        // Filter through project relation for city/state/developer/type
        if ($cityId || $stateId || $developerId || $projectTypeId) {
            $query->whereHas('project', function ($sub) use ($cityId, $stateId, $developerId, $projectTypeId) {
                if ($cityId)        $sub->where('city_id',         $cityId);
                if ($stateId)       $sub->where('state_id',        $stateId);
                if ($developerId)   $sub->where('developer_id',    $developerId);
                if ($projectTypeId) $sub->where('project_type_id', $projectTypeId);
            });
        }

        match ($request->sort ?? 'newest') {
            'popular'    => $query->orderByRaw('(visits_count + views_count*2 + shows_count*3 + orders_count*10) DESC'),
            'price_asc'  => $query->orderBy('unit_price', 'asc'),
            'price_desc' => $query->orderBy('unit_price', 'desc'),
            'area_asc'   => $query->orderBy('unit_area',  'asc'),
            'area_desc'  => $query->orderBy('unit_area',  'desc'),
            default      => $query->latest(),
        };

        $perPage = min((int)($request->per_page ?? 15), 50);
        $units   = $query->paginate($perPage);

        $units->getCollection()->transform(function (Unit $u) {
            return [
                'id'              => $u->id,
                'title'           => $u->title,
                'slug'            => $u->slug,
                'description'     => $u->description,
                'unit_type'       => $u->unit_type,
                'sale_type'       => $u->sale_type,
                'floor'           => $u->floor,
                'unit_area'       => $u->unit_area,
                'unit_price'      => $u->show_price ? $u->unit_price : null,
                'bedrooms'        => $u->beadrooms,
                'bathrooms'       => $u->bathrooms,
                'living_rooms'    => $u->living_rooms,
                'kitchen'         => $u->kitchen,
                'building_number' => $u->building_number,
                'unit_number'     => $u->unit_number,
                'case'            => $u->case,
                'case_label'      => match ((int)$u->case) {
                    0 => 'available',
                    1 => 'reserved',
                    2 => 'sold',
                    3 => 'under_construction',
                    default => 'unknown',
                },
                'image'           => $this->storageUrl($u->image),
                'floor_plan'      => $this->storageUrl($u->floor_plan),
                'latitude'        => $u->latitude,
                'longitude'       => $u->longitude,
                'features'        => $u->features->pluck('name'),
                'project'         => $u->project ? [
                    'id'        => $u->project->id,
                    'name'      => $u->project->name,
                    'slug'      => $u->project->slug,
                    'city'      => $u->project->city?->name,
                    'state'     => $u->project->state?->name,
                    'developer' => $u->project->developer?->name,
                ] : null,
                'conversion_rate' => $u->getConversionRate(),
            ];
        });

        $response = ['success' => true, 'data' => $units];

        if (!empty($unresolved)) {
            $response['warnings'] = [
                'message'    => 'Some filters were ignored because no matching record was found.',
                'unresolved' => $unresolved,
            ];
        }

        return response()->json($response);
    }

    // =========================================================================
    //  COMBINED   GET /api/ai/search
    // =========================================================================

    public function search(Request $request): JsonResponse
    {
        $projectsResponse = $this->searchProjects($request);
        $unitsResponse    = $this->searchUnits($request);

        $projects = json_decode($projectsResponse->getContent());
        $units    = json_decode($unitsResponse->getContent());

        $warnings = array_unique(array_merge(
            $projects->warnings->unresolved ?? [],
            $units->warnings->unresolved    ?? [],
        ));

        $response = [
            'success'  => true,
            'projects' => $projects->data,
            'units'    => $units->data,
        ];

        if (!empty($warnings)) {
            $response['warnings'] = [
                'message'    => 'Some filters were ignored because no matching record was found.',
                'unresolved' => $warnings,
            ];
        }

        return response()->json($response);
    }
}
