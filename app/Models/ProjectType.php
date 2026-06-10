<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use HasTranslations;

    /**
     * @var array<int, string>
     */
    protected $translatable = ['name'];

    protected $fillable = [
        'name',
        'name_en',
        'status',
        'slug',
    ];
}
