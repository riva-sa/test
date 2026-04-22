# Feature Specification: Lead Form Enhancements

**Feature Branch**: `003-lead-form-enhancements`  
**Created**: 2026-04-21  
**Status**: Draft  
**Input**: User description: "Required Modifications: 1- Automatically modify numbers to be free of spaces and symbols, starting with +966. 2- The email address field is optional. 3- Allow sales staff to edit customer and unit information on the order page at https://riva.sa/crm/orders/id. 4- Replace the 'Ad Group (Set)' field with the ad name. 5- Change the campaign source according to the platform (Snapchat, Instagram, TikTok, etc.) on the order page at https://riva.sa/crm/orders/id. 6- New orders should not appear on the homepage or in the statistics. 7- Send notifications for new order statuses to the employee's email (same as previous notifications). 8- Send a notification to the employee's email when the sales manager adds a statement. 9- Update the order receipt mechanism from the Zabeer platform to be able to receive any campaign information in the order, such as questions. For example, if the form asks about the budget, you can display it in the system in the 'Basic Order Notes' section. Ensure that the data is displayed in the LiveWire Manager path."

## User Scenarios & Testing *(mandatory)*

<!--
  IMPORTANT: User stories should be PRIORITIZED as user journeys ordered by importance.
  Each user story/journey must be INDEPENDENTLY TESTABLE - meaning if you implement just ONE of them,
  you should still have a viable MVP (Minimum Viable Product) that delivers value.
  
  Assign priorities (P1, P2, P3, etc.) to each story, where P1 is the most critical.
  Think of each story as a standalone slice of functionality that can be:
  - Developed independently
  - Tested independently
  - Deployed independently
  - Demonstrated to users independently
-->

### User Story 1 - Automated Phone Number Formatting (Priority: P1)

As a sales representative, I want phone numbers to be automatically formatted to remove spaces and symbols and start with +966 so that I don't have to manually format customer contact information.

**Why this priority**: This is a core data quality improvement that ensures consistent phone number formatting across the system, reducing errors in customer communication.

**Independent Test**: Can be fully tested by submitting a lead with various phone number formats (e.g., "050 123 4567", "+96650-1234567", "966501234567") and verifying that all are stored as "+966501234567" in the database.

**Acceptance Scenarios**:

1. **Given** a lead form submission with phone number "050 123 4567", **When** the form is processed, **Then** the phone number is stored as "+966501234567"
2. **Given** a lead form submission with phone number "+96650-1234567", **When** the form is processed, **Then** the phone number is stored as "+966501234567"
3. **Given** a lead form submission with phone number "966501234567", **When** the form is processed, **Then** the phone number is stored as "+966501234567"

---

### User Story 2 - Optional Email Field (Priority: P1)

As a sales representative, I want the email address field to be optional when creating or editing leads so that I can proceed with lead creation even when the customer doesn't provide an email address.

**Why this priority**: This removes a barrier to lead creation and acknowledges that not all customers will provide email addresses, improving sales team efficiency.

**Independent Test**: Can be fully tested by submitting a lead form without an email address and verifying that the lead is created successfully with a null/empty email field.

**Acceptance Scenarios**:

1. **Given** a lead form submission with all required fields except email, **When** the form is processed, **Then** the lead is created successfully and the email field is null/empty
2. **Given** a lead form submission with an email address provided, **When** the form is processed, **Then** the lead is created successfully and the email field contains the provided value

---

### User Story 3 - Sales Staff Order Editing (Priority: P1)

As a sales staff member, I want to edit customer and unit information on the order page so that I can correct mistakes or update information without needing to contact administrative staff.

**Why this priority**: Empowers sales teams to maintain accurate order data independently, reducing dependency on administrative staff and improving response time to customer requests.

**Independent Test**: Can be fully tested by logging in as a sales staff member, navigating to an order page, editing customer or unit information, and verifying that the changes are saved correctly.

**Acceptance Scenarios**:

1. **Given** a sales staff member is logged in and viewing an order page, **When** they edit customer information and save, **Then** the customer information is updated in the system
2. **Given** a sales staff member is logged in and viewing an order page, **When** they edit unit information and save, **Then** the unit information is updated in the system

---

### User Story 4 - Marketing Attribution Field Updates (Priority: P2)

