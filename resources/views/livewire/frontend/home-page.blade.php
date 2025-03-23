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
                        <p class="mb-2">نسعى دائما الى خلق بيئة احترافية لنجعل مخرجتنا لها صدى طويل في القطاع العقاري.</p>
                    </div>
                </div>
                    <div class="col-lg-4">
                    <div class="px-md-15 px-lg-3">
                        <figure class="mb-6"><img class="noise-container" src="{{ asset('frontend/img/PNG/lamba.png') }}" style="width: 60px !important" srcset="{{ asset('frontend/img/PNG/lamba.png') }} 2x" alt="Riva - ريفا" /></figure>
                        <h3>الإبداع</h3>
                        <p class="mb-2">نسعى دائما الى خلق بيئة احترافية لنجعل مخرجتنا لها صدى طويل في القطاع العقاري.</p>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="px-md-15 px-lg-3">
                        <figure class="mb-6"><img class="noise-container" src="{{ asset('frontend/img/PNG/Button(8).png') }}" style="width: 60px !important" srcset="{{ asset('frontend/img/PNG/Button(8).png') }} 2x" alt="Riva - ريفا" /></figure>
                        <h3>الالتزام</h3>
                        <p class="mb-2">نسعى دائما الى خلق بيئة احترافية لنجعل مخرجتنا لها صدى طويل في القطاع العقاري.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- <livewire:states-grid /> --}}
{{--
    <section class="wrapper bg-light" dir="rtl">
        <div class="container py-10 py-md-10">
            <h2 class="display-4 mb-3 text-center">الاسئلة الشائعة</h2>
            <p class="lead text-center mb-10 px-md-16 px-lg-0"></p>
            <div class="row">
                <div class="col-lg-6 mb-0">
                    <div id="accordion-1" class="accordion-wrapper">
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-1-1">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-1-1" aria-expanded="false" aria-controls="accordion-collapse-1-1">Can I cancel my subscription?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-1-1" class="collapse" aria-labelledby="accordion-heading-1-1" data-bs-target="#accordion-1">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-1-2">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-1-2" aria-expanded="false" aria-controls="accordion-collapse-1-2">Which payment methods do you accept?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-1-2" class="collapse" aria-labelledby="accordion-heading-1-2" data-bs-target="#accordion-1">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-1-3">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-1-3" aria-expanded="false" aria-controls="accordion-collapse-1-3">How can I manage my Account?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-1-3" class="collapse" aria-labelledby="accordion-heading-1-3" data-bs-target="#accordion-1">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-1-4">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-1-4" aria-expanded="false" aria-controls="accordion-collapse-1-4">Is my credit card information secure?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-1-4" class="collapse" aria-labelledby="accordion-heading-1-4" data-bs-target="#accordion-1">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!--/column -->
                <div class="col-lg-6">
                    <div id="accordion-2" class="accordion-wrapper">
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-2-1">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-2-1" aria-expanded="false" aria-controls="accordion-collapse-2-1">How do I get my subscription receipt?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-2-1" class="collapse" aria-labelledby="accordion-heading-2-1" data-bs-target="#accordion-2">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-2-2">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-2-2" aria-expanded="false" aria-controls="accordion-collapse-2-2">Are there any discounts for people in need?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-2-2" class="collapse" aria-labelledby="accordion-heading-2-2" data-bs-target="#accordion-2">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-2-3">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-2-3" aria-expanded="false" aria-controls="accordion-collapse-2-3">Do you offer a free trial edit?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-2-3" class="collapse" aria-labelledby="accordion-heading-2-3" data-bs-target="#accordion-2">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                        <div class="card accordion-item">
                        <div class="card-header" id="accordion-heading-2-4">
                            <button class="collapsed" data-bs-toggle="collapse" data-bs-target="#accordion-collapse-2-4" aria-expanded="false" aria-controls="accordion-collapse-2-4">How do I reset my Account password?</button>
                        </div>
                        <!-- /.card-header -->
                        <div id="accordion-collapse-2-4" class="collapse" aria-labelledby="accordion-heading-2-4" data-bs-target="#accordion-2">
                            <div class="card-body">
                            <p>Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Cras mattis consectetur purus sit amet fermentum. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec sed odio dui. Cras justo odio, dapibus ac facilisis.</p>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.collapse -->
                        </div>
                        <!-- /.card -->
                    </div>
                </div>
                <!--/column -->
            </div>
        </div>
    </section>
    <!-- /section -->
 --}}


    <section class="wrapper bg-light" dir="rtl">
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
    </section>
    @livewire('frontend.conponents.client-logos')

</div>
