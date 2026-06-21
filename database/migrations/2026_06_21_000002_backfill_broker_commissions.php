<?php

use App\Models\BrokerCommission;
use App\Models\UnitOrder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** Order status that represents a completed (sold) deal. */
    private const STATUS_COMPLETED = 4;

    /**
     * Seed the commission ledger for deals that were already completed before
     * the freeze-on-sale logic existed. Uses each project's current rate as the
     * snapshot, since no historical rate is available.
     */
    public function up(): void
    {
        UnitOrder::query()
            ->where('status', self::STATUS_COMPLETED)
            ->whereNotNull('broker_id')
            ->whereNotNull('unit_id')
            ->whereDoesntHave('commission')
            ->with(['unit.project'])
            ->chunkById(200, function ($orders) {
                foreach ($orders as $order) {
                    $unit = $order->unit;
                    $project = $unit?->project;

                    if (! $project) {
                        continue;
                    }

                    $price = (float) ($unit->unit_price ?? 0);

                    BrokerCommission::create([
                        'broker_id' => $order->broker_id,
                        'unit_order_id' => $order->id,
                        'unit_id' => $unit->id,
                        'project_id' => $project->id,
                        'unit_price' => $price,
                        'commission_type' => $project->commission_type,
                        'commission_value' => $project->commission_value,
                        'commission_amount' => $project->commissionForPrice($price),
                        'status' => BrokerCommission::STATUS_PENDING,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // The ledger rows are removed with the table in the previous migration's
        // rollback; nothing order-specific to undo here.
    }
};
