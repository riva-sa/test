<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            // After the broker signs, an admin must review the final signed contract
            // and approve the account before the portal is unlocked.
            $table->timestamp('contract_approved_at')->nullable()->after('contract_signed_at');
            $table->foreignId('contract_approved_by')->nullable()->after('contract_approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contract_approved_by');
            $table->dropColumn('contract_approved_at');
        });
    }
};
