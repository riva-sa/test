<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    protected $fillable = [
        'page_url',
        'route_name',
        'metric_type',
        'value',
        'request_method',
        'user_agent',
    ];

    public function scopeForRoute($query, $route)
    {
        return $query->where('route_name', $route);
    }

    public function scopeForMetric($query, $metric)
    {
        return $query->where('metric_type', $metric);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
