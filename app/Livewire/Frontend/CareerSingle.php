<?php

namespace App\Livewire\Frontend;

use App\Mail\JobApplicationReceivedMail;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;

class CareerSingle extends Component
{
    use WithFileUploads;

    public JobPosting $job;

    // Application form fields
    public $name = '';

    public $email = '';

    public $phone = '';

    public $city = '';

    public $nationality = '';

    public $education = '';

    public $years_of_experience = '';

    public $current_job = '';

    public $current_salary = '';

    public $expected_salary = '';

    public $cover_letter = '';

    public $cv;

    public $cover_letter_file;

    public $portfolio_file;

    public $success = false;

    public function mount($slug)
    {
        $this->job = JobPosting::published()->where('slug', $slug)->firstOrFail();
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|min:9|max:20',
            'city' => 'required|max:255',
            'nationality' => 'required|max:255',
            'education' => 'required|max:255',
            'years_of_experience' => 'required|integer|min:0|max:60',
            'current_job' => 'nullable|max:255',
            'current_salary' => 'nullable|max:255',
            'expected_salary' => 'nullable|max:255',
            'cover_letter' => 'nullable|max:5000',
            'cv' => 'required|file|mimes:pdf|max:5120',
            'cover_letter_file' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'portfolio_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
        ];
    }

    public function getMessages(): array
    {
        return [
            'name.required' => __('public.careers.validation.name_required'),
            'email.required' => __('public.careers.validation.email_required'),
            'email.email' => __('public.careers.validation.email_email'),
            'phone.required' => __('public.careers.validation.phone_required'),
            'city.required' => __('public.careers.validation.city_required'),
            'nationality.required' => __('public.careers.validation.nationality_required'),
            'education.required' => __('public.careers.validation.education_required'),
            'years_of_experience.required' => __('public.careers.validation.experience_required'),
            'cv.required' => __('public.careers.validation.cv_required'),
            'cv.mimes' => __('public.careers.validation.cv_pdf'),
            'cv.max' => __('public.careers.validation.cv_max'),
        ];
    }

    public function submit()
    {
        // Re-check on submit: the job may have closed while the page was open
        abort_unless($this->job->isOpenForApplications(), 403);

        $this->validate($this->rules(), $this->getMessages());

        // Files are stored on the private local disk and served only to admins
        $directory = "job-applications/{$this->job->id}";
        $cvPath = $this->cv->store($directory, 'local');
        $coverLetterPath = $this->cover_letter_file?->store($directory, 'local');
        $portfolioPath = $this->portfolio_file?->store($directory, 'local');

        $application = JobApplication::create([
            'job_posting_id' => $this->job->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'city' => $this->city,
            'nationality' => $this->nationality,
            'education' => $this->education,
            'years_of_experience' => (int) $this->years_of_experience,
            'current_job' => $this->current_job ?: null,
            'current_salary' => $this->current_salary ?: null,
            'expected_salary' => $this->expected_salary ?: null,
            'cover_letter' => $this->cover_letter ?: null,
            'cv_path' => $cvPath,
            'cover_letter_path' => $coverLetterPath,
            'portfolio_path' => $portfolioPath,
            'status' => 'new',
        ]);

        // Confirmation email must never block the submission itself
        try {
            Mail::to($application->email)->send(new JobApplicationReceivedMail($application));
        } catch (\Throwable $e) {
            Log::warning('Job application confirmation email failed', [
                'application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
        }

        $this->reset([
            'name', 'email', 'phone', 'city', 'nationality', 'education',
            'years_of_experience', 'current_job', 'current_salary',
            'expected_salary', 'cover_letter', 'cv', 'cover_letter_file', 'portfolio_file',
        ]);

        $this->success = true;
    }

    public function render()
    {
        return view('livewire.frontend.career-single');
    }
}
