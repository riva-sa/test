<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Unit;
use App\Models\TrackingEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Helpers\DatabaseHelper;
use Illuminate\Support\Facades\DB;

class TrackingService
{
/**
     * Track project visit
     */
    public function trackProjectVisit(Project $project)
    {
        if ($project->shouldTrack('visit')) {
            $project->track('visit', [
                'page' => 'project_single',
                'project_slug' => $project->slug,
            ]);
        }
    }

    /**
     * Track unit view (when unit details are shown)
     */
    public function trackUnitView(Unit $unit)
    {
        if ($unit->shouldTrack('view')) {
            $unit->track('view', [
                'page' => 'unit_popup',
                'project_id' => $unit->project_id,
            ]);
        }
        
        // Also track project view
        if ($unit->project && $unit->project->shouldTrack('view', 600)) { // 10 min window for project
            $unit->project->track('view', [
                'triggered_by' => 'unit_view',
                'unit_id' => $unit->id,
            ]);
        }
    }

    /**
     * Track unit show (when unit popup is opened)
     */
    public function trackUnitShow(Unit $unit)
    {
        if ($unit->shouldTrack('show')) {
            $unit->track('show', [
                'page' => 'unit_popup',
                'project_id' => $unit->project_id,
            ]);
        }
    }

    /**
     * Track unit order
     */
    public function trackUnitOrder(Unit $unit, array $orderData = [])
    {
        // Always track orders (no time window check)
        $unit->track('order', array_merge([
            'page' => 'unit_order',
            'project_id' => $unit->project_id,
        ], $orderData));

        // Track project order
        if ($unit->project) {
            $unit->project->track('order', array_merge([
                'triggered_by' => 'unit_order',
                'unit_id' => $unit->id,
            ], $orderData));
        }
    }

    /**
     * Track project order popup open
     */
    public function trackProjectOrderShow(Project $project)
    {
        if ($project->shouldTrack('show')) {
            $project->track('show', [
                'page' => 'project_order_popup',
            ]);
        }
    }

