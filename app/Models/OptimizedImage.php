<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptimizedImage extends Model
{
    protected $fillable = [
        'original_path',
        'variant_type',
        'variant_path',
        'format',
        'width',
        'height',
        'file_size',
        'original_size',
        'status',
        'error_message',
    ];

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForPath($query, $path)
    {
        return $query->where('original_path', $path);
    }
}
