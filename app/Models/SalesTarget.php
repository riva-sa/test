<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesTarget extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'target_value',
    ];

    protected $casts = [
        'target_value' => 'integer',
    ];

    public const TYPES = [
        'monthly_orders' => 'الطلبات الشهرية',
        'daily_orders' => 'الطلبات اليومية',
        'reservations' => 'الحجوزات',
        'sales' => 'المبيعات',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
