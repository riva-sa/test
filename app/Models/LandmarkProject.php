<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LandmarkProject extends Pivot
{
    protected $table = 'landmark_project';

    protected $fillable = [
        'project_id',
        'landmark_id',
        'distance',
    ];

    public function landmark()
    {
        return $this->belongsTo(Landmark::class);
    }
}
