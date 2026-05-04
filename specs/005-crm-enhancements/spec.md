# Feature Specification: CRM Enhancements & Additions

**Feature Branch**: `005-crm-enhancements`  
**Created**: 2026-04-30  
**Status**: Draft  
**Input**: User description: "تعديلات واضافات CRM — تحديث ألوان الحالات، تحسين الملف التعريفي للعميل، إزالة مسؤول المشروع، إضافة رقم تواصل المشروع، إصلاح يوتيوب، نظام إشعارات، إعادة تعيين كلمة المرور، أعمدة مناديب المبيعات، تعديل مصدر الطلب، نظام المستهدفات"

## User Scenarios & Testing *(mandatory)*

### User Story 1 — Unified Status Color System (Priority: P1)

As a CRM user (manager, sales rep, or admin), I see consistent, meaningful status colors across all system pages (order lists, dashboards, charts, board views, customer profiles) so that I can instantly recognize the state of any order without reading text labels.

**Why this priority**: Status colors are a foundational visual element that impacts every single page in the CRM. Updating them first ensures visual consistency for all subsequent features.

**Independent Test**: Navigate through all CRM pages (order list, dashboard charts, board view, customer profile, order details) and verify each status uses the correct color code.

**Color Mapping**:

| الحالة | اللون | الكود |
| --- | --- | --- |
| **جديد** (0) | أزرق | `#3B82F6` |
| **طلب مفتوح** (1) | برتقالي | `#F97316` |
| **معاملات بيعية** (2) | بنفسجي | `#5457E3` |
| **مغلق** (3) | رمادي | `#9CA3AF` |
| **مكتمل** (4) | أخضر | `#22C55E` |
| **قائمة انتظار** (5) | أصفر | `#EAB308` |

**Acceptance Scenarios**:

1. **Given** any page displaying order statuses, **When** an order has status "جديد", **Then** it displays with blue color `#3B82F6`
2. **Given** the dashboard charts, **When** status distribution is shown, **Then** each status segment uses its designated hex color code
3. **Given** a board/kanban view, **When** columns are rendered, **Then** column headers and cards use the new color scheme
4. **Given** any page in the system, **When** the same status appears, **Then** the color is identical across all pages

---

### User Story 2 — Enhanced Customer Profile (Priority: P1)

As a sales manager, I want to view a comprehensive customer profile that shows all of a customer's orders with full details for each order, so that I can understand the customer's complete history and provide better service.

**Why this priority**: Directly impacts sales team productivity and customer service quality. Understanding a customer's full history is critical for informed decision-making.

**Independent Test**: Navigate to any customer's profile page and verify all their orders are displayed with complete details including project, unit, status, dates, assigned sales rep, notes, and order source.

**Acceptance Scenarios**:

1. **Given** a customer with multiple orders, **When** I open their profile, **Then** I see a summary card with total orders, latest status, first and last order dates
2. **Given** a customer profile page, **When** I view the order list, **Then** each order shows: project name, unit details, current status (with correct color), assigned sales rep, order source, creation date, and last update date
3. **Given** a customer profile with orders, **When** I click on an individual order, **Then** I am navigated to the full order details page
4. **Given** a customer profile, **When** I view order details, **Then** I can see notes count, last activity summary, and marketing source for each order

---

### User Story 3 — Notification System (Priority: P1)

As a manager, I want to send individual notifications, group notifications, and general announcements to employees through the CRM dashboard. As an employee, I want to receive and view these notifications in my dashboard. The system must track read/unread status per user.

**Why this priority**: Communication is essential for team coordination. A centralized notification system eliminates reliance on external tools and ensures important messages reach all team members within the platform they're already using.

**Independent Test**: A manager sends a group notification, individual notification, and a general announcement. Verify all recipients see the notifications, can mark them as read, and the manager can see who has read each notification.

**Acceptance Scenarios**:

