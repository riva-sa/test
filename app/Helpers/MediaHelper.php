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

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($path);
    }
}
