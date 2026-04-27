<?php

namespace App\Helpers;

use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;

class MediaHelper
{
    /**
     * Get the URL for an image, optionally returning an optimized variant if available.
     *
     * @param string|null $path
     * @param string|null $size 'thumbnail', 'medium', 'large' or null for best available
     * @return string
     */
    public static function getUrl($path, ?string $size = null)
    {
        if (! is_string($path) || empty($path)) {
            return 'https://placehold.co/800x600?text=riva.sa';
        }

        try {
            /** @var ImageOptimizationService $optimizer */
            $optimizer = app(ImageOptimizationService::class);
            return $optimizer->getOptimizedUrl($path, $size);
        } catch (\Exception $e) {
            // Fallback to original path if service fails or isn't bound properly
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            return $disk->url($path);
        }
    }
}