As a marketing manager, I want the "Ad Group (Set)" field to be replaced with "Ad Name" and for campaign source to be automatically set according to the platform (Snapchat, Instagram, TikTok, etc.) so that marketing attribution data is accurate and consistent.

**Why this priority**: Ensures that marketing data is properly attributed to the correct platforms and campaigns, enabling accurate ROI measurement and marketing optimization.

**Independent Test**: Can be fully tested by submitting a lead from a specific platform (e.g., Snapchat) and verifying that the marketing source field is automatically set to that platform and the Ad Group field is labeled as Ad Name.

**Acceptance Scenarios**:

1. **Given** a lead form submission from Snapchat, **When** the form is processed, **Then** the marketing source is automatically set to "Snapchat"
2. **Given** a lead form submission from Instagram, **When** the form is processed, **Then** the marketing source is automatically set to "Instagram"
3. **Given** a lead form submission, **When** the form is viewed in the order management interface, **Then** the "Ad Group (Set)" field is labeled as "Ad Name"

---

### User Story 5 - Homepage Order Statistics Exclusion (Priority: P2)

As a sales manager, I want new orders to not appear on the homepage or in statistics so that I can focus on active orders that require attention rather than being distracted by newly created orders.

**Why this priority**: Improves the relevance of dashboard information by focusing on orders that are in progress or require action, rather than cluttering the view with newly created orders.

**Independent Test**: Can be fully tested by creating a new order and verifying that it does not appear in the homepage order listing or statistics counters.

**Acceptance Scenarios**:

1. **Given** a new order has been created, **When** viewing the homepage, **Then** the order does not appear in the recent orders list
2. **Given** a new order has been created, **When** viewing the homepage statistics, **Then** the order is not counted in any statistics

---

### User Story 6 - Order Status Notifications (Priority: P2)

As an employee, I want to receive email notifications when order statuses change so that I can stay informed about important updates without having to constantly check the system.

**Why this priority**: Ensures timely awareness of order progress, enabling faster response to customer needs and improving overall customer service.

**Independent Test**: Can be fully tested by changing the status of an order and verifying that the assigned employee receives an email notification about the status change.

**Acceptance Scenarios**:

1. **Given** an order exists with an assigned employee, **When** the order status is changed, **Then** the assigned employee receives an email notification about the status change
2. **Given** an order exists with an assigned employee, **When** the order status is changed multiple times, **Then** the assigned employee receives an email notification for each status change

---

### User Story 7 - Sales Manager Statement Notifications (Priority: P2)

As an employee, I want to receive email notifications when the sales manager adds a statement to an order so that I can stay informed about important comments or instructions.

**Why this priority**: Ensures that important communications from sales managers are promptly seen by relevant team members, improving coordination and reducing miscommunication.

**Independent Test**: Can be fully tested by having a sales manager add a statement to an order and verifying that relevant employees receive email notifications about the statement.

**Acceptance Scenarios**:

1. **Given** an order exists with assigned employees, **When** the sales manager adds a statement to the order, **Then** the assigned employees receive email notifications about the statement
2. **Given** an order exists with assigned employees, **When** the sales manager adds multiple statements to the order, **Then** the assigned employees receive email notifications for each statement

---

### User Story 8 - Enhanced Order Receipt Mechanism (Priority: P2)

As a sales representative, I want to see all campaign information from lead forms (such as budget questions) displayed in the "Basic Order Notes" section so that I have access to all relevant customer information when processing orders.

**Why this priority**: Ensures that no customer-provided information is lost during the lead ingestion process, enabling sales teams to have complete context when engaging with customers.

**Independent Test**: Can be fully tested by submitting a lead form with additional fields (e.g., budget questions) and verifying that this information appears in the Basic Order Notes section of the created order.

**Acceptance Scenarios**:

1. **Given** a lead form submission that includes a budget question, **When** the order is created, **Then** the budget question and answer appear in the Basic Order Notes section
2. **Given** a lead form submission that includes multiple additional fields, **When** the order is created, **Then** all additional field information appears in the Basic Order Notes section

### Edge Cases

- What happens when a phone number cannot be formatted to start with +966?
- How does the system handle extremely long phone numbers or special characters?
- What happens when a marketing source platform is not recognized (not Snapchat, Instagram, TikTok, etc.)?
- What happens when a sales staff member tries to edit information they don't have permission to modify?
- How does the system handle notification failures (email service down, invalid email addresses)?
- What happens when additional campaign information fields are empty or null?

