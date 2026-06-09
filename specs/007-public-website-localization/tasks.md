# Tasks: Riva Public Website Localization (Arabic / English)

**Input**: Design documents from `specs/007-public-website-localization/`
**Prerequisites**: plan.md (required), spec.md (user stories with priorities), research.md, data-model.md

**Tests**: Pest test tasks included per constitution mandate (III. Testing Discipline). Tests are written FIRST and MUST FAIL before implementation begins.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Laravel monolith**: paths relative to repository root
- All file paths are absolute from `/Users/macbox/Documents/GitHub/test`

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Create translation file skeletons and new service/middleware files

- [ ] T001 Create `lang/ar/public.php` with all existing hardcoded Arabic strings extracted from public Blade views
- [ ] T002 Create `lang/en/public.php` with professional English translations matching all keys in `lang/ar/public.php`
- [ ] T003 Create `app/Http/Middleware/SetLocale.php` — locale detection from URL prefix, cookie fallback, session storage
- [ ] T004 [P] Create `app/Helpers/LocalizationHelper.php` — hreflang and canonical tag generation helpers

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Tests

- [ ] T005 Write Pest test for `SetLocale` middleware in `tests/Feature/SetLocaleTest.php` — verify locale detection from URL prefix, cookie fallback, invalid locale redirect, non-public routes unaffected. MUST FAIL before T006.

### Implementation

- [ ] T006 Register `SetLocale` middleware as `set.locale` alias in `bootstrap/app.php`
- [ ] T007 Add structured JSON logging in `SetLocale` middleware for invalid locale attempts and unexpected locale resolution failures via `Log::channel('stack')->warning(...)` with context (request URI, detected locale, IP)
- [ ] T008 Update `routes/web.php` — wrap all 11 public frontend routes in locale prefix group (`/en`, `/ar`, no prefix)
- [ ] T009 Update `resources/views/components/layouts/app.blade.php` — add dynamic `lang`/`dir` attributes on `<html>`, inject hreflang alternates and canonical link via `LocalizationHelper`

**Checkpoint**: Foundation ready — translation files exist, middleware routes locale correctly with logging, layout reflects locale. User story implementation can begin.

---

## Phase 3: User Story 1 - Visitor Views Website in English (Priority: P1) 🎯 MVP

**Goal**: All static text on public pages (nav, home, about, services, blog, privacy, terms, footer) displays in English when locale is English. Language switcher links and switching logic functional.

**Independent Test**: Navigate to public homepage, click "English" link, verify all visible static text on the page displays in English (navigation, hero, features section, footer). Switch back to Arabic, verify original Arabic text restored.

### Tests

- [ ] T010 [P] [US1] Write Pest test for language switcher and locale persistence in `tests/Feature/LanguageSwitcherTest.php` — verify language toggle, locale persistence across navigation, URL prefix routing. MUST FAIL before T011.

### Implementation

- [ ] T011 [P] [US1] Update `resources/views/livewire/frontend/partials/nav-bar.blade.php` — replace hardcoded Arabic with `@lang('public.*')`, remove hardcoded `dir="rtl"`, add two text links ("English" / "العربية") as language switcher. The search input placeholder uses key `search.placeholder` (covers FR-012).
- [ ] T012 [US1] Update `app/Livewire/Frontend/Partials/NavBar.php` — add language switching logic (emit locale change, update session)
- [ ] T013 [P] [US1] Update `resources/views/livewire/frontend/partials/footer.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T014 [P] [US1] Update `resources/views/livewire/frontend/home-page.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T015 [P] [US1] Update `resources/views/livewire/frontend/about.blade.php` — replace hardcoded Arabic with `@lang('public.*')`, add `<!-- TODO: Bilingual ContentBlock -->` comment
- [ ] T016 [P] [US1] Update `resources/views/livewire/frontend/services.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T017 [P] [US1] Update `resources/views/livewire/frontend/blog.blade.php` and `blog-single.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T018 [P] [US1] Update `resources/views/livewire/frontend/privacy.blade.php` and `terms.blade.php` — add `<!-- TODO: Bilingual ContentBlock -->` comments, content remains Arabic
- [ ] T019 [P] [US1] Update `resources/views/livewire/frontend/conponents/client-logos.blade.php` — replace hardcoded Arabic with `@lang('public.*')`

**Checkpoint**: All general public pages render in both Arabic and English. Language switcher works on all pages. Locale persists across navigation. Pest tests pass.

---

## Phase 4: User Story 2 - Visitor Uses English-Localized Property Search and Filters (Priority: P1) 🎯 MVP

**Goal**: Projects and units listing pages show filter labels, sort options, status badges, and empty-state messages in English when locale is English.

**Independent Test**: Switch to English, navigate to `/projects`, verify filter sidebar labels, sort dropdown, search placeholder, and project/unit card text are all in English. Status badges display "Available", "Reserved", "Sold".

### Tests

