<?php

namespace App\Observers;

use App\Models\OrderStatusTransition;
use App\Models\UnitOrder;
use App\Services\ApplicationForwardingService;
use Illuminate\Support\Facades\Log;

class UnitOrderObserver
{
    public function created(UnitOrder $order): void
    {
        // 1. Auto-assign the order to an available sales representative
        app(\App\Services\AutoAssignmentService::class)->assign($order);

        // 2. Forward notifications securely
        app(ApplicationForwardingService::class)->forward($order);
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
