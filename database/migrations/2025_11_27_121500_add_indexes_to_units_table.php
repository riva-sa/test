<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->index('status');
            $table->index('case');
            $table->index(['project_id', 'status', 'case']);
        });
    }

    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['case']);
            $table->dropIndex(['project_id', 'status', 'case']);
        });
    }
};
