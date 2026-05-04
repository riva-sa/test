# Internal Event Contracts: CRM Enhancements

No external APIs are exposed. All features are internal CRM enhancements. The following internal event contracts govern inter-component communication.

---

## Broadcasting Events (Reverb WebSocket)

### NewCrmNotification

**Channel**: `private-notifications.{userId}` (per-user private channel)  
**Event name**: `NewCrmNotification`  
**Payload**:

```json
{
  "id": 42,
  "type": "individual|group|announcement|task",
  "title": "عنوان الإشعار",
  "sender_name": "أحمد",
  "created_at": "2026-04-30T14:00:00Z",
  "unread_count": 5
}
```

**Triggered by**: `CrmNotification` model creation (via observer or service)  
**Consumed by**: Livewire notification bell component (all CRM pages)

---

## Observer Contracts

### UnitOrderObserver::updating

**Trigger**: `UnitOrder.status` field changes  
**Side effects**:
1. Insert row into `order_status_transitions` table with `from_status`, `to_status`, `user_id` (from `auth()->id()` or `last_action_by_user_id`)
2. Log structured JSON: `{"event": "order_status_transition", "order_id": ..., "from": ..., "to": ..., "user_id": ...}`

---

## Service Contracts

### CrmNotificationService

| Method | Input | Output | Side Effects |
|--------|-------|--------|-------------|
| `send(type, sender, title, content, recipientIds)` | enum type, User sender, string title, string content, array userIds | CrmNotification model | Inserts notification + recipients, broadcasts event |
| `sendToGroup(sender, title, content)` | User sender, string title, string content | CrmNotification model | Resolves all sales reps, calls `send()` with type=group |
| `sendAnnouncement(sender, title, content)` | User sender, string title, string content | CrmNotification model | Resolves all CRM users, calls `send()` with type=announcement |
| `markAsRead(notificationId, userId)` | int notificationId, int userId | void | Sets `read_at` on recipient row |

### TargetTrackingService

| Method | Input | Output |
|--------|-------|--------|
| `getProgress(userId, type, periodStart, periodEnd)` | int userId, string type, Carbon start, Carbon end | int count |
| `getAllProgress(userId)` | int userId | array [type => ['current' => int, 'target' => int]] |
| `getLeaderboard(month?)` | Carbon? month | Collection of [user, scores, composite_score, rank] |
| `getHistoricalPerformance(userId, month)` | int userId, Carbon month | array [type => int count] |
