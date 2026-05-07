<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardAdjustment extends Model
{
    protected $fillable = [
        'adjusted_by',
        'user_id',
        'period_type',
        'period_date',
        'metric_type',
        'original_value',
        'adjusted_value',
        'reason',
    ];

    protected $casts = [
        'period_date' => 'date',
        'original_value' => 'float',
        'adjusted_value' => 'float',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'adjusted_by');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
