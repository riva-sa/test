# Feature Specification: CRM Performance, Notifications & Leaderboard Fixes

**Feature Branch**: `006-fix-crm-leaderboard`
**Created**: 2026-05-05
**Status**: Draft
**Input**: User description: "Fixing the CRM speed and resolving the issue of sending alert and order notifications via email, as well as displaying the top performers on the main sales manager page for motivation. Also, there's an issue with point calculation on the leaderboard: admin editing, daily view showing only current day, and Reservations only counting 'Sales Transaction' status."

## Clarifications

### Session 2026-05-05

- Q: Does a manual point adjustment apply to a specific period, a cumulative all-time total, or the current active period? → A: Per-period adjustment — admin selects the period type (daily/weekly/monthly) and the date, then edits that specific period's score.
- Q: When a reservation's status changes, how quickly should the leaderboard reflect the updated score? → A: Scheduled — score recalculates on a fixed schedule (e.g., nightly batch); not in real-time.
- Q: Who receives email notifications when a new order is created or a CRM alert fires? → A: Responsible agent receives both an in-app system notification and an email; sales managers and all admins receive email for every order. Email templates are generated with the current order data at the time of dispatch (dynamic, not a snapshot).
- Q: If a manual point adjustment would result in a negative total, what should the system do? → A: Block — system rejects the submission with an error and requires the admin to enter a valid non-negative value.
- Q: Who can view the point adjustment audit log? → A: Admins only — sales managers and agents cannot access the audit log.

### Session 2026-05-05 (Addendum)

- Q: How should the "Sales Transactions" metric (formerly "reservations") be calculated, and what label should it use? → A: Transition-based — a point is awarded each time an agent actively converts an order TO "Sales Transaction" status (status 2), regardless of later status changes. Points are permanent; admin adjustment handles erroneous conversions. The UI label "Reservations / حجوزات" is renamed to "Sales Transactions / معاملات بيعية" throughout the leaderboard, filters, and reports. The existing "sales / مبيعات" metric (status 4, Completed) is unchanged and remains a separate metric.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Admin Corrects Leaderboard Points (Priority: P1)

When a sales agent adds a completed order (earning points) then reverts it to an open order, the leaderboard incorrectly retains those points. An admin needs to manually adjust the agent's point total to reflect the accurate count, along with a documented reason.

**Why this priority**: Incorrect points damage team trust in the leaderboard as a fair motivational tool. This is the most critical business accuracy issue affecting fairness for all agents.

**Independent Test**: Create a scenario where a rep earns points for a completed order, revert the order status, then verify an admin can manually reduce the points and the leaderboard reflects the correction immediately.

**Acceptance Scenarios**:

1. **Given** an admin is viewing the leaderboard, **When** they select a sales agent's record to edit, **Then** they can choose the period type (daily/weekly/monthly), select the specific date, modify that period's point total, and must provide a reason/note before saving.
2. **Given** an admin submits a valid point adjustment with a reason, **When** the change is saved, **Then** the leaderboard immediately reflects the updated total for that agent.
3. **Given** a point adjustment is made by an admin, **When** any admin views the leaderboard audit history, **Then** the change is recorded with the editor's name, timestamp, period type, period date, original value, new value, and reason. Sales managers and agents cannot access this audit log.
4. **Given** an admin attempts to save an adjustment without a reason, **When** they submit the form, **Then** the system rejects the submission and prompts for a required reason.
5. **Given** an admin enters an adjustment value that would result in a negative period total, **When** they attempt to save, **Then** the system rejects the submission with a clear error and requires a non-negative value.

---

### User Story 2 - Email Notifications for Alerts and Orders (Priority: P1)

Sales team members, managers, and admins currently receive no email notifications when important events occur — such as new orders being created or CRM alerts being triggered. The notification system must reliably deliver emails to the correct recipients for each event type, with email content reflecting the latest order data at send time.

**Why this priority**: Broken email notifications mean the team misses critical time-sensitive updates, directly impacting sales responsiveness and follow-up speed.

**Independent Test**: Create a new order; verify the responsible agent receives both an in-app notification and an email, and that all sales managers and admins receive an email — all containing the current order details.

**Acceptance Scenarios**:

1. **Given** a new order is created in the CRM, **When** the order is saved, **Then** the responsible agent receives an in-app system notification AND an email, while all sales managers and admins also receive an email containing the current order details.
2. **Given** a CRM alert is triggered, **When** the alert fires, **Then** the responsible agent receives an in-app system notification and an email describing the alert.
3. **Given** an order email is dispatched, **When** the email content is generated, **Then** it reflects the order's current state at the time of dispatch (not a snapshot from when the event originally fired).
4. **Given** an email notification fails to deliver, **When** the system detects the failure, **Then** it retries automatically and logs the failure for admin review.
5. **Given** a recipient's email address is invalid, **When** delivery is attempted, **Then** the failure is logged with the invalid address and no further retries occur for that address.

