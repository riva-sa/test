# Implementation Plan: Platform Speed Optimization

**Branch**: `004-platform-speed-optimization` | **Date**: 2026-04-27 | **Spec**: [spec.md](file:///Users/macbox/Documents/GitHub/test/specs/004-platform-speed-optimization/spec.md)
**Input**: Feature specification from `/specs/004-platform-speed-optimization/spec.md`

## Summary

Optimize external-facing page load speed and image rendering across the Riva CRM platform. The approach combines server-side image optimization (WebP conversion + responsive resizing via Intervention Image), query/cache improvements in Livewire frontend components, frontend `<img>` tag enhancements (native lazy loading, `srcset`, `width`/`height`), and built-in performance monitoring. The `#[Lazy]` Livewire attribute is explicitly excluded as it breaks interactive tools and actions.

## Technical Context

**Language/Version**: PHP 8.2, Laravel 11.x
**Primary Dependencies**: FilamentPHP 3.x, Livewire 3.x, Intervention Image v3 (NEW)
**Storage**: Local filesystem / S3 (configurable via `FILESYSTEM_DISK` env), MySQL
**Testing**: Pest PHP
**Target Platform**: Web (Hostinger VPS / Linux server)
**Project Type**: Web application (CRM + public-facing real estate site)
**Performance Goals**: Pages < 2s load, above-fold images < 1s, 40%+ image payload reduction
**Constraints**: No `#[Lazy]` attribute, max 4 image variants per image, built-in monitoring (no external APM)
**Scale/Scope**: ~20 projects, ~200 units, ~500 images, 10 external-facing pages

## Constitution Check

*GATE: Must pass before Phase 1 research. Re-check after Phase 2 design.*

- [x] **Filament-First**: This feature targets external Livewire frontend pages, not Filament admin panels. No Filament components are modified. ✅ No violation.
- [x] **Decoupled Logic**: Image optimization logic is encapsulated in `ImageOptimizationService` (Service class). Performance monitoring is in middleware. No logic in controllers or Livewire components directly. ✅ Compliant.
- [x] **Testing Discipline**: Plan includes Pest tests for the ImageOptimizationService, MediaHelper, OptimizeImageJob, and PerformanceMonitorMiddleware. ✅ Compliant.
- [x] **i18n Readiness**: No new user-facing strings are introduced. Log messages and Artisan command outputs use English (not user-facing). ✅ Compliant.
- [x] **Observability**: Image processing events (success, failure, batch completion) are logged with structured JSON context via Laravel Log facade. Performance metrics are stored in database with entity IDs and timestamps. ✅ Compliant.

## Project Structure

### Documentation (this feature)

```text
specs/004-platform-speed-optimization/
├── plan.md              # This file
├── spec.md              # Feature specification
├── research.md          # Phase 0 research output
├── data-model.md        # Phase 1 data model
├── quickstart.md        # Phase 1 quickstart guide
├── contracts/           # Phase 1 contracts
│   └── image-serving.md # MediaHelper + ImageController + Service contracts
├── checklists/
│   └── requirements.md  # Spec quality checklist
└── tasks.md             # Phase 2 output (via /speckit.tasks)
```

### Source Code (repository root)

```text
app/
├── Console/Commands/
│   ├── OptimizeImagesCommand.php      # NEW — batch image optimization
│   ├── ImageOptimizationStatusCommand.php  # NEW — processing status report
│   └── PerformanceReportCommand.php   # NEW — performance metrics report
├── Helpers/
│   └── MediaHelper.php                # MODIFY — serve optimized variants
├── Http/Controllers/
│   └── ImageController.php            # MODIFY — WebP-aware redirects with cache headers
├── Http/Middleware/
│   └── PerformanceMonitorMiddleware.php  # NEW — record page load metrics
├── Jobs/
│   └── OptimizeImageJob.php           # NEW — queued single-image processing
├── Models/
│   ├── OptimizedImage.php             # NEW — optimized variant metadata
│   └── PerformanceMetric.php          # NEW — page performance records
├── Services/
│   └── ImageOptimizationService.php   # NEW — core optimization logic
└── Livewire/Frontend/
    ├── ProjectsPage.php               # MODIFY — query optimization
    ├── ProjectSingle.php              # MODIFY — query optimization
    ├── Conponents/
    │   ├── ProjectSlider.php          # MODIFY — query optimization
    │   └── ProjectsTab.php            # MODIFY — query optimization
    └── Partials/
        └── NavBar.php                 # INSPECT — static asset optimization

config/
└── image-optimization.php             # NEW — variant sizes, quality, format config

database/migrations/
├── XXXX_XX_XX_create_optimized_images_table.php    # NEW
└── XXXX_XX_XX_create_performance_metrics_table.php # NEW

resources/views/livewire/frontend/
├── home-page.blade.php                # MODIFY — add width/height, srcset
├── projects-page.blade.php            # MODIFY — add width/height, srcset, sizes
├── project-single.blade.php           # MODIFY — add width/height, srcset, picture elements
├── projects-map.blade.php             # MODIFY — add width/height, srcset
├── conponents/
│   ├── project-slider.blade.php       # MODIFY — add srcset, sizes
│   ├── projects-tab.blade.php         # MODIFY — add srcset, sizes
│   └── unit-popup.blade.php           # MODIFY — add width/height
└── partials/
    ├── nav-bar.blade.php              # MODIFY — add width/height to static images
    └── footer.blade.php               # MODIFY — add width/height to logo

tests/
├── Unit/
│   ├── ImageOptimizationServiceTest.php  # NEW
│   └── MediaHelperTest.php               # NEW
├── Feature/
│   ├── OptimizeImagesCommandTest.php     # NEW
│   ├── OptimizeImageJobTest.php          # NEW
│   └── PerformanceMonitorMiddlewareTest.php # NEW
└── Integration/
    └── ImageServingTest.php              # NEW — end-to-end image optimization flow
```

**Structure Decision**: This feature adds new services, jobs, commands, and middleware within the existing Laravel monolith structure. No new packages or sub-projects are needed. The changes follow the existing `app/` directory conventions.

## Detailed Implementation

### Component 1: Image Optimization Pipeline

**Goal**: Server-side image conversion (WebP) and responsive resizing.

#### [NEW] `config/image-optimization.php`
Configuration file defining:
- Variant definitions: `thumbnail` (400w, 80%), `medium` (800w, 85%), `large` (1200w, 85%)
- Output format: `webp`
- Storage path prefix: `optimized/`
- Max variants per image: 4 (including original)
- Batch size for Artisan command: 50
- Supported input formats: `jpg`, `jpeg`, `png`, `gif`, `bmp`

#### [NEW] `app/Services/ImageOptimizationService.php`
Core service using Intervention Image v3:
- `optimize(string $originalPath): array` — generates all configured variants
- `hasOptimizedVariants(string $originalPath): bool`
- `getOptimizedUrl(string $originalPath, ?string $size): string`
- Handles: format detection, dimension checking (skip upscaling), error handling for corrupted files
- Stores variant metadata in `optimized_images` table
- Logs all operations with structured JSON context

#### [NEW] `app/Jobs/OptimizeImageJob.php`
Queued job dispatched on image upload:
- Queue: `image-optimization`
- Calls `ImageOptimizationService::optimize()`
- Retries: 3 times with exponential backoff
- Handles failures gracefully (logs error, marks status as `failed`)

#### [NEW] `app/Console/Commands/OptimizeImagesCommand.php`
Artisan command for batch retroactive processing:
- Signature: `images:optimize {--batch=50} {--model=all}`
- Scans `ProjectMedia`, `Developer` (logos), `Unit` (floor plans) for unprocessed images
- Processes in batches to avoid memory issues
- Progress bar output
- Structured logging for each batch

#### [NEW] `app/Console/Commands/ImageOptimizationStatusCommand.php`
Signature: `images:status` — reports total/pending/completed/failed counts.

---

### Component 2: Enhanced Image Serving

**Goal**: Serve optimized variants transparently with backward compatibility.

#### [MODIFY] `app/Helpers/MediaHelper.php`
Enhanced `getUrl()` method:
- New optional `$size` parameter
- Checks `OptimizedImage` table for WebP variant
- Falls back to original if no variant exists
- Caches variant lookups in-memory to avoid repeated DB queries within a single request
- 100% backward compatible — existing calls without `$size` still work

#### [MODIFY] `app/Http/Controllers/ImageController.php`
Enhanced `show()` method:
- Checks `Accept` header for WebP support
- Looks up optimized variant via `ImageOptimizationService`
- Adds `Cache-Control: public, max-age=31536000, immutable` header
- Adds `Vary: Accept` header for proper CDN caching
- Falls back to original URL if no variant available

---

### Component 3: Frontend Template Optimization

**Goal**: Add proper `<img>` attributes for browser-level performance.

#### [MODIFY] All frontend Blade templates
For each `<img>` tag:
- Add explicit `width` and `height` attributes (prevents layout shift)
- Add `loading="lazy"` for below-fold images (native HTML lazy, NOT Livewire `#[Lazy]`)
- Add `decoding="async"` for non-critical images
- Add `fetchpriority="high"` for above-fold hero images
- Add responsive `srcset` using MediaHelper with size variants
- Add `sizes` attribute matching the layout breakpoints
- Use `<picture>` element with WebP source and JPEG fallback where appropriate

Key files:
- `home-page.blade.php` — hero slider images (fetchpriority=high), feature icons (width/height)
- `projects-page.blade.php` — project cards (srcset with thumbnail/medium), unit cards
- `project-single.blade.php` — gallery images (srcset with medium/large), developer logos, feature icons
- `projects-map.blade.php` — map popup images (srcset)
- `project-slider.blade.php` — slider images (data-image-src with optimized URL)
- `nav-bar.blade.php` — static logo/icon images (width/height only)
- `footer.blade.php` — logo (width/height)

---

### Component 4: Query & Cache Optimization

**Goal**: Reduce server response time for data-heavy pages.

#### [MODIFY] `app/Livewire/Frontend/ProjectsPage.php`
- Reduce eager-loaded relations to only what's displayed per view type
- Use `select()` to limit columns on eager loads
- Increase cache TTL for static reference data (developers, project types) from 3600s to 86400s (24h)
- Add cache warming on data change events

#### [MODIFY] `app/Livewire/Frontend/ProjectSingle.php`
- Already well-cached. Minor: ensure `projectMedia` eager load only fetches image types for gallery

#### [MODIFY] `app/Livewire/Frontend/Conponents/ProjectSlider.php`
- Already well-cached. Minor: limit `projectMedia` to main images only

---

### Component 5: Performance Monitoring

**Goal**: Built-in tracking of page load metrics with regression alerting.

#### [NEW] `app/Http/Middleware/PerformanceMonitorMiddleware.php`
- Records: server response time, memory usage, query count, total query time
- Only monitors routes with `frontend.*` name prefix (external pages)
- Samples at configurable rate (default: 100% initially, reducible to 10% for production)
- Writes to `performance_metrics` table via queued insert (non-blocking)

#### [NEW] `app/Models/OptimizedImage.php`
Eloquent model for the `optimized_images` table with scopes: `completed()`, `failed()`, `pending()`, `forPath()`.

#### [NEW] `app/Models/PerformanceMetric.php`
Eloquent model for the `performance_metrics` table with scopes: `forRoute()`, `forMetric()`, `recent()`.

#### [NEW] `app/Console/Commands/PerformanceReportCommand.php`
Signature: `performance:report {--days=7} {--route=}`
- Outputs average/p95/max for each metric per route
- Highlights regressions (>20% degradation from baseline)
- Structured JSON output option for automated alerting

#### [NEW] Database migrations
- `create_optimized_images_table` — per data-model.md schema
- `create_performance_metrics_table` — per data-model.md schema

---

### Component 6: Registration & Wiring

#### [MODIFY] `app/Providers/AppServiceProvider.php`
- Register `ImageOptimizationService` as singleton
- Register `PerformanceMonitorMiddleware` on frontend route group

#### [MODIFY] `routes/web.php`
- Apply `PerformanceMonitorMiddleware` to external-facing routes (homepage, projects, project single, blog, about, services, contact, etc.)

#### [MODIFY] `app/Console/Kernel.php` (or `bootstrap/app.php` for Laravel 11)
- Schedule `performance_metrics` pruning (daily, retain 30 days)
- Schedule `images:optimize --batch=50` for off-peak processing (optional, can be run manually)

## Constitution Check (Post-Design)

- [x] **Filament-First**: No Filament panels are modified. External Livewire pages optimized. ✅
- [x] **Decoupled Logic**: `ImageOptimizationService` is a standalone service. `OptimizeImageJob` delegates to service. MediaHelper is a helper. No logic in controllers/Livewire. ✅
- [x] **Testing Discipline**: 6 test files planned covering unit, feature, and integration layers. ✅
- [x] **i18n Readiness**: No new user-facing strings. Artisan output is developer-facing. ✅
- [x] **Observability**: Structured JSON logging for image processing events. Performance metrics stored in DB. ✅

## Complexity Tracking

No constitution violations to justify. All patterns used are standard Laravel conventions (Services, Jobs, Middleware, Artisan Commands).
