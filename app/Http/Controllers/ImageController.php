<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ImageController
{
    public function show(Request $request, string $path)
    {
        if (empty($path)) {
            abort(404);
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');
        
        // Always redirect to the storage URL (S3 or Local)
        // This offloads traffic from PHP to Nginx or S3 directly
        $url = $disk->url($path);
        
        return redirect()->away($url, 302);
    }
}
