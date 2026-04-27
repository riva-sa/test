---
description: "Task list template for feature implementation"
---

# Tasks: Platform Speed Optimization

**Input**: Design documents from `/specs/004-platform-speed-optimization/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Implementation MUST include automated tests using Pest. Every PR should verify logic via unit/functional tests and Livewire state transitions.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [x] T001 Install `intervention/image` package via Composer
- [x] T002 [P] Create migration for `optimized_images` table
- [x] T003 [P] Create migration for `performance_metrics` table
- [x] T004 Create `config/image-optimization.php`

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T005 Create `OptimizedImage` model in `app/Models/OptimizedImage.php`
- [x] T006 Create `PerformanceMetric` model in `app/Models/PerformanceMetric.php`
- [x] T007 Run database migrations (`php artisan migrate`)

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 2 - Fast Image Rendering Across the Platform (Priority: P1) 🎯 MVP

**Goal**: Optimize image delivery so that above-the-fold images render within 1 second and total image payload size is reduced by at least 40%.

**Independent Test**: Upload an image, verify background processing generates WebP variants, and check that `MediaHelper::getUrl` returns the optimized path. Run batch command and ensure existing images are processed.

### Implementation for User Story 2

- [x] T008 [US2] Implement `ImageOptimizationService` in `app/Services/ImageOptimizationService.php`
- [x] T009 [US2] Modify `app/Helpers/MediaHelper.php` to serve optimized WebP variants
- [x] T010 [US2] Modify `app/Http/Controllers/ImageController.php` to handle WebP-aware redirects
- [x] T011 [US2] Create `OptimizeImageJob` in `app/Jobs/OptimizeImageJob.php` to queue processing
- [x] T012 [US2] Create `OptimizeImagesCommand` in `app/Console/Commands/OptimizeImagesCommand.php` for batch processing
- [x] T013 [US2] Create `ImageOptimizationStatusCommand` in `app/Console/Commands/ImageOptimizationStatusCommand.php`
- [x] T014 [US2] Update `app/Providers/AppServiceProvider.php` to register `ImageOptimizationService`
- [x] T015 [US2] Add Pest tests for `ImageOptimizationService` and `MediaHelper`

**Checkpoint**: At this point, image optimization backend is fully functional and testable independently

---

## Phase 4: User Story 1 & 3 - Fast Page Load & Preserved Interactivity (Priority: P1)

**Goal**: External pages (homepage, projects, modules) load in under 2 seconds without using `#[Lazy]` to preserve interactivity.

**Independent Test**: Navigate to the homepage and projects page. Verify queries are minimized, images use `loading="lazy"` and `srcset`, and interactive elements function correctly.

### Implementation for User Story 1 & 3

- [x] T016 [P] [US1] Optimize `app/Livewire/Frontend/ProjectsPage.php` queries and caching
- [x] T017 [P] [US1] Optimize `app/Livewire/Frontend/ProjectSingle.php` queries
- [x] T018 [P] [US1] Optimize `app/Livewire/Frontend/Conponents/ProjectSlider.php` queries
- [x] T019 [P] [US1] Update `resources/views/livewire/frontend/home-page.blade.php` (add width, height, srcset, loading="lazy")
- [x] T020 [P] [US1] Update `resources/views/livewire/frontend/projects-page.blade.php` (add width, height, srcset, loading="lazy")
- [x] T021 [P] [US1] Update `resources/views/livewire/frontend/project-single.blade.php` (add width, height, srcset, loading="lazy")
- [x] T022 [P] [US1] Update `resources/views/livewire/frontend/projects-map.blade.php` (add width, height, srcset, loading="lazy")
- [x] T023 [P] [US1] Update `resources/views/livewire/frontend/conponents/project-slider.blade.php` (add srcset, sizes)
- [x] T024 [P] [US1] Update `resources/views/livewire/frontend/conponents/projects-tab.blade.php` (add srcset, sizes)
- [x] T025 [P] [US1] Update static image tags in `resources/views/livewire/frontend/partials/nav-bar.blade.php` and `footer.blade.php`
- [x] T026 [US1] Test all interactive components on modified pages to verify US3 compliance (no broken interactions)

**Checkpoint**: At this point, User Stories 1, 2 AND 3 should all work independently

---

## Phase 5: User Story 4 - Consistent Performance Under Load (Priority: P2)

**Goal**: Built-in performance monitoring tracks page load times and alerts on regressions.

**Independent Test**: Load a monitored page, verify metrics are stored in the database, and run the report command to see statistics.

### Implementation for User Story 4

- [x] T027 [US4] Create `PerformanceMonitorMiddleware` in `app/Http/Middleware/PerformanceMonitorMiddleware.php`
- [x] T028 [US4] Register middleware on external routes in `routes/web.php` and `bootstrap/app.php`
- [x] T029 [US4] Create `PerformanceReportCommand` in `app/Console/Commands/PerformanceReportCommand.php`
- [x] T030 [US4] Schedule pruning of old performance metrics in Console Kernel / `bootstrap/app.php`

**Checkpoint**: All user stories should now be independently functional

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T031 Run standard test suite (`pest`) to ensure no regressions
- [x] T032 Verify quickstart guide works as documented
- [x] T033 [US1] Update `resources/views/livewire/frontend/conponents/unit-popup.blade.php` with responsive images
- [x] T034 [US4] Implement performance regression alerting in `PerformanceAlertCommand` and schedule it

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies - can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion - BLOCKS all user stories
- **User Stories (Phase 3+)**: All depend on Foundational phase completion
  - User Story 2 provides the image optimization backend which User Story 1 frontend changes depend on.
- **Polish (Final Phase)**: Depends on all desired user stories being complete

### User Story Dependencies

- **User Story 2 (P1)**: Can start after Foundational (Phase 2) - No dependencies on other stories
- **User Story 1 & 3 (P1)**: Frontend `srcset` changes depend on US2 `MediaHelper` changes. Query optimizations can be done in parallel.
- **User Story 4 (P2)**: Can start after Foundational (Phase 2) - No dependencies on other stories

### Parallel Opportunities

- Migrations and config setup can run in parallel
- Query optimization tasks (T016, T017, T018) can run in parallel
- Blade template updates (T019-T025) can run in parallel
- Different user stories can be worked on in parallel by different team members

---

## Implementation Strategy

### MVP First (User Story 2 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL - blocks all stories)
3. Complete Phase 3: User Story 2 (Image Optimization Backend)
4. **STOP and VALIDATE**: Test backend image generation independently
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add User Story 2 (Backend Images) → Test independently → Deploy/Demo (MVP!)
3. Add User Story 1 & 3 (Frontend attributes & Query caching) → Test independently → Deploy/Demo
4. Add User Story 4 (Monitoring) → Test independently → Deploy/Demo
5. Each story adds value without breaking previous stories
