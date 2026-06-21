<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broker_commissions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('broker_id')->constrained('brokers')->cascadeOnDelete();
            // One commission record per completed order (freeze-on-sale).
            $table->foreignId('unit_order_id')->unique()->constrained('unit_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('unit_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();

            // Snapshot of the deal at the moment the sale completed — these never
            // change afterwards even if the project's rate is edited later.
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->string('commission_type')->default('percentage'); // percentage | fixed
            $table->decimal('commission_value', 12, 2)->default(0);   // the rate at sale time
            $table->decimal('commission_amount', 15, 2)->default(0);  // the frozen earned amount

            // Lifecycle: pending -> approved -> paid (or void).
            $table->string('status')->default('pending')->index();

            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('payment_reference')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['broker_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_commissions');
    }
};
