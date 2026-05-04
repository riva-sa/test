# Implementation Plan: CRM Enhancements & Additions

**Branch**: `005-crm-enhancements` | **Date**: 2026-04-30 | **Spec**: `specs/005-crm-enhancements/spec.md`  
**Input**: Feature specification from `/specs/005-crm-enhancements/spec.md`

## Summary

Implement 10 CRM enhancements: unified status colors (hex codes), enhanced customer profile, in-app notification system with real-time push (Reverb), sales targets with leaderboard, sales rep status columns, project manager removal (grandfather clause), project contact number, ad set display, YouTube embedding fix, and employee password reset. All features are internal CRM changes using Livewire components for the manager panel and Filament for admin configuration.

## Technical Context

**Language/Version**: PHP 8.2 / Laravel 11.x  
**Primary Dependencies**: Filament 3.x, Livewire 3.x, TailwindCSS 3.x, Spatie Permission, Laravel Reverb (new), Laravel Echo (new)  
**Storage**: MySQL 8.x (database: `riva_new`), Redis (cache)  
**Testing**: Pest 3.x (pest-plugin-laravel, pest-plugin-livewire, pest-plugin-faker)  
**Target Platform**: Web (LAMP/LEMP server)  
**Project Type**: Full-stack CRM web application  
**Performance Goals**: 2s page loads (SC-002), 5s notification delivery (SC-003), 3s target updates (SC-005)  
**Constraints**: Arabic-first RTL UI, Livewire CRM panel (16 existing components), Filament admin panel  
**Scale/Scope**: ~23 models, 16 manager components, 18+ CRM routes, ~5-50 concurrent CRM users

## Constitution Check

*GATE: Must pass before Phase 1 research. Re-check after Phase 2 design.*

- [x] **Filament-First**: Filament used for admin project/user configuration. CRM manager panel uses Livewire — this is established architecture (16 existing components in `app/Livewire/Mannager/`). New CRM pages follow the same Livewire pattern. See Complexity Tracking for justification.
- [x] **Decoupled Logic**: All business logic in dedicated services: `CrmNotificationService` (notification CRUD + broadcasting), `TargetTrackingService` (progress computation + leaderboard), `PasswordResetAction` (reset trigger). Observer handles status transition logging. No logic in controllers or components.
- [x] **Testing Discipline**: Pest tests planned for: notification send/read/broadcast, target progress computation, status color consistency, leaderboard ranking, password reset flow, YouTube URL parsing. Livewire component tests for all new components.
- [x] **i18n Readiness**: All new strings via `__()` translation keys. New keys added to `lang/ar/` and `lang/en/`. Arabic column headers for status counts, target labels, notification UI. RTL layout verified for all new views.
- [x] **Observability**: Structured JSON logging for: order status transitions (with order_id, from/to status, user_id), notification sends (with type, sender_id, recipient_count), password reset triggers (with target_user_id, triggered_by). Via Laravel Log facade.

## Project Structure

### Documentation (this feature)

```text
specs/005-crm-enhancements/
├── plan.md              # This file
├── spec.md              # Feature specification
├── research.md          # Phase 0: technology decisions
├── data-model.md        # Phase 1: database schema & entity design
├── quickstart.md        # Phase 1: developer setup guide
├── contracts/           # Phase 1: internal event/service contracts
│   └── internal-events.md
├── tasks.md             # Phase 2: implementation tasks
└── checklists/          # Pre-existing
```

### Source Code (repository root)