- [ ] T020 [P] [US2] Write Pest test for projects page localization in `tests/Feature/ProjectsLocalizationTest.php` — verify filter labels, status badges, empty-state display in correct locale. MUST FAIL before T021.

### Implementation

- [ ] T021 [P] [US2] Update `resources/views/livewire/frontend/projects-page.blade.php` — translatable filter headers (price, area, location, bedrooms, etc.), sort options, status badges, empty-state. Update `#[Title]` in `app/Livewire/Frontend/ProjectsPage.php` to use `__('public.projects.title')`
- [ ] T022 [P] [US2] Update `resources/views/livewire/frontend/project-single.blade.php` — translatable labels, status badges ("Available", "Reserved", "Sold")
- [ ] T023 [P] [US2] Update `resources/views/livewire/frontend/projects-map.blade.php` — translatable UI text. Update `#[Title]` in `app/Livewire/Frontend/ProjectsMap.php` to use `__('public.projects_map.title')`
- [ ] T024 [P] [US2] Update `resources/views/livewire/frontend/conponents/project-slider.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T025 [P] [US2] Update `resources/views/livewire/frontend/conponents/projects-tab.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T026 [P] [US2] Update `resources/views/livewire/frontend/conponents/unit-popup.blade.php` — replace hardcoded Arabic with `@lang('public.*')`
- [ ] T027 [P] [US2] Update `resources/views/livewire/frontend/conponents/unit-orderpopup.blade.php` — replace hardcoded Arabic with `@lang('public.*')`

**Checkpoint**: Project listing, single project, map, and all unit/project sub-components display correctly in both languages. Status badges localized. Pest tests pass.

---

## Phase 5: User Story 3 - Visitor Submits Contact Form in English (Priority: P2)

**Goal**: Contact form labels, validation messages, department dropdown, and success message display in English when locale is English.

**Independent Test**: Switch to English, navigate to `/contact-us`, verify form labels, placeholders, department options, and submission flow display in English. Submit invalid data — validation messages in English. Submit valid data — success message in English.

### Tests

- [ ] T028 [P] [US3] Write Pest test for contact form localization in `tests/Feature/ContactFormLocalizationTest.php` — verify form labels, validation messages, success message in correct locale. MUST FAIL before T029.

### Implementation

- [ ] T029 [P] [US3] Update `resources/views/livewire/frontend/contact-us.blade.php` — translatable form labels, placeholders, submit button
- [ ] T030 [US3] Update `app/Livewire/Frontend/ContactUs.php` — replace hardcoded Arabic validation `$messages` with `__('public.contact.*')` keys

**Checkpoint**: Contact form fully localized. Validation and success messages respond in correct locale. Pest tests pass.

---

## Phase 6: User Story 4 - Search Engine Indexes Localized Pages (Priority: P2)

**Goal**: Every public page includes proper hreflang annotations, correct `lang`/`dir` attributes, and canonical URLs for both language variants.

**Independent Test**: Inspect HTML source of any public page in both language modes. Verify `<html lang="ar" dir="rtl">` or `lang="en" dir="ltr"`, `<link rel="alternate" hreflang="..." >` for both locales, and `<link rel="canonical" ...>` pointing to the locale-neutral URL.

### Tests

- [ ] T031 [P] [US4] Write Pest test for SEO tags in `tests/Feature/SeoTagsTest.php` — verify hreflang, canonical, lang, dir attributes on all public pages in both locales. MUST FAIL before T032.

### Implementation

- [ ] T032 [US4] Verify `LocalizationHelper` generates correct hreflang and canonical tags for all 11 public routes (test edge cases: home page, routed pages, parameterized routes like `/project/{slug}`)
- [ ] T033 [US4] Verify all public pages have correct `lang` and `dir` attributes on `<html>` element in both Arabic and English modes
- [ ] T034 [US4] Verify non-public routes (CRM, login, API) are NOT affected by locale middleware

**Checkpoint**: SEO tags correct on all public pages. No duplicate content issues. Non-public routes unaffected. Pest tests pass.

---

## Phase 7: Polish & Cross-Cutting Concerns

**Purpose**: Verification, coverage scanning, and final QA

- [ ] T035 Create custom Artisan command `php artisan localization:check-strings` that scans all public Blade views and Livewire PHP files for any remaining hardcoded UI strings, cross-referencing against translation file keys
- [ ] T036 [P] Run `php artisan localization:check-strings` and resolve all reported missing translation keys
- [ ] T037 [P] Manually spot-check each public page in both Arabic and English for visual correctness (layout, direction, truncation, readability)
- [ ] T038 Verify ContentBlock-driven pages (About, Privacy, Terms) continue to display Arabic content correctly with no regressions
- [ ] T039 Verify existing Arabic routes and URLs continue to function exactly as before (no broken links or 404s)
- [ ] T040 Run full Pest test suite to confirm no regressions: `php artisan test`
- [ ] T041 Verify the Artisan translation cache can be built: `php artisan translate:cache`

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup — BLOCKS all user stories
- **User Stories (Phase 3-6)**: All depend on Foundational phase completion
  - US1 (Phase 3) and US2 (Phase 4) are both P1 — can proceed in parallel
  - US3 (Phase 5) and US4 (Phase 6) are P2 — can proceed after P1 or in parallel
