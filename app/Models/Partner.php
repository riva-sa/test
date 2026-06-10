<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    use HasTranslations;

    /**
     * @var array<int, string>
     */
    protected $translatable = ['name'];

    protected $fillable = [
        'name',
        'name_en',
        'logo',
        'website',
        'status',
    ];

    protected $casts =
        [
            'status' => 'boolean',
        ];
}
