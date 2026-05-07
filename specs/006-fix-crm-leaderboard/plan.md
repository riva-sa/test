# Implementation Plan: CRM Performance, Notifications & Leaderboard Fixes

**Branch**: `006-fix-crm-leaderboard` | **Date**: 2026-05-05 | **Spec**: [spec.md](file:///Users/macbox/Documents/GitHub/test/specs/006-fix-crm-leaderboard/spec.md)
**Input**: Feature specification from `/specs/006-fix-crm-leaderboard/spec.md`

**Note**: This template is filled in by the `/speckit.plan` command. See `.specify/templates/plan-template.md` for the execution workflow.

## Summary

This feature addresses critical CRM issues including system performance, missing email notifications for orders and alerts, and leaderboard inaccuracies. The technical approach involves:
1.  **Leaderboard Accuracy**: implementing a scheduled recalculation mechanism that only counts "Sales Transaction" statuses and allows for manual admin adjustments with an audit log.
2.  **Notifications**: enhancing the notification system to send both in-app and email alerts to agents, managers, and admins, with dynamic content generation.
3.  **Historical Data**: updating the daily leaderboard view to support historical date selection.
4.  **Performance**: optimizing primary CRM pages to ensure load times under 3 seconds.
5.  **Motivation**: adding a top performers widget to the sales manager home page.

## Technical Context

**Language/Version**: PHP 8.2, Laravel 11.x
**Primary Dependencies**: FilamentPHP 3.x, Livewire 3.x, Pest PHP
**Storage**: MySQL (MariaDB/PostgreSQL compatible)
**Testing**: Pest PHP
**Target Platform**: Web (CRM Interface)
**Project Type**: Web application
**Performance Goals**: Page load < 3s, Filter response < 2s
**Constraints**: Multi-language (AR/EN), RTL support, Admin-only audit logs
**Scale/Scope**: Internal sales team CRM with real-time notifications and scheduled leaderboard snapshots.

## Constitution Check

*GATE: Must pass before Phase 1 research. Re-check after Phase 2 design.*

- [x] **Filament-First**: Does this approach prioritize Filament components? (Using Filament for Admin adjustments and Sales Manager dashboard widgets)
- [x] **Decoupled Logic**: Are business rules encapsulated in Actions/Services (not controllers)? (Leaderboard recalculation and notification dispatching will live in Services/Actions)
- [x] **Testing Discipline**: Does the plan include Pest tests for models and components? (Pest tests planned for adjustment logic and notification triggers)
- [x] **i18n Readiness**: Are all user-facing strings planned as translation keys? (All UI labels like "Sales Transactions" will be in lang files)
- [x] **Observability**: Is structured JSON logging planned for critical events? (Notification failures and point adjustments will be logged)

## Project Structure

### Documentation (this feature)

```text
specs/006-fix-crm-leaderboard/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
└── tasks.md             # Phase 2 output
```

### Source Code (repository root)

```text
app/
├── Models/              # LeaderboardAdjustment, LeaderboardSnapshot
├── Services/            # LeaderboardService, NotificationService
├── Actions/             # AdjustLeaderboardPoints, RecalculateLeaderboard
├── Filament/            # Admin resources and Sales Manager widgets
├── Livewire/            # Updated Leaderboard components
├── Notifications/       # OrderCreatedNotification, CRMAlertNotification
└── Observers/           # OrderObserver for triggering recalculations/notifications

tests/
├── Feature/             # Leaderboard, Notification tests
└── Unit/                # Service/Action tests
```

**Structure Decision**: Standard Laravel + Filament structure as defined in the Constitution. Logic will be decoupled into Services and Actions.

## Complexity Tracking

> **Fill ONLY if Constitution Check has violations that must be justified**

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| None | N/A | N/A |
