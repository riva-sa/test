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
        Schema::create('broker_contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('pdf_path');
            $table->json('fields_config')->nullable(); // Coordinates for name, national_id, phone, email, reference_number, date, signature
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('broker_contract_templates');
    }
};
