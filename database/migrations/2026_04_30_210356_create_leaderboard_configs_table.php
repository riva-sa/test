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
        Schema::create('leaderboard_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('target_type', ['monthly_orders', 'daily_orders', 'reservations', 'sales'])->unique();
            $table->decimal('weight', 5, 2)->default(25.00)->comment('Weight percentage 0-100');
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_configs');
    }
};
