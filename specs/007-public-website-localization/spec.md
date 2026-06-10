# Feature Specification: Riva Public Website Localization (Arabic / English)

**Feature Branch**: `007-public-website-localization`  
**Created**: 2026-06-09  
**Status**: Draft — Revised (database changes deferred)  
**Input**: User description: "Implement bilingual support (Arabic + English) for the PUBLIC FACING WEBSITE ONLY."

## Clarifications

### Session 2026-06-09

- Q: How should the language switcher UI look? → A: Two separate text links ("English" / "العربية") side by side in the navbar.
- Q: When a URL prefix conflicts with a stored session locale, which should take precedence? → A: URL prefix always wins; session is overwritten to match the URL.
- Q: How should locale preference persist across browser sessions? → A: URL prefix always wins; persistent cookie as fallback for no-prefix visits.
- Q: How should we verify all UI strings are properly translated? → A: Mixed: automated scan for missing keys + manual visual spot-check of each page.
- Q: What should happen when JavaScript is disabled? → A: Graceful degradation — switcher links use standard `<a href="/en/...">` for full-page navigation without JS.

## User Scenarios & Testing

### User Story 1 - Visitor Views Website in English (Priority: P1)

A visitor who does not speak Arabic arrives at the Riva website. They see a language switcher in the navigation bar, click "English", and the entire public website content switches to English — navigation labels, hero sections, property listings, contact forms, footer, and all marketing text.

**Why this priority**: Bilingual support is the core goal. Without this, the feature has no value.

**Independent Test**: Can be fully tested by navigating to the public homepage, clicking the language switcher, and verifying that all visible static text on the page displays in English.

**Acceptance Scenarios**:

1. **Given** a visitor on the public homepage, **When** they click the "English" language option, **Then** all static UI text on the page appears in English (navigation, hero, features section, footer).
2. **Given** a visitor on any public page (projects, about, contact, blog), **When** they switch to English, **Then** that page's static content renders in English.
3. **Given** a visitor has switched to English, **When** they navigate to another public page, **Then** the English language preference persists across pages.
4. **Given** a visitor on an English page, **When** they switch back to Arabic, **Then** all content returns to the original Arabic text.

---

### User Story 2 - Visitor Uses English-Localized Property Search and Filters (Priority: P1)

An English-speaking visitor browses the projects and units listing pages. They see all filter labels, sort options, placeholder text, unit status badges, and empty-state messages in English.

**Why this priority**: The projects/units pages are the core value proposition of the website.

**Independent Test**: Can be fully tested by switching to English, navigating to `/projects`, and verifying filter sidebar labels, sort dropdown, search placeholder, and project/unit card text are all in English.

**Acceptance Scenarios**:

1. **Given** a visitor on the projects page in English mode, **When** they view the filter sidebar, **Then** all filter headers (price, area, location, bedrooms, bathrooms, kitchens, developer, project type) are displayed in English.
2. **Given** a visitor on the projects page in English mode, **When** they view a project card, **Then** status badges ("Available", "Under Construction", "Sold Out", "Fully Reserved") appear in English.
3. **Given** a visitor on the projects page in English mode, **When** no results match their filters, **Then** the empty-state message appears in English.

---

### User Story 3 - Visitor Submits Contact Form in English (Priority: P2)

An English-speaking visitor navigates to the contact page while in English mode. The form labels, validation messages, department dropdown options, and success message all display and respond in English.

**Why this priority**: Contact forms are a key conversion point; English-speaking users must be able to engage.

**Independent Test**: Can be fully tested by switching to English, navigating to `/contact-us`, and verifying form labels, placeholders, department options, and submission flow display in English.

**Acceptance Scenarios**:

1. **Given** a visitor on the contact page in English mode, **When** they view the form, **Then** all form labels, placeholders, and the submit button are in English.
2. **Given** a visitor on the contact page in English mode, **When** they submit with invalid data, **Then** validation error messages appear in English.
3. **Given** a visitor on the contact page in English mode, **When** they submit valid data, **Then** the success message appears in English.

