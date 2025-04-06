<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'living_rooms',
        'sale_type',
        'user_id',
        'project_id',
        'unit_type',
        'building_number',
        'unit_number',
        'description',
        'floor',
        'unit_area',
        'unit_price',
        'beadrooms',
        'bathrooms',
        'kitchen',
        'latitude',
        'longitude',
        'image',
        'floor_plan',
        'show_price',
        'status',
        'location',
        'case'
    ];

    protected $casts =
    [
        'status' => 'boolean',
        'show_price' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setLocationAttribute($value)
    {
        $this->attributes['location'] = json_encode($value);
    }

    public function features()
    {
        return $this->belongsToMany(Feature::class);
    }
}
