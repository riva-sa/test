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
            if (!Schema::hasColumn('unit_orders', 'external_id')) {
                $table->string('external_id')->nullable()->index()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            if (Schema::hasColumn('unit_orders', 'external_id')) {
                $table->dropColumn('external_id');
            }
        });
    }
};
