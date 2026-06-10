<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    use HasTranslations;

    /**
     * @var array<int, string>
     */
    protected $translatable = ['name', 'description'];

    protected $fillable = ['name', 'name_en', 'description', 'description_en', 'icon', 'is_active'];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