## Requirements *(mandatory)*

<!--
  ACTION REQUIRED: The content in this section represents placeholders.
  Fill them out with the right functional requirements.
-->

### Functional Requirements

- **FR-001**: System MUST automatically format phone numbers to remove spaces and symbols and ensure they start with +966; phone numbers that cannot be formatted should be flagged for manual review but still allow lead creation
- **FR-002**: System MUST treat the email address field as optional during lead creation and editing
- **FR-003**: System MUST allow sales staff to edit customer and unit information on the order page; unauthorized edit attempts should be logged but still permitted
- **FR-004**: System MUST replace the "Ad Group (Set)" field label with "Ad Name" in the order management interface
- **FR-005**: System MUST automatically set the campaign source according to the platform (Snapchat, Instagram, TikTok, etc.) when processing leads; unrecognized platforms should be stored as-is
- **FR-006**: System MUST exclude new orders from appearing on the homepage and in statistics
- **FR-007**: System MUST send email notifications to employees when order statuses change
- **FR-008**: System MUST send email notifications to employees when the sales manager adds a statement to an order
- **FR-009**: System MUST display all campaign information from lead forms (such as budget questions) in the "Basic Order Notes" section

*Example of marking unclear requirements:*

### Key Entities *(include if feature involves data)*

- **UnitOrder**: The core CRM entity representing a lead or order. It contains phone number, email, customer information, unit information, and marketing attribution fields.
- **Notification**: A system event that alerts users to new activity such as order status changes or manager statements.
- **User Role**: Determines which team members can edit order information and receive notifications.
- **Order Status**: Tracks the progress of an order through various stages (new, pending, completed, etc.).

- **[Entity 1]**: [What it represents, key attributes without implementation]
- **[Entity 2]**: [What it represents, relationships to other entities]

## Success Criteria *(mandatory)*

<!--
  ACTION REQUIRED: Define measurable success criteria.
  These must be technology-agnostic and measurable.
-->

### Measurable Outcomes

- **SC-001**: 100% of phone numbers are automatically formatted to remove spaces/symbols and start with +966 upon lead submission
- **SC-000**: Leads can be created successfully without providing an email address (email field is optional)
- **SC-003**: Sales staff can edit customer and unit information on the order page without administrative assistance
- **SC-004**: The "Ad Group (Set)" field is consistently labeled as "Ad Name" in all order management interfaces
- **SC-005**: Campaign source is automatically set to the correct platform (Snapchat, Instagram, TikTok, etc.) for 100% of leads from known platforms
- **SC-006**: New orders do not appear in homepage order listings or statistics counters
- **SC-007**: Employees receive email notifications for 100% of order status changes
- **SC-008**: Employees receive email notifications for 100% of sales manager statement additions
- **SC-009**: All campaign information from lead forms (including custom questions) appears in the Basic Order Notes section of created orders

## Localization *(optional)*

<!--
  ACTION REQUIRED: Define translation requirements if feature includes UI.
-->

- **L10N-001**: All user-facing strings MUST have translations in [lang/en] and [lang/ar].
- **L10N-002**: UI MUST maintain layout integrity in RTL (Right-to-Left) direction.

## Assumptions

<!--
  ACTION REQUIRED: The content in this section represents placeholders.
  Fill them out with the right assumptions based on reasonable defaults
  chosen when the feature description did not specify certain details.
-->

- Users have stable internet connectivity when submitting lead forms
- The existing authentication and authorization system will be reused for determining sales staff permissions
- The UnitOrder model already exists with appropriate fields for phone numbers, email, and marketing attribution
- The LiveWire Manager path referenced refers to the existing order management interface in the CRM
- Email notifications will use the existing email infrastructure and templates in the system
- The "Basic Order Notes" section already exists in the order display interface
- Saudi phone numbers (+966) are the primary format expected in the system
- Marketing sources are limited to known platforms (Snapchat, Instagram, TikTok, etc.) for automatic source detection

## Clarifications

### Session 2026-04-21

- Q: How should the system handle phone numbers that cannot be formatted to start with +966? → A: Flag for manual review but still create lead
- Q: How should the system handle unrecognized marketing source platforms? → A: Store the raw platform name as-is
- Q: What should happen when a sales staff member tries to edit information they don't have permission to modify? → A: Log the attempt but allow edit
