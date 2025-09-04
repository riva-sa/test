<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Unit;
use App\Models\TrackingEvent;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use App\Helpers\DatabaseHelper;
use Illuminate\Support\Facades\DB;

use Carbon\CarbonPeriod;

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
     * Track WhatsApp click/redirect - NEW EVENT
     */
    public function trackWhatsAppClick($trackable, array $additionalData = [])
    {
        $trackable->track('whatsapp', array_merge([
            'page' => request()->get('page', 'unknown'),
            'action' => 'whatsapp_click',
        ], $additionalData));
    }

    /**
     * Track phone call click - NEW EVENT
     */
    public function trackPhoneCall($trackable, array $additionalData = [])
    {
        $trackable->track('call', array_merge([
            'page' => request()->get('page', 'unknown'),
            'action' => 'phone_call',
        ], $additionalData));
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
    public function getPopularUnits($limit = 10, $days = 30, $campaignId = null)
    {
        $cacheKey = "popular_units_{$limit}_{$days}" . ($campaignId ? "_{$campaignId}" : '');
        
        return Cache::remember($cacheKey, 3600, function () use ($limit, $days, $campaignId) {
            $query = Unit::select('units.*')
                ->selectRaw('COALESCE(visits_count, 0) + COALESCE(views_count, 0) * 2 + COALESCE(shows_count, 0) * 3 + COALESCE(orders_count, 0) * 10 + COALESCE(whatsapp_count, 0) * 5 + COALESCE(calls_count, 0) * 7 as popularity_score')
                ->where('status', 1);

            if ($campaignId) {
                $campaign = Campaign::find($campaignId);
                if ($campaign) {
                    $query->where('project_id', $campaign->project_id);
                    $days = $campaign->start_date->diffInDays($campaign->end_date ?: Carbon::now());
                }
            }

            $query->where(function($q) use ($days) {
                $q->where('last_visited_at', '>', Carbon::now()->subDays($days))
                  ->orWhere('visits_count', '>', 0)
                  ->orWhere('views_count', '>', 0)
                  ->orWhere('shows_count', '>', 0)
                  ->orWhere('orders_count', '>', 0)
                  ->orWhere('whatsapp_count', '>', 0)
                  ->orWhere('calls_count', '>', 0);
            })
            ->with('project:id,name')
            ->orderBy('popularity_score', 'desc')
            ->limit($limit);

            return $query->get();
        });
    }

    /**
     * Get popular projects based on tracking data - PostgreSQL Compatible
     */
    public function getPopularProjects($limit = 10, $days = 30, $campaignId = null)
    {
        $cacheKey = "popular_projects_{$limit}_{$days}" . ($campaignId ? "_{$campaignId}" : '');
        
        return Cache::remember($cacheKey, 3600, function () use ($limit, $days, $campaignId) {
            $query = Project::select('projects.*')
                ->selectRaw('COALESCE(visits_count, 0) + COALESCE(views_count, 0) * 2 + COALESCE(shows_count, 0) * 3 + COALESCE(orders_count, 0) * 10 + COALESCE(whatsapp_count, 0) * 5 + COALESCE(calls_count, 0) * 7 as popularity_score')
                ->where('status', 1);

            if ($campaignId) {
                $query->where('id', Campaign::find($campaignId)?->project_id);
            }

            $query->where(function($q) use ($days) {
                $q->where('last_visited_at', '>', Carbon::now()->subDays($days))
                  ->orWhere('visits_count', '>', 0)
                  ->orWhere('views_count', '>', 0)
                  ->orWhere('shows_count', '>', 0)
                  ->orWhere('orders_count', '>', 0)
                  ->orWhere('whatsapp_count', '>', 0)
                  ->orWhere('calls_count', '>', 0);
            })
            ->orderBy('popularity_score', 'desc')
            ->limit($limit);

            return $query->get();
        });
    }

    /**
     * Get tracking analytics for dashboard - Cross-DB Compatible
     */
    public function getAnalytics($dateRange = null, $campaignId = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $query = TrackingEvent::whereBetween('created_at', [$startDate, $endDate]);

        // Filter by campaign if specified
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $query->where(function($q) use ($campaign) {
                    $q->where('trackable_type', 'project')
                      ->where('trackable_id', $campaign->project_id)
                      ->orWhere(function($subQ) use ($campaign) {
                          $subQ->where('trackable_type', 'unit')
                               ->whereIn('trackable_id', 
                                   Unit::where('project_id', $campaign->project_id)->pluck('id')
                               );
                      });
                });
            }
        }

        $analytics = [
            'overview' => [
                'total_events' => $query->count(),
                'total_visits' => $query->clone()->eventType('visit')->count(),
                'total_views' => $query->clone()->eventType('view')->count(),
                'total_shows' => $query->clone()->eventType('show')->count(),
                'total_orders' => $query->clone()->eventType('order')->count(),
                'total_whatsapp' => $query->clone()->eventType('whatsapp')->count(),
                'total_calls' => $query->clone()->eventType('call')->count(),
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
     * Get conversion rates - Enhanced with new events
     */
    public function getConversionRates($dateRange = null, $campaignId = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $query = TrackingEvent::whereBetween('created_at', [$startDate, $endDate]);

        // Filter by campaign if specified
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $query->where(function($q) use ($campaign) {
                    $q->where('trackable_type', 'project')
                      ->where('trackable_id', $campaign->project_id)
                      ->orWhere(function($subQ) use ($campaign) {
                          $subQ->where('trackable_type', 'unit')
                               ->whereIn('trackable_id', 
                                   Unit::where('project_id', $campaign->project_id)->pluck('id')
                               );
                      });
                });
            }
        }

        $visits = $query->clone()->eventType('visit')->count();
        $views = $query->clone()->eventType('view')->count();
        $shows = $query->clone()->eventType('show')->count();
        $orders = $query->clone()->eventType('order')->count();
        $whatsapp = $query->clone()->eventType('whatsapp')->count();
        $calls = $query->clone()->eventType('call')->count();

        return [
            'visit_to_view' => $visits > 0 ? round(($views / $visits) * 100, 2) : 0,
            'view_to_show' => $views > 0 ? round(($shows / $views) * 100, 2) : 0,
            'show_to_order' => $shows > 0 ? round(($orders / $shows) * 100, 2) : 0,
            'visit_to_order' => $visits > 0 ? round((($orders + $whatsapp + $calls) / $visits) * 100, 2) : 0,
            'visit_to_whatsapp' => $visits > 0 ? round(($whatsapp / $visits) * 100, 2) : 0,
            'visit_to_call' => $visits > 0 ? round(($calls / $visits) * 100, 2) : 0,
            'engagement_rate' => $visits > 0 ? round((($whatsapp + $calls + $orders) / $visits) * 100, 2) : 0,
        ];
    }

    /**
     * Get campaign analytics
     */
    public function getCampaignAnalytics(Campaign $campaign)
    {
        $analytics = $this->getAnalytics([$campaign->start_date, $campaign->end_date ?: Carbon::now()], $campaign->id);
        $conversionRates = $this->getConversionRates([$campaign->start_date, $campaign->end_date ?: Carbon::now()], $campaign->id);
        
        return [
            'analytics' => $analytics,
            'conversion_rates' => $conversionRates,
            'duration_days' => round($campaign->start_date->diffInDays($campaign->end_date ?: Carbon::now())),
            'project' => $campaign->project,
            'performance_score' => $this->calculateCampaignPerformanceScore($campaign),
        ];
    }

    /**
     * Calculate campaign performance score
     */
    private function calculateCampaignPerformanceScore(Campaign $campaign)
    {
        $analytics = $this->getAnalytics([$campaign->start_date, $campaign->end_date ?: Carbon::now()], $campaign->id);
        $overview = $analytics['overview'];
        
        $score = 0;
        $score += $overview['total_visits'] * 1;
        $score += $overview['total_views'] * 2;
        $score += $overview['total_shows'] * 3;
        $score += $overview['total_whatsapp'] * 5;
        $score += $overview['total_calls'] * 7;
        $score += $overview['total_orders'] * 10;
        
        return $score;
    }

    /**
     * Get top performing content (units and projects) - PostgreSQL Compatible
     */
    public function getTopPerformingContent(array $dateRange = null, int $limit = 5, $campaignId = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        // Units query with better compatibility
        $unitsQuery = Unit::select('units.*', 'projects.name as project_name')
            ->join('projects', 'units.project_id', '=', 'projects.id')
            ->where('units.status', 1)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('units.last_visited_at', [$startDate, $endDate])
                      ->orWhereBetween('units.last_ordered_at', [$startDate, $endDate])
                      ->orWhere(function($subQuery) {
                          $subQuery->where('units.orders_count', '>', 0)
                                   ->orWhere('units.shows_count', '>', 0)
                                   ->orWhere('units.whatsapp_count', '>', 0)
                                   ->orWhere('units.calls_count', '>', 0);
                      });
            });

        $projectsQuery = Project::where('status', 1)
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('last_visited_at', [$startDate, $endDate])
                      ->orWhereBetween('last_ordered_at', [$startDate, $endDate])
                      ->orWhere(function($subQuery) {
                          $subQuery->where('orders_count', '>', 0)
                                   ->orWhere('shows_count', '>', 0)
                                   ->orWhere('whatsapp_count', '>', 0)
                                   ->orWhere('calls_count', '>', 0);
                      });
            });

        // Filter by campaign if specified
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $unitsQuery->where('units.project_id', $campaign->project_id);
                $projectsQuery->where('id', $campaign->project_id);
            }
        }

        $units = $unitsQuery->orderByDesc('units.orders_count')
            ->orderByDesc('units.whatsapp_count')
            ->orderByDesc('units.calls_count')
            ->orderByDesc('units.shows_count')
            ->limit($limit)
            ->get();

        $projects = $projectsQuery->orderByDesc('orders_count')
            ->orderByDesc('whatsapp_count')
            ->orderByDesc('calls_count')
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
    public function getTrafficSources(array $dateRange = null, $campaignId = null)
    {
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(30);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $query = TrackingEvent::whereBetween('created_at', [$startDate, $endDate]);

        // Filter by campaign if specified
        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $query->where(function($q) use ($campaign) {
                    $q->where('trackable_type', 'project')
                      ->where('trackable_id', $campaign->project_id)
                      ->orWhere(function($subQ) use ($campaign) {
                          $subQ->where('trackable_type', 'unit')
                               ->whereIn('trackable_id', 
                                   Unit::where('project_id', $campaign->project_id)->pluck('id')
                               );
                      });
                });
            }
        }

        return $query->selectRaw("
                CASE 
                    WHEN referrer IS NULL OR referrer = '' THEN 'Direct'
                    WHEN referrer LIKE '%google.%' THEN 'Google'
                    WHEN referrer LIKE '%facebook.%' THEN 'Facebook'
                    WHEN referrer LIKE '%instagram.%' THEN 'Instagram'
                    WHEN referrer LIKE '%twitter.%' THEN 'Twitter'
                    WHEN referrer LIKE '%linkedin.%' THEN 'LinkedIn'
                    WHEN referrer LIKE '%youtube.%' THEN 'YouTube'
                    WHEN referrer LIKE '%bing.%' THEN 'Bing'
                    WHEN referrer LIKE '%yahoo.%' THEN 'Yahoo'
                    WHEN referrer LIKE '%reddit.%' THEN 'Reddit'
                    WHEN referrer LIKE '%tiktok.%' THEN 'TikTok'
                    WHEN referrer LIKE '%pinterest.%' THEN 'Pinterest'
                    WHEN referrer LIKE '%snapchat.%' THEN 'Snapchat'
                    WHEN referrer LIKE '%whatsapp.%' THEN 'WhatsApp'
                    WHEN referrer LIKE '%vk.%' THEN 'VK'
                    WHEN referrer LIKE '%baidu.%' THEN 'Baidu'
                    WHEN referrer LIKE '%yandex.%' THEN 'Yandex'
                    WHEN referrer LIKE '%quora.%' THEN 'Quora'
                    WHEN referrer LIKE '%tumblr.%' THEN 'Tumblr'
                    WHEN referrer LIKE '%weibo.%' THEN 'Weibo'
                    WHEN referrer LIKE '%twitch.%' THEN 'Twitch'
                    WHEN referrer LIKE '%discord.%' THEN 'Discord'
                    WHEN referrer LIKE '%telegram.%' THEN 'Telegram'
                    WHEN referrer LIKE '%line.%' THEN 'Line'
                    ELSE 'Other'
                END as source,
                COUNT(*) as count
            ")
            ->groupBy('source')
            ->orderBy('count', 'desc')
            ->get();
    }

    public function getCampaignDailyBreakdown(Campaign $campaign): array
    {
        $startDate = $campaign->start_date;
        $endDate = $campaign->end_date ?? Carbon::now();

        $query = TrackingEvent::whereBetween('created_at', [$startDate, $endDate])
            ->where(function($q) use ($campaign) {
                $q->whereHasMorph('trackable', [Project::class], function ($query) use ($campaign) {
                    $query->where('id', $campaign->project_id);
                })->orWhereHasMorph('trackable', [Unit::class], function ($query) use ($campaign) {
                    $query->where('project_id', $campaign->project_id);
                });
            });

        $dailyStats = $query
            ->selectRaw(DatabaseHelper::dateFormat('created_at') . ' as date, event_type, COUNT(*) as count')
            ->groupBy('date', 'event_type')
            ->orderBy('date')
            ->get();

        $results = [];
        $period = CarbonPeriod::create($startDate, $endDate);
        $eventTypes = ['visit', 'view', 'show', 'order', 'whatsapp', 'call'];

        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $results[$formattedDate] = array_fill_keys($eventTypes, 0);
            $results[$formattedDate]['date'] = $formattedDate;
        }

        foreach ($dailyStats as $stat) {
            if (isset($results[$stat->date]) && in_array($stat->event_type, $eventTypes)) {
                $results[$stat->date][$stat->event_type] = $stat->count;
            }
        }

        return array_values($results);
    }
    
    public function getSessionJourneys(array $dateRange = null, int $limit = 50): array
    {
        // 1. تحديد النطاق الزمني وجلب البيانات (لا تغيير هنا)
        $startDate = $dateRange ? $dateRange[0] : Carbon::now()->subDays(7);
        $endDate = $dateRange ? $dateRange[1] : Carbon::now();

        $allEvents = TrackingEvent::whereBetween('created_at', [$startDate, $endDate])
            ->with(['trackable' => function ($query) {
                if ($query->getQuery()->from === 'projects') {
                    $query->select('id', 'name');
                } elseif ($query->getQuery()->from === 'units') {
                    $query->select('id', 'title', 'unit_number');
                }
            }])
            ->orderBy('created_at', 'asc')
            ->get();

        // 2. الحماية من الأخطاء (لا تغيير هنا)
        if ($allEvents->isEmpty()) {
            return [
                'journeys'        => collect(),
                'stats'           => ['total_sessions' => 0, 'conversion_rate' => 0, 'avg_duration' => 0, 'avg_events' => 0],
                'top_funnels'     => [],
                'friction_points' => collect(),
            ];
        }

        // 3. تجميع الأحداث حسب الجلسة (لا تغيير هنا)
        $groupedBySession = $allEvents->groupBy('session_id');

        // 4. تهيئة متغيرات التحليل (مع تعديل)
        $sessionsWithLead = 0;
        $totalDuration = 0;
        $totalEventsCount = 0;
        
        // -- [تعديل جوهري] -- فصل المسارات الناجحة عن تحليل التسرب
        $successfulFunnels = [];
        $unitPerformanceData = [];

        // 5. المرور على كل جلسة لتحليلها (مع تعديل جوهري)
        foreach ($groupedBySession as $events) {
            $firstEvent = $events->first();
            $lastEvent = $events->last();
            $totalDuration += $firstEvent->created_at->diffInSeconds($lastEvent->created_at);
            $totalEventsCount += $events->count();

            $hasLead = $events->whereIn('event_type', ['order', 'whatsapp', 'call'])->isNotEmpty();
            
            // تبسيط المسار (لا تغيير هنا)
            $eventTypes = $events->pluck('event_type');
            $simplifiedPathArray = [];
            $lastEventType = null;
            foreach ($eventTypes as $eventType) {
                if ($eventType !== $lastEventType) {
                    $simplifiedPathArray[] = $eventType;
                    $lastEventType = $eventType;
                }
            }
            $funnelPath = implode(' > ', $simplifiedPathArray);

            // -- [المنطق الجديد والحاسم] --
            if ($hasLead) {
                // إذا كانت الجلسة ناجحة، قم بإضافتها إلى قائمة المسارات الناجحة
                $sessionsWithLead++;
                $successfulFunnels[$funnelPath] = ($successfulFunnels[$funnelPath] ?? 0) + 1;
            } else {
                // إذا كانت الجلسة فاشلة، استخدمها لتحليل نقاط التسرب
                $shownUnitsInSession = $events->where('event_type', 'show')->where('trackable_type', 'App\Models\Unit');
                foreach ($shownUnitsInSession as $showEvent) {
                    $unitId = $showEvent->trackable_id;
                    if (!isset($unitPerformanceData[$unitId])) {
                        $unitPerformanceData[$unitId] = ['shows' => 0, 'drop_offs' => 0];
                    }
                    // يتم حساب التسرب فقط من الجلسات الفاشلة
                    $unitPerformanceData[$unitId]['drop_offs']++;
                }
            }
            
            // حساب إجمالي المشاهدات لكل وحدة (من كل الجلسات، ناجحة وفاشلة)
            $allShownUnits = $events->where('event_type', 'show')->where('trackable_type', 'App\Models\Unit');
            foreach ($allShownUnits as $showEvent) {
                $unitId = $showEvent->trackable_id;
                 if (!isset($unitPerformanceData[$unitId])) {
                    $unitPerformanceData[$unitId] = ['shows' => 0, 'drop_offs' => 0];
                }
                $unitPerformanceData[$unitId]['shows']++;
            }
        }

        // 6. تجميع وتنسيق النتائج النهائية (مع تعديل)
        $totalSessions = $groupedBySession->count();
        $stats = [
            'total_sessions' => $totalSessions,
            'conversion_rate' => $totalSessions > 0 ? round(($sessionsWithLead / $totalSessions) * 100, 1) : 0,
            'avg_duration' => $totalSessions > 0 ? round($totalDuration / $totalSessions) : 0,
            'avg_events' => $totalSessions > 0 ? round($totalEventsCount / $totalSessions, 1) : 0,
        ];

        // ترتيب المسارات الناجحة فقط
        arsort($successfulFunnels);
        $topFunnels = array_slice($successfulFunnels, 0, 5, true);

        // ترتيب نقاط التسرب (لا تغيير هنا)
        $frictionPointsData = array_filter($unitPerformanceData, fn($data) => $data['drop_offs'] > 0);
        uasort($frictionPointsData, fn($a, $b) => $b['drop_offs'] <=> $a['drop_offs']);
        $frictionPointUnitIds = array_keys(array_slice($frictionPointsData, 0, 5, true));
        
        $frictionPoints = collect();
        if (!empty($frictionPointUnitIds)) {
            $frictionPoints = Unit::whereIn('id', $frictionPointUnitIds)
                ->select('id', 'title', 'unit_number')
                ->get()
                ->map(function ($unit) use ($unitPerformanceData) {
                    $unit->total_shows = $unitPerformanceData[$unit->id]['shows'];
                    $unit->drop_offs = $unitPerformanceData[$unit->id]['drop_offs'];
                    $unit->drop_off_rate = $unit->total_shows > 0 ? round(($unit->drop_offs / $unit->total_shows) * 100) : 0;
                    return $unit;
                })->sortByDesc('drop_off_rate');
        }

        // 7. تنسيق قائمة الجلسات (لا تغيير هنا)
        $journeys = $groupedBySession->map(function ($events) {
            // ... نفس الكود السابق لتنسيق journeys
        })->sortByDesc('start_time')->take($limit);

        // 8. إرجاع كل البيانات (لا تغيير هنا)
        return [
            'journeys'        => $journeys,
            'stats'           => $stats,
            'top_funnels'     => $topFunnels,
            'friction_points' => $frictionPoints,
        ];
    }


    /**
     * Helper function to extract a readable source from a referrer URL.
     */
    private function extractSourceFromReferrer($referrer)
    {
        if (empty($referrer)) return 'Direct';
        if (str_contains($referrer, 'google.com')) return 'Google';
        if (str_contains($referrer, 'facebook.com')) return 'Facebook';
        if (str_contains($referrer, 'instagram.com')) return 'Instagram';
        if (str_contains($referrer, 'twitter.com')) return 'Twitter';
        if (str_contains($referrer, 'linkedin.com')) return 'LinkedIn';
        if (str_contains($referrer, 'youtube.com')) return 'YouTube';
        if (str_contains($referrer, 'bing.com')) return 'Bing';
        if (str_contains($referrer, 'yahoo.com')) return 'Yahoo';
        if (str_contains($referrer, 'reddit.com')) return 'Reddit';
        if (str_contains($referrer, 'tiktok.com')) return 'TikTok';
        if (str_contains($referrer, 'pinterest.com')) return 'Pinterest';
        if (str_contains($referrer, 'snapchat.com')) return 'Snapchat';
        if (str_contains($referrer, 'whatsapp.com')) return 'WhatsApp';
        if (str_contains($referrer, 'vk.com')) return 'VK';
        if (str_contains($referrer, 'baidu.com')) return 'Baidu';
        if (str_contains($referrer, 'yandex.com')) return 'Yandex';
        if (str_contains($referrer, 'quora.com')) return 'Quora';
        if (str_contains($referrer, 'tumblr.com')) return 'Tumblr';
        if (str_contains($referrer, 'weibo.com')) return 'Weibo';
        if (str_contains($referrer, 'twitch.tv')) return 'Twitch';
        if (str_contains($referrer, 'discord.com')) return 'Discord';
        if (str_contains($referrer, 'telegram.org')) return 'Telegram';
        if (str_contains($referrer, 'line.me')) return 'Line';        
        $host = parse_url($referrer, PHP_URL_HOST);
        return $host ?: 'Other';
    }

}