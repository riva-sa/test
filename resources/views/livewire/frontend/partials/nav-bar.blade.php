<div class="">
    <nav class="navbar navbar-expand-lg extended navbar-light navbar-bg-light" dir="{{ \App\Helpers\LocalizationHelper::getDirection() }}">
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
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.about') }}">@lang('public.nav.about')</a></li>
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.projects') }}">@lang('public.nav.projects')</a></li>
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.contactus') }}">@lang('public.nav.contact')</a></li>
                                <li><a class="dropdown-item mb-2" href="{{ route('frontend.blog') }}">@lang('public.nav.events')</a></li>
                            </ul>

                            @php
                                $mRouteParams = request()->route()?->parameters() ?? [];
                                $mCurrentRoute = Route::currentRouteName();
                                $mCurrentLocale = app()->getLocale();
                                $mLanguages = [
                                    'ar' => ['native' => 'العربية', 'abbr' => 'ع'],
                                    'en' => ['native' => 'English', 'abbr' => 'EN'],
                                ];
                            @endphp
                            <div class="lang-switcher-mobile">
                                @foreach($mLanguages as $mCode => $mLang)
                                    <a
                                        href="{{ $mCurrentRoute ? route($mCurrentRoute, array_merge($mRouteParams, ['locale' => $mCode])) : url('/' . $mCode) }}"
                                        hreflang="{{ $mCode }}"
                                        class="lang-switcher-mobile__option @if($mCurrentLocale === $mCode) is-active @endif"
                                    >
                                        <!-- <span class="lang-switcher__flag">{{ $mLang['abbr'] }}</span> -->
                                        <span>{{ $mLang['native'] }}</span>
                                        @if($mCurrentLocale === $mCode)
                                            <i class="uil uil-check ms-auto"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                        <li class="nav-item dropdown dropdown-mega d-md-block d-none"><a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">@lang('public.nav.discover_more')</a>
                            <ul class="dropdown-menu mega-menu" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                                <li class="mega-menu-content">
                                    <div class="row gx-4 gx-lg-4">
                                        <div class="col-lg-4">
                                            <div class="text-end mapbody">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="dropdown-header h4 mb-0">@lang('public.nav.project_map')</h6>
                                                    <a href="{{ route('frontend.projects.map') }}" class="dropdown-header text-start mb-0">@lang('public.nav.view_all_projects')<i class="uil uil-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i></a>
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
                                                            <h4 class="mb-1">@lang('public.nav.projects')</h4>
                                                            <p class="mb-0">@lang('public.nav.project_description')</p>
                                                        </div>
                                                    </a>

                                                </div>

                                                <div class="col-md-6 col-lg-4">
                                                    <a href="{{ route('frontend.about') }}" class="d-flex flex-row">
                                                        <div>
                                                            <img src="{{ asset('frontend/img/icons/Button.png') }}" class="icon-svg icon-svg-sm text-aqua ms-4" alt="Riva - ريفا">
                                                        </div>
                                                        <div>
                                                            <h4 class="mb-1">@lang('public.nav.about')</h4>
                                                            <p class="mb-0">@lang('public.nav.about_description')</p>
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
                                                            <h4 class="mb-1">@lang('public.nav.events')</h4>
                                                            <p class="mb-0">@lang('public.nav.events_description')</p>
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
                                @lang('public.nav.project_map')
                            </a>
                        </li>
                        <li class="nav-item border-right">
                            <a href="{{ route('frontend.projects') }}" class="nav-link">
                                @lang('public.nav.browse_projects')
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
                                    placeholder="@lang('public.search.placeholder')"
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
                                                        <a class="fs-15" href="{{ route('frontend.projects.single', ['slug' => $property->slug]) }}">
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
                                            @lang('public.search.no_results')
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('frontend.contactus') }}" class="btn btn-expand btn-soft-primary rounded-pill">
                            <i class="uil uil-arrow-{{ app()->getLocale() === 'ar' ? 'left' : 'right' }}"></i>
                            <span>@lang('public.nav.contact')</span>
                        </a>

                        @php
                            $routeParams = request()->route()?->parameters() ?? [];
                            $currentRoute = Route::currentRouteName();
                            $currentLocale = app()->getLocale();
                            $languages = [
                                'ar' => ['native' => 'العربية', 'label' => 'Arabic',  'abbr' => 'ع'],
                                'en' => ['native' => 'English', 'label' => 'English', 'abbr' => 'EN'],
                            ];
                            $localeUrl = function ($locale) use ($currentRoute, $routeParams) {
                                return $currentRoute
                                    ? route($currentRoute, array_merge($routeParams, ['locale' => $locale]))
                                    : url('/' . $locale);
                            };
                        @endphp

                        <div class="lang-switcher align-content-center" x-data="{ open: false }" @click.away="open = false" @keydown.escape="open = false">
                            <button
                                type="button"
                                class="lang-switcher__toggle"
                                @click="open = !open"
                                :aria-expanded="open.toString()"
                                aria-haspopup="listbox"
                            >
                                <i class="uil uil-globe lang-switcher__globe"></i>
                                <span class="lang-switcher__current">{{ $languages[$currentLocale]['abbr'] }}</span>
                                <i class="uil uil-angle-down lang-switcher__caret" :class="{ 'is-open': open }"></i>
                            </button>

                            <div
                                class="lang-switcher__menu"
                                role="listbox"
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-1 scale-95"
                                x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 transform -translate-y-1 scale-95"
                            >
                                @foreach($languages as $code => $lang)
                                    <a
                                        href="{{ $localeUrl($code) }}"
                                        hreflang="{{ $code }}"
                                        role="option"
                                        aria-selected="{{ $currentLocale === $code ? 'true' : 'false' }}"
                                        class="lang-switcher__option @if($currentLocale === $code) is-active @endif"
                                    >
                                        <span class="lang-switcher__flag">{{ $lang['abbr'] }}</span>
                                        <span class="lang-switcher__names">
                                            <span class="lang-switcher__native">{{ $lang['native'] }}</span>
                                            <!-- <span class="lang-switcher__sub">{{ $lang['label'] }}</span> -->
                                        </span>
                                        @if($currentLocale === $code)
                                            <i class="uil uil-check lang-switcher__check"></i>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

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
            <img src="{{asset('frontend/img/svg/Artboard 19.svg')}}" srcset="{{asset('frontend/img/svg/Artboard 19.svg')}} 2x" alt="Riva - ريفا" />@lang('public.common.site_name')</a>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
        <div class="widget mb-8">
            <p>@lang('public.common.site_title').</p>
        </div>
        <!-- /.widget -->
        <div class="widget mb-8">
            <h4 class="widget-title text-white mb-3">@lang('public.nav.contact')</h4>
            <address> {{ setting('site_address') }} </address>
            <a href="mailto:{{ setting('site_email') }}">{{ setting('site_email') }}</a><br /> {{ setting('site_phone') }}
        </div>
        <!-- /.widget -->
        <div class="widget mb-8">
            <h4 class="widget-title text-white mb-3"></h4>
            <ul class="list-unstyled">
                <li><a href="{{ route('frontend.about') }}">@lang('public.nav.about')</a></li>
                <li><a href="{{ route('frontend.projects') }}">@lang('public.nav.projects')</a></li>
                <li><a href="{{ route('frontend.services') }}">@lang('public.nav.services')</a></li>
                <li><a href="{{ route('frontend.home') }}">@lang('public.nav.request_property')</a></li>
                <li><a href="{{ route('frontend.home') }}">@lang('public.nav.list_property')</a></li>
                <li><a href="{{ route('frontend.blog') }}">@lang('public.nav.events')</a></li>
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
