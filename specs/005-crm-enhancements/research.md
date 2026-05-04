# Research: CRM Enhancements & Additions

**Feature**: 005-crm-enhancements | **Date**: 2026-04-30

---

## 1. Real-Time Notification Delivery

- **Decision**: Laravel Reverb + Laravel Echo + Livewire event listeners.
- **Rationale**: Laravel 11 ships Reverb as a first-party WebSocket server. It integrates natively with Echo and supports the Pusher protocol. The project already uses Livewire 3 and Redis (cache driver), making Reverb the natural fit. Current broadcasting is set to `log` (`BROADCAST_CONNECTION=log` in `.env`) — must be switched to `reverb`.
- **Alternatives considered**:
  - Pusher (third-party cost, unnecessary given Reverb availability)
  - Livewire polling (`wire:poll.5s`) — simpler but does not satisfy "real-time push without page refresh" requirement; increases server load linearly with connected users
- **Implementation notes**:
  - Install `laravel/reverb` via Composer
  - Install `laravel-echo` + `pusher-js` via npm (Echo uses Pusher protocol for Reverb)
  - Configure `.env`: `BROADCAST_CONNECTION=reverb`, `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`
  - Run `php artisan reverb:start` as a daemon (Supervisor or systemd)
  - Create private channels per user: `notifications.{userId}`
  - Broadcast `NewCrmNotification` event when notification is created
  - Livewire components listen via `echo-private:notifications.{userId}` for live bell count updates

## 2. Rich Text Editor for Notifications

- **Decision**: Trix editor integrated via Alpine.js in Livewire CRM components.
- **Rationale**: Trix ships with Laravel (via `laravel/ui` or standalone JS). It provides bold, links, lists, headings — matching the spec requirement. It produces clean HTML, works well with Alpine.js bindings (`x-data`, `x-on:trix-change`), and is lightweight. The Filament RichEditor is available but tightly coupled to Filament form context; the CRM panel uses custom Livewire components, not Filament forms.
- **Alternatives considered**:
  - Filament RichEditor (requires Filament form wrapper in custom Livewire — adds complexity)
  - Quill/TinyMCE (heavier, requires CDN or npm, more config)
- **Implementation notes**:
  - Include Trix CSS/JS via Vite bundle or CDN
  - Store notification content as sanitized HTML in `content` column (TEXT type)
  - Render in notification panel/announcements with `{!! $notification->content !!}` after sanitization
  - Sanitize on save with HTMLPurifier or `strip_tags` with allowed tags

## 3. Status Color Centralization

- **Decision**: Centralize all status colors as hex codes in a config constant on the `UnitOrder` model, replacing the current Tailwind color name mapping.
- **Rationale**: The current `statusColor()` method returns Tailwind names (`blue`, `green`, `yellow`, `red`, `emerald`, `amber`). The spec requires specific hex codes (e.g., `#3B82F6` for جديد). Centralizing in one constant array ensures consistency across all 6+ pages that display statuses (order list, dashboard charts, board view, customer profile, order details, sales rep columns).
- **Alternatives considered**:
  - Tailwind CSS custom colors in `tailwind.config.js` (only works in Tailwind-rendered contexts, not charts/Canvas)
  - Database-driven config (overkill for 6 static values)
- **Implementation notes**:
  - Add `STATUS_COLORS` constant to `UnitOrder`:
    ```php
    const STATUS_COLORS = [
        0 => '#3B82F6', // جديد - blue
        1 => '#F97316', // طلب مفتوح - orange
        2 => '#5457E3', // معاملات بيعية - purple
        3 => '#9CA3AF', // مغلق - gray
        4 => '#22C55E', // مكتمل - green
        5 => '#EAB308', // قائمة انتظار - yellow
    ];
    ```
  - Update `statusColor()` to return hex codes
  - Update all Blade views using `statusColor()` to use inline `style="color: ..."` or `style="background-color: ..."`
  - Update dashboard chart config (UnitOrderStats widget) to pass hex colors

## 4. Sales Target Tracking Architecture

- **Decision**: Log every order status change in an `order_status_transitions` table. Compute target progress from this log on-demand. No cached counter table.
- **Rationale**: The spec requires (1) real-time target updates on status transitions, (2) historical performance recomputed from order transition history. A transition log is the single source of truth for both current and historical computation. At CRM scale (~100 reps, ~1000 orders/day), querying the log is fast with proper indexes. A separate counter cache adds write complexity (double-write) and staleness risk.
- **Alternatives considered**:
  - Counter cache table (TargetProgress) incremented by observer — faster reads but double-write complexity, staleness risk on missed events
  - Dynamic query from `unit_orders` with date filters — no transition history, can't track who performed the transition
- **Implementation notes**:
  - `order_status_transitions` table: `id, unit_order_id, user_id, from_status, to_status, created_at`
  - Insert via `UnitOrderObserver::updating()` when `status` field changes
  - `SalesTarget` table stores configurable target values per rep per type
  - Progress queries: `SELECT COUNT(*) FROM order_status_transitions WHERE user_id = ? AND from_status = 0 AND created_at BETWEEN ? AND ?`
  - Index on `(user_id, from_status, created_at)` for efficient queries