    /**
     * Get popular units based on tracking data - PostgreSQL Compatible
     */
    public function getPopularUnits($limit = 10, $days = 30)
    {
        $cacheKey = "popular_units_{$limit}_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit, $days) {
            return Unit::select('units.*')
                ->selectRaw('COALESCE(visits_count, 0) + COALESCE(views_count, 0) * 2 + COALESCE(shows_count, 0) * 3 + COALESCE(orders_count, 0) * 10 as popularity_score')
                ->where('status', 1)
                ->where(function($query) use ($days) {
                    $query->where('last_visited_at', '>', Carbon::now()->subDays($days))
                          ->orWhere('visits_count', '>', 0)
                          ->orWhere('views_count', '>', 0)
                          ->orWhere('shows_count', '>', 0)
                          ->orWhere('orders_count', '>', 0);
                })
                ->with('project:id,name')
                ->orderBy('popularity_score', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get popular projects based on tracking data - PostgreSQL Compatible
     */
    public function getPopularProjects($limit = 10, $days = 30)
    {
        $cacheKey = "popular_projects_{$limit}_{$days}";
        
        return Cache::remember($cacheKey, 3600, function () use ($limit, $days) {
            return Project::select('projects.*')
                ->selectRaw('COALESCE(visits_count, 0) + COALESCE(views_count, 0) * 2 + COALESCE(shows_count, 0) * 3 + COALESCE(orders_count, 0) * 10 as popularity_score')
                ->where('status', 1)
                ->where(function($query) use ($days) {
                    $query->where('last_visited_at', '>', Carbon::now()->subDays($days))
                          ->orWhere('visits_count', '>', 0)
                          ->orWhere('views_count', '>', 0)
                          ->orWhere('shows_count', '>', 0)
                          ->orWhere('orders_count', '>', 0);
                })
                ->orderBy('popularity_score', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get tracking analytics for dashboard - Cross-DB Compatible
     */
    public function getAnalytics($dateRange = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $query = TrackingEvent::whereBetween('created_at', [$startDate, $endDate]);

        $analytics = [
            'overview' => [
                'total_events' => $query->count(),
                'total_visits' => $query->clone()->eventType('visit')->count(),
                'total_views' => $query->clone()->eventType('view')->count(),
                'total_shows' => $query->clone()->eventType('show')->count(),
                'total_orders' => $query->clone()->eventType('order')->count(),
                'unique_sessions' => $query->clone()->distinct('session_id')->count('session_id'),
            ],
            'by_type' => [
                'units' => $query->clone()->trackableType('unit')->count(),
                'projects' => $query->clone()->trackableType('project')->count(),
            ],
            'daily_stats' => $query->clone()
                ->selectRaw(DatabaseHelper::dateFormat('created_at') . ' as date, event_type, COUNT(*) as count')
                ->groupBy('date', 'event_type')
                ->orderBy('date')
                ->get()
                ->groupBy('date'),
        ];

        // Get device and browser stats with cross-database compatibility
        try {
            if (DatabaseHelper::isPostgreSQL()) {
                $analytics['device_stats'] = $query->clone()
                    ->selectRaw("metadata->>'device_type' as device_type, COUNT(*) as count")
                    ->whereNotNull('metadata')
                    ->whereRaw("metadata->>'device_type' IS NOT NULL")
                    ->groupBy(DB::raw("metadata->>'device_type'"))
                    ->get();

                $analytics['browser_stats'] = $query->clone()
                    ->selectRaw("metadata->>'browser' as browser, COUNT(*) as count")
                    ->whereNotNull('metadata')
                    ->whereRaw("metadata->>'browser' IS NOT NULL")
                    ->groupBy(DB::raw("metadata->>'browser'"))
                    ->get();
            } else {
                // MySQL/SQLite
                $analytics['device_stats'] = $query->clone()
                    ->selectRaw("JSON_EXTRACT(metadata, '$.device_type') as device_type, COUNT(*) as count")
                    ->whereNotNull('metadata')
                    ->whereRaw("JSON_EXTRACT(metadata, '$.device_type') IS NOT NULL")
                    ->groupBy(DB::raw("JSON_EXTRACT(metadata, '$.device_type')"))
                    ->get();

                $analytics['browser_stats'] = $query->clone()
                    ->selectRaw("JSON_EXTRACT(metadata, '$.browser') as browser, COUNT(*) as count")
                    ->whereNotNull('metadata')
                    ->whereRaw("JSON_EXTRACT(metadata, '$.browser') IS NOT NULL")
                    ->groupBy(DB::raw("JSON_EXTRACT(metadata, '$.browser')"))
                    ->get();
            }
        } catch (\Exception $e) {
            // Fallback if JSON extraction fails
            $analytics['device_stats'] = collect();
            $analytics['browser_stats'] = collect();
        }

        return $analytics;
    }

    /**
     * Get conversion rates
     */
    public function getConversionRates($dateRange = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $query = TrackingEvent::whereBetween('created_at', [$startDate, $endDate]);

        $visits = $query->clone()->eventType('visit')->count();
        $views = $query->clone()->eventType('view')->count();
        $shows = $query->clone()->eventType('show')->count();
        $orders = $query->clone()->eventType('order')->count();

        return [
            'visit_to_view' => $visits > 0 ? round(($views / $visits) * 100, 2) : 0,
            'view_to_show' => $views > 0 ? round(($shows / $views) * 100, 2) : 0,
            'show_to_order' => $shows > 0 ? round(($orders / $shows) * 100, 2) : 0,
            'visit_to_order' => $visits > 0 ? round(($orders / $visits) * 100, 2) : 0,
        ];
    }

    /**
     * Get top performing content (units and projects) - PostgreSQL Compatible
     */
    public function getTopPerformingContent(array $dateRange = null, int $limit = 5)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        // Units query with better compatibility
        $units = Unit::select('units.*', 'projects.name as project_name')
            ->join('projects', 'units.project_id', '=', 'projects.id')
            ->where('units.status', 1)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('units.last_visited_at', [$startDate, $endDate])
                      ->orWhereBetween('units.last_ordered_at', [$startDate, $endDate])
                      ->orWhere(function($subQuery) {
                          $subQuery->where('units.orders_count', '>', 0)
                                   ->orWhere('units.shows_count', '>', 0);
                      });
            })
            ->orderByDesc('units.orders_count')
            ->orderByDesc('units.shows_count')
            ->limit($limit)
            ->get();

        $projects = Project::where('status', 1)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('last_visited_at', [$startDate, $endDate])
                      ->orWhereBetween('last_ordered_at', [$startDate, $endDate])
                      ->orWhere(function($subQuery) {
                          $subQuery->where('orders_count', '>', 0)
                                   ->orWhere('shows_count', '>', 0);
                      });
            })
            ->orderByDesc('orders_count')
            ->orderByDesc('shows_count')
            ->limit($limit)
            ->get();

        return [
            'units' => $units,
            'projects' => $projects
        ];
    }

    /**
     * Get traffic sources breakdown - PostgreSQL Compatible
     */
    public function getTrafficSources(array $dateRange = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        return TrackingEvent::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                CASE 
                    WHEN referrer IS NULL OR referrer = '' THEN 'Direct'
                    WHEN referrer LIKE '%google.%' THEN 'Google'
                    WHEN referrer LIKE '%facebook.%' THEN 'Facebook'
                    WHEN referrer LIKE '%instagram.%' THEN 'Instagram'
                    WHEN referrer LIKE '%twitter.%' THEN 'Twitter'
                    WHEN referrer LIKE '%linkedin.%' THEN 'LinkedIn'
                    ELSE 'Other'
                END as source,
                COUNT(*) as count
            ")
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();
    }

}