1. **Given** a manager is logged in, **When** they create a notification, **Then** they can choose between: individual (specific user), group (all sales reps), or general announcement
2. **Given** a notification is sent, **When** the recipient logs in, **Then** they see a notification indicator (bell icon with unread count) in the CRM header
3. **Given** an employee clicks the notification icon, **When** the notifications panel opens, **Then** they see a list of notifications sorted by date (newest first) with read/unread visual distinction
4. **Given** a general announcement is posted, **When** any CRM user views the announcements page, **Then** they see all past announcements (both group and personalized) in chronological order
5. **Given** a manager sends a notification, **When** they check the notification details, **Then** they see a list of recipients with read/unread status (who has seen it and who hasn't)
6. **Given** a manager wants to assign tasks, **When** they create a notification with notes/tasks, **Then** the notification includes the task content and is labeled as a task-type notification
7. **Given** any notification is received, **When** the user reads it, **Then** its status updates to "read" in real time and the unread count decreases
8. **Given** a user is logged into the CRM, **When** a new notification is sent to them, **Then** the notification bell count updates in real time without requiring a page refresh
9. **Given** a manager creates a notification, **When** they compose the content, **Then** a rich text editor is available with bold, links, lists, and standard formatting options
10. **Given** a notification with rich text content, **When** a recipient views it in the panel or announcements page, **Then** the formatting renders correctly

---

### User Story 4 — Sales Targets System (Priority: P2)

As a manager, I want to set performance targets for sales representatives and track their progress. As a sales representative, I want to see my targets and current progress on my dashboard.

**Why this priority**: Targets drive sales performance and accountability. Giving reps visibility into their own progress motivates achievement while giving managers data-driven oversight.

**Independent Test**: A manager sets all four target types for a sales rep. The rep's dashboard shows progress bars/metrics for each target. Verify targets update automatically as orders change status.

**Target Types**:

- **Monthly Orders Target**: Number of orders transitioned from "جديد" to any other status within the current month (e.g., 960 orders/month per rep)
- **Daily Orders Target**: Number of orders transitioned from "جديد" to any other status within the current day (24-hour period) (e.g., 40 orders/day per rep)
- **Reservations Target**: Number of orders transitioned to "معاملات بيعية" status within the current month (e.g., 5 reservations/month per rep)
- **Sales Target**: Number of orders transitioned to "مكتمل" status within the current month (e.g., 5 completed sales/month per rep)

**Acceptance Scenarios**:

1. **Given** a manager is on the targets management page, **When** they set a monthly orders target for a sales rep, **Then** the target value is saved and visible to both the manager and the rep
2. **Given** a sales rep's dashboard, **When** they log in, **Then** they see all four target types with current progress (e.g., progress bar showing 28/40 daily, 650/960 monthly)
3. **Given** a sales rep transitions an order from "جديد" to "طلب مفتوح", **When** the status change is saved, **Then** both the monthly and daily order targets increment automatically
4. **Given** an order is moved to "معاملات بيعية", **When** the status change is saved, **Then** the reservations target counter increments
5. **Given** an order is moved to "مكتمل", **When** the status change is saved, **Then** the sales target counter increments
6. **Given** a new calendar month begins, **When** a rep views their dashboard, **Then** the monthly targets reset to zero progress while the target value remains
7. **Given** a new day begins (midnight), **When** a rep views their dashboard, **Then** the daily orders target resets to zero progress
8. **Given** a manager navigates to the targets management page, **When** they view the page, **Then** they can set/edit target values for each sales rep individually or apply a default value to all reps
9. **Given** a manager views the targets page, **When** the leaderboard is displayed, **Then** they see all sales reps ranked by performance with visual comparison charts across all four target types
10. **Given** a manager navigates to the targets history view, **When** they select a past month, **Then** they see each sales rep's performance summary for that month across all four target types, recomputed from order transition history
11. **Given** a manager views the leaderboard, **When** they configure weights for each target type (e.g., sales 40%, reservations 30%, monthly orders 20%, daily orders 10%), **Then** reps are ranked by the weighted composite score and the ranking updates immediately

---

### User Story 5 — Sales Representatives Status Columns (Priority: P2)

As a sales manager, I want to see order count breakdowns by status for each sales representative on the sales reps page, so I can quickly assess workload distribution and performance.

**Why this priority**: Provides immediate visibility into team workload and pipeline distribution without navigating away from the team management view.

**Independent Test**: Open the sales representatives page and verify each rep row shows columns with correct counts for all six statuses.

**Acceptance Scenarios**:

1. **Given** the sales representatives page, **When** it loads, **Then** each row shows columns for: جديد, طلب مفتوح, قائمة انتظار, مغلق, مكتمل, معاملات بيعية
2. **Given** a sales rep with 15 "جديد" orders and 8 "طلب مفتوح" orders, **When** I view their row, **Then** I see "15" under جديد and "8" under طلب مفتوح
3. **Given** an order's status changes, **When** the sales reps page is refreshed, **Then** the counts reflect the updated status

---

### User Story 6 — Remove Project Manager from Assigned Orders View (Priority: P2)

As a system administrator, I want to remove the "project manager" role from the order assignment display mechanism, retaining only automatic distribution, manual permissions, or manual order transfer.

**Why this priority**: Simplifies the order assignment flow and removes confusion caused by an intermediate role that is no longer needed in the workflow.

**Independent Test**: Verify that the assigned orders view no longer shows or filters by project manager. Verify that auto-distribution, manual permissions, and manual transfer still work correctly.

**Acceptance Scenarios**:

1. **Given** the order assignment mechanism, **When** new orders are distributed, **Then** only automatic distribution or manual transfer/permissions are used (no project manager assignment view)
2. **Given** an existing order previously visible via project manager, **When** the project manager role is removed from assignment logic, **Then** the order remains accessible via the project manager's existing access (grandfather clause) as well as via direct assignment or explicit permissions
3. **Given** an order needs to be reassigned, **When** a manager transfers it, **Then** manual transfer to another user works correctly
4. **Given** a new order is created after the change, **When** it is assigned, **Then** the project manager role is NOT used for determining visibility — only direct assignment, permissions, or auto-distribution apply

---

### User Story 7 — Project Contact Number for External Projects Page (Priority: P2)

As a CRM user viewing the external projects page, I want to see a WhatsApp button and a call button for each project that uses a project-specific contact number, so I can quickly reach the right person for each project.

**Why this priority**: After removing the project manager, each project needs its own contact number to enable direct communication via the external projects page.

**Independent Test**: Add a contact number to a project, navigate to the external projects page, and verify the WhatsApp and call buttons use that project-specific number.

**Acceptance Scenarios**:

1. **Given** a project edit/creation form, **When** a manager adds/edits the project, **Then** there is a field to enter a contact phone number
2. **Given** a project with a contact number, **When** I view the external projects page, **Then** the WhatsApp button links to `https://wa.me/<project_contact_number>`
3. **Given** a project with a contact number, **When** I view the external projects page, **Then** the call button links to `tel:<project_contact_number>`
4. **Given** a project without a contact number, **When** I view the external projects page, **Then** the communication buttons are hidden or disabled

---

### User Story 8 — Order Source: Ad Group Name Instead of Ad Name (Priority: P3)

As a sales manager, I want the orders page to display the "Ad Group Name" (اسم المجموعة الاعلانية) instead of the "Ad Name" (اسم الاعلان) in the order source information, so that I can better categorize and analyze lead origins.

**Why this priority**: Provides better categorization of marketing leads at the campaign group level rather than individual ad level, enabling more useful analytics.

**Independent Test**: View the orders page and verify the source column shows the ad group name (ad_set field) instead of the ad name.

**Acceptance Scenarios**:

1. **Given** the orders list page, **When** it displays order source information, **Then** it shows the ad group name (المجموعة الاعلانية) instead of the individual ad name
2. **Given** an order imported from social media with an ad group name, **When** I view the order details, **Then** the marketing source displays the ad group name
3. **Given** orders filtered or sorted by source, **When** the ad group name is used, **Then** filtering and sorting work correctly based on ad group

---

### User Story 9 — Fix YouTube Video Embedding on Project Page (Priority: P3)

As a content manager, I want to add YouTube video links to a project page and have them render as embedded video players, so visitors can watch project-related content directly on the page.

**Why this priority**: Video content enhances project pages, but the current embedding is broken, degrading the user experience.

**Independent Test**: Add a YouTube link to a project page, save, and verify the video renders as a playable embedded player on the frontend.

**Acceptance Scenarios**:

1. **Given** a project edit form, **When** I add a YouTube URL, **Then** the system accepts and saves it
2. **Given** a project with a YouTube URL saved, **When** a visitor views the project page, **Then** the video is displayed as an embedded player
3. **Given** various YouTube URL formats (standard, shortened, embed), **When** any format is provided, **Then** the system normalizes it and renders the video correctly

---

### User Story 10 — Employee Password Reset (Priority: P3)

As an administrator or manager, I want to trigger a password reset for any employee, so that employees who forget their passwords can regain access without admin manually setting a new password.

**Why this priority**: Basic access management functionality that improves self-service and reduces admin burden.

**Independent Test**: Trigger a password reset for an employee, verify the reset email is sent, and confirm the employee can set a new password via the link.

**Acceptance Scenarios**:

1. **Given** a manager viewing the sales representatives page, **When** they click "Reset Password" for an employee, **Then** a password reset email is sent to the employee's email address
2. **Given** an employee receives the reset email, **When** they click the reset link, **Then** they are taken to a form to set a new password
3. **Given** the employee sets a new password, **When** they submit the form, **Then** they can log in with the new password

---

### Edge Cases

- What happens when a sales rep has zero orders? → The status count columns display "0" for all statuses
- What happens when a notification is sent to a deactivated employee? → The notification is stored but not delivered; a visual indicator shows the recipient is inactive
- What happens when a project has no contact number and the project manager is removed? → WhatsApp/call buttons are hidden on the external projects page
- What happens when a YouTube URL is invalid or removed from YouTube? → The system shows a placeholder or error message instead of a broken embed
- What happens when targets reset at midnight but the rep is mid-session? → The dashboard updates on next page load/refresh with reset values
- What happens when an order is moved back to "جديد" from another status? → It does not count toward the daily/monthly target (only "from جديد to another" transitions count)
- What happens when an order is reassigned to a different rep and then transitioned? → The rep who performs the actual status transition gets credit toward their targets, not the originally assigned rep
- What happens when the project manager role is removed but existing orders were only accessible through it? → Existing orders retain access via project.sales_manager_id (grandfather clause); only new orders follow the new assignment rules
- What happens when a manager changes target values mid-month? → The new target value applies immediately; progress (already counted transitions) is preserved

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST use the following hex color codes for all status displays across all pages: جديد=#3B82F6, طلب مفتوح=#F97316, معاملات بيعية=#5457E3, مغلق=#9CA3AF, مكتمل=#22C55E, قائمة انتظار=#EAB308
- **FR-002**: System MUST display all orders belonging to a customer on their profile page, showing project name, unit, status, assigned sales rep, order source, dates, and notes count
- **FR-003**: System MUST remove the project manager role from the assigned orders view mechanism; only automatic distribution, manual permission grants, and manual order transfers remain
- **FR-004**: System MUST provide a contact phone number field on each project record, displayed via WhatsApp and call buttons on the external projects page
- **FR-005**: System MUST correctly embed YouTube videos on project pages, supporting standard, shortened, and embed URL formats
- **FR-006**: System MUST support creating and sending notifications of types: individual, group, and general announcement, with a rich text editor for content (supporting bold, links, lists, and other standard formatting)
- **FR-007**: System MUST track read/unread status per user per notification and display who has viewed each notification to the sender
- **FR-008**: System MUST provide a dedicated announcements page showing all past notifications (group and personalized) for the logged-in user with pagination
- **FR-019**: System MUST retain all notifications indefinitely; the UI MUST paginate older notifications with a "load more" pattern rather than deleting or archiving them
- **FR-009**: System MUST support task/note-type notifications that can be assigned to team members with read tracking
- **FR-010**: System MUST allow managers to trigger a password reset email for any employee
- **FR-011**: System MUST display per-status order counts (جديد, طلب مفتوح, قائمة انتظار, مغلق, مكتمل, معاملات بيعية) as columns on the sales representatives page
- **FR-012**: System MUST display the ad group name (المجموعة الاعلانية / ad_set) instead of the ad name in order source information across all relevant pages
- **FR-013**: System MUST support four target types: monthly orders, daily orders, reservations, and sales — each with configurable values per sales rep
- **FR-014**: System MUST display target progress (current vs. target) on each sales rep's dashboard
- **FR-015**: System MUST automatically increment target counters when orders transition from "جديد" to another status (for daily/monthly targets) or to specific statuses (for reservations/sales targets)
- **FR-016**: System MUST automatically reset daily targets every 24 hours and monthly targets at the start of each calendar month
- **FR-017**: System MUST provide a manager-only page for setting and editing target values for all sales reps
- **FR-018**: System MUST display a notification bell icon with unread count in the CRM header/navigation for all authenticated users
- **FR-020**: System MUST provide a leaderboard view on the targets management page showing all sales reps ranked by a weighted composite score across all four target types, with manager-configurable weights per target type and visual comparison charts
- **FR-021**: System MUST deliver notifications in real time (live push) so that the notification bell count and notification panel update without requiring page refresh
- **FR-022**: System MUST provide a historical target performance view for managers, recomputed from order transition history, showing monthly summaries of each sales rep's performance across all four target types

### Key Entities

- **Notification**: Represents a message sent within the CRM. Has type (individual/group/announcement/task), sender, rich text content (HTML), creation date, and list of recipients
- **NotificationRecipient**: Links a notification to a user with read/unread status and read timestamp
- **SalesTarget**: Represents a performance target for a sales rep. Has target type (monthly_orders/daily_orders/reservations/sales), target value, sales rep reference, and applicable period
- **LeaderboardConfig**: Stores manager-configurable weights for each of the four target types used to compute the composite ranking score
- **TargetProgress**: Tracks actual progress toward a target. Auto-computed from order status transitions within the applicable period. Historical performance is recomputed on-demand from order transition history (no stored snapshots)
- **ProjectContactNumber**: A phone number associated with a project for external communication (may be stored as a field on the existing Project entity)

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: All six order statuses display with their designated colors consistently across 100% of system pages within one session
- **SC-002**: Customer profile loads all associated orders within 2 seconds for customers with up to 500 orders
- **SC-003**: Notifications reach recipients within 5 seconds of being sent and display in the notification panel
- **SC-004**: Read/unread tracking accuracy is 100% — every read action is recorded and reflected to the sender
- **SC-005**: Target progress updates within 3 seconds of any qualifying order status transition
- **SC-006**: Sales representatives page displays accurate per-status counts matching the actual database totals
- **SC-007**: YouTube videos render correctly for 100% of valid YouTube URL formats provided
- **SC-008**: Password reset emails are delivered within 1 minute of the manager triggering the reset
- **SC-009**: All target counters reset correctly at the designated time boundaries (daily/monthly)
- **SC-010**: 100% of order source displays show ad group name instead of ad name

## Localization *(mandatory)*

- **L10N-001**: All user-facing strings MUST have translations in Arabic (ar). The CRM is Arabic-first.
- **L10N-002**: UI MUST maintain layout integrity in RTL (Right-to-Left) direction.
- **L10N-003**: Notification content supports Arabic text with proper rendering.
- **L10N-004**: All new labels (target names, column headers, button labels) MUST be in Arabic.

## Assumptions

- The existing CRM authentication and role system (sales, sales_manager, Admin, developer, follow_up) will be reused for all new features
- Removing the project manager from the assignment view uses a grandfather clause: existing orders accessible via `project.sales_manager_id` retain that access path; only newly created orders follow the new rules (auto-distribution, manual permissions, manual transfer only)
- The notification system is internal to the CRM dashboard only (no push notifications to mobile devices or external services)
- Password reset uses the existing email delivery infrastructure already configured in the system
- The "ad group name" corresponds to the existing `ad_set` field on the UnitOrder model
- Target values are set per individual sales rep (not per team or globally), though a manager can apply a default value to all reps at once
- The external projects page already exists and currently has communication buttons that will be updated to use the new project-specific contact number
- YouTube embedding fix applies to the frontend project single page where project media is displayed
- Daily target reset occurs at midnight (server timezone); monthly reset occurs on the 1st of each month at midnight
- Target counting only counts transitions FROM "جديد" (status 0) to any other status — reverse transitions or transitions between non-جديد statuses do not count toward daily/monthly order targets
- Target credit is attributed to the user who performs the status transition (the `last_action_by_user_id`), not the originally assigned sales user
- The notification system does not require email delivery — it is purely in-app within the CRM dashboard
- Notifications are retained indefinitely in the database; no automatic cleanup or archiving is performed

## Clarifications

### Session 2026-04-30

- Q: How long are notifications retained, and what happens to old notifications? → A: Retain indefinitely, paginate in UI with a "load more" pattern
- Q: Should managers see a consolidated view of all reps' target progress? → A: Full leaderboard with ranking and visual comparison charts across all four target types
- Q: Should notifications appear in real-time or update on page load only? → A: Real-time push — live updates without page refresh
- Q: When an order is reassigned between reps, who gets target credit for the status transition? → A: The rep who performs the status transition gets credit
- Q: How should existing orders accessible only via project manager be handled after role removal? → A: Grandfather clause — existing orders retain project manager access, new orders follow new rules only
- Q: Should managers be able to view historical target performance for past months? → A: Historical via recomputation from order transition history, with a monthly summary view
- Q: What ranking metric should the sales leaderboard use? → A: Weighted composite score combining all four target types with configurable weights
- Q: Should notification content support rich text or plain text? → A: Rich text with full editor (bold, links, lists, etc.)
