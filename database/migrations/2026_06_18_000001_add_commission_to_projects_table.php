<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Commission a broker earns on each sold unit of THIS project:
            // either a percentage of the unit price, or a fixed amount per unit.
            // Each project defines its own rate, independent of the broker.
            $table->string('commission_type')->default('percentage')->after('price'); // percentage | fixed
            $table->decimal('commission_value', 12, 2)->default(0)->after('commission_type');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['commission_type', 'commission_value']);
        });
    }
};
