# Quickstart: CRM Performance & Leaderboard Fixes

## Overview
This guide covers how to test and verify the new leaderboard accuracy, manual adjustments, and notification systems.

## Local Setup

1.  **Migrate Database**:
    ```bash
    php artisan migrate
    ```

2.  **Seed Test Data**:
    ```bash
    php artisan db:seed --class=LeaderboardTestDataSeeder
    ```

3.  **Run Dev Server**:
    ```bash
    npm run dev
    ```

## Key Workflows

### 1. Manual Leaderboard Adjustment
- Log in as an **Admin**.
- Navigate to **Leaderboard Adjustments** in the admin panel.
- Click **New Adjustment**.
- Select an Agent, Period Type (Daily), and Date.
- Enter the original and adjusted values.
- **Note**: A reason is mandatory.
- Verify the change appears in the Audit Log and reflects on the Leaderboard.

### 2. Verify "Sales Transaction" Points
- Create a new Order and assign it to an Agent.
- Set status to "Open" (status 1).
- Run `php artisan leaderboard:refresh`.
- Verify the Agent's "Reservations" count is **0**.
- Update the Order status to "Sales Transaction" (status 2).
- Run `php artisan leaderboard:refresh`.
- Verify the count is now **1**.

### 3. Testing Notifications
- Create a new Order.
- Check the `crm_notifications` table for a new entry.
- Verify that the responsible agent received an in-app notification.
- If using a mail catcher (like Mailpit), verify emails were sent to:
    - The responsible agent.
    - All Sales Managers.
    - All Admins.

## Performance Verification
- Use the browser's Network tab to verify that the Leaderboard and Dashboard pages load within **3 seconds**.
- Check `storage/logs/laravel.log` for execution times of the `leaderboard:refresh` command.
