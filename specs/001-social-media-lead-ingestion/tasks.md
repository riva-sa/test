# Tasks: Social Media Lead Ingestion

**Input**: Design documents from `/specs/001-social-media-lead-ingestion/`
**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Tests**: Implementation MUST include automated tests using Pest. Every PR should verify logic via unit/functional tests and Livewire state transitions.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Route registration and controller initialization

- [ ] T001 Register webhook route in `routes/api.php`
- [ ] T002 Create controller `app/Http/Controllers/Api/ZapierLeadController.php` with placeholder methods

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Database and model groundwork

- [ ] T003 Create migration for campaign fields in `database/migrations/2026_04_15_000000_add_campaign_fields_to_unit_orders.php`
- [ ] T004 Update `app/Models/UnitOrder.php` with `ORDER_SOURCE_SOCIAL_MEDIA` constant and add attribution fields to `$fillable`
- [ ] T005 [P] Create `app/Actions/IngestSocialMediaLead.php` stub following Constitution Principle II

**Checkpoint**: Foundation ready - user story implementation can now begin

---

## Phase 3: User Story 1 - Automated Lead Ingestion (Priority: P1) 🎯 MVP

**Goal**: Capture social media lead data via authenticated webhook and persist as `UnitOrder`

**Independent Test**: Send valid JSON payload via Curl to the webhook endpoint and verify record existence in the database.

### Tests for User Story 1
> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T006 [P] [US1] Create feature test `tests/Feature/Api/ZapierLeadControllerTest.php` for lead ingestion validation and persistence

### Implementation for User Story 1

- [ ] T007 [P] [US1] Create validation request `app/Http/Requests/Api/SocialMediaLeadRequest.php` per requirements in spec.md
- [ ] T008 [US1] Implement lead creation logic in `app/Actions/IngestSocialMediaLead.php` (depends on T004)
- [ ] T009 [US1] Finalize `ZapierLeadController@store` to dispatch the ingestion Action

**Checkpoint**: User Story 1 functional and testable independently

---

## Phase 4: User Story 2 - Instant Team Notifications (Priority: P2)

**Goal**: Alert sales and admin teams when a new social media lead arrives

**Independent Test**: Ingest a lead and verify a `NewSocialMediaLead` notification is sent to users with `admin` or `sales_manager` roles.

### Tests for User Story 2

- [ ] T010 [P] [US2] Update `tests/Feature/Api/ZapierLeadControllerTest.php` to assert that correct notifications are dispatched

### Implementation for User Story 2

- [ ] T011 [P] [US2] Create notification `app/Notifications/NewSocialMediaLead.php` using standard Laravel mail and database channels
- [ ] T012 [US2] Integrate notification dispatch into `app/Actions/IngestSocialMediaLead.php`

**Checkpoint**: Leads are now ingested AND teams are notified

---

## Phase 5: User Story 3 - Lead Source Attribution (Priority: P2)

**Goal**: Display campaign and source details in the CRM Order Management UI

**Independent Test**: Navigate to the Orders page in Filament and verify that the new campaign detail columns are populated for social media leads.

### Implementation for User Story 3

- [ ] T013 [US3] Update `app/Filament/Resources/UnitOrderResource.php` to add campaign fields to the Table schema
- [ ] T014 [US3] Update `app/Filament/Resources/UnitOrderResource.php` to add lead attribution section to the Form schema
- [ ] T015 [US3] Update `resources/views/livewire/mannager/manage-orders.blade.php` to display `campaign_name` in the orders table
- [ ] T016 [US3] Update `resources/views/livewire/mannager/order-details.blade.php` to show full attribution details (Campaign, Ad Set, etc.)
- [ ] T017 [P] [US3] Add translation keys for new fields in `lang/ar/unit-order.php` and `lang/en/unit-order.php` per Constitution Principle IV

**Checkpoint**: All user stories functional and accessible in the UI

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Observability and styling cleanup

- [ ] T018 Add structured JSON logging to `IngestSocialMediaLead` action for auditability per Constitution Principle V
- [ ] T019 Run `./vendor/bin/pint` for coding standard alignment
- [ ] T020 Verify RTL layout integrity for new Filament columns and Livewire views

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies
- **Foundational (Phase 2)**: Depends on Route setup (US1) or occurs in parallel
- **User Story 1 (P1)**: BLOCKS Story 2 and Story 3 as it handles the data capture
- **User Story 2 & 3**: Can proceed in parallel once US1 ingestion logic is working

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1 & 2
2. Implement User Story 1 (Ingestion only)
3. **STOP and VALIDATE**: Verify Curl request works

### Incremental Delivery

1. Add Notifications (US2) -> Verify team alerts
2. Add UI Attribution (US3) -> Finalize visibility
