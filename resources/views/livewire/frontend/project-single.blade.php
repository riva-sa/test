<div>
    @section('title', $project->name . ' ' . $project->projectType->name)
    @section('description', Str::limit(strip_tags($project->description), 150))
    {{-- @section('keywords', implode(',', $project->tags ?? [])) --}}
    @section('og:title',  $project->name . ' ' . $project->projectType->name)
    @section('og:description', Str::limit(strip_tags($project->description), 150))
    @section('og:image', asset('storage/' .$project->getMainImages()->media_url) )
    @section('twitter:title',  $project->name . ' ' . $project->projectType->name)
    @section('twitter:description', Str::limit(strip_tags($project->description), 150))
    @section('twitter:image', asset('storage/' .$project->getMainImages()->media_url) )

    @livewire('frontend.conponents.unit-popup')
    @livewire('frontend.conponents.unit-orderpopup')
    @php
        $pdfUrl = $project->getFirstPdfUrl();
    @endphp

    <livewire:frontend.conponents.pdf-viewer :pdf-url="$project->getFirstPdfUrl()" />

    <section class="bg-white">

        {{-- <a href="https://www.google.com/maps/dir/?api=1&destination={{ $project->latitude }},{{ $project->longitude }}"
            target="_blank"
            class="btn btn-outline-primary">
            <i class="uil uil-directions me-2"></i>
            الاتجاهات
        </a> --}}

        <div class="container-fluid py-10 py-md-8 pb-md-15 px-md-9">
            <div class="row d-flex align-items-start gy-10">

                <div class="card col-lg-3 position-lg-sticky p-4 " style="top: 5rem;" wire:ignore>
                    <figcaption class="text-right" dir="rtl">
                        <div class="d-flex align-content-start justify-content-between w-100">
                            <div>
                                <p class="small text-muted mb-1">المطور</p>
                                <h2 class="post-title h3 mt-1 mb-3">
                                    {{ $project->developer->name }}
                                </h2>
                            </div>
                            <div>
                                <img src="{{ asset('storage/'. $project->developer->logo) }}" style="height: auto !important;max-width:100px" alt="Riva - ريفا">
                            </div>
                        </div>
                    </figcaption>
                    <div class="p-2 py-3 shadow mt-2 rounded" dir="rtl" style="background: #f1f1f19e !important;">
                        <ul class="post-meta row mb-2">
                            <li class="col-md-6">
                                <img src="{{ asset('frontend/img/icons/pan-03.png') }}" class="" style="width: 20px;" alt="Riva - ريفا">
                                <span class="me-1 text-dark fs-14">{{ $project->kitchen_range }}</span>
                            </li>
                            <li class="col-md-6">
                                <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                <span class="me-1 text-dark fs-14">{{ $project->bathroom_range }}</span>
                            </li>
                        </ul>
                        <ul class="post-meta row">
                            <li class="col-md-6">
                                <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                <span class="me-1 text-dark fs-14">{{ $project->bedroom_range }}</span>
                            </li>
                            <li class="col-md-6">
                                <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                <span class="me-1 text-dark fs-14">{{ $project->space_range }}</span>
                            </li>

                        </ul>
                    </div>
                    <div class="p-2 py-3 shadow mt-2 rounded" dir="rtl" style="background: #f1f1f19e !important;">
                        <ul class="post-meta row mb-4">
                            <li class="col-md-6">
                                <p class="mb-1 text-gray-800">رخصة الاعلان</p>
                                <span class="text-dark fs-14">{{ $project->AdLicense }}</span>
                            </li>
                            <li class="col-md-6">
                                <p class="mb-1 text-gray-800">تاريخ النشر</p>
                                <span class="text-dark fs-14">{{ $project->created_at->format('y-m-d') }}</span>
                            </li>
                        </ul>
                        <ul class="post-meta row">
                            <li class="col-md-12">
                                <p class="mb-1 text-gray-800">السعر</p>
                                <span class="text-success fs-14">{{ $project->price_range }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="mt-4 d-flex gap-2 actions">
                        @if($pdfUrl)
                            <button wire:click="dispatch('showPdf', ['{{ $pdfUrl }}'])" class="btn btn-soft-ash btn-sm btn-icon-end rounded w-50">
                                <i class="uil uil-eye me-1"></i> عرض ملف المشروع
                            </button>
                        @endif

                        <button
                            wire:click="showOrderPopup({{ $project->id }})"
                            wire:loading.attr="disabled"
                            wire:target="showOrderPopup"
                            class="btn btn-primary btn-sm btn-icon-end rounded w-50 @if($pdfUrl) w-50 @else w-100 @endif">

                            <span wire:loading.remove wire:target="showOrderPopup">
                                <i class="uil uil-fire me-1"></i> تسجيل اهتمام
                            </span>

                            <span wire:loading wire:target="showOrderPopup">
                                <i class="uil uil-spinner-alt fa-spin me-1"></i> جاري التحميل...
                            </span>

                        </button>
                    </div>
                </div>

                <!-- /column -->
                <div class="col-lg-9 ms-auto">
                    <div class="card mb-6" style="box-shadow:unset" wire:ignore>
                        @if($pdfUrl)
                        <a href="{{ $pdfUrl }}" download class="noise-container text-right " dir="rtl" style="position: absolute;top: 10px;left: 66px;z-index:100">
                            <span  class="badge badge-lg text-white d-flex align-content-center align-items-center">
                                حمل الملف التعريفي
                                <i class="uil uil-file-download-alt fs-25 me-2"></i>
                            </span>
                        </a>
                        @endif
                        <a href="https://www.google.com/maps?q={{ $project->latitude }},{{ $project->longitude }}" target="_blank" class="noise-container text-right heroTop" dir="rtl" style="position: absolute;top: 10px;left: 10px;z-index:100">
                            <span class="badge badge-lg text-white d-flex align-content-center align-items-center">
                                <i class="uil uil-map-pin fs-25"></i>
                            </span>
                        </a>

                        <div class="swiper-container dots-over" data-margin="10" data-dots="false" data-nav="true" data-thumbs="false">
                            <div class="swiper rounded">
                              <div class="swiper-wrapper rounded">

                                @foreach ($project->projectMedia->where('media_type', 'image') as $media)

                                    <div class="swiper-slide">
                                        <a class="item-link" href="{{ asset('storage/' .$media->media_url) }}" data-glightbox data-gallery="product-group">
                                            <figure class="rounded">
                                                <img src="{{ asset('storage/' .$media->media_url) }}" class="rounded" style="max-height:550px" srcset="{{ asset('storage/' .$media->media_url) }} 2x" alt="Riva - ريفا" />
                                            </figure>

                                        </a>
                                    </div>

                                @endforeach
                              </div>
                              <!--/.swiper-wrapper -->
                            </div>
                        </div>

                    </div>
                    <!-- /.card -->
                    <div class="card mb-6">
                        <div class="card-body d-flex flex-row" dir="rtl">
                            <div class="post-header">
                                <h4 class="post-title">
                                    <a href="{{ route('frontend.projects.single', $project->slug) }}" class="link-dark ms-2">{{ $project->name }}</a>
                                    <span class="badge bg-pale-ash text-dark rounded-pill">{{ $project->projectType->name }}</span>
                                </h4>

                                <a class="fe-20 mb-1 d-flex" style="width: fit-content;" href="https://www.google.com/maps?q={{ $project->latitude }},{{ $project->longitude }}" target="_blank">
                                    <i class="uil uil-map-marker-alt text-muted h3 mb-0 ms-1"></i>
                                    <span>{{ $project->address }}</span>
                                </a>

                                <p class="mt-2 mb-0">
                                    {!! $project->description !!}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-6">
                        <div class="card-body p-5" dir="rtl">

                            <ul class="nav nav-tabs nav-pills tab-box w-fit" dir="rtl">
                                <li class="nav-item">
                                    <a wire:click="$set('case', 'all')"
                                       class="nav-link px-4 cursor-pointer {{ $case === 'all' ? 'active noise-container bg-primary text-white' : '' }}">
                                       <i class="uil uil-apps ms-2 fs-15"></i> الكل
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a wire:click="$set('case', '0')"
                                       class="nav-link px-4 cursor-pointer {{ $case === '0' ? 'active noise-container bg-success text-white' : '' }}">
                                       <i class="uil uil-map-pin ms-2 fs-15"></i> متاح
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a wire:click="$set('case', '1')"
                                       class="nav-link px-4 cursor-pointer {{ $case === '1' ? 'active noise-container bg-warning text-dark' : '' }}">
                                       <i class="uil uil-clock ms-2 fs-15"></i> محجوز
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a wire:click="$set('case', '2')"
                                       class="nav-link px-4 cursor-pointer {{ $case === '2' ? 'active noise-container bg-danger text-white' : '' }}">
                                       <i class="uil uil-bill ms-2 fs-15"></i> تم البيع
                                    </a>
                                </li>
                            </ul>


                            {{-- Tab Contents --}}
                            <div class="mt-5">
                                {{-- Units Grid --}}
                                <div class="row">
                                    @forelse($units as $unit)
                                        <div class="col-md-6 col-lg-4" wire:key="{{$unit->id}}" style="cursor: pointer">

                                            <article class="post rounded">
                                                <figure class="rounded-top position-relative" wire:click="showUnitDetails({{ $unit->id }})">
                                                    @if ($unit->floor_plan)
                                                    <img src="{{ asset('storage/' .$unit->floor_plan ) }}"
                                                    style="max-height: 200px"
                                                    alt="{{ $unit->title }}" />
                                                    @else
                                                    <img src="https://placehold.co/700x400"
                                                    style="max-height: 200px"
                                                    alt="{{ $unit->title }}" />
                                                    @endif

                                                    @if($unit->case == 2)
                                                        <div class="position-absolute top-0 rounded start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                            style="background-color: rgba(0,0,0,0.5);">
                                                            <span class="badge bg-danger">تم البيع</span>
                                                        </div>
                                                    @elseif($unit->case == 1)
                                                        <div class="position-absolute top-0 rounded start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                            style="background-color: rgba(0,0,0,0.5);">
                                                            <span class="badge bg-warning">محجوزة</span>
                                                        </div>
                                                    @endif
                                                </figure>

                                                <div class="post-header project-data-card rounded-bottom bg-white">
                                                    <div class="d-flex align-content-start justify-content-between w-100">
                                                        <h2 class="post-title h6 mt-0 mb-0" wire:click="showUnitDetails({{ $unit->id }})">
                                                            {{ $unit->title }} <span class="badge bg-pale-ash text-dark rounded-pill">عرض بيانات الوحدة</span>
                                                            <div class="spinner-border spinner-border-sm me-1" wire:loading wire:target="showUnitDetails({{ $unit->id }})" role="status"></div>
                                                        </h2>
                                                        <div>
                                                            <span class="badge bg-pale-ash text-dark rounded-pill">{{ $unit->unit_type }}</span>
                                                        </div>
                                                    </div>
                                                    @if ($unit->show_price)
                                                    <ul class="post-meta mb-3" wire:click="showUnitDetails({{ $unit->id }})">
                                                        <li class="post-date">
                                                            <span class="fs-15 text-success">{{ number_format($unit->unit_price) . ' ريال' }}</span>
                                                        </li>
                                                    </ul>
                                                    @endif
                                                    <ul class="post-meta mb-0" wire:click="showUnitDetails({{ $unit->id }})">
                                                        <li class="post-date">
                                                            <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                            <span class="me-1 fs-15 text-gray-800">{{ $unit->bathrooms }}</span>
                                                        </li>
                                                        <li class="post-author">
                                                            <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                            <span class="me-1 fs-15 text-gray-800">{{ $unit->beadrooms }}</span>
                                                        </li>
                                                        <li class="post-comments">
                                                            <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                            <span class="me-1 fs-15 text-gray-800">{{ $unit->unit_area . ' م²' }}</span>
                                                        </li>
                                                    </ul>
                                                </div>

                                            </article>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center m-auto">
                                            <img src="{{ asset('frontend/img/EmptyInbox.png') }}" alt="Riva - ريفا">
                                            <p class="text-main fs-bold mb-1">تعذر وجود نتائج!</p>
                                            <p class="text-muted fs-15">لم نتمكن من العثور على أي وحدات</p>
                                        </div>
                                    @endforelse
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card mb-6" wire:ignore>
                        <div class="card-body p-5" dir="rtl">
                            <div class="row">
                                <div class="col-md-6 border-start border-gray mb-md-0 mb-4">
                                    <div class="text-right">
                                        <h2 class="text-uppercase fs-20 mb-5">المميزات</h2>
                                    </div>
                                    <div class="row">
                                        @foreach ($project->features as $features)
                                            <div class="d-flex flex-row col-md-4 mb-3" style="flex-wrap: wrap;" wire:key="{{$features->id}}">

                                                <div>
                                                @if ($features->icon)
                                                    <img src="{{ asset('storage/' . $features->icon) }}" class="svg-inject icon-svg icon-svg-sm text-purple" alt="Riva - ريفا" />

                                                @else
                                                    <img src="https://placehold.co/30x30" alt="" />
                                                @endif
                                                </div>
                                                <div class="me-2">
                                                    <h4 class="mb-0 fs-15">{{$features->name}}</h4>
                                                    <p class="mb-0 fs-14">{{ $features->description }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="text-right">
                                        <h2 class="text-uppercase fs-20 mb-5">الضمانات</h2>
                                    </div>
                                    <div class="row">
                                        @foreach ($project->guarantees as $guarante)
                                            <div class="d-flex flex-row col-md-4 mb-3" wire:key="{{$guarante->id}}">
                                                <div>
                                                    @if ($features->icon)
                                                        <img src="{{ asset('storage/' . $guarante->icon) }}" class="svg-inject icon-svg icon-svg-sm text-purple" alt="Riva - ريفا" />
                                                    @else
                                                        <img src="https://placehold.co/30x30" alt="" />
                                                    @endif
                                                </div>
                                                <div class="me-2">
                                                    <h4 class="mb-0 fs-15">{{$guarante->name}}</h4>
                                                    <p class="mb-0 fs-14">{{ $guarante->description }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card mb-6" wire:ignore>
                        <div class="card-body p-5" dir="rtl">
                            <div class="text-right">
                                <h2 class="text-uppercase fs-20 mb-5">المعالم القريبة</h2>
                            </div>
                            <div class="row">
                                @foreach ($project->landmarks as $landmark)
                                    <div class="d-flex flex-row col-md-4 mb-3" style="flex-wrap: wrap;" wire:key="{{$landmark->id}}">
                                        <div class="icon-card">
                                            <i class="uil uil-map-pin fs-25 text-dark"></i>
                                        </div>
                                        <div class="me-2">
                                            <h4 class="mb-0 fs-15">{{$landmark->name}}</h4>
                                            <p class="mb-0 fs-14">{{ $landmark->description }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($project->virtualTour)

                        <div class="card mb-6 rounded">
                            <div class="card-body p-0 rounded" dir="rtl">
                                <livewire:frontend.conponents.virtual-tour-viewer :project="$project" />
                            </div>
                        </div>

                    @endif
                </div>

            </div>
        </div>

    </section>


</div>
