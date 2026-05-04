# Tasks: CRM Enhancements & Additions

**Input**: Design documents from `/specs/005-crm-enhancements/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Implementation MUST include automated tests using Pest. Every PR should verify logic via unit/functional tests and Livewire state transitions.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Models**: `app/Models/`
- **Services**: `app/Services/`
- **Actions**: `app/Actions/`
- **Events**: `app/Events/`
- **Livewire CRM**: `app/Livewire/Mannager/`
- **Blade CRM views**: `resources/views/livewire/mannager/`
- **Frontend views**: `resources/views/livewire/frontend/`
- **Filament**: `app/Filament/Resources/`
- **Migrations**: `database/migrations/`
- **Tests**: `tests/Feature/`, `tests/Unit/`
- **Translations**: `lang/ar/`, `lang/en/`
- **Routes**: `routes/web.php`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Install new dependencies and configure broadcasting infrastructure

- [x] T001 Install and configure Laravel Reverb for real-time broadcasting (`php artisan install:broadcasting`)
- [x] T002 Generate migration for `sales_targets` table (done via T029)
- [x] T003 [P] Install Trix editor assets via CDN in `resources/views/layouts/custom.blade.php`
- [x] T004 Generate migration to add `contact_phone` to `projects` table (done via T043)
- [x] T005a Generate migration for `leaderboard_configs` table (done via Phase 6)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

- [x] T004 Configure `.env` broadcasting settings: set `BROADCAST_CONNECTION=reverb` with `REVERB_APP_ID`, `REVERB_APP_KEY`, `REVERB_APP_SECRET`, `REVERB_HOST`, `REVERB_PORT` values
- [x] T005 Create Laravel Echo configuration in `resources/js/echo.js` — initialize Echo with Reverb connection settings and import it in `resources/js/app.js`
- [x] T006 [P] Create migration `database/migrations/XXXX_create_order_status_transitions_table.php` with columns: id, unit_order_id (FK unit_orders CASCADE), user_id (FK users SET NULL nullable), from_status (tinyint unsigned NOT NULL), to_status (tinyint unsigned NOT NULL), created_at (timestamp). Add composite index on (user_id, from_status, created_at) and index on (unit_order_id)
- [x] T007 [P] Create `app/Models/OrderStatusTransition.php` model with `const UPDATED_AT = null`, fillable fields, belongsTo relationships to UnitOrder (order) and User (user). Add statusTransitions() hasMany relationship to `app/Models/UnitOrder.php`
- [x] T008 Update `app/Observers/UnitOrderObserver.php` — in the `updating()` method, when the `status` field is dirty, insert a row into `order_status_transitions` with from_status (original value), to_status (new value), user_id from `auth()->id()` or `$model->last_action_by_user_id`. Add structured JSON log via `Log::info()` with keys: event, order_id, from_status, to_status, user_id

**Checkpoint**: Foundation ready — broadcasting configured, status transition logging active, user story implementation can begin

---

## Phase 3: User Story 1 — Unified Status Color System (Priority: P1) 🎯 MVP

**Goal**: Replace Tailwind color names with spec-defined hex color codes across ALL CRM pages for consistent status display

**Independent Test**: Navigate through order list, dashboard charts, board view, customer profile, order details — verify each status uses the correct hex code from the spec color mapping table

### Implementation for User Story 1

- [x] T009 [US1] Add `STATUS_COLORS` constant array to `app/Models/UnitOrder.php` mapping status integers 0-5 to hex codes (#3B82F6, #F97316, #5457E3, #9CA3AF, #22C55E, #EAB308). Update `statusColor()` method to return hex codes from this constant instead of Tailwind color names. Update `STATUS_LABELS` if needed to ensure consistency
- [x] T010 [US1] Update all Blade views that use `statusColor()` to apply hex colors via inline `style` attributes instead of Tailwind utility classes. Files to update: `resources/views/livewire/mannager/manage-orders.blade.php`, `resources/views/livewire/mannager/order-details.blade.php`, `resources/views/livewire/mannager/customer-profile.blade.php`, `resources/views/livewire/mannager/manager-dashboard.blade.php`, and any other views referencing status colors
- [x] T011 [P] [US1] Update `app/Filament/Resources/UnitOrderResource/Widgets/UnitOrderStats.php` chart configuration to use hex color codes from `UnitOrder::STATUS_COLORS` for dashboard chart segments and trend lines
- [x] T012 [US1] Write Pest test in `tests/Feature/StatusColorsTest.php` verifying `UnitOrder::statusColor()` returns correct hex code for each of the 6 statuses and that `STATUS_COLORS` constant contains exactly 6 entries matching the spec

**Checkpoint**: All status colors display consistently with hex codes across every CRM page

---

## Phase 4: User Story 2 — Enhanced Customer Profile (Priority: P1)

**Goal**: Show comprehensive order details in customer profile including project, unit, status with colors, dates, assigned sales rep, notes count, order source, and marketing data

**Independent Test**: Navigate to `/crm/customers/{phone}` for a customer with multiple orders — verify summary card with total orders/latest status/dates and all order detail columns display correctly

### Implementation for User Story 2

- [x] T013 [US2] Enhance `app/Livewire/Mannager/CustomerProfile.php` — add eager loading for `unit`, `project`, `assignedSalesUser`, `notes` (withCount) relationships on the orders query. Add computed properties for summary card: total orders count, latest order status, first and last order dates. Ensure `ad_set` marketing source value is available per order
- [x] T014 [US2] Update `resources/views/livewire/mannager/customer-profile.blade.php` — render enhanced order table with columns: project name, unit details, status (with hex color from `UnitOrder::STATUS_COLORS`), assigned sales rep name, order source, ad_set (المجموعة الاعلانية), creation date, last update date, notes count. Add summary card section above the orders table showing total orders, latest status, join date, last activity
- [x] T015 [US2] Write Pest Livewire test in `tests/Feature/CustomerProfileTest.php` verifying the component loads orders with all relationships, displays summary card data correctly, and renders all expected columns for a customer with multiple orders

**Checkpoint**: Customer profile shows complete order history with full details and summary statistics

---

## Phase 5: User Story 3 — Notification System (Priority: P1)

**Goal**: Managers send individual, group, and announcement notifications with rich text content via Trix editor. Employees receive real-time notifications via bell icon in CRM header. Read/unread tracking with sender visibility into who has read each notification. Dedicated announcements page with "load more" pagination.

**Independent Test**: A manager sends a group notification — verify bell icon updates in real time for recipients, notification appears in panel and announcements page, rich text renders correctly, read status is tracked and visible to sender

### Implementation for User Story 3

- [x] T016 [P] [US3] Create migration `database/migrations/XXXX_create_crm_notifications_table.php` with columns: id (bigint PK), type (enum: individual/group/announcement/task NOT NULL), sender_id (FK users NOT NULL), title (varchar 255 NOT NULL), content (text NOT NULL for sanitized HTML), timestamps. Add indexes on (type), (sender_id), (created_at)
- [x] T017 [P] [US3] Create migration `database/migrations/XXXX_create_crm_notification_recipients_table.php` with columns: id (bigint PK), notification_id (FK crm_notifications CASCADE), user_id (FK users CASCADE), read_at (timestamp nullable), created_at (timestamp). Add unique index on (notification_id, user_id), index on (user_id, read_at)
- [x] T018 [P] [US3] Create `app/Models/CrmNotification.php` with fillable [type, sender_id, title, content], enum cast for type, relationships: sender() belongsTo User, recipients() hasMany CrmNotificationRecipient, users() belongsToMany User via crm_notification_recipients pivot
- [x] T019 [P] [US3] Create `app/Models/CrmNotificationRecipient.php` with fillable [notification_id, user_id, read_at], datetime cast for read_at, relationships: notification() belongsTo CrmNotification, user() belongsTo User. Add scope `unread()` for `whereNull('read_at')`
- [x] T020 [US3] Update `app/Models/User.php` to add relationships: sentNotifications() hasMany CrmNotification (foreign key sender_id), crmNotificationRecipients() hasMany CrmNotificationRecipient, add helper method `unreadCrmNotificationCount()` returning count of CrmNotificationRecipient where read_at is null
- [x] T021 [P] [US3] Create `app/Events/NewCrmNotification.php` broadcast event implementing ShouldBroadcast — broadcastOn() returns PrivateChannel per recipient, payload includes notification id, type, title, sender_name, unread_count
- [x] T022 [US3] Create `app/Services/CrmNotificationService.php` with send(), sendToGroup(), sendAnnouncement(), markAsRead() methods. Includes HTML sanitization and structured JSON logging
- [x] T023 [US3] Add broadcast channel authorization in `routes/channels.php`: `Broadcast::channel('notifications.{id}', fn (User $user, int $id) => $user->id === $id)`
- [x] T024 [US3] Create `app/Livewire/Mannager/NotificationBell.php` Livewire component — loads unread count on mount, listens to Echo private channel for real-time updates via `getListeners()`. Renders bell icon with unread count badge and dropdown panel showing latest 10 notifications with read/unread visual distinction. Create view at `resources/views/livewire/mannager/notification-bell.blade.php`
- [x] T025 [US3] Include NotificationBell component in the CRM sidebar header alongside existing order notification bell in `resources/views/livewire/mannager/partials/sidebar.blade.php`
- [x] T026 [US3] Create `app/Livewire/Mannager/Notifications.php` page component for managers — notification type selector, searchable user picker, Trix rich text editor, send button. Calls CrmNotificationService. Below form displays sent notifications with recipient read/unread status. Route added in `routes/web.php`. View at `resources/views/livewire/mannager/notifications.blade.php`
- [x] T027 [US3] Create `app/Livewire/Mannager/Announcements.php` page component — queries CrmNotificationRecipient for logged-in user, "load more" pagination (15 per page), renders rich text content with `{!! !!}`, read/unread styling, marks as read on view. Route added in `routes/web.php`. View at `resources/views/livewire/mannager/announcements.blade.php`
- [x] T028 [US3] Write Pest tests: `tests/Unit/CrmNotificationServiceTest.php` — test send() creates notification + recipients, test sendToGroup() resolves sales-role users, test markAsRead() sets read_at, test HTML sanitization strips script tags

**Checkpoint**: Full notification system functional — managers compose rich text notifications, bell updates in real time, read tracking works, announcements page shows paginated history

---

## Phase 6: User Story 4 — Sales Targets System (Priority: P2)

**Goal**: Managers set performance targets per sales rep across 4 types (monthly orders, daily orders, reservations, sales). Reps see progress on dashboard. Progress auto-computed from order_status_transitions. Leaderboard ranks reps by weighted composite score with configurable weights. Historical monthly performance viewable.

**Independent Test**: Manager sets all 4 target types for a rep. Rep transitions an order from جديد to طلب مفتوح. Verify daily and monthly progress increment on dashboard. View leaderboard with adjusted weights. Select past month to verify historical recomputation.

### Implementation for User Story 4

- [x] T029 [P] [US4] Create migration `database/migrations/XXXX_create_sales_targets_table.php` with columns: id (bigint PK), user_id (FK users CASCADE), type (enum: monthly_orders/daily_orders/reservations/sales NOT NULL), target_value (unsigned int NOT NULL default 0), timestamps. Add unique index on (user_id, type)
- [x] T005 Run migrations to update database schema
- [x] T006 Create `SalesTarget` model in `app/Models/SalesTarget.php`
- [x] T007 Create `TargetProgress` model in `app/Models/TargetProgress.php`
- [x] T007a [P] Create `LeaderboardConfig` model in `app/Models/LeaderboardConfig.php`
- [x] T018 [US4] Create `TargetTrackingService.php` in `app/Services/` with target increment and historical recomputation logic
- [x] T018a [P] [US4] Create `LeaderboardService.php` in `app/Services/` with weighted composite score calculation and configurable weights from `LeaderboardConfig`
- [x] T019 [US4] Update `UnitOrderObserver.php` to call `TargetTrackingService` on status changes
- [x] T020 [P] [US4] Create Livewire component `TargetsManagement.php` for managers to configure targets and leaderboard weights (`LeaderboardConfig`)
- [x] T021 [US4] Update `ManagerDashboard.php` to include the weighted targets leaderboard view (composite scores, configurable weights)
- [x] T021a [US4] Create Livewire component `TargetHistoryPage.php` for historical monthly performance view (recomputed from order history)
- [x] T022 [US4] Update Sales Representative dashboard view to display their individual progress
- [x] T023 [US4] Create Pest tests in `tests/Feature/TargetTrackingTest.php` to verify increment logic, boundary conditions, weighted scoring, and historical recomputation
- [x] T032 [P] [US4] Create `database/seeders/LeaderboardConfigSeeder.php` that creates or updates 4 rows (one per target_type) with default weight of 25.00 each. Register in DatabaseSeeder if appropriate
- [x] T033 [US4] Update `app/Models/User.php` to add salesTargets() hasMany SalesTarget relationship and statusTransitions() hasMany OrderStatusTransition relationship
- [x] T034 [US4] Create `app/Services/TargetTrackingService.php` with methods: `getProgress(int $userId, string $type, Carbon $periodStart, Carbon $periodEnd)` queries order_status_transitions counting rows matching the target type rules (from_status=0 for daily/monthly, to_status=2 for reservations, to_status=4 for sales); `getAllProgress(int $userId)` returns array of all 4 types with current count and target value using current day/month boundaries; `getLeaderboard(?Carbon $month = null)` computes weighted composite score (Σ weight × progress/target) for all sales-role users using LeaderboardConfig weights, returns ranked collection; `getHistoricalPerformance(int $userId, Carbon $month)` returns all 4 type counts for a specified past month. Reference data-model.md Key Queries section for SQL patterns
- [x] T035 [US4] Create `app/Livewire/Mannager/SalesTargets.php` page component — shows table of all sales reps (users with 'sales' role) with editable inline target values for each of the 4 types. Includes "apply default value to all reps" bulk action. On save, creates/updates SalesTarget records. Role-restricted to sales_manager/Admin. Add route `Route::get('/crm/targets', SalesTargets::class)->name('manager.targets')` in `routes/web.php`. Create view at `resources/views/livewire/mannager/sales-targets.blade.php`
- [x] T036 [US4] Create `app/Livewire/Mannager/Leaderboard.php` page component — displays ranked table of all sales reps by composite score. Includes: editable weight controls per target type (sum validated to 100%), progress bars per target type per rep, month selector for historical view (calls TargetTrackingService::getHistoricalPerformance). Add route `Route::get('/crm/leaderboard', Leaderboard::class)->name('manager.leaderboard')` in `routes/web.php`. Create view at `resources/views/livewire/mannager/leaderboard.blade.php`
- [x] T037 [US4] Update `app/Livewire/Mannager/ManagerDashboard.php` — for users with 'sales' role, add target progress section showing 4 progress bars (current count / target value) for daily orders, monthly orders, reservations, and sales using TargetTrackingService::getAllProgress(). Update `resources/views/livewire/mannager/manager-dashboard.blade.php` with the target widgets section
- [x] T038 [US4] Write Pest tests: `tests/Unit/TargetTrackingServiceTest.php` — test getProgress correctly counts from_status=0 transitions for daily/monthly, to_status=2 for reservations, to_status=4 for sales; test daily boundary (only today's transitions); test monthly boundary (only current month); test getLeaderboard composite score calculation; test getHistoricalPerformance for past month. `tests/Feature/SalesTargetsTest.php` — Livewire test for SalesTargets page (manager can set targets), Leaderboard page (ranks correctly, weight changes update ranking)

**Checkpoint**: Complete targets system — managers configure targets, reps view progress, leaderboard ranks by weighted composite, historical data viewable

---

## Phase 7: User Story 5 — Sales Representatives Status Columns (Priority: P2)

**Goal**: Sales reps page shows per-status order count columns for each rep (6 columns matching the 6 order statuses)

**Independent Test**: Open `/crm/sales-managers` — verify each rep row displays correct order counts for all 6 statuses, including "0" for statuses with no orders

### Implementation for User Story 5

- [x] T039 [US5] Update `app/Livewire/Mannager/SalesManagers.php` — add a method that queries order counts grouped by status for each displayed sales rep: `UnitOrder::where('assigned_sales_user_id', $userId)->selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')`. Pass counts array to view for each rep. Ensure all 6 status keys (0-5) have default 0 values
- [x] T040 [US5] Update `resources/views/livewire/mannager/sales-managers.blade.php` — add 6 new columns to the reps table with Arabic headers (جديد, طلب مفتوح, قائمة انتظار, مغلق, مكتمل, معاملات بيعية). Display count values with colored badges using hex codes from `UnitOrder::STATUS_COLORS`. Show "0" for statuses with no orders

