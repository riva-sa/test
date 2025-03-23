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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->string('unit_type');
            $table->string('building_number');
            $table->string('unit_number');
            $table->text('description')->nullable();
            $table->string('floor');
            $table->integer('unit_area')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('beadrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('parking')->nullable();
            $table->integer('kitchen')->nullable();
            $table->integer('living_rooms')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->json('location')->nullable();
            $table->string('image')->nullable();
            $table->string('floor_plan')->nullable();
            $table->boolean('show_price')->default(1);

            $table->unsignedTinyInteger('status')->default(1);
            $table->unsignedTinyInteger('case')->default(0);

            // Sales Information
            $table->string('sale_type')->default('direct');
            $table->string('user_id')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
