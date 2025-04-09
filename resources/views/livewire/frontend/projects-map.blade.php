@push('styles')

<!-- Leaflet MarkerCluster CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />

<style>
    /* Floating Filter Styles */
    .floating-filter {
        position: fixed;
        top: 85px;
        left: 60px;
        width: fit-content;
        z-index: 1000;
    }

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
        direction: rtl;
        top: 86.15px;
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
</style>

@endpush

<div>
     <!-- Floating Filter -->
     {{-- <div class="floating-filter">

        <div class="filter-body d-flex">

            <!-- Project Types -->
            <div class="form-select-wrapper">
                <select class="form-select" wire:model.live="selected_projectTypes">
                    <option selected>اختر نوع الوحدة</option>
                    @foreach($projectTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-select-wrapper ms-3">
                <select class="form-select" wire:model.live="selected_developer">
                    @foreach($developers as $developer)
                        <option value="{{ $developer->id }}">
                            {{ $developer->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

    </div> --}}

    <!-- Map Container -->
    <div wire:ignore id="map" style="height: calc(100vh - 76.15px); width: 100%;"></div>

    <div class="side-sheet {{ $showSideSheet ? 'active' : '' }}" style="max-width: 96% !important;">

        @if($selectedProject)
            <div class="d-lg-flex flex-row align-items-lg-center p-4">
                <a class="btn btn-circle btn-soft-primary closeSideSheet side-sheet-close" wire:click="closeSideSheet"><i class="uil uil-multiply"></i></a>
                <h6 class="mb-0">تفاصيل المشروع</h6>
            </div>

            <section class="wrapper bg-light">
                <div class="container-fluid px-md-4">
                    <div class="d-flex gap-2">
                        <div class="">
                            <figure class="card-img-top">
                                <img src="@if($selectedProject->getMainImages() !== null ) {{ App\Helpers\MediaHelper::getUrl($selectedProject->getMainImages()->media_url) }} @else {{ App\Helpers\MediaHelper::getUrl($selectedProject->projectMedia()->first()->media_url) }} @endif" class="rounded" alt="" />
                            </figure>
                            <div class="post-header mb-5 mt-5">
                                <h4 class="post-title">
                                    <a href="{{ route('frontend.projects.single', $selectedProject->slug) }}" class="link-dark ms-2">{{ $selectedProject->name }}</a>
                                    <span class="text-muted fs-15">رخصة الاعلان : <span>{{ $selectedProject->AdLicense ?? '7200206576' }}</span></span>
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
                                    {{-- {!! strip_tags($selectedProject->description) !!} --}}
                                    {!! Str::limit(strip_tags($selectedProject->description), 150) !!}
                                </p>
                            </div>

                            <div class="mb-4">
                                <a href="{{ route('frontend.projects.single', $selectedProject->slug) }}" class="btn btn-primary btn-sm rounded w-100">عرض تفاصيل المشروع</a>
                                {{-- <a class="btn btn-soft-primary btn-icon btn-sm btn-icon-start rounded-pill">تسجيل اهتمام</a> --}}
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
    // تمرير بيانات المشاريع من PHP إلى JavaScript
    const projects = @json($projects);

    // Initialize map
    const map = L.map('map').setView([24.7136, 46.6753], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Create a MarkerClusterGroup to group markers
    const markersCluster = L.markerClusterGroup();

    // Define bounds for zoom
    const bounds = L.latLngBounds(); // Initialize empty bounds

    // Add markers for each project
    const markers = [];
    projects.forEach(project => {

        if (project.latitude && project.longitude) {
            try {

                const projectName = project.name.length > 20 ? project.name.substring(0, 20) + '...' : project.name;

                const Dmarker = L.circleMarker([
                    parseFloat(project.latitude),
                    parseFloat(project.longitude)
                ], {
                    radius: 5, // حجم النقطة
                    color: '#122818', // لون الحدود
                    fillColor: '#122818', // لون التعبئة
                    fillOpacity: 1 // اجعل النقطة معبأة تمامًا
                });

                // Create custom marker with the same style as popupContent
                const customIcon = L.divIcon({
                    className: 'custom-marker', // Add a custom class for styling
                    html: `
                        <div class="project-tooltip pt-2" style="position: relative;">
                            <figcaption class="text-right px-2" dir="rtl">
                                <div class="d-flex align-content-center justify-content-between">
                                    <div>
                                        <h6 class="post-title project-title fs-14" style="cursor: pointer;" data-project-id="${project.id}">
                                            ${projectName} - ${project.developer.name.substring(0, 10)}
                                        </h6>
                                    </div>

                                </div>
                            </figcaption>
                        </div>
                    `,
                    iconSize: [200, 50], // Set size based on your design
                    iconAnchor: [100, 65], // Position the icon relative to the point
                });


                // <div>
                //     <img src="/storage/${project.developer?.logo}" class="mb-0" style="width: 60px !important;max-height:50px">
                // </div>

                // Create the marker using the custom icon
                const marker = L.marker([parseFloat(project.latitude), parseFloat(project.longitude)], {icon: customIcon})
                .on('click', function() {
                    window.livewire.emit('selectProject', project.id);
                });

                markersCluster.addLayer(marker);  // Add marker to the cluster


                // Extend bounds to include this marker's position
                bounds.extend([project.latitude, project.longitude]);

                // Add marker to the map
                marker.addTo(map);
                Dmarker.addTo(map);

                // Attach click event to the custom marker
                map.getContainer().addEventListener('click', (event) => {
                    if (event.target.classList.contains('project-title')) {
                        const projectId = event.target.getAttribute('data-project-id');
                        Livewire.dispatch('showProject', { projectId: projectId });
                    }
                });

                // Add click event to marker using Livewire v3 syntax
                marker.on('click', () => {
                    Livewire.dispatch('showProject', { projectId: project.id });
                });

                // استخدم bindTooltip بدلاً من bindPopup لعرض المحتوى دائمًا
                marker.bindTooltip(popupContent, {
                    className: 'custom-popup glass-white-card',
                    direction: 'top',
                    permanent: false,
                    offset: [0, -15],
                    maxWidth: 300,
                    minWidth: 280
                })
                .addTo(map);

            } catch (error) {
                console.error('Error adding marker for project:', project.id, error);
            }
        }
    });

    // Fit bounds if we have markers
    if (markers.length > 0) {
        const group = L.featureGroup(markers);
        map.fitBounds(group.getBounds());
    }

    // Listen for side-sheet-updated event using Livewire v3 syntax
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('side-sheet-updated', () => {
            console.log('Side sheet state updated');
        });
    });

    // map.addLayer(markersCluster);

    // Adjust the map view to fit all markers within the bounds
    if (projects.length > 0) {
        map.fitBounds(bounds);
    }
</script>
@endpush