**Checkpoint**: Sales reps page shows accurate status count breakdown per representative

---

## Phase 8: User Story 6 — Remove Project Manager from Assigned Orders View (Priority: P2)

**Goal**: Remove project_manager role from order assignment visibility for NEW orders. Existing orders retain access via grandfather clause using `project.sales_manager_id`.

**Independent Test**: Create a new order — verify project_manager role cannot see it via assignment logic. Verify an existing order (created before change) remains accessible to its project manager.

### Implementation for User Story 6

- [x] T041 [US6] Update `scopeAccessibleBy()` in `app/Models/UnitOrder.php` — remove `project_manager` from the role list that gets unrestricted "see all" access. For the sales user sub-query that joins projects via `sales_manager_id`, add a date-based condition: only orders where `unit_orders.created_at` is before the cutoff date (grandfather clause). New orders use only: assigned_sales_user_id match, explicit OrderPermission records, or auto-distribution assignment
- [x] T042 [US6] Audit UI elements in `app/Livewire/Mannager/` components that display project manager in assignment dropdowns, filter options, or role selectors — hide or remove the project_manager option from new order assignment workflows. Preserve display of existing project manager data for historical orders. Write Pest test in `tests/Feature/OrderPermissionTest.php` verifying: new orders are NOT accessible to project_manager role via scopeAccessibleBy, existing orders (created before cutoff) ARE still accessible

