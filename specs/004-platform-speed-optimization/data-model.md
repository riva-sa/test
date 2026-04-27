# Data Model: Platform Speed Optimization

**Date**: 2026-04-27
**Feature**: 004-platform-speed-optimization

## Entities

### OptimizedImage (NEW)

Tracks optimized image variants for any image asset on the platform.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| original_path | string (500) | Path to the original image in storage (e.g., `project-media/photo.jpg`) |
| variant_type | string (20) | Size category: `thumbnail`, `medium`, `large` |
| variant_path | string (500) | Path to the optimized variant in storage |
| format | string (10) | Output format: `webp`, `avif`, `jpg` |
| width | integer | Width in pixels of the generated variant |
| height | integer | Height in pixels of the generated variant |
| file_size | integer | File size in bytes of the optimized variant |
| original_size | integer | File size in bytes of the original image |
| status | string (20) | Processing status: `pending`, `processing`, `completed`, `failed` |
| error_message | text (nullable) | Error details if processing failed |
| created_at | timestamp | When the record was created |
| updated_at | timestamp | When the record was last updated |

**Indexes**:
- `unique(original_path, variant_type, format)` — prevent duplicate variants
- `index(original_path)` — fast lookup when serving images
- `index(status)` — efficient batch processing queries

**Relationships**:
- Linked to images via `original_path` (matches `ProjectMedia.media_url`, `Developer.logo`, `Unit.floor_plan`, etc.)
- No foreign key to a specific model — path-based linking supports any image source

**State Transitions**:
```
pending → processing → completed
pending → processing → failed
failed → pending (retry)
```

---

### PerformanceMetric (NEW)

Records page load performance metrics for external-facing pages.

| Field | Type | Description |
|-------|------|-------------|
| id | bigint (PK) | Auto-increment primary key |
| page_url | string (500) | Full URL of the measured page |
| route_name | string (100) | Laravel route name (e.g., `frontend.home`) |
| metric_type | string (30) | Metric category: `server_response_time`, `memory_usage`, `query_count`, `query_time` |
| value | decimal (10,4) | Metric value (milliseconds for time, MB for memory, count for queries) |
| request_method | string (10) | HTTP method: `GET`, `POST` |
| user_agent | string (500, nullable) | Browser user agent string |
| created_at | timestamp | When the metric was recorded |

**Indexes**:
- `index(route_name, created_at)` — time-series queries per page
- `index(metric_type, created_at)` — aggregate queries per metric type
- `index(created_at)` — cleanup/retention queries

**Retention**: Records older than 30 days are automatically pruned via scheduled command.

---

## Modifications to Existing Entities

### ProjectMedia (EXISTING — no schema change)

No database changes needed. The `media_url` field already contains the storage path used to look up optimized variants via `OptimizedImage.original_path`.

### Developer (EXISTING — no schema change)

The `logo` field contains a storage path. Optimized variants will be looked up via `OptimizedImage.original_path` matching.

### Unit (EXISTING — no schema change)

The `floor_plan` field contains a storage path. Same lookup pattern applies.
