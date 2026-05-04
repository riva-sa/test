<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaderboardConfig extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'target_type',
        'weight',
        'updated_at',
    ];

    protected $casts = [
        'weight' => 'float',
        'updated_at' => 'datetime',
    ];

    public const DEFAULTS = [
        'monthly_orders' => 25.00,
        'daily_orders' => 25.00,
        'reservations' => 25.00,
        'sales' => 25.00,
    ];

    /**
     * Get the current weight config as an associative array [type => weight].
     */
    public static function getWeights(): array
    {
        $stored = static::pluck('weight', 'target_type')->toArray();

        // Fill in defaults for any missing types
        return array_merge(self::DEFAULTS, $stored);
    }

    /**
     * Upsert all four weights at once.
     */
    public static function saveWeights(array $weights): void
    {
        foreach ($weights as $type => $weight) {
            static::updateOrCreate(
                ['target_type' => $type],
                ['weight' => $weight, 'updated_at' => now()]
            );
        }
    }
}
