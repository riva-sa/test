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
        Schema::create('project_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('media_type');
            $table->string('media_url');
            $table->string('media_title')->nullable();
            $table->text('media_description')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('show_in_gallery')->default(1);
            $table->boolean('show_in_slider')->default(1);
            $table->integer('main')->default(0);
            $table->string('youtube_url')->nullable();
            $table->string('vimeo_url')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_media');
    }
};
