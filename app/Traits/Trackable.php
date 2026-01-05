<?php

namespace App\Traits;

use App\Models\TrackingEvent;

trait Trackable
{
    /**
     * Get all tracking events for this model
     */
    public function trackingEvents()
    {
        return $this->morphMany(TrackingEvent::class, 'trackable');
    }

    /**
     * Track an event for this model
     */
    public function track($eventType, $metadata = [])
    {
        $request = request();
        $referrer = $request->headers->get('referer');
        if ($referrer !== null && strlen($referrer) > 2048) {
            $referrer = substr($referrer, 0, 2048);
        }
        $userAgent = $request->userAgent();
        if ($userAgent !== null && strlen($userAgent) > 2048) {
            $userAgent = substr($userAgent, 0, 2048);
        }

        $data = [
            'event_type' => $eventType,
            'session_id' => session()->getId(),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'metadata' => array_merge($metadata, [
                'device_type' => $this->getDeviceType($userAgent),
                'browser' => $this->getBrowserInfo($userAgent),
            ]),
        ];

        // Dispatch job to handle tracking asynchronously
        // dispatch()->afterResponse() ensures the job runs after the response is sent to the user
        // so it doesn't affect page load time at all.
        \App\Jobs\ProcessTrackingEvent::dispatch($this, $eventType, $data)->afterResponse();

        return true;
    }

    /**
     * Update tracking counters and timestamps
     */
    protected function updateTrackingCounters($eventType)
    {
        // Moved to ProcessTrackingEvent Job
    }

    /**
     * Check if event should be tracked (to avoid duplicate tracking in same session)
     */
    public function shouldTrack($eventType, $timeWindow = 300) // 5 minutes window
    {
        $sessionId = session()->getId();

        // Check cache first for better performance
        $cacheKey = "tracking:{$this->getTable()}:{$this->id}:{$eventType}:{$sessionId}";

        if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
            return false;
        }

        // Cache the tracking event for the time window
        \Illuminate\Support\Facades\Cache::put($cacheKey, true, $timeWindow);

        return true;
    }

    /**
     * Get device type from user agent
     */
    protected function getDeviceType($userAgent)
    {
        if (preg_match('/mobile|android|iphone|ipad|phone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'tablet';
        }

        return 'desktop';
    }

    /**
     * Get browser info from user agent
     */
    protected function getBrowserInfo($userAgent)
    {
        $browsers = [
            'Chrome' => '/chrome/i',
            'Firefox' => '/firefox/i',
            'Safari' => '/safari/i',
            'Edge' => '/edge/i',
            'Opera' => '/opera/i',
            'Internet Explorer' => '/msie/i',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match($pattern, $userAgent)) {
                return $browser;
            }
        }

        return 'Unknown';
    }

    /**
     * Get tracking statistics
     */
    public function getTrackingStats($dateRange = null)
    {
        $query = $this->trackingEvents();

        if ($dateRange) {
            $query->whereBetween('created_at', $dateRange);
        }

        return [
            'total_events' => $query->count(),
            'visits' => $query->clone()->eventType('visit')->count(),
            'views' => $query->clone()->eventType('view')->count(),
            'shows' => $query->clone()->eventType('show')->count(),
            'orders' => $query->clone()->eventType('order')->count(),
            'unique_sessions' => $query->clone()->distinct('session_id')->count('session_id'),
            'events_by_day' => $query->clone()
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
            'events_by_type' => $query->clone()
                ->selectRaw('event_type, COUNT(*) as count')
                ->groupBy('event_type')
                ->get(),
        ];
    }
}
