<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    public static function getUrl($path)
    {
        if (app()->environment('local')) {
            return url('storage/' . $path);
        } else {
            return Storage::disk('public')->url($path);
        }
    }
}
