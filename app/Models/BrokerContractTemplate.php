<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokerContractTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'pdf_path',
        'fields_config',
        'is_active',
    ];

    protected $casts = [
        'fields_config' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the current active contract template.
     */
    public static function getActiveTemplate(): ?self
    {
        return self::where('is_active', true)->latest()->first();
    }
}
