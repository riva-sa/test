# Quickstart: Platform Speed Optimization

**Date**: 2026-04-27
**Feature**: 004-platform-speed-optimization

## Prerequisites

- PHP 8.2+ with GD extension (WebP support)
- Laravel 11.x environment
- Composer
- Queue worker running (for background image processing)

## Setup Steps

### 1. Install Intervention Image

```bash
composer require intervention/image
```

### 2. Run Migrations

```bash
php artisan migrate
```

This creates:
- `optimized_images` — stores metadata for optimized image variants
- `performance_metrics` — stores page load performance measurements

### 3. Process Existing Images

```bash
# Process all existing images in batches of 50
php artisan images:optimize --batch=50

# Process only images for a specific model
php artisan images:optimize --model=project-media

# Check processing status
php artisan images:status
```

### 4. Verify Optimization

```bash
# Check performance metrics
php artisan performance:report

# Run a quick page speed check
php artisan performance:check --route=frontend.home
```

### 5. Configure Queue Worker

Ensure a queue worker is running for background image processing on new uploads:

```bash
php artisan queue:work --queue=image-optimization
```

## Key Files

| File | Purpose |
|------|---------|
| `app/Helpers/MediaHelper.php` | Modified — serves optimized variants with fallback |
| `app/Services/ImageOptimizationService.php` | NEW — handles image conversion and resizing |
| `app/Jobs/OptimizeImageJob.php` | NEW — queued job for processing individual images |
| `app/Console/Commands/OptimizeImagesCommand.php` | NEW — batch processing Artisan command |
| `app/Http/Middleware/PerformanceMonitorMiddleware.php` | NEW — records page load metrics |
| `app/Console/Commands/PerformanceReportCommand.php` | NEW — performance reporting |

## Verification

After setup, visit the homepage and check:
1. Images load in WebP format (check Network tab in browser DevTools)
2. Page load time is under 2 seconds
3. All interactive elements (buttons, modals, forms) work correctly
