<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("DROP VIEW IF EXISTS audit_logs_view");
        DB::statement("
            CREATE VIEW audit_logs_view AS
            SELECT 
                CONCAT('status_', id) as id,
                'status_change' as activity_type,
                user_id as actor_id,
                unit_order_id as order_id,
                CONCAT('Changed status from ', from_status, ' to ', to_status) as description,
                created_at
            FROM order_status_transitions
            
            UNION ALL
            
            SELECT 
                CONCAT('perm_', id) as id,
                'permission_grant' as activity_type,
                granted_by as actor_id,
                unit_order_id as order_id,
                CONCAT('Granted ', permission_type, ' permission to user ID ', user_id) as description,
                created_at
            FROM order_permissions
            
            UNION ALL
            
            SELECT 
                CONCAT('note_', id) as id,
                'note_added' as activity_type,
                user_id as actor_id,
                unit_order_id as order_id,
                note as description,
                created_at
            FROM order_notes
            
            UNION ALL
            
            SELECT 
                CONCAT('adj_', id) as id,
                'leaderboard_adjustment' as activity_type,
                adjusted_by as actor_id,
                NULL as order_id,
                reason as description,
                created_at
            FROM leaderboard_adjustments

            UNION ALL

            SELECT 
                CONCAT('data_', id) as id,
                'data_change' as activity_type,
                user_id as actor_id,
                unit_order_id as order_id,
                CONCAT('Modified field [', field, '] from \"', IFNULL(old_value, 'empty'), '\" to \"', IFNULL(new_value, 'empty'), '\"') as description,
                created_at
            FROM order_data_changes
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS audit_logs_view");
        // Revert to original view if needed, but usually we just drop it
    }
};