**Checkpoint**: New orders follow new assignment rules; existing orders grandfathered via sales_manager_id

---

## Phase 9: User Story 7 — Project Contact Number (Priority: P2)

**Goal**: Each project has a contact phone number. External projects page uses this number for WhatsApp and call buttons instead of the sales manager's phone.

**Independent Test**: Add a contact phone to a project via Filament admin, navigate to the external project page — verify WhatsApp links to `wa.me/<contact_phone>` and call links to `tel:<contact_phone>`. Remove the number — verify buttons are hidden.

### Implementation for User Story 7

- [x] T043 [US7] Create migration to add `contact_phone` string column (nullable) to `projects` table. Run migration
- [x] T044 [US7] Update `app/Models/Project.php` `$fillable` to include `contact_phone`. Update `app/Filament/Resources/ProjectResource.php` form schema to include a `TextInput` for `contact_phone`
- [x] T045 [US7] Update frontend components (`resources/views/frontend/project.blade.php`, `resources/views/frontend/projects.blade.php`, etc.) to use `$project->contact_phone ?? $project->salesManager?->phone` for all WhatsApp and Call CTA buttons, ensuring a fallback if `contact_phone` is null
- [x] T046 [US7] Update `resources/views/livewire/frontend/project-single.blade.php` — replace `$project->salesManager->phone` with `$project->contact_phone` in the WhatsApp button href (`https://wa.me/{{ $project->contact_phone }}`) and call button href (`tel:{{ $project->contact_phone }}`). Add fallback to `setting('site_phone')` when contact_phone is null. Hide both buttons entirely when neither contact_phone nor fallback is available