```text
app/
├── Actions/
│   └── ResetEmployeePasswordAction.php     # NEW: password reset logic
├── Events/
│   └── NewCrmNotification.php              # NEW: broadcast event
├── Livewire/
│   └── Mannager/
│       ├── ManagerDashboard.php            # MODIFIED: notification bell, target widgets
│       ├── CustomerProfile.php             # MODIFIED: enhanced order details display
│       ├── SalesManagers.php               # MODIFIED: status columns, password reset button
│       ├── Notifications.php               # NEW: send/manage notifications page
│       ├── Announcements.php               # NEW: announcements list page
│       ├── SalesTargets.php                # NEW: target management page
│       ├── Leaderboard.php                 # NEW: leaderboard + historical view
│       └── NotificationBell.php            # NEW: header bell component (real-time)
├── Models/
│   ├── CrmNotification.php                 # NEW
│   ├── CrmNotificationRecipient.php        # NEW
│   ├── OrderStatusTransition.php           # NEW
│   ├── SalesTarget.php                     # NEW
│   ├── LeaderboardConfig.php               # NEW
│   ├── UnitOrder.php                       # MODIFIED: STATUS_COLORS constant, statusColor()
│   ├── Project.php                         # MODIFIED: contact_phone fillable
│   ├── ProjectMedia.php                    # MODIFIED: youtubeEmbedUrl accessor
│   └── User.php                            # MODIFIED: new relationships
├── Observers/
│   └── UnitOrderObserver.php               # MODIFIED: log status transitions
├── Services/
│   ├── CrmNotificationService.php          # NEW: notification CRUD + broadcast
│   └── TargetTrackingService.php           # NEW: progress queries + leaderboard
└── Filament/
    └── Resources/
        └── ProjectResource.php             # MODIFIED: contact_phone field, uncomment youtube_url

database/migrations/
├── XXXX_create_crm_notifications_table.php
├── XXXX_create_crm_notification_recipients_table.php
├── XXXX_create_order_status_transitions_table.php
├── XXXX_create_sales_targets_table.php
├── XXXX_create_leaderboard_configs_table.php
└── XXXX_add_contact_phone_to_projects_table.php

database/seeders/
└── LeaderboardConfigSeeder.php             # NEW: default weights (25% each)

resources/views/livewire/mannager/
├── manager-dashboard.blade.php             # MODIFIED: bell component, target widgets
├── customer-profile.blade.php              # MODIFIED: enhanced order details
├── sales-managers.blade.php                # MODIFIED: status columns, reset button
├── notifications.blade.php                 # NEW: rich text editor, recipient selector
├── announcements.blade.php                 # NEW: paginated announcement list
├── sales-targets.blade.php                 # NEW: target config + progress display
├── leaderboard.blade.php                   # NEW: ranked table + charts
└── notification-bell.blade.php             # NEW: bell icon + dropdown

resources/views/livewire/frontend/
└── project-single.blade.php               # MODIFIED: YouTube embed, contact_phone buttons

resources/js/
├── echo.js                                 # NEW: Laravel Echo + Reverb config
└── app.js                                  # MODIFIED: import echo.js

lang/ar/
└── crm.php                                 # MODIFIED: new translation keys

lang/en/
└── crm.php                                 # MODIFIED: new translation keys

routes/
└── web.php                                 # MODIFIED: new CRM routes

tests/
├── Feature/
│   ├── NotificationSystemTest.php          # NEW
│   ├── SalesTargetsTest.php                # NEW
│   ├── StatusColorsTest.php                # NEW
│   ├── CustomerProfileTest.php             # NEW
│   ├── PasswordResetTest.php               # NEW
│   └── ProjectContactPhoneTest.php         # NEW
└── Unit/
    ├── TargetTrackingServiceTest.php        # NEW
    ├── CrmNotificationServiceTest.php       # NEW
    └── YoutubeEmbedTest.php                 # NEW
```

**Structure Decision**: Follows established project patterns — new Livewire components in `app/Livewire/Mannager/`, new models in `app/Models/`, new services in `app/Services/`, Filament for admin-facing project configuration. No new top-level directories.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| CRM panel uses Livewire instead of Filament | 16 existing Livewire components form the CRM manager panel. Migrating to Filament would require rewriting all existing pages — far exceeds feature scope. | Filament migration is a separate initiative; new components must match existing architecture for consistency. |
| Reverb WebSocket server (new infrastructure) | Spec explicitly requires "real-time push without page refresh" (FR-021, clarification). Polling cannot satisfy this requirement. | Livewire polling (`wire:poll`) adds server load and has 5-10s latency — rejected because the clarification specifically chose real-time push over polling. |

## Implementation Phases

### Phase A: Foundation (P1 prerequisites)

1. Status color centralization (`UnitOrder::STATUS_COLORS`, update `statusColor()`, update all views)
2. Order status transition logging (`order_status_transitions` table, `UnitOrderObserver`)
3. Reverb + Echo setup (install, configure, test connection)

### Phase B: Core Features (P1)

4. Enhanced customer profile (expand order details in `CustomerProfile.php`)
5. Notification system — backend (models, migrations, `CrmNotificationService`, broadcasting)
6. Notification system — frontend (bell component, notification panel, announcements page, rich text editor)

### Phase C: Targets & Analytics (P2)

7. Sales targets system — backend (models, `TargetTrackingService`, target management)
8. Sales targets system — frontend (dashboard widgets, leaderboard, historical view)
9. Sales rep status columns (add count columns to `SalesManagers.php`)
10. Remove project manager from assignment (update `scopeAccessibleBy()`, grandfather clause)
11. Project contact number (migration, Filament field, frontend buttons)

### Phase D: Quick Wins (P3)

12. Ad set display verification (audit all views, ensure `ad_set` used everywhere)
13. YouTube embedding fix (accessor, frontend iframe, URL normalization)
14. Employee password reset (action, Livewire form, routes, email)

### Phase E: Testing & Polish

15. Pest tests for all new features
16. Arabic translations for all new strings
17. RTL layout verification
18. Structured logging verification

---

*Plan generated by `/speckit.plan` — proceed to `/speckit.tasks` for task breakdown.*
