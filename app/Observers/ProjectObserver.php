<?php

namespace App\Observers;

use App\Models\OrderPermission;
use App\Models\Project;
use App\Models\UnitOrder;
use Illuminate\Support\Facades\Log;

class ProjectObserver
{
    /**
     * When sales_manager_id changes on a project, automatically grant
     * the OLD manager OrderPermission records on all existing orders
     * so they remain visible after switching to auto-distribution.
     */
    public function updating(Project $project): void
    {
        if (! $project->isDirty('sales_manager_id')) {
            return;
        }

        $oldManagerId = $project->getOriginal('sales_manager_id');

        // Only act if there was a previous manager being removed/changed
        if (! $oldManagerId) {
            return;
        }

        // Get all orders in this project
        $orderIds = UnitOrder::where('project_id', $project->id)->pluck('id');

        if ($orderIds->isEmpty()) {
            return;
        }

        $created = 0;
        $assigned = 0;

        foreach ($orderIds as $orderId) {
            // Create manage permission so the old manager keeps visibility
            $perm = OrderPermission::firstOrCreate([
                'user_id' => $oldManagerId,
                'unit_order_id' => $orderId,
                'permission_type' => 'manage',
            ], [
                'granted_by' => null, // System — project manager change
            ]);

            if ($perm->wasRecentlyCreated) {
                $created++;
            }

            // Also set assigned_sales_user_id if it was null
            $order = UnitOrder::find($orderId);
            if ($order && ! $order->assigned_sales_user_id) {
                $order->assigned_sales_user_id = $oldManagerId;
                $order->saveQuietly();
                $assigned++;
            }
        }

        Log::info("ProjectObserver: sales_manager_id changed on Project {$project->id} ({$project->name}). Old manager: {$oldManagerId}. Created {$created} permissions, assigned {$assigned} orders.");
    }
}
