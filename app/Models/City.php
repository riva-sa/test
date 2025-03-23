<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

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
