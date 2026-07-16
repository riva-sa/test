<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            DB::connection('pgsql')->statement(
                "DO $$ BEGIN ALTER TYPE crm_notifications_type ADD VALUE IF NOT EXISTS 'broker_contract_signed'; EXCEPTION WHEN OTHERS THEN END $$"
            );
        } elseif ($driver === 'mysql') {
            DB::connection('mysql')->statement(
                "ALTER TABLE crm_notifications MODIFY COLUMN type ENUM('individual','group','announcement','task','broker_contract_signed') NOT NULL"
            );
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            // PostgreSQL does not support removing a value from an enum type.
        } elseif ($driver === 'mysql') {
            DB::connection('mysql')->statement(
                "ALTER TABLE crm_notifications MODIFY COLUMN type ENUM('individual','group','announcement','task') NOT NULL"
            );
        }
    }
};
