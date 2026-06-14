<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Named job_postings because the framework queue already owns the `jobs` table.
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Arabic source of truth, *_en columns hold translations
            $table->string('title_en')->nullable();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('description_en')->nullable();
            $table->text('responsibilities')->nullable();
            $table->text('responsibilities_en')->nullable();
            $table->text('requirements')->nullable();
            $table->text('requirements_en')->nullable();
            $table->text('benefits')->nullable();
            $table->text('benefits_en')->nullable();
            $table->string('department')->nullable();
            $table->string('department_en')->nullable();
            $table->string('location')->nullable();
            $table->string('location_en')->nullable();
            $table->string('employment_type')->default('full_time'); // full_time | part_time | contract | internship | remote
            $table->string('experience_level')->nullable(); // entry | junior | mid | senior | manager
            $table->string('salary_range')->nullable();
            $table->unsignedInteger('vacancies')->default(1);
            $table->boolean('is_featured')->default(false);
            $table->integer('sort_order')->default(0);
            $table->string('status')->default('draft'); // draft | published | closed
            $table->timestamp('published_at')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
            $table->index('department');
            $table->index('employment_type');
        });

        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->string('city')->nullable();
            $table->string('nationality')->nullable();
            $table->string('education')->nullable();
            $table->unsignedTinyInteger('years_of_experience')->nullable();
            $table->string('current_job')->nullable();
            $table->string('current_salary')->nullable();
            $table->string('expected_salary')->nullable();
            $table->text('cover_letter')->nullable();
            // Files live on the private local disk and are served to admins only
            $table->string('cv_path');
            $table->string('cover_letter_path')->nullable();
            $table->string('portfolio_path')->nullable();
            $table->string('status')->default('new'); // new | under_review | shortlisted | interview_scheduled | interviewed | offer_sent | hired | rejected | archived
            $table->text('internal_notes')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('job_postings');
    }
};
