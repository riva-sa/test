# Data Model: CRM Enhancements & Additions

**Feature**: 005-crm-enhancements | **Date**: 2026-04-30

---

## New Tables

### crm_notifications

In-app notifications sent by managers (distinct from Laravel's system `notifications` table).

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | |
| type | enum('individual','group','announcement','task') | NOT NULL | Notification category |
| sender_id | bigint unsigned | FK → users.id, NOT NULL | Manager who sent it |
| title | varchar(255) | NOT NULL | Notification title |
| content | text | NOT NULL | Rich text content (sanitized HTML) |
| created_at | timestamp | NOT NULL | |
| updated_at | timestamp | NOT NULL | |

**Indexes**: `(type)`, `(sender_id)`, `(created_at)`

### crm_notification_recipients

Per-user read tracking for CRM notifications.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | |
| notification_id | bigint unsigned | FK → crm_notifications.id, CASCADE | |
| user_id | bigint unsigned | FK → users.id, CASCADE | |
| read_at | timestamp | nullable | NULL = unread |
| created_at | timestamp | NOT NULL | |

**Indexes**: `(user_id, read_at)` (unread count query), `(notification_id)`, UNIQUE `(notification_id, user_id)`

### order_status_transitions

Immutable log of every order status change. Source of truth for target tracking and historical performance.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | |
| unit_order_id | bigint unsigned | FK → unit_orders.id, CASCADE | |
| user_id | bigint unsigned | FK → users.id, SET NULL, nullable | User who performed the transition |
| from_status | tinyint unsigned | NOT NULL | Previous status code (0-5) |
| to_status | tinyint unsigned | NOT NULL | New status code (0-5) |
| created_at | timestamp | NOT NULL | Transition timestamp |

**Indexes**: `(user_id, from_status, created_at)` (target progress queries), `(unit_order_id)`, `(created_at)`

### sales_targets

Configurable target values per sales rep per target type.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | |
| user_id | bigint unsigned | FK → users.id, CASCADE | Sales rep |
| type | enum('monthly_orders','daily_orders','reservations','sales') | NOT NULL | Target category |
| target_value | int unsigned | NOT NULL, default 0 | Target number for the period |
| created_at | timestamp | NOT NULL | |
| updated_at | timestamp | NOT NULL | |

**Indexes**: UNIQUE `(user_id, type)`, `(type)`

### leaderboard_configs

Manager-configurable weights for the composite leaderboard ranking score.

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | bigint unsigned | PK, auto-increment | |
| target_type | enum('monthly_orders','daily_orders','reservations','sales') | NOT NULL, UNIQUE | |
| weight | decimal(5,2) | NOT NULL, default 25.00 | Percentage weight (sum should = 100) |
| updated_at | timestamp | NOT NULL | |

**Seed data**: 4 rows, each with weight = 25.00

---

## Modified Tables

### projects

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| contact_phone | varchar(20) | nullable, AFTER sales_manager_id | Project-specific contact number for WhatsApp/call buttons |

### unit_orders (no schema change)

- `statusColor()` method updated to return hex codes instead of Tailwind names
- `STATUS_COLORS` constant added with hex code mapping
- No column changes needed — `ad_set`, `last_action_by_user_id` already exist

---

## New Models

### CrmNotification

```
app/Models/CrmNotification.php
```

| Relationship | Type | Target |
|-------------|------|--------|
| sender() | belongsTo | User |
| recipients() | hasMany | CrmNotificationRecipient |
| users() | belongsToMany | User (via crm_notification_recipients) |

### CrmNotificationRecipient

```
app/Models/CrmNotificationRecipient.php
```

| Relationship | Type | Target |
|-------------|------|--------|
| notification() | belongsTo | CrmNotification |
| user() | belongsTo | User |

### OrderStatusTransition

```
app/Models/OrderStatusTransition.php
```

| Relationship | Type | Target |
|-------------|------|--------|
| order() | belongsTo | UnitOrder |
| user() | belongsTo | User |

**Note**: This model has no `updated_at`. Use `$timestamps = false` with manual `created_at` or `const UPDATED_AT = null`.

### SalesTarget

```
app/Models/SalesTarget.php
```

| Relationship | Type | Target |
|-------------|------|--------|
| user() | belongsTo | User |

### LeaderboardConfig

```
app/Models/LeaderboardConfig.php
```

No relationships. Simple key-value for weights.

---

## Modified Models

### User (app/Models/User.php)

New relationships:

| Relationship | Type | Target |
|-------------|------|--------|
| salesTargets() | hasMany | SalesTarget |
| sentNotifications() | hasMany | CrmNotification (as sender) |
| crmNotifications() | belongsToMany | CrmNotification (via crm_notification_recipients) |
| statusTransitions() | hasMany | OrderStatusTransition |

### UnitOrder (app/Models/UnitOrder.php)

New relationships:

| Relationship | Type | Target |
|-------------|------|--------|
| statusTransitions() | hasMany | OrderStatusTransition |

New constants:

```php
const STATUS_COLORS = [
    0 => '#3B82F6', 1 => '#F97316', 2 => '#5457E3',
    3 => '#9CA3AF', 4 => '#22C55E', 5 => '#EAB308',
];
```

### Project (app/Models/Project.php)

Add `contact_phone` to `$fillable`.

---

## State Transitions

### Order Status (existing, unchanged)

```
0 (جديد) → 1 (طلب مفتوح) → 2 (معاملات بيعية) → 4 (مكتمل)
         → 5 (قائمة انتظار)
         → 3 (مغلق)
Any status → Any status (no strict enforcement)
```

### Target Counting Rules

| Transition | Targets Affected |
|-----------|-----------------|
| FROM status 0 → TO any other status | monthly_orders, daily_orders |
| TO status 2 (معاملات بيعية) | reservations |
| TO status 4 (مكتمل) | sales |

Credit: `user_id` on the `order_status_transitions` row (the user who performed the change).

### Notification Lifecycle

```
Created (by manager) → Sent (recipients inserted) → Read (read_at set per recipient)
```

No deletion or archival. Retained indefinitely.

---

## Key Queries

### Unread notification count (bell icon)
```sql
SELECT COUNT(*) FROM crm_notification_recipients
WHERE user_id = ? AND read_at IS NULL
```

### Monthly orders target progress (current month)
```sql
SELECT COUNT(*) FROM order_status_transitions
WHERE user_id = ? AND from_status = 0 AND created_at >= ?
-- ? = first day of current month
```

### Daily orders target progress (today)
```sql
SELECT COUNT(*) FROM order_status_transitions
WHERE user_id = ? AND from_status = 0 AND created_at >= ?
-- ? = start of today (midnight server timezone)
```

### Reservations target progress (current month)
```sql
SELECT COUNT(*) FROM order_status_transitions
WHERE user_id = ? AND to_status = 2 AND created_at >= ?
```

### Sales target progress (current month)
```sql
SELECT COUNT(*) FROM order_status_transitions
WHERE user_id = ? AND to_status = 4 AND created_at >= ?
```

### Historical monthly performance (past month)
```sql
SELECT COUNT(*) FROM order_status_transitions
WHERE user_id = ? AND from_status = 0
AND created_at BETWEEN ? AND ?
-- ? = first day of target month, last day of target month
```

### Sales rep status counts (for status columns)
```sql
SELECT status, COUNT(*) as count FROM unit_orders
WHERE assigned_sales_user_id = ?
GROUP BY status
```
