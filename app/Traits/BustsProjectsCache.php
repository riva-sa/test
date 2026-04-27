<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait BustsProjectsCache
{
    /**
     * Boot the trait and register model observers.
     */
    protected static function bootBustsProjectsCache()
    {
        static::saved(function ($model) {
            static::bustCache($model);
        });

        static::deleted(function ($model) {
            static::bustCache($model);
        });
    }

    /**
     * Bust the projects and units related cache.
     */
    protected static function bustCache($model = null)
    {
        // Increment the global version to invalidate all pagination keys
        if (!Cache::has('projects_cache_version')) {
            Cache::put('projects_cache_version', 1, 86400 * 30);
        } else {
            Cache::increment('projects_cache_version');
        }

        // Forget static lists
        Cache::forget('developers_all');
        Cache::forget('project_types_active');

        // Bust specific project cache if applicable
        if ($model instanceof \App\Models\Project) {
            Cache::forget('project_single:' . $model->slug);
            $key = 'project_cache_version:' . $model->id;
            if (!Cache::has($key)) {
                Cache::put($key, 1, 86400 * 30);
            } else {
                Cache::increment($key);
            }
        }

        // Bust parent project cache if a unit or media is changed
        if (($model instanceof \App\Models\Unit || $model instanceof \App\Models\ProjectMedia) && $model->project_id) {
            $project = $model->project;
            if ($project) {
                Cache::forget('project_single:' . $project->slug);
                $key = 'project_cache_version:' . $model->project_id;
                if (!Cache::has($key)) {
                    Cache::put($key, 1, 86400 * 30);
                } else {
                    Cache::increment($key);
                }
            }
        }

        // If a developer or project type is updated, we need to bust all their projects
        if ($model instanceof \App\Models\Developer || $model instanceof \App\Models\ProjectType) {
            // Use the projects relation to forget cache for all associated projects
            $model->projects()->get(['slug'])->each(function($project) {
                Cache::forget('project_single:' . $project->slug);
            });
        }
    }
}
