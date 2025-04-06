<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Landmark extends Model
{
    protected $fillable = ['name', 'description', 'distance', 'type'];

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withPivot('distance')->withTimestamps();
    }
}
