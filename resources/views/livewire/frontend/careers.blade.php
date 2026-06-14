<div>
    @section('title', __('public.careers.seo_title'))
    @section('description', __('public.careers.seo_description'))
    @section('og:title', __('public.careers.seo_title'))
    @section('og:description', __('public.careers.seo_description'))

    <section class="section-frame overflow-hidden mt-5">
        <div class="wrapper bg-soft-primary">
            <div class="container-fluid py-10 py-md-10 text-center">
                <div class="row">
                    <div class="col-md-7 col-lg-6 col-xl-5 mx-auto">
                        <h1 class="display-1 mb-2">@lang('public.careers.title')</h1>
                        <p class="lead mb-0">@lang('public.careers.subtitle')</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /section -->

    <section class="wrapper bg-light">
        <div class="container py-5">

            {{-- Search & filters --}}
            <div class="card mb-8">
                <div class="card-body p-6">
                    <div class="row gx-4 gy-3 align-items-center">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input id="careers_search" type="text" wire:model.live.debounce.400ms="searchTerm" class="form-control" placeholder="">
                                <label for="careers_search">@lang('public.careers.search_placeholder')</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-select-wrapper">
                                <select class="form-select" wire:model.live="department">
                                    <option value="">@lang('public.careers.all_departments')</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept }}">{{ $dept }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-select-wrapper">
                                <select class="form-select" wire:model.live="location">
                                    <option value="">@lang('public.careers.all_locations')</option>
                                    @foreach ($locations as $loc)
                                        <option value="{{ $loc }}">{{ $loc }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-select-wrapper">
                                <select class="form-select" wire:model.live="employmentType">
                                    <option value="">@lang('public.careers.all_types')</option>
                                    @foreach ($employmentTypes as $type)
                                        <option value="{{ $type }}">@lang('public.careers.employment_types.'.$type)</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <button type="button" class="btn btn-soft-primary rounded-pill w-100" wire:click="clearFilters">
                                @lang('public.careers.clear_filters')
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Jobs list --}}
            <div class="row gy-6" wire:loading.class="opacity-50">
                @forelse ($jobs as $job)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 {{ $job->is_featured ? 'border border-primary' : '' }}">
                            <div class="card-body d-flex flex-column p-7">
                                @if ($job->is_featured)
                                    <span class="badge bg-pale-primary text-primary rounded-pill align-self-start mb-3">@lang('public.careers.featured')</span>
                                @endif
                                <h3 class="h4 mb-2">
                                    <a class="link-dark" href="{{ route('frontend.careers.single', ['slug' => $job->slug]) }}">{{ $job->title }}</a>
                                </h3>
                                @if ($job->department)
                                    <p class="text-muted mb-3"><i class="uil uil-building"></i> {{ $job->department }}</p>
                                @endif
                                <ul class="list-unstyled mb-4">
                                    @if ($job->location)
                                        <li class="mb-1"><i class="uil uil-location-pin-alt text-primary"></i> {{ $job->location }}</li>
                                    @endif
                                    <li class="mb-1"><i class="uil uil-clock text-primary"></i> {{ $job->employment_type_label }}</li>
                                    @if ($job->experience_level_label)
                                        <li class="mb-1"><i class="uil uil-chart-line text-primary"></i> {{ $job->experience_level_label }}</li>
                                    @endif
                                    @if ($job->published_at)
                                        <li class="mb-1"><i class="uil uil-calendar-alt text-primary"></i> {{ $job->published_at->translatedFormat('d F Y') }}</li>
                                    @endif
                                </ul>
                                <div class="mt-auto">
                                    <a href="{{ route('frontend.careers.single', ['slug' => $job->slug]) }}" class="btn btn-primary btn-sm rounded-pill">
                                        @lang('public.careers.view_details')
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-10">
                        <i class="uil uil-briefcase-alt fs-50 text-muted"></i>
                        <p class="lead mt-3 mb-0">@lang('public.careers.no_jobs')</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-8 d-flex justify-content-center">
                {{ $jobs->links() }}
            </div>
        </div>
    </section>

    {{-- Open application section --}}
    <section class="wrapper bg-soft-primary" id="open-application">
        <div class="container py-10 py-md-12">
            <div class="row text-center mb-8">
                <div class="col-lg-8 mx-auto">
                    <h2 class="display-4 mb-3">@lang('public.careers.open_app.title')</h2>
                    <p class="lead mb-0">@lang('public.careers.open_app.subtitle')</p>
                </div>
            </div>

            {{-- Specialization buttons --}}
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-8">
                @foreach ($openDepartments as $dept)
                    <button
                        type="button"
                        wire:click="openApplicationForm('{{ $dept }}')"
                        class="btn rounded-pill px-5 py-3 fw-semibold {{ $openDept === $dept && $showOpenForm ? 'btn-primary' : 'btn-soft-primary' }}"
                    >
                        {{ $dept }}
                    </button>
                @endforeach
            </div>

            {{-- Inline form --}}
            @if ($showOpenForm)
                <div id="open-form-area" class="row justify-content-center"
                     x-data x-init="$el.scrollIntoView({behavior:'smooth', block:'start'})">
                    <div class="col-lg-9 col-xl-8">
                        <div class="card shadow-lg border-0">
                            <div class="card-body p-6 p-md-8">
                                <div class="d-flex align-items-center justify-content-between mb-6">
                                    <h3 class="h4 mb-0">
                                        @lang('public.careers.open_app.form_title')
                                        <span class="text-primary">{{ $openDept }}</span>
                                    </h3>
                                    <button type="button" class="btn-close" wire:click="closeOpenForm" aria-label="Close"></button>
                                </div>

                                @if ($openSuccess)
                                    <div class="alert alert-success text-center" role="alert">
                                        <i class="uil uil-check-circle fs-22"></i>
                                        @lang('public.careers.application_success')
                                    </div>
                                @else
                                    <form wire:submit.prevent="submitOpenApplication">
                                        <div class="row gx-4">
                                            {{-- Department selector (editable) --}}
                                            <div class="col-12 mb-4">
                                                <div class="form-floating">
                                                    <select id="open_dept" wire:model="openDept" class="form-select">
                                                        @foreach ($openDepartments as $d)
                                                            <option value="{{ $d }}">{{ $d }}</option>
                                                        @endforeach
                                                    </select>
                                                    <label for="open_dept">@lang('public.careers.open_app.department') *</label>
                                                </div>
                                                @error('openDept') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_name" type="text" wire:model="openName" class="form-control" placeholder="">
                                                    <label for="open_name">@lang('public.careers.form.full_name') *</label>
                                                    @error('openName') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_email" type="email" wire:model="openEmail" class="form-control" placeholder="">
                                                    <label for="open_email">@lang('public.careers.form.email') *</label>
                                                    @error('openEmail') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_phone" type="tel" wire:model="openPhone" class="form-control" placeholder="">
                                                    <label for="open_phone">@lang('public.careers.form.phone') *</label>
                                                    @error('openPhone') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_city" type="text" wire:model="openCity" class="form-control" placeholder="">
                                                    <label for="open_city">@lang('public.careers.form.city') *</label>
                                                    @error('openCity') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_nationality" type="text" wire:model="openNationality" class="form-control" placeholder="">
                                                    <label for="open_nationality">@lang('public.careers.form.nationality') *</label>
                                                    @error('openNationality') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_education" type="text" wire:model="openEducation" class="form-control" placeholder="">
                                                    <label for="open_education">@lang('public.careers.form.education') *</label>
                                                    @error('openEducation') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-floating mb-4">
                                                    <input id="open_experience" type="number" min="0" wire:model="openYearsOfExperience" class="form-control" placeholder="">
                                                    <label for="open_experience">@lang('public.careers.form.years_of_experience') *</label>
                                                    @error('openYearsOfExperience') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-4">
                                                    <label for="open_cv" class="form-label">@lang('public.careers.form.cv') * <span class="text-muted">(PDF)</span></label>
                                                    <input id="open_cv" type="file" wire:model="openCv" class="form-control" accept=".pdf">
                                                    <div wire:loading wire:target="openCv" class="text-muted mt-1">@lang('public.careers.form.uploading')</div>
                                                    @error('openCv') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                                                </div>
                                            </div>

                                            <div class="col-12 text-center mt-2">
                                                <button type="submit" class="btn btn-primary rounded-pill px-8" wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="submitOpenApplication">@lang('public.careers.form.submit')</span>
                                                    <span wire:loading wire:target="submitOpenApplication">@lang('public.careers.form.submitting')</span>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>
