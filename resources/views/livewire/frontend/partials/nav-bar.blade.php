<div class="">
    <nav class="navbar navbar-expand-lg extended navbar-light navbar-bg-light" dir="rtl">
        <div class="container-fluid flex-lg-column">
            <div class="topbar d-flex flex-row w-100 justify-content-between align-items-center">
                <div class="navbar-brand d-md-none d-block"><a href="{{ route('frontend.home') }}" ><img src="{{asset('frontend/img/svg/Artboard 19.svg')}}" width="111px" srcset="{{asset('frontend/img/svg/Artboard 19.svg')}} 2x" alt="Riva - ريفا" /></a></div>
                <div class="navbar-other me-auto py-3 py-md-0">
                <ul class="navbar-nav flex-row align-items-center">
                    <li class="nav-item d-lg-none">
                        <button class="hamburger offcanvas-nav-btn"><span></span></button>
                    </li>
                </ul>
                <!-- /.navbar-nav -->
                </div>
                <!-- /.navbar-other -->
            </div>
            <!-- /.d-flex -->
            <div class="navbar-collapse-wrapper d-flex flex-row align-items-center">
                <div class="navbar-collapse offcanvas offcanvas-nav offcanvas-start">
                <div class="offcanvas-header d-lg-none">
                    <a href="{{ route('frontend.home') }}">
                        <img src="{{asset('frontend/img/svg/Artboard 19.svg')}}"  width="111px" srcset="{{asset('frontend/img/svg/Artboard 19.svg')}} 2x" alt="Riva - ريفا" />
                        {{-- ريـفـا --}}
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body d-flex flex-column h-100">
                    <ul class="navbar-nav pe-0 ps-auto">
                        <li class="nav-item d-md-block d-none">
                            <a class="nav-link pe-0" href="{{ route('frontend.home') }}" style="padding: 15px 0 0 20px;">
                                <img src="{{asset('frontend/img/svg/Artboard 18.svg')}}" width="155px" srcset="{{asset('frontend/img/svg/Artboard 18.svg')}} 2x" alt="Riva - ريفا" />
                                {{-- ريـــــفــــــا --}}
                            </a>
                        </li>
                        <div class="d-lg-none text-end">
                            <ul class="list-unstyled pe-0">
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.about') }}">تعرف على ريڤا</a></li>
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.projects') }}">مشاريعنا</a></li>
                                {{-- <li><a class="dropdown-item" href="{{ route('frontend.about') }}">ماذا نُقدم؟</a></li> --}}
                                {{-- <li><a class="dropdown-item" href="{{ route('frontend.home') }}">اطلب عقارك</a></li> --}}
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.contactus') }}">تواصل معنا</a></li>
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.blog') }}">الأحداث العقارية</a></li>
                            </ul>
                        </div>
                        <li class="nav-item dropdown dropdown-mega d-md-block d-none"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">اكتشف المزيد</a>
                            <ul class="dropdown-menu mega-menu" dir="rtl">
                                <li class="mega-menu-content">
                                    <div class="row gx-4 gx-lg-4">
                                        <div class="col-lg-4">
                                            <div class="text-end mapbody">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="dropdown-header h4 mb-0">خريطة مشاريعُنا</h6>
                                                    <a href="{{ route('frontend.projects.map') }}" class="dropdown-header text-start mb-0">عرض كل المشاريع<i class="uil uil-arrow-left"></i></a>
                                                </div>
                                                <img class="dropdown-header" style="max-width: 100%" src="{{ asset('frontend/Rectangle 5.png') }}" alt="Riva - ريفا">
                                            </div>
                                        </div>
                                        <!--/column -->
                                        <div class="col-lg-8">
                                            <div class="row gx-lg-8 gx-xl-12 gy-8 m-auto text-end">
                                                <!--/column -->
                                                <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('frontend.projects') }}" class="d-flex flex-row">
                                                        <div>
                                                            <img src="{{ asset('frontend/img/icons/Button4.png') }}" class="icon-svg icon-svg-sm text-pink ms-4" alt="Riva - ريفا">
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-1">مشاريعنا</h4>
                                                            <p class="mb-0">تقديم حلول تسويقية عقارية متكاملة وذات جودة عالية تلبي احتياجات وتطلعات عملائنا.</p>
                                                        </div>
                                                    </a>

                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('frontend.about') }}" class="d-flex flex-row">
                                                        <div>
                                                            <img src="{{ asset('frontend/img/icons/Button.png') }}" class="icon-svg icon-svg-sm text-aqua ms-4" alt="Riva - ريفا">
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-1">تعرف على ريڤا</h4>
                                                            <p class="mb-0">نحن في ريفا العقارية نتميز بالاحترافية والإتقان في إدارة المبيعات العقارية.</p>
                                                        </div>
                                                    </a>
                                                </div>
                                                <!--/column -->
                                                {{-- <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('frontend.about') }}" class="d-flex flex-row">
                                                        <div>
                                                            <img src="{{ asset('frontend/img/icons/Button2.png') }}" class="icon-svg icon-svg-sm text-yellow ms-4" alt="Riva - ريفا">
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-1">ماذا نُقدم؟</h4>
                                                            <p class="mb-0">في ريفا العقارية، نعتز بنجاحنا وإبداعنا في مجال التسويق العقاري.</p>
                                                        </div>
                                                    </a>
                                                </div> --}}

                                                <!--/column -->
                                                <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('frontend.blog') }}" class="d-flex flex-row">
                                                        <div>
                                                            <img src="{{ asset('frontend/img/icons/Button5.png') }}" class="icon-svg icon-svg-sm text-green ms-4" alt="Riva - ريفا">
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-1">الأحداث العقارية</h4>
                                                            <p class="mb-0">تقديم حلول تسويقية عقارية متكاملة وذات جودة عالية تلبي احتياجات وتطلعات عملائنا.</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/.row -->
                                </li>
                                <!--/.mega-menu-content-->
                            </ul>
                            <!--/.dropdown-menu -->
                        </li>

                        <li class="nav-item border-right">
                            <a href="{{ route('frontend.projects.map') }}" class="nav-link">
                                خريطة مشاريعُنا
                            </a>
                        </li>
                        <li class="nav-item border-right">
                            <a href="{{ route('frontend.projects') }}" class="nav-link">
                                تصفح المشاريع
                            </a>
                        </li>
                    </ul>
                    <!-- /.navbar-nav -->
                    <div class="d-lg-none mt-auto pt-6 pb-6 order-4">
                        <a href="mailto:first.last@email.com" class="link-inverse">{{ setting('site_email') }}</a>
                        <br /> {{ setting('site_phone') }} <br />
                        <nav class="nav social social-white mt-4">
                            <a href="https://twitter.com/riva_aqar"><i class="uil uil-twitter"></i></a>
                            <a href="https://www.linkedin.com/company/riva_aqar"><i class="uil uil-linkedin"></i></a>
                            <a href="https://snapchat.com/add/riva_aqar"><i class="uil uil-snapchat-alt"></i></a>
                            <a href="https://www.instagram.com/riva_aqar/"><i class="uil uil-instagram"></i></a>
                            <a href="https://www.youtube.com/@riva_aqar"><i class="uil uil-youtube"></i></a>
                        </nav>
                        <!-- /.social -->
                    </div>
                    <!-- /offcanvas-nav-other -->
                </div>
                <!-- /.offcanvas-body -->
                </div>
                <!-- /.navbar-collapse -->
                <div class="navbar-other me-auto w-100 d-none d-lg-block">

                    <nav class="nav social social-muted justify-content-end text-end">
                        {{-- <a href="#"><i class="uil uil-twitter"></i></a>
                        <a href="#"><i class="uil uil-facebook-f"></i></a>
                        <a href="#"><i class="uil uil-dribbble"></i></a>
                        <a href="#"><i class="uil uil-instagram"></i></a> --}}

                        <div class="form-search ms-3 relative" x-data="{ isOpen: false }">
                            <div class="relative search-wrapper">
                                <input
                                    wire:model.live.debounce.300ms="search"
                                    type="text"
                                    class="form-control"
                                    placeholder="ابحث عن عقارك"
                                    @focus="isOpen = true"
                                    @click.away="isOpen = false"
                                >
                                <i class="uil uil-search search-icon"></i>

                                @if(strlen($search) > 0)
                                    <button wire:click="clearSearch" class="clear-btn">
                                        <i class="uil uil-times"></i>
                                    </button>
                                @endif
                            </div>

                            <!-- Dropdown Results -->
                            <div
                                class="dropdown-results"
                                x-show="isOpen && @entangle('showDropdown')"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform scale-95"
                                x-transition:enter-end="opacity-100 transform scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 transform scale-100"
                                x-transition:leave-end="opacity-0 transform scale-95"
                            >
                                @if(count($results) > 0)
                                    <ul class="results-list">
                                        @foreach($results as $property)
                                            <li class="result-item">
                                                <button class="result-btn">
                                                    <div class="result-title">
                                                        <a class="fs-15" href="{{ route('frontend.projects.single', $property->slug) }}">
                                                            <i class="uil uil-windsock fs-15"></i> {{ $property->name }}
                                                        </a>
                                                    </div>
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    @if(count($results) > 0)
                                        <div class="no-results">
                                            لا توجد نتائج للبحث
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('frontend.contactus') }}" class="btn btn-expand btn-soft-primary rounded-pill">
                            <i class="uil uil-arrow-left"></i>
                            <span>تواصل معنا</span>
                        </a>

                    </nav>
                    <!-- /.social -->
                </div>
                <!-- /.navbar-other -->
            </div>
            <!-- /.navbar-collapse-wrapper -->
        </div>
        <!-- /.container -->
    </nav>
    <!-- /.navbar -->
    <div class="offcanvas offcanvas-end text-inverse" id="offcanvas-info" data-bs-scroll="true">
        <div class="offcanvas-header">
        <a href="{{ route('frontend.home') }}">
            <img src="{{asset('frontend/img/svg/Artboard 19.svg')}}" srcset="{{asset('frontend/img/svg/Artboard 19.svg')}} 2x" alt="Riva - ريفا" />ريفا</a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
        <div class="widget mb-8">
            <p>ريفا.</p>
        </div>
        <!-- /.widget -->
        <div class="widget mb-8">
            <h4 class="widget-title text-white mb-3">تواصل معنا</h4>
            <address> {{ setting('site_address') }} </address>
            <a href="mailto:{{ setting('site_email') }}">{{ setting('site_email') }}</a><br /> {{ setting('site_phone') }}
        </div>
        <!-- /.widget -->
        <div class="widget mb-8">
            <h4 class="widget-title text-white mb-3"></h4>
            <ul class="list-unstyled">
                <li><a href="{{ route('frontend.home') }}">تعرف على ريڤا</a></li>
                <li><a href="{{ route('frontend.projects') }}">مشاريعنا</a></li>
                <li><a href="{{ route('frontend.home') }}">ماذا نُقدم؟</a></li>
                <li><a href="{{ route('frontend.home') }}">اطلب عقارك</a></li>
                <li><a href="{{ route('frontend.home') }}">اعرض عقارك</a></li>
                <li><a href="{{ route('frontend.home') }}">الأحداث العقارية</a></li>
            </ul>
        </div>
        <!-- /.widget -->
        <div class="widget">
            <h4 class="widget-title text-white mb-3"></h4>
            <nav class="nav social social-white">
                <a href="https://twitter.com/riva_aqar"><i class="uil uil-twitter"></i></a>
                <a href="https://www.linkedin.com/company/riva_aqar"><i class="uil uil-linkedin"></i></a>
                <a href="https://snapchat.com/add/riva_aqar"><i class="uil uil-snapchat-alt"></i></a>
                <a href="https://www.instagram.com/riva_aqar/"><i class="uil uil-instagram"></i></a>
                <a href="https://www.youtube.com/@riva_aqar"><i class="uil uil-youtube"></i></a>
            </nav>
            <!-- /.social -->
        </div>
        <!-- /.widget -->
        </div>
        <!-- /.offcanvas-body -->
    </div>
    <!-- /.offcanvas -->
</div>
