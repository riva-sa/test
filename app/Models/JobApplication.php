<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    public const STATUSES = [
        'new',
        'under_review',
        'shortlisted',
        'interview_scheduled',
        'interviewed',
        'offer_sent',
        'hired',
        'rejected',
        'archived',
    ];

    protected $fillable = [
        'job_posting_id',
        'department',
        'name',
        'email',
        'phone',
        'city',
        'nationality',
        'education',
        'years_of_experience',
        'current_job',
        'current_salary',
        'expected_salary',
        'cover_letter',
        'cv_path',
        'cover_letter_path',
        'portfolio_path',
        'status',
        'internal_notes',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }
}
