<?php

namespace App\Services;

use App\Models\OrderPermission;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoAssignmentService
{
    /**
     * Automatically assign the order to an available sales representative.
     */
    public function assign(UnitOrder $order): void
    {
        // Allow assigning if it already doesn't have an assignee and is not from bulk import
        if ($order->assigned_sales_user_id || $order->order_source === UnitOrder::ORDER_SOURCE_BULK_IMPORT) {
            return;
        }

        $allowedSources = [
            UnitOrder::ORDER_SOURCE_FRONTEND_POPUP,
            UnitOrder::ORDER_SOURCE_FRONTEND_UNIT,
            'landing_page', // Common adding for webhooks
        ];

        // Also if we have no specific logic we should fallback. But let's check config if present.
        if (!in_array($order->order_source, $allowedSources, true)) {
            // Still we might want to auto assign everything that has no assigned sales user? 
            // For now let's be flexible and just accept anything that doesn't have a user, 
            // except manager or legacy.
            if (in_array($order->order_source, [UnitOrder::ORDER_SOURCE_MANAGER, UnitOrder::ORDER_SOURCE_LEGACY])) {
                return;
            }
        }

        try {
            DB::transaction(function () use ($order) {
                // Find all active sales users not on vacation
                $salesUsers = User::role(config('lead_import.sales_role', 'sales'))
                    ->where('is_active', true)
                    ->where('on_vacation', false)
                    ->get();

                if ($salesUsers->isEmpty()) {
                    Log::warning("AutoAssignmentService: No active sales representatives available for Order ID: {$order->id}");
                    return;
                }

                $today = now()->startOfDay();

                // Get assignment counts for today for these users to ensure round-robin distribution
                // We count all orders assigned to each eligible user today
                $counts = UnitOrder::whereNotNull('assigned_sales_user_id')
                    ->whereIn('assigned_sales_user_id', $salesUsers->pluck('id'))
                    ->where('created_at', '>=', $today)
                    ->select('assigned_sales_user_id', DB::raw('count(*) as count'))
                    ->groupBy('assigned_sales_user_id')
                    ->pluck('count', 'assigned_sales_user_id')
                    ->toArray();

                $bestUser = null;
                $minCount = PHP_INT_MAX;

                // Pick the user with the minimum assignments today. In case of a tie, 
                // the order of `$salesUsers` (usually by ID) will act as a secondary deterministic factor.
                foreach ($salesUsers as $user) {
                    $userCount = $counts[$user->id] ?? 0;
                    if ($userCount < $minCount) {
                        $minCount = $userCount;
                        $bestUser = $user;
                    }
                }

                if ($bestUser) {
                    // Double check if not assigned yet
                    $order = UnitOrder::where('id', $order->id)->lockForUpdate()->first();
                    
                    if ($order && !$order->assigned_sales_user_id) {
                        $order->assigned_sales_user_id = $bestUser->id;
                        $order->save();

                        // Grant permission to manage for this specific user
                        OrderPermission::firstOrCreate([
                            'user_id' => $bestUser->id,
                            'unit_order_id' => $order->id,
                            'permission_type' => 'manage',
                        ], [
                            'granted_by' => null, // System auto assignment doesn't have a granter
                        ]);

                        Log::info("AutoAssignmentService: Order ID: {$order->id} automatically assigned to Sales User: {$bestUser->id}");
                    }
                }
            });
        } catch (\Throwable $e) {
            Log::error('AutoAssignmentService Error: ' . $e->getMessage());
        }
    }
}
