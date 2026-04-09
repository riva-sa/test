<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->boolean('is_waiting_list')->default(false)->after('status');
            $table->string('waiting_list_unit_type')->nullable()->after('is_waiting_list');
            $table->string('waiting_list_budget')->nullable()->after('waiting_list_unit_type');
            $table->string('waiting_list_location')->nullable()->after('waiting_list_budget');
            $table->text('waiting_list_notes')->nullable()->after('waiting_list_location');
        });
    }

    public function down(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->dropColumn([
                'is_waiting_list',
                'waiting_list_unit_type',
                'waiting_list_budget',
                'waiting_list_location',
                'waiting_list_notes',
            ]);
        });
    }
};
