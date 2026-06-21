<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Immutable, append-only audit ledger of money events on a commission.
     * Rows are never updated or deleted — a correction is a new 'reversed' row.
     */
    public function up(): void
    {
        Schema::create('broker_commission_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('broker_commission_id')->constrained('broker_commissions')->cascadeOnDelete();
            $table->foreignId('broker_id')->constrained('brokers')->cascadeOnDelete();

            $table->string('action'); // paid | reversed
            $table->decimal('amount', 15, 2); // snapshot of the commission amount at the event

            // Proof captured at payment time.
            $table->string('payment_reference')->nullable();
            $table->string('receipt_path')->nullable();

            // Required when action = reversed.
            $table->text('reason')->nullable();

            // Who/where — the immutable accountability trail.
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('performed_by_name')->nullable(); // frozen, survives user deletion
            $table->string('ip_address', 45)->nullable();

            $table->timestamps();

            $table->index(['broker_commission_id', 'action']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broker_commission_payments');
    }
};