**Checkpoint**: Projects have dedicated contact numbers used on external project pages

---

## Phase 10: User Story 8 — Order Source: Ad Group Name (Priority: P3)

**Goal**: Display ad group name (ad_set / المجموعة الاعلانية) instead of ad name across all order-related pages

**Independent Test**: View orders list, order details, and customer profile — verify source column/field shows ad_set value with Arabic label. Verify filtering/sorting by source works.

### Implementation for User Story 8

- [x] T047 [US8] Audit all Blade views and Livewire components that display order source information — replace any remaining `ad_name` or `campaign_name` references with `ad_set`. Files to check: `app/Livewire/Mannager/ManageOrders.php`, `resources/views/livewire/mannager/manage-orders.blade.php`, `resources/views/livewire/mannager/order-details.blade.php`, `resources/views/livewire/mannager/customer-profile.blade.php`, `app/Filament/Resources/UnitOrderResource.php`, any export files in `app/Exports/`. Update column header labels to "المجموعة الاعلانية". Note: commit 365067a2 partially implemented this — verify completeness rather than reimplementing
- [x] T048 [US8] Verify sorting and filtering on the orders page work correctly with the `ad_set` field — update any `$sortField`, `$filterField`, or search query references in `app/Livewire/Mannager/ManageOrders.php` to use `ad_set`

