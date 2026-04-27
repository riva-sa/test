# Contract: Image Serving (MediaHelper)

**Date**: 2026-04-27
**Feature**: 004-platform-speed-optimization

## Overview

The `MediaHelper::getUrl()` method is the single entry point for resolving image URLs across all frontend Blade templates. This contract defines the enhanced behavior that serves optimized image variants when available.

## Current Behavior

```
MediaHelper::getUrl(string|null $path) → string
```

- If `$path` is null/empty → returns placeholder URL
- Otherwise → returns `Storage::disk('public')->url($path)`

## Enhanced Behavior

```
MediaHelper::getUrl(string|null $path, ?string $size = null) → string
```

### Parameters

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `$path` | `string\|null` | required | Storage path to the original image |
| `$size` | `string\|null` | `null` | Desired variant: `thumbnail` (400w), `medium` (800w), `large` (1200w), or `null` (auto-detect best) |

### Return Value

Returns the public URL to serve. Resolution order:
1. If `$path` is null/empty → placeholder URL (unchanged)
2. If a WebP variant exists for the requested size → WebP variant URL
3. If no variant exists → original image URL (graceful fallback)

### Usage in Blade Templates

```blade
{{-- Current (still works — backward compatible) --}}
<img src="{{ App\Helpers\MediaHelper::getUrl($media->media_url) }}">

{{-- New — request specific size --}}
<img src="{{ App\Helpers\MediaHelper::getUrl($media->media_url, 'thumbnail') }}">

{{-- New — responsive with srcset --}}
<img src="{{ App\Helpers\MediaHelper::getUrl($media->media_url, 'medium') }}"
     srcset="{{ App\Helpers\MediaHelper::getUrl($media->media_url, 'thumbnail') }} 400w,
             {{ App\Helpers\MediaHelper::getUrl($media->media_url, 'medium') }} 800w,
             {{ App\Helpers\MediaHelper::getUrl($media->media_url, 'large') }} 1200w"
     sizes="(max-width: 600px) 400px, (max-width: 1024px) 800px, 1200px">
```

---

## Contract: ImageController (Media Route)

### Current Behavior

```
GET /media/{path} → 302 redirect to Storage URL
```

### Enhanced Behavior

```
GET /media/{path} → 302 redirect to optimized Storage URL (if available)
```

The controller checks for an optimized WebP variant based on `Accept` header:
- If browser sends `Accept: image/webp` and WebP variant exists → redirect to WebP URL
- Otherwise → redirect to original URL (unchanged behavior)

Response headers include:
- `Cache-Control: public, max-age=31536000, immutable` (1 year cache for optimized images)
- `Vary: Accept` (to enable CDN/proxy caching per format)

---

## Contract: ImageOptimizationService

### Interface

```php
interface ImageOptimizationServiceContract
{
    /**
     * Optimize a single image, generating all configured variants.
     * Returns array of created OptimizedImage records.
     */
    public function optimize(string $originalPath): array;

    /**
     * Check if optimized variants exist for a given path.
     */
    public function hasOptimizedVariants(string $originalPath): bool;

    /**
     * Get the URL for the best available variant.
     * Falls back to original if no variant exists.
     */
    public function getOptimizedUrl(string $originalPath, ?string $size = null): string;
}
```

### Variant Configuration

| Variant | Width | Quality | Format |
|---------|-------|---------|--------|
| thumbnail | 400px | 80% | WebP |
| medium | 800px | 85% | WebP |
| large | 1200px | 85% | WebP |

Images smaller than the target width are not upscaled — the original is used.
