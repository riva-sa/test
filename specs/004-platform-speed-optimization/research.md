# Research: Platform Speed Optimization

**Date**: 2026-04-27
**Feature**: 004-platform-speed-optimization

## R1: Image Optimization Approach for Laravel + Livewire

### Decision
Use **Intervention Image v3** (PHP GD/Imagick) for server-side image processing with an Artisan command for batch retroactive optimization, and modify `MediaHelper::getUrl()` to serve optimized variants when available.

### Rationale
- Intervention Image is the de facto standard for PHP/Laravel image manipulation
- No external services needed — runs on the same server
- Supports WebP output natively via GD or Imagick drivers
- Can generate responsive size variants (thumbnail, medium, large) from originals
- Integrates cleanly with Laravel's filesystem/Storage API (local + S3)

### Alternatives Considered
- **Spatie Media Library**: More opinionated, would require model refactoring across `ProjectMedia`, `Developer`, `Unit`. Too invasive for a speed optimization feature.
- **Glide (league/glide)**: On-demand image manipulation via URL. Excellent for CDN setups but adds per-request processing overhead on first load. Doesn't align with the hybrid batch approach chosen.
- **External CDN (Cloudflare Images, imgix)**: Best performance but adds external dependency and cost. Out of scope per spec assumptions.

---

## R2: Page Speed Optimization Without #[Lazy]

### Decision
Use a combination of:
1. **Query optimization** — reduce N+1 queries, eager load selectively
2. **Expanded caching** — cache rendered partials and computed data
3. **Frontend optimizations** — proper `loading="lazy"` on `<img>` tags (native HTML lazy loading, NOT Livewire `#[Lazy]`), `srcset` with responsive sizes, proper `width`/`height` attributes
4. **Asset optimization** — minify/combine CSS/JS, defer non-critical scripts

### Rationale
- `#[Lazy]` (Livewire attribute) defers component rendering which breaks interactive tools/actions — explicitly forbidden by the spec
- Native HTML `loading="lazy"` on `<img>` tags is safe and does NOT affect interactivity — it only defers image download until near viewport
- Query optimization and caching are the most impactful server-side improvements
- Frontend `<img>` attributes (`loading`, `decoding`, `fetchpriority`, `srcset`) are zero-cost improvements

### Alternatives Considered
- **Turbo/Hotwire**: Would require significant frontend rewrite. Not appropriate for a speed optimization feature.
- **Server-Side Rendering cache (Full-page cache)**: Risky with Livewire's stateful components. Better to cache at the data layer.

---

## R3: Image Format Selection

### Decision
Generate **WebP** as the primary optimized format. AVIF is deferred for future consideration.

### Rationale
- WebP has 95%+ browser support (all modern browsers)
- WebP provides 25-34% smaller files than JPEG at equivalent quality
- GD driver (default in most PHP installations) supports WebP natively
- AVIF requires Imagick with libavif, which is not available on most shared/VPS hosting (Hostinger)
- Serving WebP via `<picture>` element with JPEG fallback ensures universal compatibility

### Alternatives Considered
- **AVIF**: Better compression than WebP but limited server-side support. Can be added later.
- **WebP only (no fallback)**: Risky for older browsers. `<picture>` element with fallback is safer.

---

## R4: Responsive Image Size Variants

### Decision
Generate **3 size variants** per image (plus retain original = 4 total, matching the max-4 spec constraint):
1. **Thumbnail**: 400px wide — for project cards/listings
2. **Medium**: 800px wide — for project single page gallery
3. **Large**: 1200px wide — for full-screen/hero displays

All variants in WebP format. Original retained in its uploaded format.

### Rationale
- 3 variants + original = 4 total (within spec FR-013 limit)
- Sizes match actual usage in blade templates: cards show ~200px height images, single pages show ~550px, hero sliders show full width
- Width-based resizing maintains aspect ratio automatically

---

## R5: Performance Monitoring Approach

### Decision
Use a **lightweight middleware** that records page load metrics (server response time, memory usage) to a `performance_metrics` table, with a simple monitoring dashboard or Artisan command for reporting.

### Rationale
- Built-in monitoring per spec requirement (FR-014)
- Middleware approach captures all page requests without modifying individual components
- Storing to database allows historical comparison and threshold alerting
- Artisan command for reporting avoids building a full dashboard initially

### Alternatives Considered
- **Laravel Telescope**: Development tool, not production monitoring. Heavy overhead.
- **Custom Log Parsing**: Fragile, harder to query and alert on.
- **Third-party APM (New Relic, Datadog)**: External dependency, violates spec assumption of built-in monitoring.

---

## R6: Batch Processing Strategy (Hybrid Approach)

### Decision
1. **New uploads**: Optimize on upload via a queued job (immediate processing in background)
2. **Existing images**: Artisan command `php artisan images:optimize` processes all existing images in batches
3. **Serving logic**: `MediaHelper::getUrl()` checks for WebP variant → serves it if exists → falls back to original

### Rationale
- Queued jobs for new uploads ensure no user-facing delay
- Artisan command for batch processing can be run during off-peak hours
- Fallback logic in MediaHelper ensures graceful degradation — no broken images during transition
- Aligns with hybrid approach from spec clarification (Q2)

### Alternatives Considered
- **On-demand processing**: Adds latency on first request. Rejected per clarification.
- **Cron-based processing**: Less control than Artisan command. Can be added as a scheduled task later.