**Checkpoint**: All order source displays show ad group name consistently across the CRM

---

## Phase 11: User Story 9 — Fix YouTube Video Embedding (Priority: P3)

**Goal**: YouTube URLs saved on project media render as embedded video players on the frontend project page, supporting standard, shortened, embed, and shorts URL formats

**Independent Test**: Add a YouTube URL to a project via Filament admin, navigate to the project frontend page — verify the video displays as a playable embedded iframe player. Test with standard, shortened, and embed URLs.

### Implementation for User Story 9

- [x] T049 [P] [US9] Add `getYoutubeEmbedUrlAttribute()` accessor to `app/Models/ProjectMedia.php` — use regex to extract the 11-char video ID from standard (`youtube.com/watch?v=`), shortened (`youtu.be/`), embed (`youtube.com/embed/`), and shorts (`youtube.com/shorts/`) URL formats. Return `https://www.youtube.com/embed/{videoId}` or null for invalid/missing URLs
- [x] T050 [US9] Update `resources/views/livewire/frontend/project-single.blade.php` — add a YouTube video section after the image gallery. Iterate `$project->media` entries that have a non-null `youtube_url`, render each as a responsive 16:9 iframe using `$media->youtube_embed_url`. Show a placeholder if the URL is invalid. Uncomment the `youtube_url` TextInput field in `app/Filament/Resources/ProjectResource.php` to enable admin entry
- [x] T051 [US9] Write Pest test in `tests/Unit/YoutubeEmbedTest.php` — verify the accessor correctly extracts video IDs from all 4 URL formats (standard, short, embed, shorts), returns null for invalid URLs, returns null for empty strings, and produces correct `youtube.com/embed/` output URLs

