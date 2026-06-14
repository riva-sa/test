<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            // Allow general (non-job-specific) applications by making the FK nullable
            $table->foreignId('job_posting_id')->nullable()->change();
            // Department the applicant is interested in (used for general applications)
            $table->string('department')->nullable()->after('job_posting_id');
        });
    }

    public function down(): void
    {
        Schema::table('job_applications', function (Blueprint $table) {
            $table->dropColumn('department');
            $table->foreignId('job_posting_id')->nullable(false)->change();
        });
    }
};