## 5. Leaderboard Weighted Composite Ranking

- **Decision**: Store weight configuration in a `leaderboard_configs` table. Compute composite scores on-the-fly.
- **Rationale**: Manager-configurable weights require persistence. Computing scores at query time (not stored) avoids staleness and ensures real-time accuracy. At the scale of a sales team (~10-50 reps), this computation is trivial.
- **Implementation notes**:
  - Default weights: monthly_orders=25%, daily_orders=25%, reservations=25%, sales=25%
  - Composite score = Σ (weight_i × (progress_i / target_i)) for each of 4 target types
  - If target_i is 0 or not set, that type contributes 0 to the score
  - Display as percentage and rank

## 6. Project Manager Removal & Grandfather Clause

- **Decision**: Keep `sales_manager_id` on `Project` model. Remove project manager from `scopeAccessibleBy()` for new order visibility logic. Existing orders remain accessible via legacy path.
- **Rationale**: The `scopeAccessibleBy()` method in `UnitOrder.php` currently gives `project_manager` role access to all orders. For new orders, this path should be removed. For existing orders, `project.sales_manager_id` still provides access via the join condition in the scope.
- **Implementation notes**:
  - Modify `scopeAccessibleBy()`: remove `project_manager` from the "see all" role list
  - Add a date-based condition: orders created AFTER the change date follow new rules
  - Keep `sales_manager_id` column and `salesManager()` relationship for historical access
  - Hide "project manager" from any assignment UI dropdowns

## 7. Project Contact Number

- **Decision**: Add `contact_phone` (string, nullable) to the `projects` table.
- **Rationale**: Simple column addition. The project-single Blade view already uses `$project->salesManager->phone` for WhatsApp/call buttons — update to use `$project->contact_phone` instead, with fallback to `setting('site_phone')`.
- **Implementation notes**:
  - Migration: `$table->string('contact_phone')->nullable()->after('sales_manager_id')`
  - Update ProjectResource (Filament) to include contact_phone in the form
  - Update `project-single.blade.php` to use `$project->contact_phone`

## 8. Password Reset for CRM Users

- **Decision**: Use Laravel's built-in `Password::broker()->sendResetLink()` triggered from the SalesManagers component.
- **Rationale**: The existing ManagerAuthController only has login/logout. Filament panels already use `->passwordReset()`. For CRM users, we can use Laravel's password reset broker with the existing SMTP config (`crm@riva.sa` via Hostinger). The reset flow: manager clicks button → system sends email → employee clicks link → sets new password.
- **Implementation notes**:
  - Add "Reset Password" button to each row in `SalesManagers.php`
  - Call `Password::broker()->sendResetLink(['email' => $user->email])`
  - Create a Livewire component for the password reset form (or reuse Filament's route)
  - Add routes: `GET /crm/reset-password/{token}` → reset form, `POST /crm/reset-password` → process

## 9. YouTube Video Embedding

- **Decision**: Extract YouTube video ID via regex in a model accessor and render via iframe in the project-single view.
- **Rationale**: The `youtube_url` field already exists on `ProjectMedia` model (migration `2025_02_02_030116`). The field is populated but never rendered in the frontend. The Filament ProjectResource has the field commented out — uncomment it.
- **Implementation notes**:
  - Add `getYoutubeEmbedUrlAttribute()` accessor to `ProjectMedia`:
    ```php
    preg_match('/(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches);
    return $matches[1] ? "https://www.youtube.com/embed/{$matches[1]}" : null;
    ```
  - Add iframe section to `project-single.blade.php` after the image gallery
  - Handle null/invalid URLs gracefully (show placeholder)

## 10. Ad Set Display (Order Source)

- **Decision**: Already partially implemented per recent commit `365067a2` ("refactor: update order display to show ad_set instead of campaign_name"). Verify completeness across all pages.
- **Rationale**: The `ad_set` field exists on `UnitOrder` and is populated by `IngestSocialMediaLead` action. The recent commit updated the order display. Need to verify: orders list, order details, customer profile, and any analytics/export views.
- **Implementation notes**:
  - Audit all views that reference `campaign_name` or `ad_name` and switch to `ad_set`
  - Update column headers to Arabic: "المجموعة الاعلانية"
  - Ensure filters/sorting work on the `ad_set` field

## 11. CRM Notification System (In-App)

- **Decision**: Build a separate CRM notification system (`crm_notifications` table) distinct from Laravel's built-in notifications table.
- **Rationale**: The existing `notifications` table (UUID-based, Laravel default) is used for system-triggered notifications (order updates, lead ingestion). The new CRM notification system is manager-initiated (individual, group, announcement, task) with rich text content, explicit recipient tracking, and a dedicated UI. Mixing these in one table would complicate querying and role-based filtering.
- **Implementation notes**:
  - `crm_notifications` table: id, type, sender_id, title, content (HTML), created_at, updated_at
  - `crm_notification_recipients` table: id, notification_id, user_id, read_at, created_at
  - For "group" type: insert one recipient row per sales rep
  - For "announcement" type: insert one recipient row per CRM user
  - Broadcast event on creation for real-time bell update