**Checkpoint**: YouTube videos render correctly on project frontend pages

---

## Phase 12: User Story 10 — Employee Password Reset (Priority: P3)

**Goal**: Managers trigger a password reset email for any employee from the sales reps page. Employee clicks the emailed link and sets a new password.

**Independent Test**: Click "Reset Password" for a rep on `/crm/sales-managers` — verify reset email is sent to the employee's email. Click the link — verify the password reset form loads and the employee can set a new password and log in.

### Implementation for User Story 10

- [x] T052 [P] [US10] Create `app/Actions/ResetEmployeePasswordAction.php` — accepts a User model, calls `Password::broker()->sendResetLink(['email' => $user->email])`, returns status (success/failure). Logs the event via `Log::info()` with structured JSON: target_user_email, target_user_id, triggered_by_user_id
- [x] T053 [US10] Update `app/Livewire/Mannager/SalesManagers.php` — add a `resetPassword(int $userId)` method that loads the User, calls ResetEmployeePasswordAction, and shows a success/error flash message. Role-restrict to sales_manager/Admin. Update `resources/views/livewire/mannager/sales-managers.blade.php` with a "إعادة تعيين كلمة المرور" (Reset Password) button per rep row with confirmation dialog
- [x] T054 [US10] Add password reset routes in `routes/web.php`: `GET /crm/reset-password/{token}` renders a password reset form (use a simple Blade view at `resources/views/auth/crm-reset-password.blade.php` with email, password, password_confirmation fields). `POST /crm/reset-password` processes the reset using `Password::broker()->reset()` and redirects to `/crm/login` with success message
- [x] T055 [US10] Write Pest test in `tests/Feature/PasswordResetTest.php` — test ResetEmployeePasswordAction generates a reset token and queues email notification, test the reset form renders with valid token, test password can be successfully reset and user can log in with new password

