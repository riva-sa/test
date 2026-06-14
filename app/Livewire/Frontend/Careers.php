<?php

namespace App\Livewire\Frontend;

use App\Mail\JobApplicationReceivedMail;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Careers extends Component
{
    use WithFileUploads, WithPagination;

    #[Url(as: 'q')]
    public $searchTerm = '';

    #[Url]
    public $department = '';

    #[Url]
    public $location = '';

    #[Url(as: 'type')]
    public $employmentType = '';

    public $jobsPerPage = 9;

    // Open application form state
    public bool $showOpenForm = false;
    public string $openDept = '';
    public string $openName = '';
    public string $openEmail = '';
    public string $openPhone = '';
    public string $openCity = '';
    public string $openNationality = '';
    public string $openEducation = '';
    public string $openYearsOfExperience = '';
    public $openCv = null;
    public bool $openSuccess = false;

    public const OPEN_DEPARTMENTS = [
        'إدارة الموارد البشرية',
        'تسويق',
        'مبيعات',
        'محاسبة',
        'تطوير أعمال',
    ];

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function updatingDepartment()
    {
        $this->resetPage();
    }

    public function updatingLocation()
    {
        $this->resetPage();
    }

    public function updatingEmploymentType()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['searchTerm', 'department', 'location', 'employmentType']);
        $this->resetPage();
    }

    public function openApplicationForm(string $dept): void
    {
        $this->openDept = $dept;
        $this->showOpenForm = true;
        $this->openSuccess = false;
        $this->dispatch('scroll-to-open-form');
    }

    public function closeOpenForm(): void
    {
        $this->showOpenForm = false;
        $this->openDept = '';
        $this->resetOpenForm();
    }

    protected function openFormRules(): array
    {
        return [
            'openDept'              => 'required|in:'.implode(',', self::OPEN_DEPARTMENTS),
            'openName'              => 'required|min:3|max:255',
            'openEmail'             => 'required|email|max:255',
            'openPhone'             => 'required|min:9|max:20',
            'openCity'              => 'required|max:255',
            'openNationality'       => 'required|max:255',
            'openEducation'         => 'required|max:255',
            'openYearsOfExperience' => 'required|integer|min:0|max:60',
            'openCv'                => 'required|file|mimes:pdf|max:5120',
        ];
    }

    protected function openFormMessages(): array
    {
        return [
            'openDept.required'              => __('public.careers.validation.department_required'),
            'openName.required'              => __('public.careers.validation.name_required'),
            'openEmail.required'             => __('public.careers.validation.email_required'),
            'openEmail.email'                => __('public.careers.validation.email_email'),
            'openPhone.required'             => __('public.careers.validation.phone_required'),
            'openCity.required'              => __('public.careers.validation.city_required'),
            'openNationality.required'       => __('public.careers.validation.nationality_required'),
            'openEducation.required'         => __('public.careers.validation.education_required'),
            'openYearsOfExperience.required' => __('public.careers.validation.experience_required'),
            'openCv.required'                => __('public.careers.validation.cv_required'),
            'openCv.mimes'                   => __('public.careers.validation.cv_pdf'),
            'openCv.max'                     => __('public.careers.validation.cv_max'),
        ];
    }

    public function submitOpenApplication(): void
    {
        $this->validate($this->openFormRules(), $this->openFormMessages());

        $cvPath = $this->openCv->store('general-applications', 'local');

        $application = JobApplication::create([
            'job_posting_id'      => null,
            'department'          => $this->openDept,
            'name'                => $this->openName,
            'email'               => $this->openEmail,
            'phone'               => $this->openPhone,
            'city'                => $this->openCity,
            'nationality'         => $this->openNationality,
            'education'           => $this->openEducation,
            'years_of_experience' => (int) $this->openYearsOfExperience,
            'cv_path'             => $cvPath,
            'status'              => 'new',
        ]);

        try {
            Mail::to($application->email)->send(new JobApplicationReceivedMail($application));
        } catch (\Throwable $e) {
            Log::warning('Open application confirmation email failed', [
                'application_id' => $application->id,
                'error'          => $e->getMessage(),
            ]);
        }

        $this->resetOpenForm();
        $this->openSuccess = true;
    }

    protected function resetOpenForm(): void
    {
        $this->reset([
            'openName', 'openEmail', 'openPhone', 'openCity',
            'openNationality', 'openEducation', 'openYearsOfExperience', 'openCv',
        ]);
    }

    public function render()
    {
        $query = JobPosting::query()->published();

        if ($this->searchTerm) {
            $term = '%'.$this->searchTerm.'%';
            $query->where(function ($q) use ($term) {
                $q->where('title', 'like', $term)
                    ->orWhere('title_en', 'like', $term)
                    ->orWhere('department', 'like', $term)
                    ->orWhere('department_en', 'like', $term);
            });
        }

        if ($this->department) {
            $query->where(function ($q) {
                $q->where('department', $this->department)
                    ->orWhere('department_en', $this->department);
            });
        }

        if ($this->location) {
            $query->where(function ($q) {
                $q->where('location', $this->location)
                    ->orWhere('location_en', $this->location);
            });
        }

        if ($this->employmentType) {
            $query->where('employment_type', $this->employmentType);
        }

        $jobs = $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->paginate($this->jobsPerPage);

        $publishedJobs = JobPosting::published()
            ->get(['department', 'department_en', 'location', 'location_en', 'employment_type']);

        $departments    = $publishedJobs->pluck('department')->filter()->unique()->sort()->values();
        $locations      = $publishedJobs->pluck('location')->filter()->unique()->sort()->values();
        $employmentTypes = $publishedJobs->pluck('employment_type')->unique()->values();

        return view('livewire.frontend.careers', [
            'jobs'            => $jobs,
            'departments'     => $departments,
            'locations'       => $locations,
            'employmentTypes' => $employmentTypes,
            'openDepartments' => self::OPEN_DEPARTMENTS,
        ]);
    }
}
