<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            // Commission the broker earns on each sold unit:
            // either a percentage of the unit price, or a fixed amount per unit.
            $table->string('commission_type')->default('percentage')->after('iban'); // percentage | fixed
            $table->decimal('commission_value', 12, 2)->default(0)->after('commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('brokers', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }
};
