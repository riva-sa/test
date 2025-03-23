<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectMedia extends Model
{
    protected $fillable = [
        'project_id',
        'media_type',
        'media_url',
        'media_title',
        'media_description',
        'status',
        'show_in_gallery',
        'show_in_slider',
        'main',
        'youtube_url',
        'vimeo_url',
    ];

    // get media images
    public function getMediaImages($project_id)
    {
        return $this->where('project_id', $project_id)->where('media_type', 'image')->get();
    }

    // get media videos
    public function getMediaVideos($project_id)
    {
        return $this->where('project_id', $project_id)->where('media_type', 'video')->get();
    }

    // get media pdf
    public function getMediaPdf($project_id)
    {
        return $this->where('project_id', $project_id)->where('media_type', 'pdf')->get();
    }

    // get main image
    public function getMainImage($project_id)
    {
        return $this->where('project_id', $project_id)->where('main', 1)->first();
    }

    // show onlu status 1
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
