<?php

namespace App\Http\Controllers;

use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController
{
    public function show(Request $request, string $path)
    {
        if (empty($path)) {
            abort(404);
        }

        /** @var ImageOptimizationService $optimizer */
        $optimizer = app(ImageOptimizationService::class);
        
        // If client accepts WebP, serve the WebP version if available
        if (str_contains($request->header('Accept', ''), 'image/webp')) {
            $url = $optimizer->getOptimizedUrl($path);
        } else {
            /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
            $disk = Storage::disk('public');
            $url = $disk->url($path);
        }

        return redirect()->away($url, 302, [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'Vary' => 'Accept'
        ]);
    }
}
