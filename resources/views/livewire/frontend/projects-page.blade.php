@push('styles')

<style>
    .pagination .page-item.active .page-link {
        background-color: #122818;
        color: #FFF;
        border-color: #122818;
    }

    .pagination .page-item.active:hover .page-link {
        background-color: #122818;
        color: #FFF;
    }
    /* Filter Sidebar Specific Styles */
    .filter-sidebar__toggle-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1040;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #007bff;
        border: none;
        color: white;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .filter-sidebar__overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1045;
        display: none;
    }

    .filter-sidebar {
        position: fixed;
        height: 100%;
        width: 85%;
        max-width: 400px;
        background-color: white;
        z-index: 1069 !important;
    }

    /* Mobile Styles */
    @media (max-width: 991.98px) {
        .filter-sidebar {
            top: 0;
            right: 0;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .filter-sidebar--active {
            transform: translateX(0);
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .filter-sidebar__overlay--active {
            display: block;
        }
    }

    /* Desktop Styles */
    @media (min-width: 992px) {
        .filter-sidebar {
            position: absolute;;
            top: 20px;
            right: 0px !important;
            height: fit-content;
            transform: none;
            max-width: calc((2.1 / 12) * 100%);
            border-radius: 15px;
            border: 1px solid #e6e6e640;
            box-shadow: 0 2px 10px rgba(185, 185, 185, 0.05);
        }
    }

    .filter-sidebar__container {
        height: 100%;
        overflow-y: auto;
        padding: .8rem;
    }

    .filter-sidebar__header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .filter-sidebar__title {
        font-size: 0.9375rem;
        margin: 0;
    }

    .filter-sidebar__close-btn {
        width: 32px;
        height: 32px;
        background: transparent url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3E%3C/svg%3E") 50%/1em auto no-repeat;
        border: 0;
        border-radius: 0.25rem;
        opacity: .5;
        cursor: pointer;
    }

    /* Prevent body scroll when sidebar is open on mobile */
    .filter-sidebar--open {
        overflow: hidden;
    }
</style>
@endpush
<section class="section-frame mx-xxl-5 position-relative projectspage" dir="rtl">
    @livewire('frontend.conponents.unit-popup')

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-10 me-auto">
            <div class="container-fluid py-7 py-md-10 pe-md-8">
                <!-- Header -->
                <div class="row align-items-center mb-5 position-relative zindex-1">
                    <div class="col-md-7 col-xl-8 ps-xl-20 d-flex">
                        <ul class="nav nav-tabs nav-pills tab-box" dir="rtl" style="width: fit-content;">
                            <li class="nav-item">
                                <a wire:click="$set('view_type', 'projects')"
                                   class="nav-link px-4 {{ $view_type === 'projects' ? 'active noise-container' : '' }}"
                                   style="cursor: pointer;">
                                    المشاريع
                                </a>
                            </li>
                            <li class="nav-item">
                                <a wire:click="$set('view_type', 'units')"
                                   class="nav-link px-4 {{ $view_type === 'units' ? 'active noise-container' : '' }}"
                                   style="cursor: pointer;">
                                    الوحدات
                                </a>
                            </li>
                        </ul>
                        <div class="my-auto">
                            <p class="mb-0 me-3">
                                <span class="h2 text-main mb-0">{{ $items->total() }}</span>
                                <span class="text-muted small">متاح</span>
                            </p>
                        </div>
                        <div wire:loading class="my-auto">
                            <div class="spinner-border spinner-border-sm me-4" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-5 col-xl-2 me-md-auto text-md-end mt-5 mt-md-0">
                        <!-- Mobile Filter Button -->
                        <button class="btn btn-primary rounded-pill d-lg-none position-fixed bottom-0 start-50 translate-middle-x mb-4 px-4"
                                style="z-index: 1040;"
                                onclick="filterSidebarToggle()">
                            <i class="uil uil-filter me-1"></i> فلترة
                        </button>

                        <div class="form-select-wrapper">
                            <select wire:change="sortBy($event.target.value)" class="form-select">
                                <option selected disabled>ترتيب</option>
                                <option value="unit_price" {{ $sort_by === 'unit_price' ? 'selected' : '' }}>ترتيب حسب السعر {{ $sort_by === 'unit_price' && $sort_direction === 'asc' ? '▲' : '▼' }}</option>
                                {{-- <option value="name" {{ $sort_by === 'name' ? 'selected' : '' }}>ترتيب حسب الاسم {{ $sort_by === 'name' && $sort_direction === 'asc' ? '▲' : '▼' }}</option> --}}
                                <option value="created_at" {{ $sort_by === 'created_at' ? 'selected' : '' }}>ترتيب حسب وقت النشر {{ $sort_by === 'created_at' && $sort_direction === 'asc' ? '▲' : '▼' }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                @if($view_type === 'projects')
                    <!-- Projects Grid -->
                    <div class="projects-masonry shop">
                        <div class="row" wire:loading.class="opacity-50">

                            @forelse($items as $project)
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-3" wire:key="{{ $project->id }}" data-tracking='{"type":"project","id":{{ $project->id }}}' data-project-id="{{ $project->id }}">
                                    <article class="post">

                                        <figure class="rounded-top position-relative">
                                            <a href="{{ route('frontend.projects.single', $project->slug) }}"> <img src="@if($project->getMainImages() !== null ) {{ App\Helpers\MediaHelper::getUrl($project->getMainImages()->media_url) }} @else {{ App\Helpers\MediaHelper::getUrl($project->projectMedia()->first()->media_url) }} @endif" style="max-height: 200px" alt="{{ $project->name }}" loading="lazy" /></a>
                                            <figcaption class="noise-container text-right heroTop position-absolute" style="top: 6px;right: 6px;" dir="rtl">
                                                <span class="badge badge-lg text-white d-flex align-content-center align-items-center">
                                                    <i class="uil uil-map-marker fs-15 ms-1"></i>
                                                    {{ $project->address }}
                                                </span>
                                            </figcaption>

                                        </figure>

                                        <div class="post-header project-data-card rounded-bottom bg-white">
                                            <div class="d-flex align-content-start justify-content-between w-100">
                                                <h2 class="post-title h6 mt-0">
                                                    <a href="{{ route('frontend.projects.single', $project->slug) }}">
                                                        {{ $project->name }}
                                                        {{-- <span class="badge rounded-pill  @if($project->dynamic_project_status == 'متاح') bg-pale-leaf text-leaf @elseif ($project->dynamic_project_status == 'تحت الانشاء') bg-pale-red text-danger @else bg-pale-yellow text-yellow @endif">{{ $project->dynamic_project_status }}</span> --}}
                                                        <span class="badge rounded-pill
                                                            @if($project->project_status_type == 'available')
                                                                bg-pale-leaf text-leaf
                                                            @elseif($project->project_status_type == 'under_construction')
                                                                bg-pale-orange text-orange
                                                            @elseif($project->project_status_type == 'sold_out')
                                                                bg-pale-red text-danger
                                                            @elseif($project->project_status_type == 'fully_reserved')
                                                                bg-pale-blue text-primary
                                                            @else
                                                                bg-pale-purple text-purple
                                                            @endif">
                                                            {{ $project->dynamic_project_status }}
                                                        </span>
                                                    </a>

                                                </h2>
                                                <div>
                                                    <span class="badge bg-pale-ash text-dark rounded-pill">{{ $project->projectType->name }}</span>
                                                </div>
                                            </div>

                                            <ul class="post-meta mb-3">
                                                <li class="post-date">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $project->price_range }}  <img src="{{ asset('frontend/img/SaudiRiyal.svg') }}" width="14px" alt=""></span>
                                                </li>
                                            </ul>
                                            <ul class="post-meta mb-0">
                                                <li class="post-date">
                                                    <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}"  class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $project->bathroom_range }}</span>
                                                </li>
                                                <li class="post-author">
                                                    <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $project->bedroom_range }}</span>
                                                </li>
                                                <li class="post-comments">
                                                    <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $project->space_range }}</span>
                                                </li>
                                            </ul>
                                            <!-- /.post-meta -->
                                        </div>
                                        <!-- /.post-footer -->
                                    </article>
                                    <!-- /.post -->
                                </div>

                            @empty

                                <div class="col-12 text-center m-auto">
                                    <img src="{{ asset('frontend/img/EmptyInbox.png') }}" alt="Riva - ريفا">
                                    <p class="text-main fs-bold mb-1">تعذر وجود نتائج!</p>
                                    <p class="text-muted fs-15">لم نتمكن من العثور على أي عقارات تتناسب مع جميع <br> المعايير التي حددتها.</p>
                                </div>

                            @endforelse
                        </div>

                    </div>
                @else
                    <!-- units Grid -->
                    <div class="projects-masonry shop">
                        <div class="row" wire:loading.class="opacity-50">
                            <!-- Units grid -->
                            @forelse($items as $unit)
                                <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-3" wire:key="{{ $unit->id }}">
                                    <article class="post">

                                        <figure class="rounded-top position-relative">
                                            <a wire:click="showUnitDetails({{ $unit->id }})" data-unit-id="{{ $unit->id }}">
                                                @if ($unit->floor_plan)
                                                    <img src="{{ App\Helpers\MediaHelper::getUrl($unit->floor_plan ) }}" style="max-height: 200px" alt="{{ $unit->title }}" loading="lazy" />
                                                @else
                                                    <img src="{{ App\Helpers\MediaHelper::getUrl($unit->project->getMainImages()->media_url ) }}" style="max-height: 200px" alt="{{ $unit->title }}" loading="lazy" />
                                                @endif
                                            </a>
                                            <figcaption class="glass-white-card text-right heroTop position-absolute" style="top: 6px;right: 6px;" dir="rtl">
                                                <span class="badge badge-lg text-main d-flex align-content-center align-items-center">
                                                    <i class="uil uil-map-marker fs-15 ms-1"></i>
                                                    {{ $unit->project->name }}
                                                </span>
                                            </figcaption>

                                        </figure>

                                        <div class="post-header project-data-card rounded-bottom bg-white">
                                            <div class="d-flex align-content-start justify-content-between w-100">
                                                <h2 class="post-title h6 mt-0 mb-0">
                                                    <a wire:click="showUnitDetails({{ $unit->id }})" data-unit-id="{{ $unit->id }}">
                                                        {{ $unit->title }}
                                                        <span class="badge rounded-pill  @if($unit->case == 1) bg-pale-yellow text-yellow @elseif($unit->case == 2) bg-pale-red text-red @else bg-pale-leaf text-leaf @endif">
                                                            @if($unit->case == 1) محجوزة @elseif($unit->case == 2) مباعة @else متاحة @endif
                                                        </span>
                                                    </a>
                                                </h2>
                                                <div>
                                                    <span class="badge bg-pale-ash text-dark rounded-pill">{{ $unit->unit_type }}</span>
                                                </div>
                                            </div>
                                            @if ($unit->show_price && $unit->unit_price)
                                                <ul class="post-meta mb-3">
                                                    <li class="post-date">
                                                        <span class="fs-15 text-success">
                                                            {{ number_format($unit->unit_price) }} <img src="{{ asset('frontend/img/SaudiRiyal.svg') }}" width="14px" alt="">
                                                        </span>
                                                    </li>
                                                </ul>
                                            @else
                                                <span>---</span>
                                            @endif
                                            <ul class="post-meta mb-0  @if (!$unit->show_price) mt-4 @endif">
                                                <li class="post-date">
                                                    <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}"  class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $unit->bathrooms }}</span>
                                                </li>
                                                <li class="post-author">
                                                    <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $unit->beadrooms }}</span>
                                                </li>
                                                {{-- <li class="post-author">
                                                    <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $unit->kitchen }}</span>
                                                </li> --}}
                                                <li class="post-comments">
                                                    <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                    <span class="me-1 fs-15 text-gray-800">{{ $unit->unit_area . ' م²' }}</span>
                                                </li>
                                            </ul>
                                            <!-- /.post-meta -->
                                        </div>
                                        <!-- /.post-footer -->
                                    </article>
                                    <!-- /.post -->
                                </div>
                            @empty
                                <div class="col-12 text-center m-auto">
                                    <img src="{{ asset('frontend/img/EmptyInbox.png') }}" alt="Riva - ريفا">
                                    <p class="text-main fs-bold mb-1">تعذر وجود نتائج!</p>
                                    <p class="text-muted fs-15">لم نتمكن من العثور على أي عقارات تتناسب مع جميع <br> المعايير التي حددتها.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endif

                <!-- Pagination -->
                <div class="mt-5" dir="ltr">
                    {{ $items->links() }}
                </div>
            </div>
        </div>

    </div>

    <!-- Sidebar -->
    <aside class="filter-sidebar" id="filterSidebar" wire:ignore.self >

        <div class="filter-sidebar__container" style="border: 1px solid #e6e6e6;border-radius: 15px;">

            <div class="filter-sidebar__header border-bottom border-muted pb-2">
                <h3 class="filter-sidebar__title">فلتر مُخصص</h3>
                <a href="{{route('frontend.projects')}}" class="text-warning fs-12 mb-0">اعداة تعيين</a>
                <!-- Mobile Close Button -->
                <div class="widget d-lg-none">
                    <button class="filter-sidebar__close-btn" id="filterSidebarOverlay" onclick="filterSidebarToggle()"></button>
                </div>
            </div>

            <!-- Project Types -->
            <div class="widget mt-3 border-bottom border-muted pb-2">
                <h6 class="text-gray-700 fs-13 mb-3">حالات المشاريع</h6>
                <ul class="list-unstyled pe-0">
                    <li class="mb-1">
                        <div class="form-check">
                            <!-- Custom Checkbox Container -->
                            <label class="custom-checkbox-container" for="type-a">
                                <input class="form-check-input custom-checkbox" type="checkbox"
                                    wire:model.live="projectCaseAvilable"
                                    id="type-a">
                                <!-- Custom Checkbox -->
                                <span class="custom-checkbox-checkmark"></span>
                                <span class="form-check-label fw-light text-main">
                                    عرض المتاح فقط
                                </span>
                            </label>
                        </div>
                    </li>
                </ul>
            </div>

            <div class="widget mt-3 border-bottom border-muted pb-2">
                <h6 class="fs-13 mb-3 text-gray-700">السعر</h6>
                <div class="position-relative multi-range-slider">
                    <!-- Min price range -->
                    <input type="range" class="form-range range-min position-absolute w-100"
                        wire:model.live="price_min"
                        min="0" max="10000000" step="100">

                    <!-- Max price range -->
                    <input type="range" class="form-range range-max position-absolute w-100"
                        wire:model.live="price_max"
                        min="0" max="10000000" step="100">
                </div>
                <div class="d-flex justify-content-between mt-0">
                    <span class="small">{{ number_format($price_min) }}</span>
                    <span class="small">{{ number_format($price_max) }}</span>
                </div>
            </div>

            <div class="widget mt-3 border-bottom border-muted pb-2">
                <h6 class="text-gray-700 fs-13 mb-3">المساحة</h6>
                <div class="position-relative multi-range-slider">
                    <!-- Min space range -->
                    <input type="range" class="form-range range-min position-absolute w-100"
                        wire:model.live="space_min"
                        min="0" max="5000" step="10">

                    <!-- Max space range -->
                    <input type="range" class="form-range range-max position-absolute w-100"
                        wire:model.live="space_max"
                        min="0" max="5000" step="10">
                </div>
                <div class="d-flex justify-content-between mt-0">
                    <span class="small">{{ number_format($space_min) }}</span>
                    <span class="small">{{ number_format($space_max) }}</span>
                </div>
            </div>

            <!-- State Dropdown -->
            <div class="widget mt-3 border-bottom border-muted pb-2">
                <h6 class="text-gray-700 fs-13 mb-3">الموقع</h6>
                <div class="d-flex">
                    <!-- City Dropdown -->
                    <select wire:model.live="selected_cities" class="form-select shadow-0 ms-2">
                        <option value="">المدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>

                    <select wire:model.live="selected_states" class="form-select shadow-0">
                        <option value="">الحي</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Project Types -->
            <div class="widget mt-3 border-bottom border-muted pb-2">
                <h6 class="text-gray-700 fs-13 mb-3">نوع الوحدة</h6>
                <ul class="list-unstyled pe-0">
                    @foreach($projectTypes as $type)
                        <li class="mb-1">
                            <div class="form-check">
                                <!-- Custom Checkbox Container -->
                                <label class="custom-checkbox-container" for="type-{{ $type->id }}">
                                    <input class="form-check-input custom-checkbox" type="checkbox"
                                        wire:model.live="selected_projectTypes"
                                        value="{{ $type->id }}"
                                        id="type-{{ $type->id }}">
                                    <!-- Custom Checkbox -->
                                    <span class="custom-checkbox-checkmark"></span>
                                    <span class="form-check-label fw-light text-main">
                                        {{ $type->name }}
                                    </span>
                                </label>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Developers -->
            <div class="accordion accordion-wrapper border-bottom border-muted" id="accordionSimpleExample">
                <div class="card plain accordion-item">
                    <div class="card-header" id="headingSimpleOne">
                        <button class="collapsed text-gray-700 fs-13 mb-32 text-right" data-bs-toggle="collapse" data-bs-target="#collapseSimpleOne" aria-expanded="false" aria-controls="collapseSimpleOne">
                            المطور
                        </button>
                    </div>
                    <!--/.card-header -->
                    <div id="collapseSimpleOne" wire:ignore.self class="accordion-collapse collapse" aria-labelledby="headingSimpleOne" data-bs-parent="#accordionSimpleExample">
                        <div class="card-body mb-3">

                            @foreach($developers as $developer)
                                <div class="form-check d-inline p-0" >
                                    <input class="form-check-input d-none" type="checkbox"
                                        wire:model.live="selected_developer"
                                        value="{{ $developer->id }}"
                                        id="dev-{{ $developer->id }}">
                                    <label class="form-check-label bg-white p-2 mb-1 border rounded {{ in_array($developer->id, $selected_developer) ? 'border-primary' : '' }}" for="dev-{{ $developer->id }}">
                                        <!-- Developer Logo -->
                                        @if($developer->logo)
                                            <img src="{{ App\Helpers\MediaHelper::getUrl($developer->logo) }}" alt="{{ $developer->name }} logo" class="developer-logo" style="height: 30px; object-fit: cover;">
                                        @endif
                                        <span class="fs-14 fw-light me-2">{{ $developer->name }}</span>
                                    </label>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>


            <div class="widget mt-3">
                <h6 class="text-gray-700 fs-13 mb-3">غرف النوم</h6>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bedrooms" value="2"
                           id="selected_bedrooms-2">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('2', $selected_bedrooms)) bg-main text-white @else  @endif"
                           for="selected_bedrooms-2"> 2 </label>
                </div>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bedrooms" value="3"
                           id="selected_bedrooms-3">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('3', $selected_bedrooms)) bg-main text-white @else  @endif"
                           for="selected_bedrooms-3"> 3 </label>
                </div>

                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bedrooms" value="4"
                           id="selected_bedrooms-4">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('4', $selected_bedrooms)) bg-main text-white @else  @endif"
                           for="selected_bedrooms-4"> 4 </label>
                </div>

                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bedrooms" value="5"
                           id="selected_bedrooms-5">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('5', $selected_bedrooms)) bg-main text-white @else  @endif"
                           for="selected_bedrooms-5"> 5 </label>
                </div>

            </div>

            <div class="widget mt-3">
                <h6 class="text-gray-700 fs-13 mb-3">الحمامات</h6>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bathrooms" value="2"
                           id="selected_bathrooms-2">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('2', $selected_bathrooms)) bg-main text-white @else @endif"
                           for="selected_bathrooms-2"> 2 </label>
                </div>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bathrooms" value="3"
                           id="selected_bathrooms-3">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('3', $selected_bathrooms)) bg-main text-white @else @endif"
                           for="selected_bathrooms-3"> 3 </label>
                </div>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bathrooms" value="4"
                           id="selected_bathrooms-4">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('4', $selected_bathrooms)) bg-main text-white @else @endif"
                           for="selected_bathrooms-4"> 4 </label>
                </div>

                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_bathrooms" value="5"
                           id="selected_bathrooms-5">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('5', $selected_bathrooms)) bg-main text-white @else @endif"
                           for="selected_bathrooms-5"> 5 </label>
                </div>
            </div>

            <div class="widget mt-3">
                <h6 class="text-gray-700 fs-13 mb-3">المطابخ</h6>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_kitchens" value="2"
                           id="selected_kitchens-2">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('2', $selected_kitchens)) bg-main text-white @else @endif"
                           for="selected_kitchens-2"> 2 </label>
                </div>
                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_kitchens" value="3"
                           id="selected_kitchens-3">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('3', $selected_kitchens)) bg-main text-white @else @endif"
                           for="selected_kitchens-3"> 3 </label>
                </div>

                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_kitchens" value="4"
                           id="selected_kitchens-4">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('4', $selected_kitchens)) bg-main text-white @else @endif"
                           for="selected_kitchens-4"> 4 </label>
                </div>

                <div class="form-check d-inline p-0 mb-1">
                    <input class="form-check-input d-none" type="checkbox"
                           wire:model.live="selected_kitchens" value="5"
                           id="selected_kitchens-5">
                    <label class="form-check-label p-1 px-3 mb-1 border rounded @if(in_array('5', $selected_kitchens)) bg-main text-white @else @endif"
                           for="selected_kitchens-5"> 5 </label>
                </div>

            </div>

        </div>
    </aside>
</section>
@push('scripts')
<script>
    function filterSidebarToggle() {
        const sidebar = document.getElementById('filterSidebar');
        const overlay = document.getElementById('filterSidebarOverlay');
        const body = document.body;

        sidebar.classList.toggle('filter-sidebar--active');
        overlay.classList.toggle('filter-sidebar__overlay--active');
        body.classList.toggle('filter-sidebar--open');
    }

    // Prevent clicks inside sidebar from bubbling up to overlay
    document.getElementById('filterSidebar').addEventListener('click', function(event) {
        event.stopPropagation();
    });
</script>
@endpush
