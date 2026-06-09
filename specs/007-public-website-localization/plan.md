# Implementation Plan: Riva Public Website Localization (Arabic / English)

**Branch**: `007-public-website-localization` | **Date**: 2026-06-09 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `specs/007-public-website-localization/spec.md`

## Summary

Add bilingual (Arabic/English) support to the public-facing Riva website. Static UI text in Blade views and Livewire components is extracted into Laravel translation files (`lang/ar/public.php`, `lang/en/public.php`). A `SetLocale` middleware handles locale detection from URL prefixes (`/en`, `/ar`), with cookie fallback for no-prefix visits. Language switcher renders as two text links in the navbar. No database changes. ContentBlock pages (About, Privacy, Terms) remain Arabic-only and deferred.

## Technical Context

**Language/Version**: PHP 8.2, Laravel 11.x  
**Primary Dependencies**: Livewire 3.x, TailwindCSS  
**Storage**: N/A (translations in PHP array files; no DB changes)  
**Testing**: Pest PHP  
**Target Platform**: Web (Laravel + Livewire)  
**Project Type**: Web application feature (public site i18n)  
**Performance Goals**: Locale switch on single page load; no added latency targets beyond existing page load budget  
**Constraints**: No database changes; existing Arabic URLs unchanged; public routes only (no CRM/admin/API); ContentBlock pages deferred  
**Scale/Scope**: 11 public routes, ~19 Blade views, ~24 files total

## Constitution Check

*GATE: Must pass before Phase 1 research. Re-check after Phase 2 design.*

- [x] **Filament-First**: N/A — this is the public website, not admin. Filament governs admin panels only.
- [x] **Decoupled Logic**: Locale detection encapsulated in `SetLocale` middleware; SEO tag generation in `LocalizationHelper` service. No logic in controllers.
- [x] **Testing Discipline**: Pest tests planned for middleware, translation coverage, route localization, and Livewire component locale behavior.
- [x] **i18n Readiness**: Central to this feature — all public strings extracted to `__()` / `@lang()` keys.
- [x] **Observability**: Locale switching is a user-facing action; unexpected locale errors logged via structured JSON.

## Project Structure

### Documentation (this feature)

```text
specs/007-public-website-localization/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output (N/A — internal feature)
└── tasks.md             # Phase 2 output (created by /speckit.tasks)
```

### Source Code (repository root)

```text
resources/views/
├── components/layouts/
│   └── app.blade.php               # Modified: dynamic lang/dir, hreflang, canonical
└── livewire/frontend/
    ├── home-page.blade.php          # Modified: translatable strings
    ├── about.blade.php              # Modified: translatable strings + TODO for ContentBlock
    ├── services.blade.php           # Modified: translatable strings
    ├── contact-us.blade.php         # Modified: translatable strings
    ├── projects-page.blade.php      # Modified: translatable filters/statuses
    ├── project-single.blade.php     # Modified: translatable labels/statuses
    ├── projects-map.blade.php       # Modified: translatable UI text
    ├── blog.blade.php               # Modified: translatable labels
    ├── blog-single.blade.php        # Modified: translatable labels
    ├── privacy.blade.php            # Modified: TODO for ContentBlock
    ├── terms.blade.php              # Modified: TODO for ContentBlock
    ├── partials/
    │   ├── nav-bar.blade.php        # Modified: translatable + language switcher links
    │   └── footer.blade.php         # Modified: translatable strings
    └── conponents/
        ├── project-slider.blade.php # Modified: translatable text
        ├── projects-tab.blade.php   # Modified: translatable text
        ├── unit-popup.blade.php     # Modified: translatable text
        ├── unit-orderpopup.blade.php# Modified: translatable text
        └── client-logos.blade.php   # Modified: translatable text

app/Livewire/Frontend/
├── NavBar.php                       # Modified: language switching logic
├── ContactUs.php                    # Modified: translatable validation messages
├── ProjectsPage.php                 # Modified: translatable #[Title]
├── ProjectSingle.php                # Modified: translatable labels (no #[Title] present; Blade view only)
└── ProjectsMap.php                  # Modified: translatable #[Title]

app/Http/Middleware/
└── SetLocale.php                    # NEW

app/Helpers/
└── LocalizationHelper.php           # NEW (SEO tag generation)

lang/
├── ar/
│   └── public.php                   # NEW (Arabic strings mirrored from existing)
└── en/
    └── public.php                   # NEW (English translations)

routes/
└── web.php                          # Modified: public routes wrapped in locale prefix group

bootstrap/
└── app.php                          # Modified: register SetLocale middleware
```

**Structure Decision**: Existing Laravel monolith structure. No new projects, packages, or source directories. All changes are in-place modifications to existing files plus a small number of new files (middleware, helper, translation files).
