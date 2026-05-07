# Internal Contracts: Leaderboard & Notifications

## CLI Commands

### `leaderboard:refresh`
Calculates and persists leaderboard snapshots.

**Signature**: `php artisan leaderboard:refresh {--date= : Y-m-d format}`

**Behavior**:
1. Defaults to `today` if no date provided.
2. Fetches all active users with role `sales`.
3. Calculates metrics using `TargetTrackingService` (transition-based).
4. Applies `LeaderboardAdjustment` overrides.
5. Updates or creates `LeaderboardSnapshot` for each user.
6. Logs execution time and results.

## Notifications

### `UnitOrderUpdated`
Central notification for all order-related events.

**Payload**:
```php
[
    'order_id' => int,
    'type' => string, // 'new_order', 'status_update', 'new_note', etc.
    'data' => array,  // dynamic metadata (e.g., old_status, new_status)
    'message_ar' => string,
    'message_en' => string,
]
```

**Channels**:
- `database`: Used for real-time in-app alerts for agents.
- `mail`: Used for critical updates for agents, and all updates for managers/admins.

## Services

### `NotificationService`
Encapsulates notification dispatching logic.

**`notifyNewOrder(UnitOrder $order): void`**
- Agent: `database` + `mail`
- Sales Managers: `mail`
- Admins: `mail`

**`notifyCRMAlert(Alert $alert): void`**
- Agent: `database` + `mail`
