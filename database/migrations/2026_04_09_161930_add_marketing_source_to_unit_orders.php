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
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->string('marketing_source')->nullable()->after('order_source');
            $table->string('session_id')->nullable()->after('marketing_source');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->dropColumn(['marketing_source', 'session_id']);
        });
    }
};
