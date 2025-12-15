<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE tracking_events ALTER COLUMN referrer TYPE text');
        DB::statement('ALTER TABLE tracking_events ALTER COLUMN user_agent TYPE text');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE tracking_events ALTER COLUMN referrer TYPE varchar(255)');
        DB::statement('ALTER TABLE tracking_events ALTER COLUMN user_agent TYPE varchar(255)');
    }
};
