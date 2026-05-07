<?php

namespace App\Observers;

use App\Models\OrderStatusTransition;
use App\Models\UnitOrder;
use App\Services\ApplicationForwardingService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class UnitOrderObserver
{
    public function created(UnitOrder $order): void
    {
        // 1. Auto-assign the order to an available sales representative
        app(\App\Services\AutoAssignmentService::class)->assign($order);

        // Refresh so the in-memory model reflects the assigned_sales_user_id
        // written by AutoAssignmentService — without this, notifyNewOrder sees null.
        $order->refresh();

        // 2. Forward notifications securely
        app(ApplicationForwardingService::class)->forward($order);

        // 3. Send new-order notifications to agent (in-app + email) and managers/admins (email)
        app(NotificationService::class)->notifyNewOrder($order);
    }

    public function updating(UnitOrder $order): void
    {
        if (! $order->isDirty('status')) {
            return;
        }

        $userId = auth()->id() ?? $order->last_action_by_user_id;

        OrderStatusTransition::create([
            'unit_order_id' => $order->id,
            'user_id' => $userId,
            'from_status' => $order->getOriginal('status'),
            'to_status' => $order->status,
        ]);

        Log::info('order_status_transition', [
            'event' => 'order_status_transition',
            'order_id' => $order->id,
            'from_status' => $order->getOriginal('status'),
            'to_status' => $order->status,
            'user_id' => $userId,
        ]);
    }
}
