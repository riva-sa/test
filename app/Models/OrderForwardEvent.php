<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderForwardEvent extends Model
{
    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (OrderForwardEvent $event): void {
            if ($event->created_at === null) {
                $event->created_at = now();
            }
        });
    }

    protected $fillable = [
        'unit_order_id',
        'strategy',
        'status',
        'payload',
        'created_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public function unitOrder(): BelongsTo
    {
        return $this->belongsTo(UnitOrder::class);
    }
}
