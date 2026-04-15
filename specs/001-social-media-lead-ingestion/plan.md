# Implementation Plan: Social Media Lead Ingestion

**Branch**: `001-social-media-lead-ingestion` | **Date**: 2026-04-15 | **Spec**: [spec.md](./spec.md)
**Input**: Feature specification from `/specs/001-social-media-lead-ingestion/spec.md`

## Summary

Implement a new API endpoint `/api/zapier/social-media-lead` to capture leads from social media platforms via Zapier. This involves extending the `UnitOrder` model with campaign attribution fields, creating a dedicated controller for lead ingestion, and dispatching notifications to the sales and admin teams.

## Technical Context

**Language/Version**: PHP 8.2+  
**Primary Dependencies**: Laravel 11.x, Filament 3.x, Livewire 3.x  
**Storage**: MySQL (`unit_orders` table)  
**Testing**: Pest PHP  
**Target Platform**: Web (CRM Backend)
**Project Type**: Laravel Web Application  
**Performance Goals**: < 1s response time for webhook ingestion  
**Constraints**: Source whitelist (TikTok, Snapchat, etc.)  
**Scale/Scope**: Support hundreds of daily leads via Zapier webhooks

## Constitution Check

*GATE: Must pass before Phase 1 research. Re-check after Phase 2 design.*

- [x] **Filament-First**: Does this approach prioritize Filament components? (Yes, updating `UnitOrderResource` and related custom Livewire views)
- [x] **Decoupled Logic**: Are business rules encapsulated in Actions/Services (not controllers)? (Will use `IngestLeadAction`)
- [x] **Testing Discipline**: Does the plan include Pest tests for models and components? (Yes, `ZapierLeadControllerTest`)
- [x] **i18n Readiness**: Are all user-facing strings planned as translation keys? (Yes, for notifications and labels)
- [x] **Observability**: Is structured JSON logging planned for critical events? (Yes, logging lead payload on failure/success)

## Project Structure

### Documentation (this feature)

```text
specs/001-social-media-lead-ingestion/
├── plan.md              # This file
├── research.md          # Research findings
├── data-model.md        # UnitOrder extensions
├── quickstart.md        # Integration guide
└── tasks.md             # Implementation tasks
```

### Source Code (repository root)

```text
app/
├── Actions/
│   └── IngestSocialMediaLead.php
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       └── ZapierLeadController.php
├── Models/
│   └── UnitOrder.php (updated)
├── Notifications/
│   └── NewSocialMediaLead.php
app/Filament/
└── Resources/
    └── UnitOrderResource.php (updated)
resources/views/livewire/mannager/
├── manage-orders.blade.php (updated)
└── order-details.blade.php (updated)
database/
└── migrations/
    └── 2026_04_15_000000_add_campaign_fields_to_unit_orders.php
tests/
└── Feature/
    └── Api/
        └── ZapierLeadControllerTest.php
```

**Structure Decision**: Standard Laravel architecture with Action classes for business logic decoupling, following project constitution.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| N/A       |            |                                     |
