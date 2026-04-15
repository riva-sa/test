<!--
Sync Impact Report
- Version change: 0.0.0 -> 1.0.0
- List of modified principles:
  - [NEW] I. Filament-First Architecture
  - [NEW] II. Logic Decoupling (Actions/Services)
  - [NEW] III. Testing Discipline (Pest-First)
  - [NEW] IV. Multi-Language Integrity (i18n)
  - [NEW] V. Observability & Contextual Logging
- Added sections: Governance
- Removed sections: N/A
- Templates requiring updates:
  - .specify/templates/plan-template.md (✅ updated)
  - .specify/templates/spec-template.md (✅ updated)
  - .specify/templates/tasks-template.md (✅ updated)
- Follow-up TODOs: N/A
-->

# Riva CRM Constitution

## Core Principles

### I. Filament-First Architecture
The admin and management interfaces MUST be built using FilamentPHP components. Custom Livewire components should only be used when Filament's standard resources, pages, or widgets cannot satisfy the user experience requirements. Consistency with Filament's design system is non-negotiable for internal panels.

### II. Logic Decoupling (Actions/Services)
Application logic MUST NOT reside directly in Controllers, Livewire components, or Eloquent Observers. Complex business operations MUST be encapsulated within dedicated `Action` or `Service` classes. This ensures that logic is reusable across web, API, and console contexts, and is independently testable.

### III. Testing Discipline (Pest-First)
All new features MUST include automated tests using Pest. Every PR should verify:
- Feature/Service logic via unit or functional tests.
- Livewire component state and transitions for UI features.
- API response structures and status codes for integration endpoints.

### IV. Multi-Language Integrity (i18n)
The system MUST fully support both Arabic and English. Hardcoded strings in views or classes are FORBIDDEN. All user-facing text must use Laravel translation keys (`__()` or `trans()`). RTL (Right-to-Left) layout compatibility must be verified for all UI changes.

### V. Observability & Contextual Logging
Critical business events (lead ingestion, order status changes, etc.) MUST be logged using structured JSON context via Laravel's Log facade. Logs should include relevant entity IDs and actor information to facilitate debugging and auditing without needing database rollbacks.

## Development Standards

### Technology Stack
- **Framework**: Laravel 11.x
- **UI Framework**: Filament 3.x / TailwindCSS
- **Reactivity**: Livewire 3.x
- **Testing**: Pest PHP
- **Linting**: Laravel Pint (PSR-12 compliant)

## Review Process

### Quality Gates
1. **Lint Checks**: All code must pass `@pint`.
2. **Static Analysis**: Critical paths must pass `@phpstan`.
3. **Test Completion**: All tests must pass `@pest` with no regressions.
4. **Translation Check**: All new strings must have corresponding keys in `lang/ar/` and `lang/en/`.

## Governance
This constitution supersedes all informal development practices. Amendments to these principles require a documented proposal and a version bump in this document. All implementation plans (plan.md) MUST verify compliance with these principles in the "Constitution Check" section.

**Version**: 1.0.0 | **Ratified**: 2026-04-15 | **Last Amended**: 2026-04-15
