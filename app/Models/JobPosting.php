<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class JobPosting extends Model
{
    use HasTranslations;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_CLOSED = 'closed';

    public const EMPLOYMENT_TYPES = ['full_time', 'part_time', 'contract', 'internship', 'remote'];

    public const EXPERIENCE_LEVELS = ['entry', 'junior', 'mid', 'senior', 'manager'];

    /**
     * Visitor-facing columns with English (`*_en`) translations.
     *
     * @var array<int, string>
     */
    protected $translatable = [
        'title',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'department',
        'location',
    ];

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'description_en',
        'responsibilities',
        'responsibilities_en',
        'requirements',
        'requirements_en',
        'benefits',
        'benefits_en',
        'department',
        'department_en',
        'location',
        'location_en',
        'employment_type',
        'experience_level',
        'salary_range',
        'vacancies',
        'is_featured',
        'sort_order',
        'status',
        'published_at',
        'expiry_date',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'expiry_date' => 'date',
    ];

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Jobs visible on the public careers pages: published, publish date
     * reached (or unset) and not past the expiry date.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('expiry_date')->orWhere('expiry_date', '>=', today());
            });
    }

    public function isOpenForApplications(): bool
    {
        return $this->status === self::STATUS_PUBLISHED
            && ($this->published_at === null || $this->published_at->isPast())
            && ($this->expiry_date === null || ! $this->expiry_date->isPast());
    }

    public function getEmploymentTypeLabelAttribute(): string
    {
        return __('public.careers.employment_types.'.$this->employment_type);
    }

    public function getExperienceLevelLabelAttribute(): ?string
    {
        return $this->experience_level
            ? __('public.careers.experience_levels.'.$this->experience_level)
            : null;
    }
}
