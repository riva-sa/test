<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, Trackable;

    protected $fillable = [
        'title',
        'slug',
        'living_rooms',
        'sale_type',
        'user_id',
        'project_id',
        'unit_type',
        'building_number',
        'unit_number',
        'description',
        'floor',
        'unit_area',
        'unit_price',
        'beadrooms',
        'bathrooms',
        'kitchen',
        'latitude',
        'longitude',
        'image',
        'floor_plan',
        'show_price',
        'status',
        'location',
        'case',
        'visits_count',
        'views_count',
        'shows_count',
        'orders_count',
        'last_visited_at',
        'last_viewed_at',
        'last_shown_at',
        'last_ordered_at',
    ];

    protected $casts =
        [
            'status' => 'boolean',
            'show_price' => 'boolean',
            'last_visited_at' => 'datetime',
            'last_viewed_at' => 'datetime',
            'last_shown_at' => 'datetime',
            'last_ordered_at' => 'datetime',
        ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = json_encode($value);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }

    // Scope for popular units
    public function scopePopular($query, $days = 30)
    {
        return $query->selectRaw('*, (visits_count + views_count * 2 + shows_count * 3 + orders_count * 10) as popularity_score')
            ->where('last_visited_at', '>', now()->subDays($days))
            ->orderBy('popularity_score', 'desc');
    }

    // Scope for most viewed units
    public function scopeMostViewed($query, $days = 30)
    {
        return $query->where('last_viewed_at', '>', now()->subDays($days))
            ->orderBy('views_count', 'desc');
    }

    // Scope for most ordered units
    public function scopeMostOrdered($query, $days = 30)
    {
        return $query->where('last_ordered_at', '>', now()->subDays($days))
            ->orderBy('orders_count', 'desc');
    }

    // Get conversion rate for this unit
    public function getConversionRate()
    {
        if ($this->shows_count == 0) {
            return 0;
        }

        return round(($this->orders_count / $this->shows_count) * 100, 2);
    }
}
