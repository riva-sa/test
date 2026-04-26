# Tasks: Lead Form Enhancements

**Input**: Design documents from `/specs/003-lead-form-enhancements/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Implementation MUST include automated tests using Pest. Every PR should verify logic via unit/functional tests and Livewire state transitions.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- **Single project**: `src/`, `tests/` at repository root
- **Web app**: `backend/src/`, `frontend/src/`
- **Mobile**: `api/src/`, `ios/src/` or `android/src/`
- Paths shown below assume single project - adjust based on plan.md structure

<!-- 
   ============================================================================
   IMPORTANT: The tasks below are SAMPLE TASKS for illustration purposes only.
   
   The /speckit.tasks command MUST replace these with actual tasks based on:
   - User stories from spec.md (with their priorities P1, P2, P3...)
   - Feature requirements from plan.md
   - Entities from data-model.md
   - Endpoints from contracts/
   
   Tasks MUST be organized by user story so each story can be:
   - Implemented independently
   - Tested independently
   - Delivered as an MVP increment
   
   DO NOT keep these sample tasks in the generated tasks.md file.
   ============================================================================
-->

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Project initialization and basic structure

- [x] T001 Create project structure per implementation plan
- [x] T002 Initialize PHP project with Laravel 11.x dependencies
- [x] T003 [P] Configure linting and formatting tools (Laravel Pint)

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that MUST be complete before ANY user story can be implemented

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

Examples of foundational tasks (adjust based on your project):

- [x] T004 Setup database schema and migrations framework
- [x] T005 [P] Implement authentication/authorization framework
- [x] T006 [P] Setup API routing and middleware structure
- [x] T007 Create base models/entities that all stories depend on
- [x] T008 Configure error handling and logging infrastructure
- [x] T009 Setup environment configuration management

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel

---

## Phase 3: User Story 1 - Automated Phone Number Formatting (Priority: P1) 🎯 MVP

**Goal**: As a sales representative, I want phone numbers to be automatically formatted to remove spaces and symbols and start with +966 so that I don't have to manually format customer contact information.

**Independent Test**: Can be fully tested by submitting a lead with various phone number formats (e.g., "050 123 4567", "+96650-1234567", "966501234567") and verifying that all are stored as "+966501234567" in the database.

### Tests for User Story 1 (OPTIONAL - only if tests requested) ⚠️

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [x] T010 [P] [US1] Create unit test for phone normalization in tests/Unit/Actions/NormalizePhoneActionTest.php

### Implementation for User Story 1

- [x] T011 [P] [US1] Create NormalizePhoneAction in app/Actions/NormalizePhoneAction.php
- [x] T012 [US1] Update IngestSocialMediaLead action to use NormalizePhoneAction
- [x] T013 [US1] Add validation for phone numbers that cannot be formatted (flag for manual review)
- [x] T014 [US1] Update UnitOrder model to ensure phone field is properly formatted
- [x] T015 [US1] Add structured JSON logging for phone normalization events

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently

---

## Phase 4: User Story 2 - Optional Email Field (Priority: P1) 🎯 MVP

**Goal**: As a sales representative, I want the email address field to be optional when creating or editing leads so that I can proceed with lead creation even when the customer doesn't provide an email address.

**Independent Test**: Can be fully tested by submitting a lead form without an email address and verifying that the lead is created successfully with a null/empty email field.

### Tests for User Story 2 (OPTIONAL - only if tests requested) ⚠️

- [x] T016 [P] [US2] Create unit test for optional email validation in tests/Unit/Requests/SocialMediaLeadRequestTest.php

### Implementation for User Story 2

- [x] T017 [P] [US2] Update SocialMediaLeadRequest to make email field optional
- [x] T018 [US2] Update UnitOrder model to allow nullable email field
- [x] T019 [US2] Update database migration to make email column nullable
- [x] T020 [US2] Update IngestSocialMediaLead action to handle null email values
- [x] T021 [US2] Add validation tests for email field (optional but validated when provided)

**Checkpoint**: At this point, User Stories 1 AND 2 should both work independently

---

## Phase 5: User Story 3 - Sales Staff Order Editing (Priority: P1) 🎯 MVP

**Goal**: As a sales staff member, I want to edit customer and unit information on the order page so that I can correct mistakes or update information without needing to contact administrative staff.

**Independent Test**: Can be fully tested by logging in as a sales staff member, navigating to an order page, editing customer or unit information, and verifying that the changes are saved correctly.

### Tests for User Story 3 (OPTIONAL - only if tests requested) ⚠️

- [x] T022 [P] [US3] Create feature test for order editing in tests/Feature/Orders/OrderEditingTest.php

### Implementation for User Story 3

- [x] T023 [P] [US3] Update Order management Livewire component to allow sales staff editing
- [x] T024 [US3] Add authorization logging for unauthorized edit attempts (but still permit per clarification)
- [x] T025 [US3] Update Blade views to show edit fields for sales staff role
- [x] T026 [US3] Create policy for sales staff order editing permissions
- [x] T027 [US3] Add tests for permission logging functionality

**Checkpoint**: At this point, User Stories 1, 2, AND 3 should all work independently

---

## Phase 6: User Story 4 - Marketing Attribution Field Updates (Priority: P2)

**Goal**: As a marketing manager, I want the "Ad Group (Set)" field to be replaced with "Ad Name" and for campaign source to be automatically set according to the platform (Snapchat, Instagram, TikTok, etc.) so that marketing attribution data is accurate and consistent.

**Independent Test**: Can be fully tested by submitting a lead from a specific platform (e.g., Snapchat) and verifying that the marketing source field is automatically set to that platform and the Ad Group field is labeled as Ad Name.

### Tests for User Story 4 (OPTIONAL - only if tests requested) ⚠️

- [x] T028 [P] [US4] Create unit test for marketing source detection in tests/Unit/Services/MarketingSourceDetectorTest.php

### Implementation for User Story 4

- [x] T029 [P] [US4] Create MarketingSourceDetector service to auto-set platform based on referral/user-agent
- [x] T030 [US4] Update IngestSocialMediaLead action to use MarketingSourceDetector
- [x] T031 [US4] For unrecognized platforms, store raw value as-is
- [x] T032 [US4] Update UnitOrder resource Filament to change "Ad Group (Set)" label to "Ad Name"
- [x] T033 [US4] Update Livewire manage-orders.blade.php to display marketing source correctly
- [x] T034 [US4] Update order-details.blade.php to show full attribution details
- [x] T035 [US4] Add translation keys for new fields in language files

**Checkpoint**: At this point, User Stories 1, 2, 3, AND 4 should all work independently

---

## Phase 7: User Story 5 - Homepage Order Statistics Exclusion (Priority: P2)

**Goal**: As a sales manager, I want new orders to not appear on the homepage or in statistics so that I can focus on active orders that require attention rather than being distracted by newly created orders.

**Independent Test**: Can be fully tested by creating a new order and verifying that it does not appear in the homepage order listing or statistics counters.

### Tests for User Story 5 (OPTIONAL - only if tests requested) ⚠️

- [ ] T036 [P] [US5] Create feature test for dashboard order filtering in tests/Feature/Livewire/ManagerDashboardTest.php

### Implementation for User Story 5

- [ ] T037 [P] [US5] Update ManagerDashboard Livewire component to exclude new orders
- [ ] T038 [US5] Modify statistics queries to exclude new orders (status = 'new' or recent creation)
- [ ] T039 [US5] Update homepage widgets to use filtered query
- [ ] T040 [US5] Add tests to verify new orders are excluded from display and statistics

**Note**: US5 implementation reverted - showing all orders based on role as per user request.

**Checkpoint**: At this point, User Stories 1 through 5 should all work independently

---

## Phase 8: User Story 6 - Order Status Notifications (Priority: P2)

**Goal**: As an employee, I want to receive email notifications when order statuses change so that I can stay informed about important updates without having to constantly check the system.

**Independent Test**: Can be fully tested by changing the status of an order and verifying that the assigned employee receives an email notification about the status change.

### Tests for User Story 6 (OPTIONAL - only if tests requested) ⚠️

- [x] T041 [P] [US6] Create unit test for order status notification in tests/Unit/Notifications/OrderStatusChangedNotificationTest.php

### Implementation for User Story 6

- [x] T042 [P] [US6] Create OrderStatusChangedNotification using Laravel mail and database channels
- [x] T043 [US6] Create event listener for order status updates
- [x] T044 [US6] Integrate notification dispatch into order status update logic
- [x] T045 [US6] Add structured JSON logging for order status change events
- [x] T046 [US6] Update notification preferences to include status change notifications
- [x] T047 [US6] Test notification delivery via mail and database channels

**Checkpoint**: At this point, User Stories 1 through 6 should all work independently

---

## Phase 9: User Story 7 - Sales Manager Statement Notifications (Priority: P2)

**Goal**: As an employee, I want to receive email notifications when the sales manager adds a statement to an order so that I can stay informed about important comments or instructions.

**Independent Test**: Can be fully tested by having a sales manager add a statement to an order and verifying that relevant employees receive email notifications about the statement.

### Tests for User Story 7 (OPTIONAL - only if tests requested) ⚠️

- [x] T048 [P] [US7] Create unit test for manager statement notification in tests/Unit/Notifications/ManagerStatementAddedNotificationTest.php

### Implementation for User Story 7

- [x] T049 [P] [US7] Create ManagerStatementAddedNotification using Laravel mail and database channels
- [x] T050 [US7] Create event listener for manager statement additions
- [x] T051 [US7] Integrate notification dispatch into statement creation logic
- [x] T052 [US7] Add structured JSON logging for manager statement events
- [x] T053 [US7] Update notification preferences to include manager statement notifications
- [x] T054 [US7] Test notification delivery via mail and database channels

**Checkpoint**: At this point, User Stories 1 through 7 should all work independently

---

## Phase 10: User Story 8 - Enhanced Order Receipt Mechanism (Priority: P2)

**Goal**: As a sales representative, I want to see all campaign information from lead forms (such as budget questions) displayed in the "Basic Order Notes" section so that I have access to all relevant customer information when processing orders.

**Independent Test**: Can be fully tested by submitting a lead form with additional fields (e.g., budget questions) and verifying that this information appears in the Basic Order Notes section of the created order.

### Tests for User Story 8 (OPTIONAL - only if tests requested) ⚠️

- [x] T055 [P] [US8] Create unit test for order receipt mechanism in tests/Unit/Actions/IngestSocialMediaLeadTest.php

### Implementation for User Story 8

- [x] T056 [P] [US8] Update IngestSocialMediaLead action to capture all form fields
- [x] T057 [US8] Concatenate additional fields into basic_order_notes field
- [x] T058 [US8] Ensure Basic Order Notes section displays captured information
- [x] T059 [US8] Update UnitOrder model to handle extended basic_order_notes field
- [x] T060 [US8] Add tests for various additional field types (budget questions, etc.)
- [x] T061 [US8] Verify information persists correctly in database and display

**Checkpoint**: At this point, all User Stories 1 through 8 should work independently

---

## Phase 11: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories

- [x] T062 [P] Documentation updates in docs/
- [x] T063 Code cleanup and refactoring
- [x] T064 Performance optimization across all stories
- [x] T065 [P] Additional unit tests (if requested) in tests/unit/
- [x] T066 Security hardening
- [x] T067 Run quickstart.md validation
- [x] T068 [P] Add structured JSON logging for all critical events
- [x] T069 Verify RTL layout integrity for new Filament columns and Livewire views
- [x] T070 Run Laravel Pint for coding standard alignment
- [x] T071 Run Pest tests to ensure no regressions

---