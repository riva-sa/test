<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'project_id',
        'source',
        'start_date',
        'end_date',
        'status',
        'budget',
        'target_audience',
        'goals',
        'metadata',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'metadata' => 'array',
        'goals' => 'array',
    ];

    // Campaign sources
    const SOURCES = [
        'snapchat' => 'Snapchat',
        'tiktok' => 'TikTok',
        'instagram' => 'Instagram',
        'facebook' => 'Facebook',
        'google' => 'Google Ads',
        'twitter' => 'Twitter',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'email' => 'Email Marketing',
        'sms' => 'SMS Marketing',
        'direct' => 'Direct Marketing',
        'other' => 'Other',
    ];

    // Campaign statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the project that owns the campaign
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get campaign duration in days
     */
    public function getDurationAttribute()
    {
        if (!$this->start_date) return 0;
        
        $endDate = $this->end_date ?: now();
        return $this->start_date->diffInDays($endDate);
    }

    /**
     * Check if campaign is active
     */
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE 
            && $this->start_date <= now() 
            && (!$this->end_date || $this->end_date >= now());
    }

    /**
     * Get formatted source name
     */
    public function getSourceNameAttribute()
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    /**
     * Scope for active campaigns
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for campaigns by project
     */
    public function scopeForProject($query, $projectId)
    {
        return $query->where('project_id', $projectId);
    }

}
