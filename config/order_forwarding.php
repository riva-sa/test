<?php

use App\Models\UnitOrder;

return [

    /*
    |--------------------------------------------------------------------------
    | Enable automatic forwarding for new unit orders
    |--------------------------------------------------------------------------
    |
    | When false, no strategies run (default: backward compatible).
    |
    */
    'enabled' => (bool) env('ORDER_FORWARDING_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Order sources that trigger forwarding
    |--------------------------------------------------------------------------
    |
    | Only orders with an order_source in this list are processed.
    | Typical frontend submissions: frontend_popup, frontend_unit.
    |
    */
    'sources' => [
        UnitOrder::ORDER_SOURCE_FRONTEND_POPUP,
        UnitOrder::ORDER_SOURCE_FRONTEND_UNIT,
        UnitOrder::ORDER_SOURCE_MANAGER,
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude sources from forwarding (e.g. to prevent loops)
    |--------------------------------------------------------------------------
    |
    | Orders with these sources will skip the forwarding logic.
    |
    */
    'exclude_sources' => [
        UnitOrder::ORDER_SOURCE_SOCIAL_MEDIA,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notify specific users (database notifications)
    |--------------------------------------------------------------------------
    */
    'notify_user_ids' => array_filter(array_map('intval', explode(',', (string) env('ORDER_FORWARDING_NOTIFY_USER_IDS', '')))),

    /*
    |--------------------------------------------------------------------------
    | Grant order permissions to all users having these Spatie role names
    |--------------------------------------------------------------------------
    */
    'grant_permission_roles' => array_values(array_filter(array_map('trim', explode(',', (string) env('ORDER_FORWARDING_GRANT_ROLES', ''))))),

    'permission_type' => 'manage',

    /*
    |--------------------------------------------------------------------------
    | Optional webhook (POST JSON with order id and minimal payload)
    |--------------------------------------------------------------------------
    */
    'webhook_url' => env('ORDER_FORWARDING_WEBHOOK_URL'),

    'webhook_timeout' => 5,
];
