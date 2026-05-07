# Tasks: CRM Performance, Notifications & Leaderboard Fixes

## Phase 1: Setup

- [ ] T001 [P] Create migration for `leaderboard_adjustments` table in `database/migrations/2026_05_05_164233_create_leaderboard_adjustments_table.php`
- [ ] T002 [P] Create migration for `leaderboard_snapshots` table in `database/migrations/2026_05_05_164233_create_leaderboard_snapshots_table.php`
- [ ] T003 [P] Create `LeaderboardAdjustment` model in `app/Models/LeaderboardAdjustment.php`
- [ ] T004 [P] Create `LeaderboardSnapshot` model in `app/Models/LeaderboardSnapshot.php`
- [ ] T005 Create `LeaderboardTestDataSeeder` in `database/seeders/LeaderboardTestDataSeeder.php`

## Phase 2: Foundational

- [ ] T006 Implement transition-based tracking logic in `app/Services/TargetTrackingService.php`
- [ ] T007 [P] Create `LeaderboardService` to encapsulate score calculation and adjustment logic in `app/Services/LeaderboardService.php`
- [ ] T008 [P] Refine `NotificationService` to support multi-recipient and multi-channel dispatching in `app/Services/NotificationService.php`

## Phase 3: [US1] Admin Corrects Leaderboard Points

- [ ] T009 [US1] Create Filament resource for `LeaderboardAdjustment` in `app/Filament/Resources/LeaderboardAdjustmentResource.php`
- [ ] T010 [US1] Implement validation for non-negative totals in `LeaderboardAdjustmentResource`
- [ ] T011 [US1] Create `AdjustLeaderboardPoints` action in `app/Actions/AdjustLeaderboardPoints.php`
- [ ] T012 [P] [US1] Add point adjustment audit log view for Admins in `app/Filament/Resources/LeaderboardAdjustmentResource/Pages/ListLeaderboardAdjustments.php`
- [ ] T013 [US1] Write Pest tests for manual point adjustments in `tests/Feature/LeaderboardAdjustmentTest.php`

## Phase 4: [US2] Email Notifications for Alerts and Orders

- [ ] T014 [US2] Update `UnitOrderUpdated` notification to support `mail` and `database` channels in `app/Notifications/UnitOrderUpdated.php`
- [ ] T015 [US2] Implement dynamic email content generation in `app/Notifications/UnitOrderUpdated.php`
- [ ] T016 [US2] Trigger order notifications from `UnitOrderObserver` or `NotificationService`
- [ ] T017 [US2] Create `CRMAlertNotification` for system alerts in `app/Notifications/CRMAlertNotification.php`
- [ ] T018 [US2] Write Pest tests for notification delivery and recipient logic in `tests/Feature/OrderNotificationTest.php`

## Phase 5: [US3] Reservations Only Count "Sales Transaction" Status

- [ ] T019 [US3] Update `RefreshLeaderboardCommand` to use transition-based logic for status 2 in `app/Console/Commands/RefreshLeaderboardCommand.php`
- [ ] T020 [US3] Integrate `LeaderboardAdjustment` overrides into `RefreshLeaderboardCommand`
- [ ] T021 [US3] Schedule `leaderboard:refresh` command in `routes/console.php`
- [ ] T022 [US3] Write unit tests for `RefreshLeaderboardCommand` in `tests/Unit/RefreshLeaderboardCommandTest.php`

## Phase 6: [US4] Daily Leaderboard View Shows Historical Data

- [ ] T023 [US4] Add date picker to `Leaderboard` Livewire component in `resources/views/livewire/mannager/leaderboard.blade.php`
- [ ] T024 [US4] Update `Leaderboard` component logic to filter snapshots by selected date in `app/Livewire/Mannager/Leaderboard.php`
- [ ] T025 [US4] Implement "No data for this date" empty state in `resources/views/livewire/mannager/leaderboard.blade.php`

## Phase 7: [US5] Top Performers on Sales Manager Home Page

- [ ] T026 [US5] Create `TopPerformersWidget` for Filament dashboard in `app/Filament/Widgets/TopPerformersWidget.php`
- [ ] T027 [US5] Register `TopPerformersWidget` for Sales Manager role in `app/Providers/Filament/ManagerPanelProvider.php`
- [ ] T028 [US5] Style the widget to show ranked agents with names and scores

## Phase 8: [US6] CRM Performance Improvement

- [ ] T029 [US6] Optimize `UnitOrder` index queries with eager loading in `app/Filament/Resources/UnitOrderResource.php`
- [ ] T030 [US6] Implement caching for composite score calculations in `LeaderboardService`
- [ ] T031 [US6] Audit and fix N+1 query issues in `ManagerDashboard` widgets in `app/Livewire/Mannager/ManagerDashboard.php`

## Phase 9: Polish & Cross-Cutting Concerns

- [ ] T032 Rename "Reservations / حجوزات" to "Sales Transactions / معاملات بيعية" in all lang files (`lang/ar/` and `lang/en/`)
- [ ] T033 Verify RTL layout for the new date picker and performers widget
- [ ] T034 Final manual walkthrough of all user stories per `quickstart.md`

## Dependencies

- Phase 1 & 2 must be completed before any User Story phases.
- [US1] and [US3] are tightly coupled via `LeaderboardService`.
- [US4] depends on [US3] for historical snapshot data.
- [US5] depends on [US3] for accurate current standings.

## Parallel Execution

- US1, US2, and US6 can be worked on in parallel once Phase 1 & 2 are complete.
- Setup tasks T001-T004 can be run in parallel.
