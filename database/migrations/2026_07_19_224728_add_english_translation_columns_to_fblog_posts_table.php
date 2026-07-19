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
        Schema::table('fblog_posts', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('sub_title_en')->nullable()->after('sub_title');
            $table->longText('body_en')->nullable()->after('body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fblog_posts', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'sub_title_en', 'body_en']);
        });
    }
};
