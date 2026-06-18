<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Commission is now defined per-project (see projects.commission_type/value),
     * not per-broker. A broker's commission on a sale is derived from the sold
     * unit's project, so these broker columns are obsolete.
     */
    public function up(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }

    public function down(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->string('commission_type')->default('percentage')->after('iban');
            $table->decimal('commission_value', 12, 2)->default(0)->after('commission_type');
        });
    }
};
