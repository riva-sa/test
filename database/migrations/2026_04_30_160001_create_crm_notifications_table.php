<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['individual', 'group', 'announcement', 'task']);
            $table->foreignId('sender_id')->constrained('users');
            $table->string('title', 255);
            $table->text('content');
            $table->timestamps();

            $table->index('type');
            $table->index('sender_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_notifications');
    }
};
