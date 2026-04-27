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
        Schema::create('optimized_images', function (Blueprint $table) {
            $table->id();
            $table->string('original_path', 500)->index();
            $table->string('variant_type', 20);
            $table->string('variant_path', 500);
            $table->string('format', 10);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('original_size')->nullable();
            $table->string('status', 20)->index()->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['original_path', 'variant_type', 'format'], 'opt_img_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimized_images');
    }
};
