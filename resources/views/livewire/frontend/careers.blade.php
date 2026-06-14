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
</div>
