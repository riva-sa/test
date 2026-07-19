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
        Schema::table('fblog_categories', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('fblog_tags', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('fblog_seo_details', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->text('keywords_en')->nullable()->after('keywords');
            $table->text('description_en')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fblog_categories', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('fblog_tags', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('fblog_seo_details', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'keywords_en', 'description_en']);
        });
    }
};
