# Data Model: CRM Performance, Notifications & Leaderboard Fixes

## Entities

### LeaderboardSnapshot
Captures the state of an agent's performance metrics for a specific date.

| Field | Type | Description |
|-------|------|-------------|
| `id` | primary key | Unique identifier |
| `user_id` | foreign key | Link to `users` table (sales agent) |
| `snapshot_date` | date | The date this snapshot represents |
| `monthly_orders`| unsigned int| Points from monthly order transitions |
| `daily_orders`  | unsigned int| Points from daily order transitions |
| `reservations`  | unsigned int| Points from "Sales Transaction" transitions |
| `sales`         | unsigned int| Points from "Completed" status |
| `composite_score`| decimal(5,2)| Weighted total score for the leaderboard |
| `created_at`    | timestamp | Record creation time |
| `updated_at`    | timestamp | Record update time |

*Indices: Unique on `(user_id, snapshot_date)`*

### LeaderboardAdjustment
Stores manual overrides for leaderboard metrics with an audit trail.

| Field | Type | Description |
|-------|------|-------------|
| `id` | primary key | Unique identifier |
| `adjusted_by` | foreign key | User ID of the admin who made the change |
| `user_id` | foreign key | User ID of the agent whose score was adjusted |
| `period_type` | enum | 'daily', 'weekly', 'monthly' |
| `period_date` | date | The start date of the period being adjusted |
| `metric_type` | enum | Which metric is being adjusted |
| `original_value`| decimal(5,2)| Score before adjustment |
| `adjusted_value`| decimal(5,2)| Score after adjustment |
| `reason` | text | Mandatory explanation for the adjustment |
| `created_at` | timestamp | Record creation time |

### CrmNotification
Tracks the dispatch and delivery status of system-generated notifications.

| Field | Type | Description |
|-------|------|-------------|
| `id` | primary key | Unique identifier |
| `type` | string | Type of notification (e.g., 'new_order', 'alert') |
| `order_id` | foreign key | Link to the related `unit_orders` (if applicable) |
| `created_at` | timestamp | When the notification event occurred |

### CrmNotificationRecipient
Tracks delivery status for individual recipients of a notification.

| Field | Type | Description |
|-------|------|-------------|
| `id` | primary key | Unique identifier |
| `notification_id`| foreign key | Link to `crm_notifications` |
| `user_id` | foreign key | Recipient user ID |
| `channel` | enum | 'mail', 'database' |
| `status` | enum | 'pending', 'sent', 'failed' |
| `error_message` | text | Failure details if status is 'failed' |
| `retry_count` | unsigned int| Number of delivery attempts |
| `sent_at` | timestamp | Time of successful delivery |

## Relationships

- `User` hasMany `LeaderboardSnapshot`
- `User` hasMany `LeaderboardAdjustment` (as agent)
- `User` hasMany `LeaderboardAdjustment` (as admin via `adjusted_by`)
- `CrmNotification` hasMany `CrmNotificationRecipient`
- `UnitOrder` hasMany `CrmNotification`
