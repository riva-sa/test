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
        Schema::create('tracking_events', function (Blueprint $table) {
            $table->id();
            $table->string('trackable_type'); // 'unit' or 'project'
            $table->unsignedBigInteger('trackable_id');
            $table->string('event_type'); // 'visit', 'view', 'show', 'order'
            $table->string('session_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('referrer')->nullable();
            $table->json('metadata')->nullable(); // Additional data like device info, etc.
            $table->timestamps();

            // Indexes for better performance
            $table->index(['trackable_type', 'trackable_id']);
            $table->index('event_type');
            $table->index('session_id');
            $table->index('created_at');
        });

        // Add tracking columns to units table
        Schema::table('units', function (Blueprint $table) {
            $table->unsignedInteger('visits_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('shows_count')->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_shown_at')->nullable();
            $table->timestamp('last_ordered_at')->nullable();
        });

        // Add tracking columns to projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('visits_count')->default(0);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('shows_count')->default(0);
            $table->unsignedInteger('orders_count')->default(0);
            $table->timestamp('last_visited_at')->nullable();
            $table->timestamp('last_viewed_at')->nullable();
            $table->timestamp('last_shown_at')->nullable();
            $table->timestamp('last_ordered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events');
        
        Schema::table('units', function (Blueprint $table) {
            $table->dropColumn([
                'visits_count', 'views_count', 'shows_count', 'orders_count',
                'last_visited_at', 'last_viewed_at', 'last_shown_at', 'last_ordered_at'
            ]);
        });

        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'visits_count', 'views_count', 'shows_count', 'orders_count',
                'last_visited_at', 'last_viewed_at', 'last_shown_at', 'last_ordered_at'
            ]);
        });
    }
};