---

### User Story 4 - Search Engine Indexes Localized Pages (Priority: P2)

Search engines discover the English version of the public website. Each page includes proper hreflang annotations, correct `lang` attributes, and canonical URLs, enabling search engines to index both language variants correctly without duplicate content penalties.

**Why this priority**: SEO is essential for organic discovery of the English version.

**Independent Test**: Can be tested by inspecting the HTML source of any public page in both language modes and verifying hreflang tags, `lang` attribute, and canonical link are present and correct.

**Acceptance Scenarios**:

1. **Given** any public page in Arabic mode, **When** viewing the HTML source, **Then** `<html lang="ar" dir="rtl">` is set, and `<link rel="alternate" hreflang="en" ...>` is present.
2. **Given** any public page in English mode, **When** viewing the HTML source, **Then** `<html lang="en" dir="ltr">` is set, and `<link rel="alternate" hreflang="ar" ...>` is present.
3. **Given** any public page in either language, **When** viewing the HTML source, **Then** a `<link rel="canonical" ...>` tag points to the same page without language prefix.

---

### Edge Cases

- What happens when a visitor directly accesses `/en/projects` (English-prefixed URL) — the page should display in English.
- What happens when a locale is stored in session/cookie but the URL prefix conflicts — URL prefix always wins; session is overwritten to match the URL prefix.
- What happens when JavaScript is disabled — language switcher links degrade gracefully via standard `<a href="/en/...">` full-page navigation without JS.
- What happens with database-driven content like ContentBlock (about page, privacy, terms) — these use `\App\Helpers\ContentHelper::get()` and remain in Arabic for this phase. Bilingual ContentBlock support is deferred to a future phase (see Future Phases section).
- What happens with the search input placeholder "ابحث عن عقارك" in English mode — the placeholder text uses the translation key `search.placeholder` in `lang/*/public.php`, rendered via `@lang('public.search.placeholder')` in the nav-bar Blade view.
- What happens with the `#[Title]` annotation on Livewire components (e.g. `#[Title('المشاريع')]`) when in English mode.
- What happens with blog categories/tags labels that are hardcoded in English ("Categories", "Tags") and "No posts found" fallback — these English strings are extracted to translation keys `blog.categories`, `blog.tags`, `blog.search`, and `blog.no_posts` in `lang/*/public.php`. Arabic translations are added to `lang/ar/public.php` for consistency, while `lang/en/public.php` preserves the original English text.

## Requirements

### Functional Requirements

- **FR-001**: Public website MUST display a language switcher in the navigation bar as two separate text links ("English" / "العربية") side by side, allowing visitors to toggle between Arabic and English. Links MUST fall back to standard `<a href="/en/...">` full-page navigation when JavaScript is disabled.
- **FR-002**: All static text in public-facing Blade views MUST be extracted into Laravel translation files (`lang/ar/*.php` and `lang/en/*.php`).
- **FR-003**: All static text in public-facing Livewire components (PHP class files) MUST use the `__()` or `@lang()` helper for translatable strings.
- **FR-004**: Arabic text currently hardcoded in Blade views MUST remain unchanged and serve as the source of truth for Arabic translations.
- **FR-005**: The public layout MUST dynamically set the `lang` attribute on the `<html>` element based on the active locale.
- **FR-006**: The public layout MUST dynamically set the `dir` attribute (`rtl` for Arabic, `ltr` for English) on the `<html>` element.
- **FR-007**: The locale selection MUST persist across page visits using URL prefix when present, falling back to a persistent cookie for no-prefix visits.
- **FR-008**: Public routes MUST support localized URL prefixes (`/en` for English, `/ar` for Arabic, no prefix defaults to Arabic).
- **FR-009**: Arabic URLs MUST remain unchanged — existing SEO equity must not be broken.
- **FR-010**: Hreflang tags MUST be present in the `<head>` of all public pages for both language alternatives.
- **FR-011**: Canonical link tags MUST be present on all public pages.
- **FR-012**: The livewire search component (SearchDropdown/NavBar) placeholder text MUST be translatable.
- **FR-013**: The `#[Title]` annotations on Livewire components MUST dynamically reflect the current locale.
- **FR-014**: Contact form validation messages in the ContactUs Livewire component MUST be translatable.
- **FR-015**: Unit status badges ("متاح", "محجوز", "مباع") and project status badges must display English equivalents when locale is English.
- **FR-016**: Unit and project status display strings MUST use translation keys in Blade views only (`@lang('public.status.available')` etc.) — no model accessors or database changes.
- **FR-017**: The locale middleware MUST intercept requests to locale-prefixed public routes and set the application locale based on the URL prefix. Non-public routes (CRM, login, API) remain outside the locale prefix group and are unaffected.
- **FR-018**: Database-driven ContentBlock pages (About, Privacy, Terms) MUST continue displaying existing Arabic database content unchanged. Bilingual ContentBlock storage is deferred to a future phase (see Future Phases).

