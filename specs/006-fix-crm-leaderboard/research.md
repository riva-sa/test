# Research: CRM Performance, Notifications & Leaderboard Fixes

## Findings

### 1. Leaderboard Points Calculation
- **Status Mapping**: "Sales Transaction" is status 2. "Completed" is status 4.
- **Current Logic**: `RefreshLeaderboardCommand` counts the *current* number of orders with status 2.
- **New Requirement**: Transition-based logic. A point is awarded each time an agent converts an order TO "Sales Transaction" status (status 2). These points are permanent.
- **Decision**: Update `RefreshLeaderboardCommand` or create a new `RecalculateLeaderboard` service that processes historical transitions.
- **Implementation**: We can use `TargetTrackingService` which already has `getProgress` for transitions.

### 2. Manual Point Adjustments
- **Current State**: `LeaderboardAdjustment` model and migration exist.
- **Integration**: Not currently integrated into the snapshot calculation logic.
- **Decision**: Update `RefreshLeaderboardCommand` to include adjustments from the `LeaderboardAdjustment` table when calculating the composite score for a period.

### 3. Email Notifications
- **Current State**: `NotificationService` exists and triggers `UnitOrderUpdated` notification.
- **Requirement**: "Responsible agent receives both an in-app system notification and an email; sales managers and all admins receive email for every order."
- **Issue**: Need to verify `UnitOrderUpdated` has the `via` method correctly returning `['mail', 'database']` for agents and `['mail']` for managers/admins.
- **Action**: Check `app/Notifications/UnitOrderUpdated.php`.

### 4. Leaderboard Historical Data (Daily View)
- **Current State**: `LeaderboardSnapshot` stores data by date.
- **Requirement**: Date picker to view past dates.
- **Action**: Update `Leaderboard` Livewire component to accept a date parameter and filter snapshots accordingly.

### 5. Top Performers Widget
- **Requirement**: Widget for Sales Manager dashboard.
- **Action**: Create a Filament widget `TopPerformersWidget` that pulls data from the latest `LeaderboardSnapshot`.

### 6. Performance Optimization
- **Current State**: Reports of slowness on dashboard and orders list.
- **Investigation**: Check for N+1 queries in `UnitOrder` listings and heavy calculations in `Leaderboard` component.
- **Decision**: Use eager loading for relationships and cache complex calculations where appropriate.

## Decisions
- **Decision**: Use `TargetTrackingService` for transition-based point calculation to ensure permanence.
- **Decision**: Adjustments will be stored in `LeaderboardAdjustment` and applied to the relevant snapshot during refresh.
- **Decision**: Notification retry logic will rely on Laravel's built-in queue workers if queues are enabled.

## Alternatives Considered
- **Real-time vs Scheduled**: Scheduled recalculation was chosen per spec to avoid heavy real-time processing during bulk status updates.
- **Manual Adjustments vs Auto-correction**: Manual adjustments with audit log was chosen to handle edge cases that automatic logic might miss.
