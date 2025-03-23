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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('developer_id')->constrained('developers')->onDelete('cascade');
            $table->foreignId('project_type_id')->constrained('project_types')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->string('city_id')->nullable();
            $table->string('state_id')->nullable();
            $table->string('country')->default('SA');
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->json('location')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('show_price')->default(1);
            $table->string('price')->nullable();
            $table->string('bulding_style')->nullable();
            $table->boolean('is_featured')->default(0);
            $table->string('AdLicense')->nullable();
            $table->longText('virtualTour')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
