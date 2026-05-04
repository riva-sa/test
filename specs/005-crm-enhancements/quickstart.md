# Quickstart: CRM Enhancements

## Prerequisites

- PHP 8.2+, Composer, Node.js 18+, MySQL, Redis
- Existing Riva CRM codebase on branch `005-crm-enhancements`

## Setup

```bash
# Install PHP dependencies (adds laravel/reverb)
composer install

# Install JS dependencies (adds laravel-echo, pusher-js, trix)
npm install

# Copy env additions for Reverb
# Add to .env:
#   BROADCAST_CONNECTION=reverb
#   REVERB_APP_ID=riva-crm
#   REVERB_APP_KEY=<generate>
#   REVERB_APP_SECRET=<generate>
#   REVERB_HOST=127.0.0.1
#   REVERB_PORT=8080

# Run migrations (new tables + project contact_phone column)
php artisan migrate

# Seed leaderboard default weights
php artisan db:seed --class=LeaderboardConfigSeeder

# Build frontend assets
npm run build
```

## Running

```bash
# Start Laravel dev server
php artisan serve

# Start Reverb WebSocket server (required for real-time notifications)
php artisan reverb:start

# Start queue worker (for notification broadcasting)
php artisan queue:work

# Frontend dev (with hot reload)
npm run dev
```

## Key Routes (new)

| Route | Component | Purpose |
|-------|-----------|---------|
| `/crm/notifications` | Notifications | Send & manage notifications |
| `/crm/announcements` | Announcements | View all announcements |
| `/crm/targets` | SalesTargets | Set & view target values |
| `/crm/leaderboard` | Leaderboard | Performance rankings |
| `/crm/reset-password/{token}` | PasswordReset | Employee password reset form |

## Verification

1. Navigate to `/crm` — verify status colors match hex codes in spec
2. Open a customer profile — verify all order details display
3. Send a test notification — verify bell icon updates in real time
4. Set a target for a rep — transition an order and verify progress updates
5. Check sales reps page — verify status count columns show correct numbers
6. Add a YouTube URL to a project — verify embed renders on frontend
7. Add a contact phone to a project — verify WhatsApp/call buttons work
