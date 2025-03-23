<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    protected $fillable = ['name', 'description', 'icon', 'is_active'];

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }
}
