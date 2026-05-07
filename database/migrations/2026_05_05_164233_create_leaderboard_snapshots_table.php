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
        Schema::create('leaderboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('snapshot_date');
            $table->unsignedInteger('monthly_orders')->default(0);
            $table->unsignedInteger('daily_orders')->default(0);
            $table->unsignedInteger('reservations')->default(0);
            $table->unsignedInteger('sales')->default(0);
            $table->decimal('composite_score', 5, 2)->default(0.00);
            $table->timestamps();
            $table->unique(['user_id', 'snapshot_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_snapshots');
    }
};