**Checkpoint**: Managers trigger password resets; employees reset passwords via emailed link

---

## Phase 13: Polish & Cross-Cutting Concerns

**Purpose**: Translations, RTL verification, logging audit, and final quality assurance

- [x] T056 [P] Add all new Arabic translation keys to `lang/ar/crm.php` and English translations to `lang/en/crm.php` — notification UI labels (إشعارات, إعلانات, إرسال, مقروء, غير مقروء), target type names (الطلبات الشهرية, الطلبات اليومية, الحجوزات, المبيعات), leaderboard labels (لوحة المتصدرين, الأوزان, النتيجة), status column headers, button labels (إعادة تعيين كلمة المرور, إرسال إشعار), page titles, form labels
- [x] T057 [P] Verify RTL layout integrity for all new views: notifications page, announcements page, targets page, leaderboard page, notification bell dropdown, enhanced customer profile columns, status columns in sales reps table. Fix any alignment, margin, or text-direction issues
- [x] T058 [P] Verify structured JSON logging is in place for all critical events per constitution principle V: order status transitions in UnitOrderObserver (event, order_id, from_status, to_status, user_id), notification sends in CrmNotificationService (type, sender_id, recipient_count), password reset triggers in ResetEmployeePasswordAction (target_user_id, triggered_by). Confirm logs contain entity IDs and actor information
- [x] T059 Run full Pest test suite (`php artisan test`) to verify all new tests pass with no regressions on existing tests. Run Laravel Pint (`./vendor/bin/pint`) to ensure PSR-12 compliance
- [x] T060 Run quickstart.md validation end-to-end: follow setup steps (install deps, configure env, migrate, seed, build assets, start servers), verify all 5 new routes load correctly, test the independent test criteria for each user story

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion — BLOCKS all user stories
- **User Stories (Phase 3-12)**: All depend on Foundational phase completion
  - User stories can proceed in parallel (if staffed)
  - Or sequentially in priority order (P1 → P2 → P3)
- **Polish (Phase 13)**: Depends on all desired user stories being complete

### User Story Dependencies

- **US1 (P1) Status Colors**: Can start after Phase 2. No dependencies on other stories. **MVP candidate.**
- **US2 (P1) Customer Profile**: Can start after Phase 2. Benefits from US1 (correct colors in profile) but not blocked by it
- **US3 (P1) Notifications**: Can start after Phase 2. Requires Reverb from Phase 1-2. Independent of US1/US2
- **US4 (P2) Sales Targets**: Can start after Phase 2. Uses order_status_transitions from Phase 2. Independent of P1 stories
- **US5 (P2) Status Columns**: Can start after Phase 2. Benefits from US1 colors. Independent otherwise
- **US6 (P2) Remove PM**: Can start after Phase 2. Touches `UnitOrder.php` — coordinate with US1 if developing in parallel
- **US7 (P2) Contact Number**: Can start after Phase 2. Fully independent of all other stories
- **US8 (P3) Ad Set Display**: Can start after Phase 2. Fully independent. Partially implemented (commit 365067a2)
- **US9 (P3) YouTube Embed**: Can start after Phase 2. Fully independent of all other stories
- **US10 (P3) Password Reset**: Can start after Phase 2. Fully independent of all other stories

### Within Each User Story

- Migrations before models
- Models before services
- Services before Livewire components
- Livewire components before views/routes
- Core implementation before tests
- Story complete before moving to next priority

### Parallel Opportunities

