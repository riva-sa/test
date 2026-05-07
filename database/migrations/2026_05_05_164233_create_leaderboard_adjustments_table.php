<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leaderboard_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('adjusted_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('period_type', ['daily', 'weekly', 'monthly']);
            $table->date('period_date');
            $table->enum('metric_type', ['monthly_orders', 'daily_orders', 'reservations', 'sales', 'composite_score']);
            $table->decimal('original_value', 5, 2);
            $table->decimal('adjusted_value', 5, 2);
            $table->text('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_adjustments');
    }
};
