<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brokers', function (Blueprint $table) {
            $table->id();
            $table->string('broker_type')->default('individual'); // individual | company (coming soon)
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('national_id')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('city')->nullable();
            $table->string('iban')->nullable();
            $table->string('employment_status')->nullable();
            $table->string('heard_about_us')->nullable();
            $table->string('reference_number')->unique()->nullable();
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            // Contract flow: admin sends a PDF contract after approval; broker must approve and upload the signed copy
            $table->string('contract_path')->nullable();
            $table->timestamp('contract_sent_at')->nullable();
            $table->string('contract_signed_path')->nullable();
            $table->timestamp('contract_signed_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('broker_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->constrained('brokers')->cascadeOnDelete();
            $table->string('type'); // national_id | fal_license | iban_file
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        Schema::create('broker_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // admin actor
            $table->string('action'); // registered | approved | rejected | login | lead_submitted ...
            $table->text('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();

            $table->index(['broker_id', 'action']);
        });

        Schema::table('unit_orders', function (Blueprint $table) {
            $table->foreignId('broker_id')->nullable()->after('user_id')->constrained('brokers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('broker_id');
        });
        Schema::dropIfExists('broker_activity_logs');
        Schema::dropIfExists('broker_documents');
        Schema::dropIfExists('brokers');
    }
};
