<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'trackable_type',
        'trackable_id',
        'event_type',
        'session_id',
        'ip_address',
        'user_agent',
        'referrer',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the trackable model (Unit or Project)
     */
    public function trackable()
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by event type
     */
    public function scopeEventType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to filter by trackable type
     */
    public function scopeTrackableType($query, $type)
    {
        return $query->where('trackable_type', $type);
    }

    /**
     * Scope to get events within date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get unique sessions
     */
    public function scopeUniqueSessions($query)
    {
        return $query->select('session_id')->distinct();
    }
}
