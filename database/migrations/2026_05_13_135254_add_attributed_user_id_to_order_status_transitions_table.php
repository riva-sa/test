<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_status_transitions', function (Blueprint $table) {
            if (!Schema::hasColumn('order_status_transitions', 'attributed_user_id')) {
                $table->foreignId('attributed_user_id')->after('user_id')->nullable()->constrained('users')->nullOnDelete();
            }
            // If the index wasn't created because it failed, we might need to check for it too, 
            // but let's just try to create it if it doesn't exist.
        });

        // Try adding the index separately to handle partial failures
        try {
            Schema::table('order_status_transitions', function (Blueprint $table) {
                $table->index(['attributed_user_id', 'to_status', 'created_at'], 'ost_attr_user_status_index');
            });
        } catch (\Exception $e) {
            // Already exists or other issue
        }

        // Backfill existing data: attribute to the user who did the change by default
        DB::table('order_status_transitions')->whereNull('attributed_user_id')->update(['attributed_user_id' => DB::raw('user_id')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_status_transitions', function (Blueprint $table) {
            $table->dropForeign(['attributed_user_id']);
            $table->dropColumn('attributed_user_id');
        });
    }
};
