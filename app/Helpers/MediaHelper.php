<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    public static function getUrl($path)
    {
        if (empty($path)) {
            return 'https://placehold.co/800x600?text=No+Image';
        }

        if (app()->environment('local') || env('FILESYSTEM_DISK') === 'local') {
            return url('storage/'.$path);
        }

        $disk = Storage::disk('public');
        try {
            $version = $disk->lastModified($path);
        } catch (\Throwable $e) {
            $version = time();
        }

        return route('media.show', ['path' => $path, 'v' => $version]);
    }
}