### Key Entities

- **Locale**: The active language setting (ar or en) persisted via URL prefix and session.
- **Translation File**: Laravel PHP array-based translation files under `lang/ar/` and `lang/en/` containing key-value pairs for all public-facing UI strings.
- **ContentBlock**: Existing database model for dynamic page content (About, Privacy, Terms). Bilingual support is deferred to a future phase — currently displays Arabic content only.

## Success Criteria

### Measurable Outcomes

- **SC-001**: Visitors can toggle between Arabic and English from any public page and see the full page translate in a single page load.
- **SC-002**: All 11 public-facing Blade views and 7 Livewire sub-components have zero hardcoded UI strings — all text is sourced from translation files.
- **SC-003**: HTML source of every public page includes correct `lang`, `dir`, `hreflang`, and `canonical` tags for both language variants.
- **SC-004**: Search engines can discover both Arabic and English versions of every public page without duplicate content issues.
- **SC-005**: Locale selection persists across page navigation, page refresh, and browser session without requiring re-selection.
- **SC-006**: All existing Arabic routes and URLs continue to function exactly as before — no broken links or 404s.

## Localization

- **L10N-001**: English translation files must cover every public-facing string currently hardcoded in Arabic in Blade views and Livewire components.
- **L10N-002**: Arabic content must remain untouched — only English translations are to be created.
- **L10N-003**: UI layout must correctly switch between RTL (Arabic) and LTR (English) direction.
- **L10N-004**: English translations must use professional real-estate terminology consistent with the Riva brand.

## Assumptions

- **No database changes** — no migrations, no new columns, no schema changes of any kind in this phase.
- ContentBlock-driven pages (About, Privacy, Terms) display existing Arabic database content only. Bilingual storage is deferred to a future phase.
- Unit and project statuses will use translation keys in Blade views only — no model changes.
- Locale URL prefixes (`/en`, `/ar`) are the chosen strategy for route localization — no prefix defaults to Arabic.
- A `SetLocale` middleware will be created and registered to handle locale detection from URL prefix and set the application locale, with fallback to a persistent cookie for no-prefix visits.
- Existing routes for manager/CRM/dashboard areas will NOT be prefixed — locale middleware only applies to public routes.
- The `#[Title]` attribute on Livewire components can be made dynamic by using `__()` helper in the title string.
- Unit/project statuses will use translation keys mapped to their `case`/`status` database values.
- The public layout (`resources/views/components/layouts/app.blade.php`) will be modified to support dynamic `lang` and `dir` attributes.

## Gap Analysis

1. **No translation files exist for public website content** — only CRM/leaderboard translations exist in `lang/ar/` and `lang/en/`.
2. **All Blade views have hardcoded Arabic strings** — none use `__()` or `@lang()`.
3. **No locale middleware exists** — no `SetLocale` middleware in `app/Http/Middleware/`.
4. **No language switcher UI exists** — no language toggle component in the navbar.
5. **No hreflang or canonical SEO tags** — not present in the layout.
6. **Layout `lang` attribute is dynamic** but `dir` is not set dynamically (public layout uses `str_replace('_', '-', app()->getLocale())` for `lang` but has no `dir` attribute at all).
7. **Livewire `#[Title]` attributes are hardcoded Arabic** — need to be made translatable.
8. **Contact form validation messages are hardcoded Arabic** — in ContactUs component `$messages` property.
9. **ContentBlock/ContentHelper has no locale support** — single-column content that needs bilingual storage (deferred to future phase).
10. **Blog labels** ("Categories", "Tags", "Search", "No posts found") are hardcoded in English already — need translation support.

