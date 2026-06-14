<div>
    @section('title', $job->title . ' - ' . __('public.careers.title'))
    @section('description', Str::limit(strip_tags($job->description), 150))
    @section('og:title', $job->title . ' - ' . __('public.careers.title'))
    @section('og:description', Str::limit(strip_tags($job->description), 150))
    @section('twitter:title', $job->title . ' - ' . __('public.careers.title'))
    @section('twitter:description', Str::limit(strip_tags($job->description), 150))

    <section class="section-frame overflow-hidden mt-5">
        <div class="wrapper bg-soft-primary">
            <div class="container-fluid py-10 py-md-10 text-center">
                <div class="row">
                    <div class="col-md-8 col-lg-7 col-xl-6 mx-auto">
                        <h1 class="display-2 mb-3">{{ $job->title }}</h1>
                        <div class="d-flex flex-wrap justify-content-center gap-3">
                            @if ($job->department)
                                <span class="badge bg-pale-primary text-primary rounded-pill fs-15"><i class="uil uil-building"></i> {{ $job->department }}</span>
                            @endif
                            @if ($job->location)
                                <span class="badge bg-pale-primary text-primary rounded-pill fs-15"><i class="uil uil-location-pin-alt"></i> {{ $job->location }}</span>
                            @endif
                            <span class="badge bg-pale-primary text-primary rounded-pill fs-15"><i class="uil uil-clock"></i> {{ $job->employment_type_label }}</span>
                            @if ($job->experience_level_label)
                                <span class="badge bg-pale-primary text-primary rounded-pill fs-15"><i class="uil uil-chart-line"></i> {{ $job->experience_level_label }}</span>
                            @endif
                        </div>
                        <div class="mt-5">
                            <a href="#apply" class="btn btn-primary rounded-pill">@lang('public.careers.apply_now')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /section -->

    <section class="wrapper bg-light">
        <div class="container py-8 py-md-10">
            <div class="row gx-lg-8 gy-8">
                <div class="col-lg-8">
                    @if ($job->description)
                        <h2 class="h3 mb-3">@lang('public.careers.description')</h2>
                        <div class="mb-8">{!! $job->description !!}</div>
                    @endif

                    @if ($job->responsibilities)
                        <h2 class="h3 mb-3">@lang('public.careers.responsibilities')</h2>
                        <div class="mb-8">{!! $job->responsibilities !!}</div>
                    @endif

                    @if ($job->requirements)
                        <h2 class="h3 mb-3">@lang('public.careers.requirements')</h2>
                        <div class="mb-8">{!! $job->requirements !!}</div>
                    @endif

                    @if ($job->benefits)
                        <h2 class="h3 mb-3">@lang('public.careers.benefits')</h2>
                        <div class="mb-8">{!! $job->benefits !!}</div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body p-6">
                            <h3 class="h4 mb-4">@lang('public.careers.job_summary')</h3>
                            <ul class="list-unstyled mb-0">
                                @if ($job->department)
                                    <li class="mb-3"><strong>@lang('public.careers.department'):</strong> {{ $job->department }}</li>
                                @endif
                                @if ($job->location)
                                    <li class="mb-3"><strong>@lang('public.careers.location'):</strong> {{ $job->location }}</li>
                                @endif
                                <li class="mb-3"><strong>@lang('public.careers.employment_type'):</strong> {{ $job->employment_type_label }}</li>
                                @if ($job->experience_level_label)
                                    <li class="mb-3"><strong>@lang('public.careers.experience_level'):</strong> {{ $job->experience_level_label }}</li>
                                @endif
                                @if ($job->salary_range)
                                    <li class="mb-3"><strong>@lang('public.careers.salary_range'):</strong> {{ $job->salary_range }}</li>
                                @endif
                                @if ($job->vacancies > 1)
                                    <li class="mb-3"><strong>@lang('public.careers.vacancies'):</strong> {{ $job->vacancies }}</li>
                                @endif
                                @if ($job->published_at)
                                    <li class="mb-3"><strong>@lang('public.careers.published_at'):</strong> {{ $job->published_at->translatedFormat('d F Y') }}</li>
                                @endif
                                @if ($job->expiry_date)
                                    <li class="mb-0"><strong>@lang('public.careers.expiry_date'):</strong> {{ $job->expiry_date->translatedFormat('d F Y') }}</li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    <a href="{{ route('frontend.careers') }}" class="btn btn-soft-primary rounded-pill w-100 mt-4">
                        <i class="uil uil-arrow-right"></i> @lang('public.careers.back_to_jobs')
                    </a>
                </div>
            </div>

            {{-- Application form --}}
            <div class="row mt-10" id="apply">
                <div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2">
                    <h2 class="display-5 mb-3 text-center">@lang('public.careers.apply_title')</h2>
                    <p class="lead text-center mb-8">@lang('public.careers.apply_subtitle')</p>

                    @if ($success)
                        <div class="alert alert-success text-center mb-6" role="alert">
                            <i class="uil uil-check-circle fs-22"></i>
                            @lang('public.careers.application_success')
                        </div>
                    @else
                        <form wire:submit.prevent="submit">
                            <div class="row gx-4">
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_name" type="text" wire:model="name" class="form-control" placeholder="">
                                        <label for="app_name">@lang('public.careers.form.full_name') *</label>
                                        @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_email" type="email" wire:model="email" class="form-control" placeholder="">
                                        <label for="app_email">@lang('public.careers.form.email') *</label>
                                        @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_phone" type="tel" wire:model="phone" class="form-control" placeholder="">
                                        <label for="app_phone">@lang('public.careers.form.phone') *</label>
                                        @error('phone') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_city" type="text" wire:model="city" class="form-control" placeholder="">
                                        <label for="app_city">@lang('public.careers.form.city') *</label>
                                        @error('city') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_nationality" type="text" wire:model="nationality" class="form-control" placeholder="">
                                        <label for="app_nationality">@lang('public.careers.form.nationality') *</label>
                                        @error('nationality') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_education" type="text" wire:model="education" class="form-control" placeholder="">
                                        <label for="app_education">@lang('public.careers.form.education') *</label>
                                        @error('education') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_experience" type="number" min="0" wire:model="years_of_experience" class="form-control" placeholder="">
                                        <label for="app_experience">@lang('public.careers.form.years_of_experience') *</label>
                                        @error('years_of_experience') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_current_job" type="text" wire:model="current_job" class="form-control" placeholder="">
                                        <label for="app_current_job">@lang('public.careers.form.current_job')</label>
                                        @error('current_job') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_current_salary" type="text" wire:model="current_salary" class="form-control" placeholder="">
                                        <label for="app_current_salary">@lang('public.careers.form.current_salary')</label>
                                        @error('current_salary') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-4">
                                        <input id="app_expected_salary" type="text" wire:model="expected_salary" class="form-control" placeholder="">
                                        <label for="app_expected_salary">@lang('public.careers.form.expected_salary')</label>
                                        @error('expected_salary') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-4">
                                        <textarea id="app_cover_letter" wire:model="cover_letter" class="form-control" placeholder="" style="height: 120px"></textarea>
                                        <label for="app_cover_letter">@lang('public.careers.form.cover_letter')</label>
                                        @error('cover_letter') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="app_cv" class="form-label">@lang('public.careers.form.cv') * <span class="text-muted">(PDF)</span></label>
                                        <input id="app_cv" type="file" wire:model="cv" class="form-control" accept=".pdf">
                                        <div wire:loading wire:target="cv" class="text-muted mt-1">@lang('public.careers.form.uploading')</div>
                                        @error('cv') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="app_cover_letter_file" class="form-label">@lang('public.careers.form.cover_letter_file')</label>
                                        <input id="app_cover_letter_file" type="file" wire:model="cover_letter_file" class="form-control" accept=".pdf,.doc,.docx">
                                        <div wire:loading wire:target="cover_letter_file" class="text-muted mt-1">@lang('public.careers.form.uploading')</div>
                                        @error('cover_letter_file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-4">
                                        <label for="app_portfolio" class="form-label">@lang('public.careers.form.portfolio')</label>
                                        <input id="app_portfolio" type="file" wire:model="portfolio_file" class="form-control" accept=".pdf,.doc,.docx,.zip">
                                        <div wire:loading wire:target="portfolio_file" class="text-muted mt-1">@lang('public.careers.form.uploading')</div>
                                        @error('portfolio_file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="col-12 text-center mt-2">
                                    <button type="submit" class="btn btn-primary rounded-pill btn-send" wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="submit">@lang('public.careers.form.submit')</span>
                                        <span wire:loading wire:target="submit">@lang('public.careers.form.submitting')</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
