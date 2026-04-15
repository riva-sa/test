# Feature Specification: Social Media Lead Ingestion

**Feature Branch**: `001-social-media-lead-ingestion`  
**Created**: 2026-04-15  
**Status**: Draft  
**Input**: User description from `speckit_social_media_lead_spec.md`

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Automated Lead Ingestion (Priority: P1)

As a marketing manager, I want lead data from social media platforms (TikTok, Snapchat, etc.) to flow automatically into our CRM so that sales teams can act on them immediately without manual data entry.

**Why this priority**: Core functionality of the feature. Without automated ingestion, the manual process remains slow and error-prone.

**Independent Test**: Can be fully tested by sending a simulated Zapier webhook payload to the `/api/zapier/social-media-lead` endpoint and verifying that a `UnitOrder` record is created with all mapped fields.

**Acceptance Scenarios**:

1. **Given** a valid JSON payload containing lead details (name, email, phone, marketing_source, campaign_name), **When** a POST request is sent to the Zapier endpoint, **Then** a new `UnitOrder` record is created with `order_source` set to 'social_media'.
2. **Given** an invalid JSON payload (e.g., missing phone number), **When** a POST request is sent, **Then** the system returns a 422 validation error and no record is created.

---

### User Story 2 - Instant Team Notifications (Priority: P2)

As a sales manager or admin, I want to receive instant notifications via email and in-system alerts when a new social media lead arrives so that I can ensure quick follow-up.

**Why this priority**: Essential for lead responsiveness. Ingesting data without alerting the team delays the sales process.

**Independent Test**: Trigger a successful lead ingestion and verify that users with 'admin' or 'sales_manager' roles receive both an email and a database notification.

**Acceptance Scenarios**:

1. **Given** a successful lead ingestion, **When** the `UnitOrder` is persisted, **Then** the `NewSocialMediaLead` notification is dispatched to eligible recipients via mail and database channels.
2. **Given** a recipient's notification preferences, **When** they check their inbox or CRM dashboard, **Then** they see lead details (Name, Source, Campaign) and a link to view the lead record.

---

### User Story 3 - Lead Source Attribution (Priority: P2)

As an administrator, I want to see detailed campaign data (Ad Squad, Ad Set, Ad Name) associated with each lead so that I can accurately measure the ROI of different marketing efforts.

**Why this priority**: Crucial for marketing analytics and strategy.

**Independent Test**: View a newly created social media lead in the `Manage Orders` Livewire component and verify that all attribution fields (Campaign, Ad Set, Source) are correctly displayed.

**Acceptance Scenarios**:

1. **Given** a `UnitOrder` record created via the Zapier endpoint, **When** viewed in the CRM admin panel, **Then** the `marketing_source`, `campaign_name`, `ad_squad`, `ad_set`, and `ad_name` fields are visible and populated.

---

### Edge Cases

- **Duplicate Leads**: How does the system handle a user submitting the same form twice on social media? (Informed guess: System should create multiple orders as per current `UnitOrder` behavior unless deduplication logic is requested).
- **Missing Optional Fields**: What happens if `message` is null? (System should handle nullable fields gracefully).
- **Unsupported Marketing Source**: What happens if a source not in the predefined list is sent? (Validation should catch this and return a 422 error).

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST provide a authenticated/validated public endpoint `/api/zapier/social-media-lead` for POST requests.
- **FR-002**: System MUST validate incoming data including name, email, phone (max 20), and marketing_source (restricted to a whitelist).
- **FR-003**: System MUST create a `UnitOrder` record upon successful validation of the webhook payload.
- **FR-004**: System MUST map incoming fields to `UnitOrder` attributes, specifically setting `order_source` to a new `social_media` constant.
- **FR-005**: System MUST store campaign metadata (Campaign Name, Ad Squad, Ad Set, Ad Name) in the `unit_orders` table.
- **FR-006**: System MUST dispatch a `NewSocialMediaLead` notification via email and database to 'admin' and 'sales_manager' roles.
- **FR-007**: System MUST update the Order Management UI to display marketing source and campaign detail columns.

### Key Entities *(include if feature involves data)*

- **UnitOrder**: The core CRM entity representing a lead or order. It is extended with marketing attribution fields.
- **Notification**: A system event that alerts users to new activity.
- **User Role**: Determines which team members receive lead notifications.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Social media leads appear in the CRM within 30 seconds of the webhook being triggered.
- **SC-002**: 100% of payloads meeting validation rules result in a successful `UnitOrder` creation.
- **SC-003**: Team members receive notifications for 100% of newly ingested social media leads (assuming correct mail configurations).
- **SC-004**: CRM admins can view full campaign details for every lead sourced from social media without database inspection.

## Assumptions

- Zapier is used as the intermediary to format social media lead data into the required JSON structure.
- The `unit_orders` table already has `marketing_source` and `order_source` columns as per existing migrations, but may need new attribution columns.
- Standard Laravel notification and validation packages will be utilized.
- Round-robin assignment is NOT required for this initial integration (leads remain unassigned or follow default system rules).
