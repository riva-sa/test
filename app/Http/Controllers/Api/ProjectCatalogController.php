<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectCatalogController extends Controller
{
    /**
     * Convert relative storage path to absolute URL.
     */
    private function storageUrl(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return asset(Storage::url($path));
    }

    /**
     * Helper to get case label in Arabic.
     */
    private function getUnitCaseLabel($case): string
    {
        return match ((int) $case) {
            0 => 'متاح',
            1 => 'محجوز',
            2 => 'مباع',
            3 => 'تحت الإنشاء',
            default => 'غير معروف',
        };
    }

    /**
     * Format a single project model into a rich API array.
     */
    private function formatProject(Project $project, bool $includeUnits = true): array
    {
        // Media separation
        $mediaCollection = $project->projectMedia ?? collect();

        $mainImageObj = $mediaCollection->where('media_type', 'image')->where('main', 1)->first()
            ?? $mediaCollection->where('media_type', 'image')->first();

        $images = $mediaCollection->where('media_type', 'image')->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $this->storageUrl($item->media_url),
                'title' => $item->media_title,
                'is_main' => (bool) $item->main,
                'show_in_gallery' => (bool) $item->show_in_gallery,
                'show_in_slider' => (bool) $item->show_in_slider,
            ];
        })->values()->all();

        $videos = $mediaCollection->where('media_type', 'video')->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $item->media_url ? $this->storageUrl($item->media_url) : null,
                'youtube_url' => $item->youtube_url,
                'youtube_embed_url' => $item->youtube_embed_url,
                'vimeo_url' => $item->vimeo_url,
                'title' => $item->media_title,
                'description' => $item->media_description,
            ];
        })->values()->all();

        $pdfFiles = $mediaCollection->where('media_type', 'pdf')->map(function ($item) {
            return [
                'id' => $item->id,
                'url' => $this->storageUrl($item->media_url),
                'title' => $item->media_title ?? 'كتالوج المشروع',
                'description' => $item->media_description,
            ];
        })->values()->all();

        $data = [
            'id' => $project->id,
            'name' => $project->name,
            'name_en' => $project->name_en,
            'slug' => $project->slug,
            'description' => $project->description,
            'description_en' => $project->description_en,
            'address' => $project->address,
            'address_en' => $project->address_en,
            'country' => $project->country,
            'latitude' => $project->latitude,
            'longitude' => $project->longitude,
            'location' => $project->location,
            'status' => (bool) $project->status,
            'show_price' => (bool) $project->show_price,
            'price' => $project->price,
            'price_range' => $project->price_range,
            'space_range' => $project->space_range,
            'bedroom_range' => $project->bedroom_range,
            'bathroom_range' => $project->bathroom_range,
            'kitchen_range' => $project->kitchen_range,
            'building_style' => $project->bulding_style,
            'building_style_en' => $project->bulding_style_en,
            'is_featured' => (bool) $project->is_featured,
            'ad_license' => $project->AdLicense,
            'virtual_tour' => $project->virtualTour,
            'contact_phone' => $project->contact_phone,

            // Relations
            'developer' => $project->developer ? [
                'id' => $project->developer->id,
                'name' => $project->developer->name,
                'logo' => $this->storageUrl($project->developer->logo),
            ] : null,
            'project_type' => $project->projectType ? [
                'id' => $project->projectType->id,
                'name' => $project->projectType->name,
                'slug' => $project->projectType->slug,
            ] : null,
            'city' => $project->city ? [
                'id' => $project->city->id,
                'name' => $project->city->name,
            ] : null,
            'state' => $project->state ? [
                'id' => $project->state->id,
                'name' => $project->state->name,
            ] : null,
            'features' => $project->features->map(fn ($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'icon' => $f->icon,
            ])->values()->all(),
            'guarantees' => $project->guarantees->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->name,
                'icon' => $g->icon,
            ])->values()->all(),
            'landmarks' => $project->landmarks->map(fn ($l) => [
                'id' => $l->id,
                'name' => $l->name,
                'distance' => $l->pivot?->distance,
            ])->values()->all(),

            // Dynamic Status & Analytics
            'status_summary' => $project->dynamic_project_status,
            'status_details' => $project->project_status_details,
            'status_type' => $project->project_status_type,

            // Media
            'media' => [
                'main_image' => $mainImageObj ? $this->storageUrl($mainImageObj->media_url) : null,
                'images' => $images,
                'videos' => $videos,
                'pdf_files' => $pdfFiles,
            ],

            'units_count' => $project->units->count(),
            'created_at' => $project->created_at ? $project->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $project->updated_at ? $project->updated_at->format('Y-m-d H:i:s') : null,
        ];

        if ($includeUnits) {
            $data['units'] = $project->units->map(fn ($unit) => $this->formatUnit($unit, false))->values()->all();
        }

        return $data;
    }

    /**
     * Format a single unit model into a rich API array.
     */
    private function formatUnit(Unit $unit, bool $includeProject = true): array
    {
        $additionalImages = [];
        if (is_array($unit->images)) {
            $additionalImages = array_map(fn ($path) => $this->storageUrl($path), $unit->images);
        }

        $data = [
            'id' => $unit->id,
            'title' => $unit->title,
            'title_en' => $unit->title_en,
            'slug' => $unit->slug,
            'description' => $unit->description,
            'description_en' => $unit->description_en,
            'unit_type' => $unit->unit_type,
            'unit_type_en' => $unit->unit_type_en,
            'sale_type' => $unit->sale_type,
            'building_number' => $unit->building_number,
            'unit_number' => $unit->unit_number,
            'floor' => $unit->floor,
            'unit_area' => $unit->unit_area,
            'unit_price' => (bool) $unit->show_price ? $unit->unit_price : null,
            'show_price' => (bool) $unit->show_price,
            'bedrooms' => $unit->beadrooms,
            'bathrooms' => $unit->bathrooms,
            'kitchen' => $unit->kitchen,
            'living_rooms' => $unit->living_rooms,
            'status' => (bool) $unit->status,
            'case' => (int) $unit->case,
            'case_label' => $this->getUnitCaseLabel($unit->case),
            'latitude' => $unit->latitude,
            'longitude' => $unit->longitude,
            'location' => $unit->location,
            'media' => [
                'image' => $this->storageUrl($unit->image),
                'floor_plan' => $this->storageUrl($unit->floor_plan),
                'gallery' => $additionalImages,
            ],
            'features' => $unit->relationLoaded('features') ? $unit->features->map(fn ($f) => [
                'id' => $f->id,
                'name' => $f->name,
                'icon' => $f->icon,
            ])->values()->all() : [],
            'created_at' => $unit->created_at ? $unit->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $unit->updated_at ? $unit->updated_at->format('Y-m-d H:i:s') : null,
        ];

        if ($includeProject && $unit->project) {
            $data['project'] = [
                'id' => $unit->project->id,
                'name' => $unit->project->name,
                'slug' => $unit->project->slug,
                'city' => $unit->project->city?->name,
                'state' => $unit->project->state?->name,
                'developer' => $unit->project->developer?->name,
            ];
        }

        return $data;
    }

    /**
     * Paginated Projects List with full media, details and units.
     * GET /api/projects
     */
    public function index(Request $request): JsonResponse
    {
        $query = Project::with([
            'developer',
            'projectType',
            'city',
            'state',
            'features',
            'guarantees',
            'landmarks',
            'projectMedia',
            'units.features',
        ]);

        // Filter by active status by default unless status=all
        if ($request->input('status') !== 'all') {
            $query->where('status', true);
        }

        // Search query
        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('address', 'like', "%{$q}%");
            });
        }

        // Filters
        if ($cityId = $request->input('city_id')) {
            $query->where('city_id', $cityId);
        }
        if ($stateId = $request->input('state_id')) {
            $query->where('state_id', $stateId);
        }
        if ($developerId = $request->input('developer_id')) {
            $query->where('developer_id', $developerId);
        }
        if ($projectTypeId = $request->input('project_type_id')) {
            $query->where('project_type_id', $projectTypeId);
        }
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        $query->orderBy('created_at', 'desc');

        $perPage = min((int) $request->input('per_page', 15), 100);
        $projects = $query->paginate($perPage);

        $includeUnits = $request->boolean('with_units', true);

        $formattedData = $projects->getCollection()->map(function ($project) use ($includeUnits) {
            return $this->formatProject($project, $includeUnits);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedData,
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    /**
     * Single Project Details with all media, files & units.
     * GET /api/projects/{id}
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $project = Project::with([
            'developer',
            'projectType',
            'city',
            'state',
            'features',
            'guarantees',
            'landmarks',
            'projectMedia',
            'units.features',
        ])->find($id);

        if (! $project) {
            return response()->json([
                'success' => false,
                'message' => 'المشروع غير موجود.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatProject($project, true),
        ]);
    }

    /**
     * Paginated Units List with full details.
     * GET /api/units
     */
    public function units(Request $request): JsonResponse
    {
        $query = Unit::with([
            'project.developer',
            'project.city',
            'project.state',
            'features',
        ]);

        if ($request->input('status') !== 'all') {
            $query->where('status', true);
        }

        if ($q = $request->input('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhere('unit_number', 'like', "%{$q}%");
            });
        }

        if ($projectId = $request->input('project_id')) {
            $query->where('project_id', $projectId);
        }
        if ($unitType = $request->input('unit_type')) {
            $query->where('unit_type', $unitType);
        }
        if ($request->has('case')) {
            $query->where('case', $request->input('case'));
        }
        if ($minPrice = $request->input('min_price')) {
            $query->where('unit_price', '>=', $minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('unit_price', '<=', $maxPrice);
        }
        if ($minArea = $request->input('min_area')) {
            $query->where('unit_area', '>=', $minArea);
        }
        if ($maxArea = $request->input('max_area')) {
            $query->where('unit_area', '<=', $maxArea);
        }
        if ($bedrooms = $request->input('bedrooms')) {
            $query->where('beadrooms', '>=', $bedrooms);
        }
        if ($bathrooms = $request->input('bathrooms')) {
            $query->where('bathrooms', '>=', $bathrooms);
        }

        $query->orderBy('created_at', 'desc');

        $perPage = min((int) $request->input('per_page', 15), 100);
        $paginatedUnits = $query->paginate($perPage);

        $formattedUnits = $paginatedUnits->getCollection()->map(function ($unit) {
            return $this->formatUnit($unit, true);
        });

        return response()->json([
            'success' => true,
            'data' => $formattedUnits,
            'meta' => [
                'current_page' => $paginatedUnits->currentPage(),
                'last_page' => $paginatedUnits->lastPage(),
                'per_page' => $paginatedUnits->perPage(),
                'total' => $paginatedUnits->total(),
            ],
        ]);
    }

    /**
     * Single Unit Details.
     * GET /api/units/{id}
     */
    public function showUnit(Request $request, string $id): JsonResponse
    {
        $unit = Unit::with([
            'project.developer',
            'project.city',
            'project.state',
            'features',
        ])->find($id);

        if (! $unit) {
            return response()->json([
                'success' => false,
                'message' => 'الوحدة غير موجودة.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->formatUnit($unit, true),
        ]);
    }
}