- **Polish (Phase 7)**: Depends on all desired user stories being complete

### User Story Dependencies

- **US1 (Phase 3)**: Can start after Phase 2 — No dependencies on other stories
- **US2 (Phase 4)**: Can start after Phase 2 — No dependencies on other stories (independent page group)
- **US3 (Phase 5)**: Can start after Phase 2 — No dependencies on other stories
- **US4 (Phase 6)**: Can start after Phase 2 — Verifies output from T009 (layout) which is foundational

### Within Each User Story

- Test tasks MUST be written and FAIL before implementation
- All implementation tasks within a story are independent (different files) unless noted
- Translation keys used in Blade views must exist in both `lang/ar/public.php` and `lang/en/public.php` (created in Phase 1)

### Parallel Opportunities

- **Phase 1**: T001, T002 sequential (extract first, then translate). T003, T004 parallel [P].
- **Phase 2**: T005 (test), T006-T009 sequential (middleware registration → routes → layout). T007 is part of T006 implementation.
- **Phase 3 (US1)**: T010 (test), then T011, T013-T019 all [P] parallel. T012 depends on T011.
- **Phase 4 (US2)**: T020 (test), then T021-T027 all [P] parallel.
- **Phase 5 (US3)**: T028 (test), then T029 [P], T030 sequential.
- **Phase 6 (US4)**: T031 (test), then T032, T033, T034 all parallel [P].
- **Polish (Phase 7)**: T035, T036-T041 sequential with some [P] opportunities.

---

## Parallel Example: User Story 1

```bash
# Launch test first, then all Blade view updates in parallel:
Task: "T010: Write Pest test for language switcher"
Task: "T011: Update nav-bar.blade.php in resources/views/livewire/frontend/partials/"
Task: "T013: Update footer.blade.php in resources/views/livewire/frontend/partials/"
Task: "T014: Update home-page.blade.php in resources/views/livewire/frontend/"
Task: "T015: Update about.blade.php in resources/views/livewire/frontend/"
Task: "T016: Update services.blade.php in resources/views/livewire/frontend/"
Task: "T017: Update blog and blog-single in resources/views/livewire/frontend/"
Task: "T019: Update client-logos.blade.php in resources/views/livewire/frontend/conponents/"
```

## Parallel Example: User Story 2

```bash
# Launch test first, then all Blade view updates in parallel:
Task: "T020: Write Pest test for projects page localization"
Task: "T021: Update projects-page.blade.php and ProjectsPage.php"
Task: "T022: Update project-single.blade.php"
Task: "T023: Update projects-map.blade.php and ProjectsMap.php"
Task: "T024: Update project-slider.blade.php in conponents/"
Task: "T025: Update projects-tab.blade.php in conponents/"
Task: "T026: Update unit-popup.blade.php in conponents/"
Task: "T027: Update unit-orderpopup.blade.php in conponents/"
```

---

## Implementation Strategy

### MVP First (User Stories 1 + 2 — both P1)

1. Complete Phase 1: Setup (translation files, middleware stub, helper stub)
2. Complete Phase 2: Foundational (tests, middleware, routes, layout)
3. Complete Phase 3: User Story 1 (tests, general pages + language switcher)
4. Complete Phase 4: User Story 2 (tests, projects + filters + statuses)
5. **STOP and VALIDATE**: Test both P1 stories independently
6. Deploy/demo if ready

### Incremental Delivery

1. Setup + Foundational → Locale infrastructure ready (no visible change yet)
2. Add US1 → All general pages translatable with language switcher → **MVP Demo**
3. Add US2 → Full project/unit pages localized → **Full P1 Delivery**
4. Add US3 → Contact form localized → **P2 Delivery**
5. Add US4 → SEO verification → **Complete Delivery**

### Parallel Team Strategy

With multiple developers:

1. Team completes Phase 1 + Phase 2 together
2. Once Foundational is done:
   - Developer A: User Stories 1 + 4 (general pages + SEO verification)
   - Developer B: User Stories 2 + 3 (project pages + contact form)
3. Stories complete and integrate independently (no shared files between US1/US2 beyond infrastructure)

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story is independently completable and testable
- Tests use Pest and Laravel HTTP tests — written FIRST, MUST fail before implementation
- All translation keys reference `public.*` namespace (e.g., `@lang('public.nav.home')`)
- Arabic strings extracted from existing Blade views serve as the source of truth for `lang/ar/public.php`
- ContentBlock-driven pages (About, Privacy, Terms) display only Arabic content in this phase
- Invalid locale attempts are logged via structured JSON with context (request URI, IP)
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