## Technical Design

### Route Localization Strategy

Use **localized URL prefixes** (`/en`, `/ar`):
- Arabic (default): `https://riva.com/` (no prefix) or `https://riva.com/ar/...`
- English: `https://riva.com/en/...`
- A `SetLocale` middleware extracts the prefix, sets `app()->setLocale()`, and stores preference in session and a persistent cookie
- For no-prefix visits, the middleware falls back to the persistent cookie, then session, then default `ar`
- Existing un-prefixed routes continue to serve Arabic content

### Locale Middleware

Create `app/Http/Middleware/SetLocale.php`:
- Reads locale from first URL segment (`en` or `ar`)
- Validates against allowed locales `['ar', 'en']`
- Sets `app()->setLocale($locale)`
- Sets `session(['locale' => $locale])`
- Redirects if invalid locale is provided
- Falls back to persistent cookie locale → session locale → default `ar`
- URL prefix always takes precedence over any stored locale preference

### Translation File Structure

```
lang/
  ar/
    public.php    # All public website Arabic strings
  en/
    public.php    # All public website English translations
```

### Public Layout Changes

- `<html>` tag: dynamic `lang` and `dir` attributes
- Head: inject hreflang alternates and canonical link
- Keep existing structure, only modify SEO/direction attributes

### Affected Public Routes (scope-limited)

Routes needing localization prefix (11 routes):
- `frontend.home` (GET /)
- `frontend.projects` (GET /projects)
- `frontend.projects.map` (GET /projects-map)
- `frontend.projects.single` (GET /project/{slug})
- `frontend.about` (GET /about)
- `frontend.blog` (GET /blog)
- `frontend.blog.single` (GET /blog/{slug})
- `frontend.services` (GET /services)
- `frontend.contactus` (GET /contact-us)
- `frontend.privacy` (GET /privacy)
- `frontend.terms` (GET /terms)

### Affected Files List

#### Blade Views (public-facing)
1. `resources/views/components/layouts/app.blade.php`
2. `resources/views/livewire/frontend/home-page.blade.php`
3. `resources/views/livewire/frontend/about.blade.php`
4. `resources/views/livewire/frontend/services.blade.php`
5. `resources/views/livewire/frontend/contact-us.blade.php`
6. `resources/views/livewire/frontend/projects-page.blade.php`
7. `resources/views/livewire/frontend/project-single.blade.php`
8. `resources/views/livewire/frontend/projects-map.blade.php`
9. `resources/views/livewire/frontend/blog.blade.php`
10. `resources/views/livewire/frontend/blog-single.blade.php`
11. `resources/views/livewire/frontend/privacy.blade.php`
12. `resources/views/livewire/frontend/terms.blade.php`
13. `resources/views/livewire/frontend/partials/nav-bar.blade.php`
14. `resources/views/livewire/frontend/partials/footer.blade.php`
15. `resources/views/livewire/frontend/conponents/project-slider.blade.php`
16. `resources/views/livewire/frontend/conponents/projects-tab.blade.php`
17. `resources/views/livewire/frontend/conponents/unit-popup.blade.php`
18. `resources/views/livewire/frontend/conponents/unit-orderpopup.blade.php`
19. `resources/views/livewire/frontend/conponents/client-logos.blade.php`

#### Livewire Components (PHP)
20. `app/Livewire/Frontend/Partials/NavBar.php`
21. `app/Livewire/Frontend/ContactUs.php`
22. `app/Livewire/Frontend/ProjectsPage.php`
23. `app/Livewire/Frontend/ProjectSingle.php`
24. `app/Livewire/Frontend/ProjectsMap.php`

