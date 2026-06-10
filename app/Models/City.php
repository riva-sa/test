<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory, HasTranslations;

    /**
     * @var array<int, string>
     */
    protected $translatable = ['name'];

    protected $guarded = [];

    public function states()
    {
        return $this->hasMany(State::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
