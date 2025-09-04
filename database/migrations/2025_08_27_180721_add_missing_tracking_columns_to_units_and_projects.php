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
        // Add missing tracking columns to units
        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'whatsapp_count')) {
                $table->unsignedInteger('whatsapp_count')->default(0)->after('orders_count');
            }
            if (!Schema::hasColumn('units', 'calls_count')) {
                $table->unsignedInteger('calls_count')->default(0)->after('whatsapp_count');
            }
        });

        // Add missing tracking columns to projects
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'whatsapp_count')) {
                $table->unsignedInteger('whatsapp_count')->default(0)->after('orders_count');
            }
            if (!Schema::hasColumn('projects', 'calls_count')) {
                $table->unsignedInteger('calls_count')->default(0)->after('whatsapp_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'whatsapp_count')) {
                $table->dropColumn('whatsapp_count');
            }
            if (Schema::hasColumn('units', 'calls_count')) {
                $table->dropColumn('calls_count');
            }
        });

        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'whatsapp_count')) {
                $table->dropColumn('whatsapp_count');
            }
            if (Schema::hasColumn('projects', 'calls_count')) {
                $table->dropColumn('calls_count');
            }
        });
    }
};
