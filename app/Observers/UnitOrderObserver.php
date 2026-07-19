<?php

namespace App\Observers;

use App\Models\OrderStatusTransition;
use App\Models\UnitOrder;
use App\Services\ApplicationForwardingService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UnitOrderObserver
{
    public function creating(UnitOrder $order): void
    {
        if (auth()->check()) {
            $user = auth()->user();
            if ($user instanceof \App\Models\User && $user->hasRole(config('lead_import.sales_role', 'sales'))) {
                // Assign to the creator sales rep
                $order->assigned_sales_user_id = $user->id;

                // If order_source is not set, default to manager (manual add)
                if (!$order->order_source) {
                    $order->order_source = UnitOrder::ORDER_SOURCE_MANAGER;
                }

                // Ensure user_id is set to creator if not set
                if (!$order->user_id) {
                    $order->user_id = $user->id;
                }
            }
        }
    }

    public function created(UnitOrder $order): void
    {
        // If the order already has an assigned sales representative (e.g., set to creator during manual creation),
        // ensure they have management permission on the order.
        if ($order->assigned_sales_user_id) {
            \App\Models\OrderPermission::firstOrCreate([
                'user_id' => $order->assigned_sales_user_id,
                'unit_order_id' => $order->id,
                'permission_type' => 'manage',
            ], [
                'granted_by' => auth()->id() ?? $order->user_id,
            ]);
        }

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
        $userId = auth()->id() ?? $order->last_action_by_user_id;

        // 1. Handle Status Transitions
        if ($order->isDirty('status')) {
            OrderStatusTransition::create([
                'unit_order_id' => $order->id,
                'user_id' => $userId,
                'attributed_user_id' => $order->assigned_sales_user_id,
                'from_status' => $order->getOriginal('status'),
                'to_status' => $order->status,
            ]);

            // Log::info('order_status_transition', [
            //     'event' => 'order_status_transition',
            //     'order_id' => $order->id,
            //     'from_status' => $order->getOriginal('status'),
            //     'to_status' => $order->status,
            //     'user_id' => $userId,
            // ]);
        }

        // 2. Handle Data Changes
        $excludedFields = ['status', 'updated_at', 'created_at', 'last_action_by_user_id', 'import_batch_id'];
        $changes = array_diff_assoc($order->getDirty(), $order->getOriginal());
        
        foreach ($changes as $field => $newValue) {
            if (in_array($field, $excludedFields)) continue;

            DB::table('order_data_changes')->insert([
                'unit_order_id' => $order->id,
                'user_id' => $userId,
                'field' => $field,
                'old_value' => $order->getOriginal($field),
                'new_value' => $newValue,
                'created_at' => now(),
            ]);
        }
    }
}