---

### User Story 3 - Reservations Only Count "Sales Transaction" Status (Priority: P1)

The leaderboard's Reservations section currently awards points for reservations regardless of their status. It must only award points when a reservation has the exact status of "Sales Transaction." Scores reflect this on the next scheduled recalculation, not instantly.

**Why this priority**: Counting incorrect statuses inflates scores unfairly and misrepresents actual closed sales, undermining the leaderboard's integrity.

**Independent Test**: Create a reservation with "Open" status and verify 0 points appear on the leaderboard after the next scheduled recalculation; change that reservation's status to "Sales Transaction" and verify the appropriate points appear after the following recalculation.

**Acceptance Scenarios**:

1. **Given** a reservation exists with any status other than "Sales Transaction", **When** the scheduled leaderboard recalculation runs, **Then** that reservation contributes 0 points to the responsible agent's score.
2. **Given** a reservation's status is updated to "Sales Transaction", **When** the next scheduled recalculation runs, **Then** the appropriate points are awarded to the responsible agent.
3. **Given** a reservation previously counted as "Sales Transaction" has its status changed to a different status, **When** the next scheduled recalculation runs, **Then** those points are removed from the agent's score.
4. **Given** an agent has reservations with mixed statuses, **When** the leaderboard displays their score after recalculation, **Then** only the "Sales Transaction" reservations are included in the count.

---

### User Story 4 - Daily Leaderboard View Shows Historical Data (Priority: P1)

The daily leaderboard view currently only displays data for today's date, preventing managers from reviewing performance on any specific past day.

**Why this priority**: Without historical daily view access, managers cannot review past performance or investigate point discrepancies on specific dates.

**Independent Test**: Navigate to the daily leaderboard view, select a past date (e.g., 3 days ago), and verify that performance data from that specific date is displayed accurately.

**Acceptance Scenarios**:

1. **Given** a manager opens the daily leaderboard view, **When** they select a specific past date using the date picker, **Then** leaderboard data recorded for that date is displayed.
2. **Given** a manager selects today's date, **When** they apply the date filter, **Then** today's current data is displayed (existing behavior preserved).
3. **Given** a date with no recorded activity is selected, **When** the filter is applied, **Then** the view displays an empty leaderboard with a clear "No data for this date" message rather than showing today's data.

---

### User Story 5 - Top Performers on Sales Manager Home Page (Priority: P2)

Sales managers see a dedicated top performers widget on their main dashboard home page, showing the leading sales agents' rankings to motivate the team.

**Why this priority**: Visible top performers drive healthy competition and motivation. Valuable for team culture but does not block daily operations.

**Independent Test**: Log in as a sales manager and verify the home page displays a top performers section showing ranked agents with their names and current scores.

**Acceptance Scenarios**:

1. **Given** a sales manager opens their home page, **When** the page loads, **Then** a top performers section is visible showing ranked sales agents with their names and scores.
2. **Given** leaderboard data is updated, **When** the sales manager refreshes their home page, **Then** the top performers widget reflects the latest standings.
3. **Given** fewer agents exist than the configured display limit, **When** the top performers widget loads, **Then** all available agents are shown without errors or blank rows.

---

### User Story 6 - CRM Performance Improvement (Priority: P2)

Users across all roles experience slow load times and sluggish response on CRM pages. All regularly used pages must respond noticeably faster so users can work efficiently.

**Why this priority**: Performance impacts the productivity of every CRM user daily, but since the system remains functional (not broken), correctness bugs take precedence.

**Independent Test**: Load the CRM main dashboard, orders list, and leaderboard pages and verify each becomes fully interactive within an acceptable time under normal conditions.

**Acceptance Scenarios**:

1. **Given** a user navigates to the CRM main dashboard, **When** the page loads, **Then** the page is fully interactive within 3 seconds under normal usage conditions.
2. **Given** a user performs a search or applies a filter, **When** results are requested, **Then** results appear within 2 seconds.
3. **Given** multiple users are using the CRM simultaneously, **When** they navigate between pages, **Then** no user experiences load times exceeding 5 seconds on primary pages.

---

### Edge Cases

