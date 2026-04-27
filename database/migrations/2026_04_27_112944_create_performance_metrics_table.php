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
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('page_url', 500);
            $table->string('route_name', 100);
            $table->string('metric_type', 30);
            $table->decimal('value', 10, 4);
            $table->string('request_method', 10);
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['route_name', 'created_at']);
            $table->index(['metric_type', 'created_at']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};
