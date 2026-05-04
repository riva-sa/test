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

    /**
     * Get the YouTube embed URL from the youtube_url field.
     * Supports standard, shortened, embed, and shorts URL formats.
     */
    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        $url = $this->youtube_url;

        if (empty($url)) {
            return null;
        }

        $videoId = null;

        // Standard: youtube.com/watch?v=VIDEO_ID
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Shortened: youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Embed: youtube.com/embed/VIDEO_ID
        elseif (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }
        // Shorts: youtube.com/shorts/VIDEO_ID
        elseif (preg_match('/youtube\.com\/shorts\/([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
        }

        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
    }
}
