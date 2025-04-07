<div class="unit-sheet">
    <div class="side-sheet border {{ $showSideSheet ? 'active' : '' }}" style="max-width: 96% !important;max-height: 100vh;overflow-y: scroll;">
        @if($selectedUnit)
            <div class="d-lg-flex flex-row align-items-lg-center p-4">
                <a class="btn btn-circle btn-soft-primary closeSideSheet side-sheet-close" wire:click="closeSideSheet"><i class="uil uil-multiply"></i></a>
                <h6 class="mb-0"> تفاصيل الوحدة</h6>
            </div>

            <section class="wrapper bg-light">
                <div class="container-fluid px-md-4">
                    <div class="d-flex gap-2">
                        <div class="w-100">

                            <!-- In your livewire/frontend/conponents/unit-popup.blade.php file -->

                            @if($currentStep == 1)
                            <!-- Unit Images Swiper Carousel with Lightbox -->
                            <div wire:ignore>
                                <div class="swiper-container unit-images-swiper mb-3" data-margin="0" data-nav="true" data-dots="true" data-items-xl="1" data-items-md="1" data-items-xs="1">
                                    <div class="swiper">
                                        <div class="swiper-wrapper">
                                            @foreach($unitImages as $index => $image)
                                            <div class="swiper-slide">
                                                <a href="{{ asset('storage/' . $image['url']) }}" class="unit-lightbox-item"
                                                data-glightbox="type: image; title: {{ $selectedUnit->title }}; description: {{ $selectedUnit->unit_type }};"
                                                data-gallery="unit-gallery">
                                                    <figure class="card-img-top rounded" style="background-image: url('{{ asset('storage/' . $image['url']) }}'); background-size: cover; height: 240px;"></figure>
                                                </a>
                                            </div>
                                            @endforeach
                                        </div>
                                        <!-- Add Navigation -->
                                        <div class="swiper-navigation d-flex">
                                            <div class="swiper-button-prev"></div>
                                            <div class="swiper-button-next"></div>
                                        </div>
                                        <!-- Add Pagination -->
                                        <div class="swiper-pagination"></div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- @if($currentStep == 1)
                                <div wire:ignore>
                                    <figure class="card-img-top rounded" style="background-image: url('@if($selectedUnit->image) {{ asset('storage/' .$selectedUnit->image ) }} @else (){{ asset('storage/' .$selectedUnit->project->projectMedia()->first()->media_url ) }} @endif');background-size:cover;height: 240px;">
                                    </figure>
                                </div>
                            @endif --}}
                            <!-- Step 1: Show Unit Data -->
                            @if($currentStep == 1)

                                <div class="post-header mb-5 mt-5">
                                    <h4 class="post-title"> {{ $selectedUnit->title }} <span class="badge bg-pale-ash text-dark rounded-pill">{{ $selectedUnit->unit_type }}</span> </h4>

                                    <!-- Unit Details -->
                                    <div class="p-2 shadow mt-2 rounded" style="background: #f1f1f1da !important;">
                                        <ul class="post-meta mb-0">
                                            <li class="post-date">
                                                <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->bathrooms }}</span>
                                            </li>
                                            <li class="post-author">
                                                <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->beadrooms }}</span>
                                            </li>
                                            <li class="post-comments">
                                                <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->unit_area . ' م²' }}</span>
                                            </li>
                                            <li class="post-comments">
                                                {{-- <img src="{{ asset('frontend/img/icons/pan-03(1).png') }}" class="dark-image" style="width: 17px;" alt="Riva - ريفا"> --}}
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="19" height="19" color="#808080" fill="none">
                                                    <path d="M21 17C18.2386 17 16 14.7614 16 12C16 9.23858 18.2386 7 21 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                    <path d="M21 21C16.0294 21 12 16.9706 12 12C12 7.02944 16.0294 3 21 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                                    <path d="M6 3L6 8M6 21L6 11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M3.5 8H8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M9 3L9 7.35224C9 12.216 3 12.2159 3 7.35207L3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->kitchen}}</span>
                                            </li>
                                            <li class="post-comments">
                                                <span class="text-dark fs-15">الدور :</span>
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->floor}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mt-2">
                                        {!! Str::limit(strip_tags($selectedUnit->description), 150) !!}
                                    </p>
                                    <div class="mb-3">
                                        <div class="text-right">
                                            <h2 class="text-uppercase fs-20 mb-3">المميزات</h2>
                                        </div>
                                        <div class="d-flex gap-4">
                                            @foreach ($selectedUnit->features as $features)
                                                <div class="d-flex flex-row" style="flex-wrap: wrap;" wire:key="{{$features->id}}">
                                                    <div>
                                                        <img src="{{ asset('storage/' . $features->icon) }}" style="width:50px" class="text-purple" alt="Riva - ريفا" />
                                                    </div>
                                                    <div class="me-2">
                                                        <h4 class="mb-0 fs-15">{{$features->name}}</h4>
                                                        <p class="mb-0 fs-14">{{ $features->description }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <hr style="margin: 10px;">
                                @if ($selectedUnit->show_price)
                                    <h3 class="h3 mb-3 text-success px-2">
                                        <span class="text-muted small fs-15">السعر</span>
                                        {{ number_format($selectedUnit->unit_price) . ' ريال' }}
                                    </h3>
                                @endif
                                @if ($selectedUnit->case == 0)
                                    <div class="mb-4">
                                        <a wire:click="goToFormStep" class="btn btn-primary btn-icon btn-sm btn-icon-start rounded w-100">تسجيل اهتمام بالوحدة <i class="uil uil-fire"></i></a>
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <a class="btn btn-soft-orange btn-icon btn-sm btn-icon-start rounded w-100 disabled" disabled>
                                            @if ($selectedUnit->case == 0)
                                                الوحدة محجوزة
                                            @else
                                                الوحدة مباعة
                                            @endif
                                        </a>
                                    </div>
                                @endif

                            @endif

                            <!-- Step 2: Show Interest Form -->
                            @if($currentStep == 2)
                                <div class="post-header mb-5 mt-5">
                                    <h4 class="post-title">تقديم اهتمام لشراء الوحدة</h4>
                                    <form wire:submit.prevent="submitInterest">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label text-gray-900">اسمك</label>
                                                <input type="text"
                                                       id="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       wire:model="name">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label text-gray-900">بريد الكتروني</label>
                                                <input type="email"
                                                       id="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       wire:model="email">
                                                @error('email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label text-gray-900">رقم الهاتف</label>
                                            <input type="text"
                                                   id="phone"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   wire:model="phone">
                                            @error('phone')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="row mb-6">
                                            <div class="col-12 mb-3">
                                                <label class="form-label text-gray-900 mb-3">طريقة الشراء</label>
                                                <div class="custom-radio-group">
                                                    @foreach($purchaseTypes as $value => $label)
                                                        <div class="custom-radio-item">
                                                            <input type="radio"
                                                                   name="purchaseType"
                                                                   id="purchaseType_{{ $value }}"
                                                                   value="{{ $value }}"
                                                                   wire:model.live="purchaseType"
                                                                   class="custom-radio-input">
                                                            <label for="purchaseType_{{ $value }}" class="custom-radio-label">
                                                                <span class="radio-icon"></span>
                                                                <span class="radio-text">{{ $label }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('purchaseType')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            @if($purchaseType === 'bank')
                                                <div class="col-12 mb-3">
                                                    <label for="support_type" class="form-label text-gray-900">نوع الدعم</label>
                                                    <select wire:model="support_type" class="form-select" id="support_type">
                                                        <option value="">اختر نوع الدعم</option>
                                                        <option value="مدعوم">مدعوم</option>
                                                        <option value="غير مدعوم">غير مدعوم</option>
                                                    </select>
                                                    @error('support_type')
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            @endif

                                            <div class="col-12">
                                                <label class="form-label text-gray-900 mb-3">الغرض من الشراء</label>
                                                <div class="custom-radio-group">
                                                    @foreach($purchasePurposes as $value => $label)
                                                        <div class="custom-radio-item">
                                                            <input type="radio"
                                                                   name="purchasePurpose"
                                                                   id="purchasePurpose_{{ $value }}"
                                                                   value="{{ $value }}"
                                                                   wire:model="purchasePurpose"
                                                                   class="custom-radio-input">
                                                            <label for="purchasePurpose_{{ $value }}" class="custom-radio-label">
                                                                <span class="radio-icon"></span>
                                                                <span class="radio-text">{{ $label }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('purchasePurpose')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="mb-5">
                                            <label for="message" class="form-label text-gray-900">رسالة</label>
                                            <textarea id="message"
                                                      class="form-control @error('message') is-invalid @enderror"
                                                      wire:model="message"></textarea>
                                            @error('message')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div> --}}

                                        <button type="submit" class="btn btn-primary btn-icon btn-sm btn-icon-start rounded w-100" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="submitInterest">
                                                إرسال
                                            </span>
                                            <div wire:loading wire:target="submitInterest">
                                                <div class="spinner-border spinner-border-sm me-4 mb-2" role="status">
                                                    <span class="visually-hidden">جاري الإرسال...</span>
                                                </div>
                                                جاري الإرسال...
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif

    </div>

    <div class="side-sheet-overlay {{ $showSideSheet ? 'active' : '' }}" wire:click="closeSideSheet"></div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

</div>


@push('scripts')
<script>
    // Initialize Swiper and GLightbox
    function initUnitGallery() {
        // Initialize Swiper
        const swiperElements = document.querySelectorAll('.unit-images-swiper');

        swiperElements.forEach(element => {
            const container = element.querySelector('.swiper');

            // Destroy existing Swiper instance if it exists
            if (container && container.swiper) {
                container.swiper.destroy();
            }

            // Create new Swiper instance
            if (container) {
                const margin = parseInt(element.dataset.margin || 0);
                const nav = element.dataset.nav === 'true';
                const dots = element.dataset.dots === 'true';
                const itemsXl = parseInt(element.dataset.itemsXl || 1);
                const itemsMd = parseInt(element.dataset.itemsMd || 1);
                const itemsXs = parseInt(element.dataset.itemsXs || 1);

                new Swiper(container, {
                    slidesPerView: itemsXs,
                    spaceBetween: margin,
                    navigation: nav ? {
                        nextEl: element.querySelector('.swiper-button-next'),
                        prevEl: element.querySelector('.swiper-button-prev'),
                    } : false,
                    pagination: dots ? {
                        el: element.querySelector('.swiper-pagination'),
                        clickable: true,
                    } : false,
                    breakpoints: {
                        768: {
                            slidesPerView: itemsMd,
                        },
                        1200: {
                            slidesPerView: itemsXl,
                        },
                    },
                    autoplay: {
                        delay: 5000,
                    },
                });
            }
        });

        // Initialize GLightbox
        if (document.querySelectorAll('.unit-lightbox-item').length > 0) {
            const lightbox = GLightbox({
                selector: '.unit-lightbox-item',
                touchNavigation: true,
                loop: true,
                autoplayVideos: false
            });
        }
    }

    // Run on page load
    document.addEventListener('DOMContentLoaded', initUnitGallery);

    // Run when Livewire updates the DOM
    document.addEventListener('livewire:initialized', function () {
        Livewire.hook('element.updated', (el) => {
            if (el.querySelector('.unit-images-swiper')) {
                initUnitGallery();
            }
        });

        // Listen for sideSheetOpened event
        Livewire.on('sideSheetOpened', () => {
            // Small delay to ensure DOM is ready
            setTimeout(initUnitGallery, 100);
        });
    });
</script>
@endpush

@push('styles')
    <style>
        .unit-images-swiper {
            position: relative;
        }

        .unit-images-swiper .swiper-button-next,
        .unit-images-swiper .swiper-button-prev {
            color: #fff;
            background: rgba(0,0,0,0.3);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .unit-images-swiper .swiper-button-next:after,
        .unit-images-swiper .swiper-button-prev:after {
            font-size: 18px;
        }

        .unit-images-swiper .swiper-pagination {
            bottom: 10px;
        }

        .unit-images-swiper .swiper-pagination-bullet {
            background: #fff;
            opacity: 0.7;
        }

        .unit-images-swiper .swiper-pagination-bullet-active {
            opacity: 1;
        }
    </style>
@endpush
