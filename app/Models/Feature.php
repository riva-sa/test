<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Feature extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'is_active'];

    public function units()
    {
        return $this->belongsToMany(Unit::class);
    }
    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

}
