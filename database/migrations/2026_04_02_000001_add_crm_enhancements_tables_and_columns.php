<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('unit_orders', function (Blueprint $table) {
            $table->string('bank_employee_name')->nullable()->after('waiting_list_notes');
            $table->string('bank_employee_phone')->nullable()->index();
            $table->string('order_source', 32)->default('legacy')->index();
            $table->uuid('import_batch_id')->nullable()->index();
            $table->foreignId('assigned_sales_user_id')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('order_forward_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_order_id')->constrained('unit_orders')->cascadeOnDelete();
            $table->string('strategy', 64);
            $table->string('status', 16);
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('developer_id')->nullable()->after('phone')->constrained('developers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('developer_id');
        });

        Schema::dropIfExists('order_forward_events');

        Schema::table('unit_orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_sales_user_id']);
            $table->dropColumn([
                'bank_employee_name',
                'bank_employee_phone',
                'order_source',
                'import_batch_id',
                'assigned_sales_user_id',
            ]);
        });
    }
};
