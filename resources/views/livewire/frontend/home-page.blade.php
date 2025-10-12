<div>

    @livewire('frontend.conponents.ProjectSlider')

    @livewire('frontend.conponents.projects-tab')

    <section class="wrapper bg-light">
        <div class="container py-14 py-md-10">
            <div class="row gx-lg-8 gx-xl-12 gy-11 px-xxl-5 text-center d-flex align-items-end">
                <div class="col-lg-4">
                    <div class="px-md-15 px-lg-3">
                        <figure class="mb-6"><img class="noise-container" src="{{ asset('frontend/img/PNG/tag.png') }}" style="width: 60px !important" srcset="{{ asset('frontend/img/PNG/tag.png') }} 2x" alt="Riva - ريفا" /></figure>
                        <h3>الاحترافية</h3>
                        {{-- <p class="mb-2">نسعى دائما الى خلق بيئة احترافية لنجعل مخرجتنا لها صدى طويل في القطاع العقاري.</p> --}}
                    </div>
                </div>
                    <div class="col-lg-4">
                    <div class="px-md-15 px-lg-3">
                        <figure class="mb-6"><img class="noise-container" src="{{ asset('frontend/img/PNG/lamba.png') }}" style="width: 60px !important" srcset="{{ asset('frontend/img/PNG/lamba.png') }} 2x" alt="Riva - ريفا" /></figure>
                        <h3>الإبداع</h3>
                        {{-- <p class="mb-2">نسعى دائما الى خلق بيئة احترافية لنجعل مخرجتنا لها صدى طويل في القطاع العقاري.</p> --}}
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="px-md-15 px-lg-3">
                        <figure class="mb-6"><img class="noise-container" src="{{ asset('frontend/img/PNG/Button(8).png') }}" style="width: 60px !important" srcset="{{ asset('frontend/img/PNG/Button(8).png') }} 2x" alt="Riva - ريفا" /></figure>
                        <h3>الالتزام</h3>
                        {{-- <p class="mb-2">نسعى دائما الى خلق بيئة احترافية لنجعل مخرجتنا لها صدى طويل في القطاع العقاري.</p> --}}
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- <livewire:states-grid /> --}}

    {{-- <section class="wrapper bg-light" dir="rtl">
        <div class="container-fluid py-10 py-md-16">
            <div class="row">
                <div class="col-xl-10 mx-auto">
                    <div class="card image-wrapper bg-full bg-image bg-overlay bg-overlay-400 noise-container" data-image-src="{{ asset('frontend/img/cta.png') }}">
                        <div class="card-body p-6 p-md-11 d-lg-flex flex-row align-items-lg-center justify-content-md-between text-center text-lg-end">
                            <div>
                                <h3 class="display-6 mb-6 mb-lg-0 ps-lg-10 ps-xl-5 ps-xxl-18 text-white">تواصل معنا الان</h3>
                                <p class="mb-0 text-white"> إذا تطابقت اهتماماتك مع أحد مشاريعنا سنقوم بالتواصل معك فورًا بإذن الله.</p>
                            </div>
                            <a href="{{ route('frontend.contactus') }}" class="btn btn-white rounded-pill mb-0 text-nowrap px-7">شاركنا آمالك لبيت أحلامك <i class="uil uil-arrow-up-left me-2"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}
    {{-- @livewire('frontend.conponents.client-logos') --}}

</div>
