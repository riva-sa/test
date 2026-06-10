<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentBlock extends Model
{
    use HasTranslations;

    /**
     * Only the visitor-facing content is translated; `description` is an
     * admin-side label used for key generation.
     *
     * @var array<int, string>
     */
    protected $translatable = ['content'];

    protected $fillable = ['key', 'content', 'content_en', 'description'];

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