#### New Files
25. `lang/en/public.php` (English translation file)
26. `lang/ar/public.php` (Arabic translation file, mirrored from existing text)
27. `app/Http/Middleware/SetLocale.php`
28. `app/Helpers/LocalizationHelper.php` (if needed for SEO tags)

#### Configuration
29. `bootstrap/app.php` (register SetLocale middleware)
30. `routes/web.php` (wrap public routes in locale prefix group)

## Translation Strategy

1. Extract all hardcoded Arabic strings from Blade views into `lang/ar/public.php`.
2. Create `lang/en/public.php` with professional English translations for real estate.
3. Keys follow section-based naming: `nav.*`, `home.*`, `projects.*`, `project.*`, `about.*`, `contact.*`, `footer.*`, `blog.*`, `search.*`, `common.*`, `status.*`, `unit.*`, `filter.*`.
4. Use `__('public.nav.home')` style references in Blade views.
5. For Livewire PHP classes, use `__('public.status.available')` etc.
6. For dynamic `#[Title]`, compute title from translation in `render()` or `mount()`.

### Brand Terminology for English Translations

| Arabic | English (Brand Term) |
|--------|---------------------|
| ريفا العقارية | Riva Real Estate |
| مشاريعنا | Our Projects |
| الوحدات | Units |
| متاح | Available |
| محجوز | Reserved |
| مباع | Sold |
| تواصل معنا | Contact Us |
| خريطة مشاريعُنا | Project Map |
| تعرف على ريڤا | About Riva |
| الأحداث العقارية | Real Estate Insights |

## Route Localization Strategy

- Use `Route::localized()` via a custom macro or prefix group
- Arabic: no prefix (backward compatible) or `/ar` prefix
- English: `/en` prefix
- Wrap existing public routes in a locale-prefixed group
- The `SetLocale` middleware reads the prefix and removes it before route matching
- Non-public routes (CRM, developer, login, API) remain outside the prefix group

## SEO Considerations

- **hreflang**: Each page `<head>` must include:
  `<link rel="alternate" hreflang="ar" href="https://riva.com/page">`
  `<link rel="alternate" hreflang="en" href="https://riva.com/en/page">`
- **Canonical**: Each page must have:
  `<link rel="canonical" href="https://riva.com/en/page">` (or `/ar/page`)
- **lang attribute**: Dynamically set `lang="ar"` or `lang="en"` on `<html>` tag
- **dir attribute**: Dynamically set `dir="rtl"` or `dir="ltr"` on `<html>` tag
- No trailing slash normalization needed beyond what Laravel already provides
- Existing Arabic URLs must not change to preserve SEO equity

## Implementation Tasks

1. Create `lang/ar/public.php` with all extracted Arabic strings from Blade views
2. Create `lang/en/public.php` with English translations
3. Create `app/Http/Middleware/SetLocale.php`
4. Register SetLocale middleware in `bootstrap/app.php`
5. Update `routes/web.php` — wrap public routes in locale prefix group
6. Update `resources/views/components/layouts/app.blade.php` — dynamic `lang`, `dir`, hreflang, canonical
7. Update `resources/views/livewire/frontend/partials/nav-bar.blade.php` — replace hardcoded Arabic with `__()`, remove hardcoded `dir="rtl"`, and add two separate text links ("English" / "العربية") as the language switcher
8. Update `app/Livewire/Frontend/Partials/NavBar.php` — add language switching logic
9. Update `resources/views/livewire/frontend/partials/footer.blade.php` — replace hardcoded Arabic
10. Update `resources/views/livewire/frontend/home-page.blade.php` — replace hardcoded Arabic
11. Update `resources/views/livewire/frontend/about.blade.php` — replace hardcoded Arabic. ContentHelper output remains in Arabic (deferred to future phase). Add TODO comment for future bilingual ContentBlock support.
12. Update `resources/views/livewire/frontend/contact-us.blade.php` + `app/Livewire/Frontend/ContactUs.php` — translatable forms
13. Update `resources/views/livewire/frontend/projects-page.blade.php` — translatable filters, labels, statuses
14. Update `resources/views/livewire/frontend/project-single.blade.php` — translatable labels, statuses
15. Update `resources/views/livewire/frontend/blog.blade.php` + `blog-single.blade.php` — translatable labels
16. Update `resources/views/livewire/frontend/privacy.blade.php` + `terms.blade.php` — add TODO comments for future bilingual ContentBlock support. Content remains in Arabic for this phase.
17. Update `resources/views/livewire/frontend/projects-map.blade.php` — translatable UI text
18. Update `resources/views/livewire/frontend/conponents/*.blade.php` — translatable text in sub-components
19. Update Livewire `#[Title]` annotations to use translation helpers
20. Add hreflang and canonical generation logic (helper or service)
21. Test all public pages in both languages