- If an admin enters an adjustment that would bring an agent's period total below zero, the system rejects the submission with a clear error message. The admin must enter a non-negative value before the adjustment can be saved.
- What if a notification recipient list is empty for an order or alert — should the event be logged as sent with no recipients, or flagged as a configuration error?
- What happens when a reservation cycles between "Sales Transaction" and another status multiple times — are historical point changes fully reversed each time?
- What happens when the daily view is requested for a date before the CRM recorded any leaderboard data (system inception date)?

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Admins MUST be able to manually edit an agent's leaderboard point total for a specific period — selecting the period type (daily, weekly, or monthly) and the relevant date — with a mandatory reason/note field before saving.
- **FR-002**: All point adjustments MUST be recorded in an audit log containing: editor identity, timestamp, period type, period date, original value, new value, and reason. The audit log MUST be accessible to admins only; sales managers and agents MUST NOT have access.
- **FR-003**: Admins MUST be prevented from saving a point adjustment without providing a reason/note.
- **FR-003a**: The system MUST reject any point adjustment that would result in a negative period total, displaying a clear error message and requiring the admin to enter a non-negative value.
- **FR-004**: The leaderboard MUST only include reservations with the status "Sales Transaction" when calculating an agent's points during each scheduled recalculation.
- **FR-005**: When a reservation's status changes away from "Sales Transaction," those points MUST be removed from the agent's score on the next scheduled recalculation.
- **FR-005a**: The leaderboard scoring recalculation MUST run on a configurable schedule (default: nightly) and process all reservation status changes since the previous run.
- **FR-006**: The daily leaderboard view MUST allow users to select any past date and display the leaderboard data recorded for that date.
- **FR-007**: When no leaderboard data exists for a selected date, the system MUST display a clear "no data" message rather than falling back to today's data.
- **FR-008**: When a new order is created, the system MUST send an in-app system notification AND an email to the responsible agent, and send an email to all sales managers and admins.
- **FR-008a**: Order email content MUST be generated using the order's current state at the time of dispatch, not a snapshot captured when the event was triggered.
- **FR-009**: When a CRM alert is triggered, the system MUST send an in-app system notification AND an email to the responsible agent associated with that alert.
- **FR-010**: Failed email notifications MUST be retried automatically and the failure (including recipient address and event details) logged for admin visibility.
- **FR-011**: The sales manager home page MUST include a top performers widget displaying ranked agents with names and scores.
- **FR-012**: The top performers widget MUST reflect the current leaderboard standings on every page load.
- **FR-013**: All primary CRM pages (dashboard, orders list, leaderboard) MUST load and become fully interactive within 3 seconds under normal usage.

### Key Entities

- **Leaderboard Entry**: An agent's score record for a given period (daily/weekly/monthly), including points total, period type, period date, and last modification metadata. Each period+date combination is a distinct record that can be adjusted independently.
- **Point Adjustment**: A manual override record linked to a specific Leaderboard Entry (agent + period type + period date), containing the original value, adjusted value, reason, editor identity, and timestamp.
- **Order / Reservation**: A sales record with a status field; only records with status "Sales Transaction" contribute to leaderboard points.
- **Email Notification**: A system-triggered message describing a CRM event. For orders: sent to the responsible agent (also receives an in-app notification) plus all sales managers and admins. For alerts: sent to the responsible agent only (also receives an in-app notification). Email content is generated from current order/alert data at dispatch time. Tracks delivery status and retry count.
- **In-App System Notification**: A real-time notification displayed within the CRM interface, sent to the responsible agent when an order is created or an alert fires.
- **Alert**: A CRM-generated event requiring attention, linked to a responsible agent who receives both an in-app and email notification.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Admins can locate, edit, and save a leaderboard point adjustment in under 2 minutes from start to completion.
- **SC-002**: 100% of point adjustments are captured in the audit log with all required fields (editor, timestamp, original value, new value, reason).
- **SC-003**: After each scheduled recalculation, zero reservations with a status other than "Sales Transaction" contribute points to any agent's leaderboard score.
- **SC-004**: Users can successfully view daily leaderboard data for any date within the past 90 days without errors or incorrect data being displayed.
- **SC-005**: Email notifications for new orders and CRM alerts are delivered to recipients within 5 minutes of the triggering event under normal system load.
- **SC-006**: Email notification delivery success rate reaches 95% or higher for all valid recipient addresses.
- **SC-007**: The top performers widget on the sales manager home page loads and displays accurate, up-to-date rankings on every page load with no errors.
- **SC-008**: CRM primary pages (dashboard, orders list, leaderboard) load within 3 seconds for 90% of page requests under normal usage conditions.

## Assumptions

- Users are authenticated and assigned appropriate roles; admins have leaderboard editing rights and exclusive access to the point adjustment audit log; sales managers have read-only leaderboard access; agents can view their own leaderboard standing only.
- "Sales Transaction" is a fixed, predefined order status in the system — it is not configurable or user-definable.
- The leaderboard supports daily, weekly, and monthly views; the date selection bug only affects the daily view (weekly/monthly are assumed working).
- Email recipient configuration (who receives order notifications and alerts) is already set up in the system; this feature does not add new recipient management UI.
- The top performers widget on the sales manager home page will display standings for the current active leaderboard period (defaulting to the current month).
- CRM performance improvements target the most commonly used pages; a full audit of all pages is out of scope.
- Leaderboard scores are recalculated on a nightly schedule by default; the specific schedule interval is configurable by admins but the default is once per day.
- Manual point adjustments by admins are overrides and do not trigger an immediate recalculation from raw order data — they coexist alongside the system's automated scheduled scoring.
- The system retains daily leaderboard snapshots; historical daily view data is available for at least 90 days back.
