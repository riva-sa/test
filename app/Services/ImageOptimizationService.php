<?php

namespace App\Services;

use App\Models\OptimizedImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;

class ImageOptimizationService
{
    protected ImageManager $manager;

    public function __construct()
    {
        // Use GD driver by default, as configured for this environment
        $this->manager = new ImageManager(new Driver());
    }

    /**
     * Optimize an original image into all configured variants.
     */
    public function optimize(string $originalPath): array
    {
        $disk = Storage::disk('public');

        if (!$disk->exists($originalPath)) {
            Log::error('Image optimization failed: original not found', ['path' => $originalPath]);
            return [];
        }

        $config = config('image-optimization');
        $format = $config['format'] ?? 'webp';
        $variants = $config['variants'] ?? [];
        $prefix = $config['storage_prefix'] ?? 'optimized/';

        $extension = pathinfo($originalPath, PATHINFO_EXTENSION);
        if (!in_array(strtolower($extension), $config['supported_formats'] ?? [])) {
            Log::info('Image optimization skipped: unsupported format', ['path' => $originalPath, 'format' => $extension]);
            return [];
        }

        try {
            $imageData = $disk->get($originalPath);
            $image = $this->manager->decode($imageData);
            $originalWidth = $image->width();
            $originalHeight = $image->height();
            $originalSize = $disk->size($originalPath);

            $results = [];

            foreach ($variants as $type => $settings) {
                $targetWidth = $settings['width'];
                $quality = $settings['quality'] ?? 80;

                // Skip upscaling
                if ($originalWidth <= $targetWidth) {
                    continue;
                }

                // Prepare variant path
                $filename = pathinfo($originalPath, PATHINFO_FILENAME);
                $dir = pathinfo($originalPath, PATHINFO_DIRNAME);
                $variantPath = $prefix . ($dir === '.' ? '' : $dir . '/') . $filename . '-' . $type . '.' . $format;

                // Make sure directory exists
                $disk->makeDirectory(dirname($variantPath));

                // Clone and resize
                $variantImage = clone $image;
                $variantImage->scale(width: $targetWidth);
                
                $encoded = $variantImage->encode(new WebpEncoder(quality: $quality));
                $disk->put($variantPath, $encoded->toString());

                // Store metadata
                $record = OptimizedImage::updateOrCreate(
                    [
                        'original_path' => $originalPath,
                        'variant_type' => $type,
                        'format' => $format,
                    ],
                    [
                        'variant_path' => $variantPath,
                        'width' => $variantImage->width(),
                        'height' => $variantImage->height(),
                        'file_size' => $disk->size($variantPath),
                        'original_size' => $originalSize,
                        'status' => 'completed',
                        'error_message' => null,
                    ]
                );

                $results[] = $record;
            }

            Log::info('Image optimized successfully', ['path' => $originalPath, 'variants_created' => count($results)]);
            return $results;

        } catch (\Exception $e) {
            Log::error('Image optimization failed', ['path' => $originalPath, 'error' => $e->getMessage()]);
            
            OptimizedImage::updateOrCreate(
                [
                    'original_path' => $originalPath,
                    'variant_type' => 'all',
                    'format' => $format,
                ],
                [
                    'variant_path' => '',
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]
            );

            return [];
        }
    }

    /**
     * Check if variants exist for a path.
     */
    public function hasOptimizedVariants(string $originalPath): bool
    {
        return OptimizedImage::where('original_path', $originalPath)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get the best variant URL.
     */
    public function getOptimizedUrl(string $originalPath, ?string $size = null): string
    {
        $query = OptimizedImage::where('original_path', $originalPath)
            ->where('status', 'completed');
            
        if ($size) {
            $query->where('variant_type', $size);
        } else {
            // Default to largest available if not specified
            $query->orderBy('width', 'desc');
        }

        $variant = $query->first();

        $disk = Storage::disk('public');
        
        if ($variant && $disk->exists($variant->variant_path)) {
            return $disk->url($variant->variant_path);
        }

        // Fallback to original
        return $disk->url($originalPath);
    }
}