## Testing Tasks

1. Test each public page renders correctly in Arabic (no regressions)
2. Test each public page renders correctly in English
3. Test language switcher is visible and functional on all public pages
4. Test locale persistence across navigation
5. Test locale persistence across page refresh
6. Test direct URL access with `/en/` prefix
7. Test direct URL access with `/ar/` prefix
8. Test direct URL access without prefix (defaults to Arabic)
9. Test contact form submission in English — validation messages and success message
10. Test contact form submission in Arabic — no regression
11. Test project filter/sort functionality in both languages
12. Test unit popup details in both languages
13. Test search functionality in both languages
14. Test blog listing and single post pages in both languages
15. Verify hreflang tags present on every public page
16. Verify canonical tags present on every public page
17. Verify correct `lang` and `dir` attributes in both modes
18. Verify CRM/manager routes are NOT affected by locale changes
19. Verify login page is NOT affected
20. Verify API routes are NOT affected
21. Run automated Artisan command to scan Blade views and Livewire PHP files for missed hardcoded strings (all keys must exist in translation files)
22. Manually spot-check each public page in both languages for visual correctness
23. Verify ContentHelper-driven pages (About, Privacy, Terms) continue to display Arabic content correctly with no regressions

## Rollback Plan

1. **Revert routes**: Restore `routes/web.php` from git to remove locale prefix group
2. **Remove middleware**: Remove `SetLocale` middleware registration from `bootstrap/app.php`
3. **Restore layout**: Restore `resources/views/components/layouts/app.blade.php` from git
4. **Remove translation files**: Delete `lang/en/public.php` and `lang/ar/public.php`
5. **Revert Blade views**: Restore all modified Blade views from git
6. **Revert Livewire components**: Restore all modified PHP component files from git
7. **Remove helper/service files**: Delete any new helper/service classes
8. **Verify**: Check all public pages render original Arabic-only content correctly
9. **Deploy**: Deploy reverted code to production

## Out of Scope — Deferred to Future Phase

The following items are explicitly excluded from the current implementation and documented as future enhancement candidates:

### Bilingual ContentBlock Storage

**TODO**: Database-driven pages (About, Privacy, Terms) use `\App\Helpers\ContentHelper::get()` which reads from the `ContentBlock` model. Currently displays Arabic only.

**Future approach**: When implementing bilingual ContentBlock support, consider either:
- Adding a `locale` column to the `content_blocks` table
- Adding `content_ar` / `content_en` columns
- Using a separate translation model

**Affected views** (add `<!-- TODO: Bilingual ContentBlock -->` during current implementation):
- `resources/views/livewire/frontend/about.blade.php`
- `resources/views/livewire/frontend/privacy.blade.php`
- `resources/views/livewire/frontend/terms.blade.php`

**Affected files for future phase**:
- `app/Helpers/ContentHelper.php` — needs locale-aware retrieval
- `app/Models/ContentBlock.php` — may need schema update
- Database migration for ContentBlock schema change

### Dashboard / Admin / CRM / API / Email / Notification Localization

Explicitly excluded per scope restrictions. Only the public-facing website is in scope for this feature.
