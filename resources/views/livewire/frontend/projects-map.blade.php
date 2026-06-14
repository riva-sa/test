@push('styles')

<!-- Leaflet base CSS (no longer loaded globally; MarkerCluster depends on it) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

<style>
    .leaflet-control-attribution{
        display: none !important;
    }
    
    .side-sheet {
        position: fixed;
        right: -4010px;
        width: 0px;
        height: 0px;
        background: white;
        box-shadow: 0px 0px 0px 2px rgba(18, 40, 24, 0.03);
        transition: right 0.3s ease;
        z-index: 1000;
        overflow-y: auto;
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        top: 13.15px !important;
        border-radius: 20px;
    }
    .side-sheet.active {
        height: calc(100vh - 96.15px);
        width: 600px;
        right: 10px;
    }
    .side-sheet-close {
        position: absolute;
        top: 14px;
        left: 17px;
        background: #CCC0;
        color: rgb(128, 128, 128);
        border: 1px solid rgba(204, 204, 204, 0.754) !important;
        width: 1.5rem !important;
        height: 1.5rem !important;
        font-size: 14px !important;
    }
    .side-sheet-close:hover {
        transform: unset !important;
        background: unset !important;
    }
    .post-meta li::before {
        height: 10px;
        border-radius: unset
    }

    .custom-marker{
        margin-left: -30px;
        margin-top: -50px;
        width: 200px !important;
        height: fit-content !important;
        z-index: 119;
        background: rgba(255, 255, 255, 0);
        backdrop-filter: blur(8px);
        border-radius: 9px;
        box-shadow: 0 0px 1px 0px rgb(18, 40, 24);
    }

    .custom-marker::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.4;
        border-radius: 9px;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%' height='100%' filter='url(%23noise)'/%3E%3C/svg%3E");
        background-size: 150px;
    }
    .marker-cluster-small {
        background-color: rgb(144, 154, 146);
    }
    .marker-cluster-small div{
        background-color: rgba(0, 0, 0, 0.6);
    }
    .marker-cluster span {
        color: #FFF !important;
    }

    /* Floating Filter Bar Styles */
    .map-floating-bar-wrapper {
        position: fixed;
        bottom: 40px; /* Increased from 30px */
        left: 50%;
        transform: translateX(-50%);
        z-index: 1060; /* Higher than map controls */
        width: fit-content;
        max-width: 95vw;
    }

    .map-floating-bar {
        background: #ffffff;
        border: 1px solid rgba(18, 40, 24, 0.1);
        border-radius: 100px;
        display: flex;
        align-items: center;
        padding: 6px;
        box-shadow: 0 10px 40px rgba(18, 40, 24, 0.15);
        backdrop-filter: blur(12px);
        overflow: visible;
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        position: relative;
    }

    .noise-container::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.02;
        border-radius: 100px;
        pointer-events: none;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%' height='100%' filter='url(%23noise)'/%3E%3C/svg%3E");
    }

    .filter-section {
        display: flex !important;
        align-items: center;
        padding: 0 15px;
        height: 40px;
        position: relative;
    }

    .filter-btn {
        background: transparent;
        border: none;
        color: #122818;
        font-size: 14px;
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 0;
        white-space: nowrap;
        transition: all 0.2s;
    }

    .filter-btn:hover {
        opacity: 0.8;
    }

    .filter-divider {
        width: 1px;
        height: 20px;
        background: rgba(18, 40, 24, 0.1);
    }

    .dropdown-section.active .arrow-icon {
        transform: rotate(180deg);
    }

    .arrow-icon {
        transition: transform 0.2s;
    }

    .filter-dropdown {
        position: absolute;
        bottom: calc(100% + 15px);
        left: 50%;
        transform: translateX(-50%);
        background: #ffffff;
        border: 1px solid rgba(18, 40, 24, 0.1);
        border-radius: 20px;
        padding: 8px;
        min-width: 220px;
        max-height: 350px;
        overflow-y: auto;
        box-shadow: 0 20px 50px rgba(18, 40, 24, 0.2);
        direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
        z-index: 1070;
    }

    .dropdown-item {
        padding: 10px 12px;
        color: rgba(18, 40, 24, 0.8);
        font-size: 13px;
        cursor: pointer;
        border-radius: 8px;
        display: flex;
        align-items: center;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background: rgba(18, 40, 24, 0.05);
        color: #122818;
    }

    .dropdown-item.selected {
        background: rgba(18, 40, 24, 0.05);
        color: #122818;
    }

    .dropdown-item .dot {
        width: 8px;
        height: 8px;
        background: transparent;
        border-radius: 50%;
        margin-left: 12px;
        flex-shrink: 0;
    }

    .dropdown-item.selected .dot {
        background: #122818; 
        box-shadow: 0 0 12px rgba(18, 40, 24, 0.3);
    }

    /* Help Tooltip Card */
    .help-tooltip-card {
        position: absolute;
        bottom: calc(100% + 20px);
        left: 50%;
        transform: translateX(-50%);
        background: #122818;
        color: white;
        padding: 16px 20px;
        border-radius: 18px;
        width: 280px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.4);
        z-index: 1100;
        text-align: right;
    }

    .help-tooltip-card::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translateX(-50%);
        border-width: 10px;
        border-style: solid;
        border-color: #122818 transparent transparent transparent;
    }

    .help-tooltip-card h6 {
        color: #fff;
        margin-bottom: 8px;
        font-size: 15px;
    }

    .help-tooltip-card p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 13px;
        margin-bottom: 15px;
        line-height: 1.5;
    }

    .help-card-btn {
        background: #ffffff;
        color: #122818;
        border: none;
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        text-decoration: none !important;
    }

    .help-card-btn:hover {
        background: #f1f1f1;
        transform: translateY(-2px);
    }

    @keyframes slideUpFade {
        from { opacity: 0; transform: translate(-50%, 10px); }
        to { opacity: 1; transform: translate(-50%, 0); }
    }

    .animate-help {
        animation: slideUpFade 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .map-highlight-green {
        box-shadow: inset 0 0 100px rgba(18, 40, 24, 0.2);
    }

    @media (max-width: 768px) {
        .map-floating-bar-wrapper {
            bottom: unset;
            top: 15px;
            width: 100%;
            display: flex;
            justify-content: center;
        }
        .map-floating-bar {
            padding: 4px 8px;
            overflow: visible !important;
            max-width: 92vw;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(18, 40, 24, 0.2);
        }
        .map-floating-bar::-webkit-scrollbar {
            display: none;
        }
        .filter-section {
            padding: 0 8px;
            height: 36px;
        }
        .count-section {
            display: none !important;
        }
        .filter-btn span {
            display: none;
        }
        .filter-btn i {
            font-size: 18px;
            margin: 0 !important;
        }
        /* Make dropdowns open downwards on mobile */
        .filter-dropdown {
            bottom: unset !important;
            top: calc(100% + 10px) !important;
        }
        /* Make help tooltip open downwards on mobile */
        .help-tooltip-card {
            bottom: unset !important;
            top: calc(100% + 15px) !important;
        }
        .help-tooltip-card::after {
            top: unset !important;
            bottom: 100% !important;
            border-color: transparent transparent #122818 transparent !important;
        }
    }
</style>

@endpush

<div class="projects-map-wrapper" style="width: 100vw; position: relative; margin-right: calc(-50vw + 50%); margin-left: calc(-50vw + 50%); left: 50%; transform: translateX(-50%);">
    
    <!-- Floating Filter Bar -->
    <div class="map-floating-bar-wrapper" x-data="{ 
        openFilter: null,
        showHelp: false,
        toggleFilter(name) {
            this.openFilter = this.openFilter === name ? null : name;
        }
    }" @click.away="openFilter = null" 
       @help-triggered.window="showHelp = true;">
        
        <!-- Help Card (No Results) -->
        <template x-if="showHelp">
            <div class="help-tooltip-card animate-help">
                <h6>@lang('public.map.no_results')</h6>
                <p>@lang('public.map.no_results_desc')</p>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="https://wa.me/{{ setting('site_phone') }}?text={{ urlencode(__('public.map.whatsapp_help')) }}" target="_blank" class="help-card-btn">
                        <i class="uil uil-whatsapp"></i> @lang('public.map.contact_us')
                    </a>
                    <button @click="showHelp = false" class="btn btn-link btn-sm text-white opacity-50 p-0 text-decoration-none">@lang('public.map.close')</button>
                </div>
            </div>
        </template>
        <div class="map-floating-bar noise-container">
            <!-- Project Count -->
            <div class="filter-section count-section">
                <span class="fs-13 opacity-75" style="color: #122818;">{{ count($projects) }} @lang('public.map.projects_count')</span>
            </div>
            
            <div class="filter-divider"></div>

            <!-- Project Type Dropdown -->
            <div class="filter-section dropdown-section" :class="{ 'active': openFilter === 'type' }">
                <button class="filter-btn" @click="toggleFilter('type')">
                    <i class="uil uil-apps me-1 opacity-60"></i>
                    <span class="mx-1">{{ $selected_projectTypes ? ($projectTypes->firstWhere('id', $selected_projectTypes)->name ?? __('public.map.type')) : __('public.map.type') }}</span>
                    <i class="uil uil-angle-down ms-1 arrow-icon opacity-40"></i>
                </button>
                
                <div class="filter-dropdown" x-show="openFilter === 'type'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" style="display: none;">
                    <div class="dropdown-item" :class="{ 'selected': @js($selected_projectTypes) === '' }" wire:click="$set('selected_projectTypes', '')" @click="openFilter = null">
                        <span class="dot"></span> @lang('public.map.all')
                    </div>
                    @foreach($projectTypes as $type)
                        <div class="dropdown-item" :class="{ 'selected': @js((string)$selected_projectTypes) === @js((string)$type->id) }" wire:click="$set('selected_projectTypes', '{{ $type->id }}')" @click="openFilter = null">
                            <span class="dot"></span> {{ $type->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            <!-- Developer Dropdown -->
            <div class="filter-section dropdown-section" :class="{ 'active': openFilter === 'developer' }">
                <button class="filter-btn" @click="toggleFilter('developer')">
                    <i class="uil uil-building me-1 opacity-60"></i>
                    <span class="mx-1">{{ $selected_developer ? (Str::limit($developers->firstWhere('id', $selected_developer)->name ?? __('public.map.developer'), 10)) : __('public.map.developer') }}</span>
                    <i class="uil uil-angle-down ms-1 arrow-icon opacity-40"></i>
                </button>
                
                <div class="filter-dropdown" x-show="openFilter === 'developer'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" style="display: none;">
                    <div class="dropdown-item" :class="{ 'selected': @js($selected_developer) === '' }" wire:click="$set('selected_developer', '')" @click="openFilter = null">
                        <span class="dot"></span> @lang('public.map.all')
                    </div>
                    @foreach($developers as $developer)
                        <div class="dropdown-item" :class="{ 'selected': @js((string)$selected_developer) === @js((string)$developer->id) }" wire:click="$set('selected_developer', '{{ $developer->id }}')" @click="openFilter = null">
                            <span class="dot"></span> {{ $developer->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider"></div>

            <!-- City Dropdown -->
            <div class="filter-section dropdown-section" :class="{ 'active': openFilter === 'city' }">
                <button class="filter-btn" @click="toggleFilter('city')">
                    <i class="uil uil-map-marker me-1 opacity-60"></i>
                    <span class="mx-1">{{ $selected_cities ? ($cities->firstWhere('id', $selected_cities)->name ?? __('public.map.city')) : __('public.map.city') }}</span>
                    <i class="uil uil-angle-down ms-1 arrow-icon opacity-40"></i>
                </button>
                
                <div class="filter-dropdown" x-show="openFilter === 'city'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" style="display: none;">
                    <div class="dropdown-item" :class="{ 'selected': @js($selected_cities) === null }" wire:click="$set('selected_cities', null)" @click="openFilter = null">
                        <span class="dot"></span> @lang('public.map.all')
                    </div>
                    @foreach($cities as $city)
                        <div class="dropdown-item" :class="{ 'selected': @js((string)$selected_cities) === @js((string)$city->id) }" wire:click="$set('selected_cities', '{{ $city->id }}')" @click="openFilter = null">
                            <span class="dot"></span> {{ $city->name }}
                        </div>
                    @endforeach
                </div>
            </div>

            @if($selected_cities)
                <div class="filter-divider"></div>
                <!-- Neighborhood Dropdown -->
                <div class="filter-section dropdown-section" :class="{ 'active': openFilter === 'state' }">
                    <button class="filter-btn" @click="toggleFilter('state')">
                        <i class="uil uil-location-pin-alt me-1 opacity-60"></i>
                        <span class="mx-1">{{ $selected_states ? ($states->firstWhere('id', $selected_states)->name ?? __('public.map.district')) : __('public.map.district') }}</span>
                        <i class="uil uil-angle-down ms-1 arrow-icon opacity-40"></i>
                    </button>
                    
                    <div class="filter-dropdown" x-show="openFilter === 'state'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" style="display: none;">
                        <div class="dropdown-item" :class="{ 'selected': @js($selected_states) === null }" wire:click="$set('selected_states', null)" @click="openFilter = null">
                            <span class="dot"></span> @lang('public.map.all')
                        </div>
                        @foreach($states as $state)
                            <div class="dropdown-item" :class="{ 'selected': @js((string)$selected_states) === @js((string)$state->id) }" wire:click="$set('selected_states', '{{ $state->id }}')" @click="openFilter = null">
                                <span class="dot"></span> {{ $state->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="filter-divider"></div>

            <!-- Price Range Dropdown -->
            <div class="filter-section dropdown-section" :class="{ 'active': openFilter === 'price' }">
                <button class="filter-btn" @click="toggleFilter('price')">
                    <i class="uil uil-usd-circle me-1 opacity-60"></i>
                    <span class="mx-1">{{ $price_range ? __('public.map.less_than') . number_format($price_range) : __('public.map.price') }}</span>
                    <i class="uil uil-angle-down ms-1 arrow-icon opacity-40"></i>
                </button>
                
                <div class="filter-dropdown" x-show="openFilter === 'price'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2" style="display: none;">
                    <div class="dropdown-item" :class="{ 'selected': @js($price_range) == 0 }" wire:click="$set('price_range', 0)" @click="openFilter = null">
                        <span class="dot"></span> @lang('public.map.all')
                    </div>
                    @foreach([500000, 1000000, 2000000, 3000000, 5000000] as $price)
                        <div class="dropdown-item" :class="{ 'selected': @js($price_range) == @js($price) }" wire:click="$set('price_range', {{ $price }})" @click="openFilter = null">
                            <span class="dot"></span> @lang('public.map.less_than') {{ number_format($price) }}
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="filter-divider d-none d-md-block"></div>

            <!-- Reset Button -->
            <div class="filter-section reset-section d-none d-md-block">
                <button class="filter-btn text-danger" wire:click="resetFilters" @click="openFilter = null; showHelp = false;">
                    <i class="uil uil-refresh"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div wire:ignore id="map" 
         :class="{ 'map-highlight-green': showHelp }"
         style="height: calc(100vh - 76.15px); width: 100vw; position: relative; transition: all 0.5s ease;"></div>

    <div class="side-sheet {{ $showSideSheet ? 'active' : '' }}" style="max-width: 95% !important;max-height: 69vh !important;;overflow-y: scroll;">

        @if($selectedProject)
            <div class="d-lg-flex flex-row align-items-lg-center p-4">
                <a class="btn btn-circle btn-soft-primary closeSideSheet side-sheet-close" wire:click="closeSideSheet"><i class="uil uil-multiply"></i></a>
                <h6 class="mb-0">@lang('public.map.project_details')</h6>
            </div>

            <section class="wrapper bg-light">
                <div class="container-fluid px-md-4">
                    <div class="d-flex gap-2">
                        <div class="">
                            <figure class="card-img-top">
                                <img src="{{ App\Helpers\MediaHelper::getUrl(optional($selectedProject->getMainImages())->media_url ?? optional($selectedProject->projectMedia->first())->media_url) }}" class="rounded" alt="{{ $selectedProject->name }}" loading="lazy" decoding="async" fetchpriority="low" />
                            </figure>
                            <div class="post-header mb-5 mt-5">
                                <h4 class="post-title">
                                    <a href="{{ route('frontend.projects.single', ['slug' => $selectedProject->slug]) }}" class="link-dark ms-2">{{ $selectedProject->name }}</a>
                                    <span class="text-muted fs-15">@lang('public.map.ad_license') <span>{{ $selectedProject->AdLicense ?? '7200206576' }}</span></span>
                                </h4>
                                <p class="fe-20 mb-1 d-flex">
                                    <i class="uil uil-map-marker-alt text-muted h3 mb-0 ms-1"></i>
                                    <span>{{ $selectedProject->address }}</span>
                                </p>
                                <!-- Project Details -->
                                <div class="p-2 shadow mt-2 rounded" style="background: #f1f1f1 !important;">
                                    <ul class="post-meta">
                                        <li class="">
                                            <img src="{{ asset('frontend/img/icons/money-03.png') }}" class="dark-image" style="width: 20px;" alt="">
                                            <span class="me-1 text-dark fs-14">{{ $selectedProject->price_range }}</span>
                                        </li>
                                        <li class="">
                                            <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}" class="dark-image" style="width: 20px;" alt="">
                                            <span class="me-1 text-dark fs-14">{{ $selectedProject->bathroom_range }}</span>
                                        </li>
                                        <li class="">
                                            <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="">
                                            <span class="me-1 text-dark fs-14">{{ $selectedProject->bedroom_range }}</span>
                                        </li>
                                        <li class="">
                                            <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="">
                                            <span class="me-1 text-dark fs-14">{{ $selectedProject->space_range }}</span>
                                        </li>

                                    </ul>
                                </div>
                                <p class="mt-2">
                                    {!! Str::limit(strip_tags($selectedProject->description), 150) !!}
                                </p>
                            </div>

                            <div class="mb-4">
                                <a href="{{ route('frontend.projects.single', ['slug' => $selectedProject->slug]) }}" class="btn btn-primary btn-sm rounded w-100">@lang('public.map.view_details')</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

    </div>
</div>

@push('scripts')

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<!-- Leaflet MarkerCluster JavaScript -->
<script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

<script>
    const projects = @json($projects);
    const map = L.map('map').setView([24.7136, 46.6753], 6);

@if(app()->getLocale() === 'en')
    L.tileLayer('https://services.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, HERE, Garmin, USGS, Intermap, EPA, NPS',
        maxZoom: 19
    }).addTo(map);
    @else
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);
    @endif

    const markersCluster = L.markerClusterGroup();
    const bounds = L.latLngBounds();
    const markers = [];

    function addProjectToMap(project) {
        if (project.latitude && project.longitude) {
            const projectName = project.name.length > 20 ? project.name.substring(0, 20) + '...' : project.name;
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: `
                    <div class=\"project-tooltip pt-2\" style=\"position: relative;\">
                        <figcaption class=\"text-right px-2\" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}\">
                            <div class=\"d-flex align-content-center justify-content-between\">
                                <div>
                                    <h6 class=\"post-title project-title fs-14\" style=\"cursor: pointer;\" data-project-id=\"${project.id}\">
                                        ${projectName}
                                    </h6>
                                </div>
                            </div>
                        </figcaption>
                    </div>
                `,
                iconSize: [200, 50],
                iconAnchor: [100, 65],
            });

            const marker = L.marker([parseFloat(project.latitude), parseFloat(project.longitude)], {icon: customIcon})
            .on('click', () => {
                Livewire.dispatch('showProject', { projectId: project.id });
            });

            markersCluster.addLayer(marker);
            markers.push(marker);
            bounds.extend([project.latitude, project.longitude]);
        }
    }

    let saGeoJsonLayer = null;
    let saGeoJsonData = null;

    async function showSaHighlight() {
        if (saGeoJsonLayer) return;

        if (!saGeoJsonData) {
            try {
                const response = await fetch('https://raw.githubusercontent.com/mledoze/countries/master/data/sau.geo.json');
                saGeoJsonData = await response.json();
            } catch (error) {
                console.error('Error fetching SA GeoJSON:', error);
                return;
            }
        }

        saGeoJsonLayer = L.geoJSON(saGeoJsonData, {
            style: {
                color: '#122818',
                weight: 3,
                fillColor: '#122818',
                fillOpacity: 0.1,
                dashArray: '5, 5',
                lineJoin: 'round'
            }
        }).addTo(map);
    }

    function removeSaHighlight() {
        if (saGeoJsonLayer) {
            map.removeLayer(saGeoJsonLayer);
            saGeoJsonLayer = null;
        }
    }

    projects.forEach(addProjectToMap);
    map.addLayer(markersCluster);
    if (projects.length > 0) {
        map.fitBounds(bounds);
    }

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('projectsUpdated', (event) => {
            const newProjects = event[0].projects;
            markersCluster.clearLayers();
            markers.length = 0;
            const newBounds = L.latLngBounds();

            newProjects.forEach(project => {
                if (project.latitude && project.longitude) {
                    addProjectToMap(project);
                    newBounds.extend([project.latitude, project.longitude]);
                }
            });

            if (newProjects.length === 0) {
                map.setView([23.8859, 45.0792], 5);
                showSaHighlight();
                window.dispatchEvent(new CustomEvent('help-triggered'));
            } else if (newProjects.length > 0) {
                removeSaHighlight();
                map.fitBounds(newBounds);
            }
        });

        Livewire.on('side-sheet-updated', () => {
            // Side sheet logic
        });
    });
</script>
@endpush
