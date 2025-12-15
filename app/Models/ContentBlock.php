<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentBlock extends Model
{
    protected $fillable = ['key', 'content', 'description'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->key)) {
                $model->key = static::generateUniqueKey($model->description);
            }
        });
    }

    protected static function generateUniqueKey(string $description): string
    {
        $baseKey = Str::slug($description ?: 'content_block', '_');
        $key = $baseKey;
        $counter = 1;

        while (static::where('key', $key)->exists()) {
            $key = $baseKey.'_'.$counter;
            $counter++;
        }

        return $key;
    }
}
