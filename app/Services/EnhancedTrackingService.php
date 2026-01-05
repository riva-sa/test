<?php

namespace App\Services;

use App\Helpers\DatabaseHelper;
use App\Models\Campaign;
use App\Models\Project;
use App\Models\TrackingEvent;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EnhancedTrackingService extends TrackingService
{
    /**
     * Get comprehensive dashboard overview statistics
     */
    public function getDashboardOverview(array $filters = []): array
    {

        $cacheKey = 'dashboard_overview_'.md5(serialize($filters));

        return Cache::remember($cacheKey, 300, function () use ($filters) {
            $query = $this->buildBaseQuery($filters);
            $startDate = $filters['start_date'] ?? Carbon::now()->subDays(30);
            $endDate = $filters['end_date'] ?? Carbon::now();

            // Previous period for comparison
            $previousStartDate = Carbon::parse($startDate)->subDays($startDate->diffInDays($endDate));
            $previousEndDate = Carbon::parse($startDate)->subDay();

            $currentStats = $this->getStatsForPeriod($query, $startDate, $endDate);
            $previousStats = $this->getStatsForPeriod($query, $previousStartDate, $previousEndDate);

            return [
                'current' => $currentStats,
                'previous' => $previousStats,
                'growth' => $this->calculateGrowth($currentStats, $previousStats),
                'campaigns' => [
                    'total' => Campaign::count(),
                    'active' => Campaign::where('status', 'active')->count(),
                    'paused' => Campaign::where('status', 'paused')->count(),
                    'completed' => Campaign::where('status', 'completed')->count(),
                ],
                'top_performing_campaign' => $this->getTopPerformingCampaign($filters),
            ];
        });

    }

    /**
     * Get detailed campaign analytics with comparison
     */
    public function getCampaignDetailedAnalytics($campaignId, array $options = []): array
    {

        $campaign = Campaign::with('project')->findOrFail($campaignId);
        $startDate = $options['start_date'] ?? $campaign->start_date;
        $endDate = $options['end_date'] ?? ($campaign->end_date ?? Carbon::now());

        $filters = [
            'campaign_id' => $campaignId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        return [
            'campaign' => $campaign,
            'overview' => $this->getCampaignOverview($campaign, $startDate, $endDate),
            'daily_breakdown' => $this->getCampaignDailyBreakdown($campaign),
            'hourly_breakdown' => $this->getCampaignHourlyBreakdown($campaign, $startDate, $endDate),
            'conversion_funnel' => $this->getCampaignConversionFunnel($campaign, $startDate, $endDate),
            'traffic_sources' => $this->getCampaignTrafficSources($campaign, $startDate, $endDate),
            'device_breakdown' => $this->getCampaignDeviceBreakdown($campaign, $startDate, $endDate),
            'geographic_data' => $this->getCampaignGeographicData($campaign, $startDate, $endDate),
            'top_content' => $this->getCampaignTopContent($campaign, $startDate, $endDate),
            'roi_analysis' => $this->getCampaignROIAnalysis($campaign, $startDate, $endDate),
        ];

    }

    /**
     * Get advanced filtering options for campaigns
     */
    public function getAdvancedFilters(): array
    {

        return [
            'projects' => Project::where('status', 1)->select('id', 'name')->get(),
            'sources' => Campaign::distinct('source')->whereNotNull('source')->pluck('source'),
            'event_types' => ['visit', 'view', 'show', 'order', 'whatsapp', 'call'],
            'device_types' => $this->getDeviceTypes(),
            'date_presets' => [
                'today' => ['start' => Carbon::today(), 'end' => Carbon::now()],
                'yesterday' => ['start' => Carbon::yesterday(), 'end' => Carbon::yesterday()->endOfDay()],
                'last_7_days' => ['start' => Carbon::now()->subDays(7), 'end' => Carbon::now()],
                'last_30_days' => ['start' => Carbon::now()->subDays(30), 'end' => Carbon::now()],
                'this_month' => ['start' => Carbon::now()->startOfMonth(), 'end' => Carbon::now()],
                'last_month' => ['start' => Carbon::now()->subMonth()->startOfMonth(), 'end' => Carbon::now()->subMonth()->endOfMonth()],
            ],
        ];

    }

    /**
     * Get campaign comparison data
     */
    public function getCampaignComparison(array $campaignIds, array $filters = []): array
    {

        $campaigns = Campaign::whereIn('id', $campaignIds)->with('project')->get();
        $comparison = [];

        foreach ($campaigns as $campaign) {
            $campaignFilters = array_merge($filters, ['campaign_id' => $campaign->id]);
            $comparison[$campaign->id] = [
                'campaign' => $campaign,
                'metrics' => $this->getCampaignMetrics($campaign, $campaignFilters),
                'performance_score' => $this->calculatePerformanceScore($campaign, $campaignFilters),
            ];
        }

        return $comparison;

    }

    /**
     * Get real-time campaign updates
     */
    public function getRealTimeUpdates($campaignId = null): array
    {

        $query = TrackingEvent::where('created_at', '>=', Carbon::now()->subMinutes(5));

        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $query->where(function ($q) use ($campaign) {
                    $q->whereHasMorph('trackable', [Project::class], function ($query) use ($campaign) {
                        $query->where('id', $campaign->project_id);
                    })->orWhereHasMorph('trackable', [Unit::class], function ($query) use ($campaign) {
                        $query->where('project_id', $campaign->project_id);
                    });
                });
            }
        }

        $recentEvents = $query->with('trackable')->latest()->limit(10)->get();

        return [
            'recent_events' => $recentEvents,
            'live_stats' => [
                'active_visitors' => $this->getActiveVisitors($campaignId),
                'events_last_hour' => $query->where('created_at', '>=', Carbon::now()->subHour())->count(),
                'conversion_rate_today' => $this->getTodayConversionRate($campaignId),
            ],
        ];

    }

    /**
     * Export campaign data for reporting
     */
    public function exportCampaignData($campaignId, $format = 'array'): array
    {

        $campaign = Campaign::with('project')->findOrFail($campaignId);
        $analytics = $this->getCampaignDetailedAnalytics($campaignId);

        $exportData = [
            'campaign_info' => [
                'name' => $campaign->name,
                'project' => $campaign->project->name,
                'source' => $campaign->source,
                'start_date' => $campaign->start_date->format('Y-m-d'),
                'end_date' => $campaign->end_date ? $campaign->end_date->format('Y-m-d') : null,
                'budget' => $campaign->budget,
                'status' => $campaign->status,
            ],
            'summary_metrics' => $analytics['overview'],
            'daily_data' => $analytics['daily_breakdown'],
            'conversion_funnel' => $analytics['conversion_funnel'],
            'traffic_sources' => $analytics['traffic_sources'],
            'device_breakdown' => $analytics['device_breakdown'],
            'top_content' => $analytics['top_content'],
            'roi_analysis' => $analytics['roi_analysis'],
            'generated_at' => Carbon::now()->toISOString(),
        ];

        return $exportData;

    }

    // Private helper methods

    private function buildBaseQuery(array $filters = [])
    {
        $query = TrackingEvent::query();

        if (isset($filters['start_date']) && isset($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        }

        if (isset($filters['campaign_id'])) {
            $campaign = Campaign::find($filters['campaign_id']);
            if ($campaign) {
                $query->where(function ($q) use ($campaign) {
                    $q->whereHasMorph('trackable', [Project::class], function ($query) use ($campaign) {
                        $query->where('id', $campaign->project_id);
                    })->orWhereHasMorph('trackable', [Unit::class], function ($query) use ($campaign) {
                        $query->where('project_id', $campaign->project_id);
                    });
                });
            }
        }

        if (isset($filters['project_id'])) {
            $query->where(function ($q) use ($filters) {
                $q->whereHasMorph('trackable', [Project::class], function ($query) use ($filters) {
                    $query->where('id', $filters['project_id']);
                })->orWhereHasMorph('trackable', [Unit::class], function ($query) use ($filters) {
                    $query->where('project_id', $filters['project_id']);
                });
            });
        }

        if (isset($filters['event_types']) && is_array($filters['event_types'])) {
            $types = $filters['event_types'];
            if (in_array('whatsapp', $types)) {
                $types[] = 'WhatsAppClick';
            }
            if (in_array('call', $types)) {
                $types[] = 'PhoneCall';
            }
            $query->whereIn('event_type', $types);
        }

        return $query;
    }

    private function getStatsForPeriod($query, $startDate, $endDate): array
    {
        $periodQuery = $query->clone()->whereBetween('created_at', [$startDate, $endDate]);

        return [
            'total_events' => $periodQuery->count(),
            'unique_sessions' => $periodQuery->distinct('session_id')->count('session_id'),
            'visits' => $periodQuery->clone()->where('event_type', 'visit')->count(),
            'views' => $periodQuery->clone()->where('event_type', 'view')->count(),
            'shows' => $periodQuery->clone()->where('event_type', 'show')->count(),
            'orders' => $periodQuery->clone()->where('event_type', 'order')->count(),
            'whatsapp' => $periodQuery->clone()->whereIn('event_type', ['whatsapp', 'WhatsAppClick'])->count(),
            'calls' => $periodQuery->clone()->whereIn('event_type', ['call', 'PhoneCall'])->count(),
        ];
    }

    private function calculateGrowth(array $current, array $previous): array
    {
        $growth = [];
        foreach ($current as $key => $value) {
            $previousValue = $previous[$key] ?? 0;
            if ($previousValue > 0) {
                $growth[$key] = round((($value - $previousValue) / $previousValue) * 100, 2);
            } else {
                $growth[$key] = $value > 0 ? 100 : 0;
            }
        }

        return $growth;
    }

    private function getTopPerformingCampaign(array $filters = []): ?array
    {
        $campaigns = Campaign::with('project')->get();
        $bestCampaign = null;
        $bestScore = 0;

        foreach ($campaigns as $campaign) {
            $campaignFilters = array_merge($filters, ['campaign_id' => $campaign->id]);
            $score = $this->calculatePerformanceScore($campaign, $campaignFilters);

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestCampaign = [
                    'campaign' => $campaign,
                    'score' => $score,
                    'metrics' => $this->getCampaignMetrics($campaign, $campaignFilters),
                ];
            }
        }

        return $bestCampaign;
    }

    private function getCampaignOverview(Campaign $campaign, $startDate, $endDate): array
    {
        $filters = [
            'campaign_id' => $campaign->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $query = $this->buildBaseQuery($filters);
        $stats = $this->getStatsForPeriod($query, $startDate, $endDate);

        // Calculate additional metrics
        $stats['conversion_rate'] = $stats['visits'] > 0 ? round(($stats['orders'] / $stats['visits']) * 100, 2) : 0;
        $stats['engagement_rate'] = $stats['visits'] > 0 ? round((($stats['whatsapp'] + $stats['calls']) / $stats['visits']) * 100, 2) : 0;
        $stats['cost_per_acquisition'] = $campaign->budget && $stats['orders'] > 0 ? round($campaign->budget / $stats['orders'], 2) : 0;
        $stats['return_on_investment'] = $this->calculateROI($campaign, $stats);

        return $stats;
    }

    private function getCampaignHourlyBreakdown(Campaign $campaign, $startDate, $endDate): array
    {
        $query = $this->buildBaseQuery(['campaign_id' => $campaign->id])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $hourlyStats = $query
            ->selectRaw('HOUR(created_at) as hour, event_type, COUNT(*) as count')
            ->groupBy('hour', 'event_type')
            ->orderBy('hour')
            ->get();

        $results = [];
        for ($hour = 0; $hour < 24; $hour++) {
            $results[$hour] = [
                'hour' => $hour,
                'visit' => 0,
                'view' => 0,
                'show' => 0,
                'order' => 0,
                'whatsapp' => 0,
                'call' => 0,
            ];
        }

        foreach ($hourlyStats as $stat) {
            $eventType = $stat->event_type;
            if ($eventType === 'WhatsAppClick') {
                $eventType = 'whatsapp';
            }
            if ($eventType === 'PhoneCall') {
                $eventType = 'call';
            }

            if (isset($results[$stat->hour][$eventType])) {
                $results[$stat->hour][$eventType] += $stat->count;
            }
        }

        return array_values($results);
    }

    private function getCampaignConversionFunnel(Campaign $campaign, $startDate, $endDate): array
    {
        $query = $this->buildBaseQuery(['campaign_id' => $campaign->id])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $visits = $query->clone()->where('event_type', 'visit')->count();
        $views = $query->clone()->where('event_type', 'view')->count();
        $shows = $query->clone()->where('event_type', 'show')->count();
        $contacts = $query->clone()->whereIn('event_type', ['whatsapp', 'WhatsAppClick', 'call', 'PhoneCall'])->count();
        $orders = $query->clone()->where('event_type', 'order')->count();

        return [
            ['stage' => 'زيارات', 'count' => $visits, 'percentage' => 100],
            ['stage' => 'مشاهدات', 'count' => $views, 'percentage' => $visits > 0 ? round(($views / $visits) * 100, 2) : 0],
            ['stage' => 'عروض', 'count' => $shows, 'percentage' => $visits > 0 ? round(($shows / $visits) * 100, 2) : 0],
            ['stage' => 'تواصل', 'count' => $contacts, 'percentage' => $visits > 0 ? round(($contacts / $visits) * 100, 2) : 0],
            ['stage' => 'طلبات', 'count' => $orders, 'percentage' => $visits > 0 ? round(($orders / $visits) * 100, 2) : 0],
        ];
    }

    private function getCampaignTrafficSources(Campaign $campaign, $startDate, $endDate): array
    {
        $query = $this->buildBaseQuery(['campaign_id' => $campaign->id])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $raw = $query
            ->select('referrer', DB::raw('COUNT(*) as count'))
            ->whereNotNull('referrer')
            ->groupBy('referrer')
            ->orderByDesc('count')
            ->limit(1000)
            ->get();

        $grouped = $raw
            ->map(function ($row) {
                return [
                    'source' => $this->extractSourceFromReferrer($row->referrer),
                    'count' => (int) $row->count,
                ];
            })
            ->groupBy('source')
            ->map(function ($items, $source) {
                return [
                    'source' => $source,
                    'count' => collect($items)->sum('count'),
                ];
            })
            ->values()
            ->sortByDesc('count')
            ->take(10);

        return $grouped->toArray();
    }

    private function getCampaignDeviceBreakdown(Campaign $campaign, $startDate, $endDate): array
    {
        $query = $this->buildBaseQuery(['campaign_id' => $campaign->id])
            ->whereBetween('created_at', [$startDate, $endDate]);

        try {
            if (DatabaseHelper::isPostgreSQL()) {
                return $query
                    ->selectRaw("metadata->>'device_type' as device_type, COUNT(*) as count")
                    ->whereNotNull('metadata')
                    ->whereRaw("metadata->>'device_type' IS NOT NULL")
                    ->groupBy(DB::raw("metadata->>'device_type'"))
                    ->get()
                    ->toArray();
            } else {
                return $query
                    ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.device_type')) as device_type, COUNT(*) as count")
                    ->whereNotNull('metadata')
                    ->whereRaw("JSON_EXTRACT(metadata, '$.device_type') IS NOT NULL")
                    ->groupBy('device_type')
                    ->get()
                    ->toArray();
            }
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getCampaignGeographicData(Campaign $campaign, $startDate, $endDate): array
    {
        // This would require IP geolocation data
        // For now, return empty array or mock data
        return [
            ['country' => 'Saudi Arabia', 'count' => 150],
            ['country' => 'UAE', 'count' => 75],
            ['country' => 'Egypt', 'count' => 50],
        ];
    }

    private function getCampaignTopContent(Campaign $campaign, $startDate, $endDate): array
    {
        $query = $this->buildBaseQuery(['campaign_id' => $campaign->id])
            ->whereBetween('created_at', [$startDate, $endDate]);

        $topUnits = $query->clone()
            ->where('trackable_type', Unit::class)
            ->selectRaw('trackable_id, COUNT(*) as interactions')
            ->groupBy('trackable_id')
            ->orderByDesc('interactions')
            ->limit(5)
            ->get();

        $units = [];
        foreach ($topUnits as $item) {
            $unit = Unit::find($item->trackable_id);
            if ($unit) {
                $units[] = [
                    'unit' => $unit,
                    'interactions' => $item->interactions,
                ];
            }
        }

        return $units;
    }

    private function getCampaignROIAnalysis(Campaign $campaign, $startDate, $endDate): array
    {
        $stats = $this->getCampaignOverview($campaign, $startDate, $endDate);

        return [
            'budget' => $campaign->budget ?? 0,
            'cost_per_click' => $campaign->budget && $stats['visits'] > 0 ? round($campaign->budget / $stats['visits'], 2) : 0,
            'cost_per_acquisition' => $campaign->budget && $stats['orders'] > 0 ? round($campaign->budget / $stats['orders'], 2) : 0,
            'return_on_investment' => $this->calculateROI($campaign, $stats),
            'estimated_revenue' => $stats['orders'] * 1000, // Assuming average order value
        ];
    }

    private function getCampaignMetrics(Campaign $campaign, array $filters): array
    {
        $query = $this->buildBaseQuery($filters);
        $startDate = $filters['start_date'] ?? $campaign->start_date;
        $endDate = $filters['end_date'] ?? ($campaign->end_date ?? Carbon::now());

        return $this->getStatsForPeriod($query, $startDate, $endDate);
    }

    private function calculatePerformanceScore(Campaign $campaign, array $filters): float
    {
        $metrics = $this->getCampaignMetrics($campaign, $filters);

        // Simple scoring algorithm - can be made more sophisticated
        $score = 0;
        $score += $metrics['visits'] * 0.1;
        $score += $metrics['orders'] * 10;
        $score += $metrics['whatsapp'] * 2;
        $score += $metrics['calls'] * 3;

        return round($score, 2);
    }

    private function calculateROI(Campaign $campaign, array $stats): float
    {
        if (! $campaign->budget || $campaign->budget <= 0) {
            return 0;
        }

        $estimatedRevenue = $stats['orders'] * 1000; // Assuming average order value

        return round((($estimatedRevenue - $campaign->budget) / $campaign->budget) * 100, 2);
    }

    private function getDeviceTypes(): array
    {

        try {
            if (DatabaseHelper::isPostgreSQL()) {
                return TrackingEvent::selectRaw("metadata->>'device_type' as device_type")
                    ->whereNotNull('metadata')
                    ->whereRaw("metadata->>'device_type' IS NOT NULL")
                    ->distinct()
                    ->pluck('device_type')
                    ->filter()
                    ->toArray();
            } else {
                return TrackingEvent::selectRaw("JSON_UNQUOTE(JSON_EXTRACT(metadata, '$.device_type')) as device_type")
                    ->whereNotNull('metadata')
                    ->whereRaw("JSON_EXTRACT(metadata, '$.device_type') IS NOT NULL")
                    ->distinct()
                    ->pluck('device_type')
                    ->filter()
                    ->toArray();
            }
        } catch (\Exception $e) {
            return ['desktop', 'mobile', 'tablet'];
        }

    }

    private function getActiveVisitors($campaignId = null): int
    {

        $query = TrackingEvent::where('created_at', '>=', Carbon::now()->subMinutes(30));

        if ($campaignId) {
            $campaign = Campaign::find($campaignId);
            if ($campaign) {
                $query->where(function ($q) use ($campaign) {
                    $q->whereHasMorph('trackable', [Project::class], function ($query) use ($campaign) {
                        $query->where('id', $campaign->project_id);
                    })->orWhereHasMorph('trackable', [Unit::class], function ($query) use ($campaign) {
                        $query->where('project_id', $campaign->project_id);
                    });
                });
            }
        }

        return $query->distinct('session_id')->count('session_id');

    }

    private function getTodayConversionRate($campaignId = null): float
    {

        $today = Carbon::today();
        $filters = [
            'start_date' => $today,
            'end_date' => Carbon::now(),
        ];

        if ($campaignId) {
            $filters['campaign_id'] = $campaignId;
        }

        $query = $this->buildBaseQuery($filters);
        $visits = $query->clone()->where('event_type', 'visit')->count();
        $orders = $query->clone()->where('event_type', 'order')->count();

        return $visits > 0 ? round(($orders / $visits) * 100, 2) : 0;

    }
}