- Setup: T002 and T003 can run in parallel
- Foundational: T006 and T007 can run in parallel
- Once Phase 2 completes, ALL 10 user stories can start in parallel
- Within US3: T016, T017, T018, T019 can all run in parallel (different files)
- Within US4: T029, T030, T031, T032 can all run in parallel (different files)
- Within US7: T043 and T044 can run in parallel
- US1, US2, US5, US7, US8, US9, US10 are fully independent — zero coordination needed
- US3 and US4 are independent of each other but both are large — good for two parallel developers
- Polish: T056, T057, T058 can all run in parallel

---

## Parallel Example: User Story 3 (Notifications)

```bash
# Wave 1 — Launch all migrations and models in parallel (4 tasks):
Task T016: "Create crm_notifications migration"
Task T017: "Create crm_notification_recipients migration"
Task T018: "Create CrmNotification model"
Task T019: "Create CrmNotificationRecipient model"

# Wave 2 — Sequential dependencies:
Task T020: "Update User model relationships"
Task T021: "Create NewCrmNotification broadcast event"
Task T022: "Create CrmNotificationService"
Task T023: "Add channel authorization"

# Wave 3 — Frontend components (sequential):
Task T024: "Create NotificationBell component"
Task T025: "Include bell in CRM layout"
Task T026: "Create Notifications send page"
Task T027: "Create Announcements page"

# Wave 4 — Tests:
Task T028: "Write Pest tests for notification system"
```

## Parallel Example: User Story 4 (Sales Targets)

```bash
# Wave 1 — Launch all migrations, models, seeder in parallel (4 tasks):
Task T029: "Create sales_targets migration"
Task T030: "Create leaderboard_configs migration"
Task T031: "Create SalesTarget + LeaderboardConfig models"
Task T032: "Create LeaderboardConfigSeeder"

# Wave 2 — Sequential:
Task T033: "Update User model with target relationships"
Task T034: "Create TargetTrackingService"

# Wave 3 — Frontend pages (can partially parallel):
Task T035: "Create SalesTargets management page"
Task T036: "Create Leaderboard page"
Task T037: "Add target widgets to dashboard"

# Wave 4 — Tests:
Task T038: "Write Pest tests for target tracking"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL — blocks all stories)
3. Complete Phase 3: User Story 1 (Status Colors — 4 tasks)
4. **STOP and VALIDATE**: Test status colors across all pages
5. Deploy/demo if ready

### Incremental Delivery

1. Complete Setup + Foundational → Foundation ready
2. Add US1 (Colors) → Test independently → Deploy (MVP!)
3. Add US2 (Customer Profile) → Test independently → Deploy
4. Add US3 (Notifications) → Test independently → Deploy
5. Add US4 (Targets) → Test independently → Deploy
6. Add US5-US7 (P2 quick wins) → Test → Deploy
7. Add US8-US10 (P3 quick wins) → Test → Deploy
8. Polish phase → Final deploy

### Parallel Team Strategy

With multiple developers after Foundational:

- **Developer A**: US1 (Colors, 4 tasks) → US5 (Columns, 2 tasks) → US6 (PM Removal, 2 tasks)
- **Developer B**: US3 (Notifications, 13 tasks — largest story)
- **Developer C**: US2 (Profile, 3 tasks) → US4 (Targets, 10 tasks)
- **Developer D**: US7 (Contact, 4 tasks) → US8 (Ad Set, 2 tasks) → US9 (YouTube, 3 tasks) → US10 (Reset, 4 tasks)
- **All**: Converge on Phase 13 (Polish, 5 tasks)

---

## Notes

- [P] tasks = different files, no dependencies on incomplete tasks
- [Story] label maps task to specific user story for traceability
- Each user story is independently completable and testable
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- US8 (Ad Set) partially implemented in commit 365067a2 — verify before reimplementing
- Trix editor (T003, T026) provides rich text for notifications
- Reverb WebSocket server must be running alongside web server for real-time features (US3)
- Order status transitions (Phase 2) are foundational — they enable both US4 targets and observability logging
