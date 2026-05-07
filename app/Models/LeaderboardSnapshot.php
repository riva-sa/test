<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardSnapshot extends Model
{
    protected $fillable = [
        'user_id',
        'snapshot_date',
        'monthly_orders',
        'daily_orders',
        'reservations',
        'sales',
        'composite_score',
    ];

    protected $casts = [
        'snapshot_date' => 'date',
        'composite_score' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForDate(Builder $query, Carbon $date): Builder
    {
        return $query->where('snapshot_date', $date->toDateString());
    }

    public function scopeForMonth(Builder $query, Carbon $month): Builder
    {
        return $query->whereBetween('snapshot_date', [
            $month->copy()->startOfMonth()->toDateString(),
            $month->copy()->endOfMonth()->toDateString(),
        ]);
    }
}
