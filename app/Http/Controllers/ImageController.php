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

        // Local environment: serve from public/storage directly
        if (app()->environment('local') || env('FILESYSTEM_DISK') === 'local') {
            $fullPath = public_path('storage/'.$path);
            if (! file_exists($fullPath)) {
                $fullPath = storage_path('app/public/'.$path);
            }
            if (! file_exists($fullPath)) {
                abort(404);
            }

            $mtime = filemtime($fullPath) ?: time();
            $size = filesize($fullPath) ?: 0;
            $etag = 'W/"'.dechex($size).'-'.dechex($mtime).'"';
            $headers = [
                'Cache-Control' => 'public, max-age=31536000, immutable',
                'ETag' => $etag,
                'Last-Modified' => gmdate('D, d M Y H:i:s', $mtime).' GMT',
                'Content-Type' => mime_content_type($fullPath) ?: 'application/octet-stream',
            ];

            $ifNoneMatch = $request->headers->get('If-None-Match');
            $ifModifiedSince = $request->headers->get('If-Modified-Since');
            if ($ifNoneMatch === $etag || $ifModifiedSince === $headers['Last-Modified']) {
                return response('', 304, $headers);
            }

            return response()->file($fullPath, $headers);
        }

        // Non-local: use the configured public disk (S3)
        $disk = Storage::disk('public');
        try {
            $mtime = $disk->lastModified($path);
            $size = $disk->size($path);
        } catch (\Throwable $e) {
            $url = Storage::url($path);
            if ($url) {
                return redirect()->away($url, 302);
            }
            abort(404);
        }
        $etag = 'W/"'.dechex($size).'-'.dechex($mtime).'"';

        $ifNoneMatch = $request->headers->get('If-None-Match');
        $ifModifiedSince = $request->headers->get('If-Modified-Since');

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $contentType = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            default => 'application/octet-stream',
        };

        $headers = [
            'Cache-Control' => 'public, max-age=31536000, immutable',
            'ETag' => $etag,
            'Last-Modified' => gmdate('D, d M Y H:i:s', $mtime).' GMT',
            'Content-Type' => $contentType,
        ];

        if ($ifNoneMatch === $etag || $ifModifiedSince === $headers['Last-Modified']) {
            return response('', 304, $headers);
        }
        try {
            $stream = $disk->readStream($path);
            if ($stream !== false && $stream !== null) {
                return response()->stream(function () use ($stream) {
                    fpassthru($stream);
                    if (is_resource($stream)) {
                        fclose($stream);
                    }
                }, 200, $headers);
            }
        } catch (\Throwable $e) {
            // Fallback redirect to S3/CloudFront URL
        }
        $url = Storage::url($path);
        if ($url) {
            return redirect()->away($url, 302);
        }
        abort(404);
    }
}
