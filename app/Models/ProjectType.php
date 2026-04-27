<?php

namespace App\Models;

use App\Traits\BustsProjectsCache;
use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    use BustsProjectsCache;

    protected $fillable = [
        'name',
        'status',
        'slug',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
