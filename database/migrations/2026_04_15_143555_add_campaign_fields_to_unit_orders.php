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
            if (!Schema::hasColumn('unit_orders', 'marketing_source')) {
                $table->string('marketing_source')->nullable()->after('assigned_sales_user_id');
            }
            if (!Schema::hasColumn('unit_orders', 'session_id')) {
                $table->string('session_id')->nullable()->after('marketing_source');
            }
            if (!Schema::hasColumn('unit_orders', 'campaign_name')) {
                $table->string('campaign_name')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('unit_orders', 'ad_squad')) {
                $table->string('ad_squad')->nullable()->after('campaign_name');
            }
            if (!Schema::hasColumn('unit_orders', 'ad_set')) {
                $table->string('ad_set')->nullable()->after('ad_squad');
            }
            if (!Schema::hasColumn('unit_orders', 'ad_name')) {
                $table->string('ad_name')->nullable()->after('ad_set');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->dropColumn([
                'marketing_source',
                'session_id',
                'campaign_name',
                'ad_squad',
                'ad_set',
                'ad_name'
            ]);
        });
    }
};
