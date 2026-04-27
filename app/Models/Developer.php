<?php

namespace App\Models;

use App\Traits\BustsProjectsCache;
use Illuminate\Database\Eloquent\Model;

class Developer extends Model
{
    use BustsProjectsCache;
    protected $fillable = [
        'name',
        'logo',
        'description',
        'email',
        'phone',
        'website',
        'address',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
