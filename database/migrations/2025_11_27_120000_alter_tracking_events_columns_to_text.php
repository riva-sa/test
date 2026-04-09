<?php

// use Illuminate\Database\Migrations\Migration;
// use Illuminate\Support\Facades\DB;

// return new class extends Migration
// {
//     public function up(): void
//     {
//         DB::statement('ALTER TABLE tracking_events ALTER COLUMN referrer TYPE text');
//         DB::statement('ALTER TABLE tracking_events ALTER COLUMN user_agent TYPE text');
//     }

//     public function down(): void
//     {
//         DB::statement('ALTER TABLE tracking_events ALTER COLUMN referrer TYPE varchar(255)');
//         DB::statement('ALTER TABLE tracking_events ALTER COLUMN user_agent TYPE varchar(255)');
//     }
// };

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->text('referrer')->change();
            $table->text('user_agent')->change();
        });
    }

    public function down(): void
    {
        Schema::table('tracking_events', function (Blueprint $table) {
            $table->string('referrer', 255)->change();
            $table->string('user_agent', 255)->change();
        });
    }
};
