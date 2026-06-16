<footer class="custom-footer py-6 py-md-10 border-top">
    <div class="container">
        <div class="row gy-8 gy-lg-0 gx-lg-8">
            <!-- Column 1: Brand Info -->
            <div class="col-lg-4 col-12 text-start">
                <div class="widget">
                    <a href="{{ route('frontend.home') }}" class="d-inline-block mb-4">
                        <img class="footer-logo" src="{{ asset('frontend/img/logoyy.png') }}" width="65" srcset="{{ asset('frontend/img/logoyy.png') }} 2x" alt="Riva - ريفا">
                    </a>
                    <p class="mb-4 footer-desc">
                        @lang('public.about.company_footer')
                    </p>
                    <div class="social-icons d-flex align-items-center">
                        <a href="https://twitter.com/riva_aqar" target="_blank" aria-label="X (Twitter)">
                            <svg width="15" height="15" viewBox="0 0 1200 1227" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M714.163 519.284L1160.89 0H1055.03L667.137 450.887L357.328 0H0L468.492 681.821L0 1226.37H105.866L515.491 750.218L842.672 1226.37H1200L714.137 519.284H714.163ZM569.165 687.828L521.697 619.934L144.011 79.6944H306.615L611.412 515.685L658.88 583.579L1055.08 1150.3H892.476L569.165 687.854V687.828Z" fill="currentColor"/>
                            </svg>
                        </a>
                        <a href="https://www.linkedin.com/company/riva_aqar" target="_blank" aria-label="LinkedIn">
                            <i class="uil uil-linkedin"></i>
                        </a>
                        <a href="https://snapchat.com/add/riva_aqar" target="_blank" aria-label="Snapchat">
                            <i class="uil uil-snapchat-alt"></i>
                        </a>
                        <a href="https://www.instagram.com/riva_aqar/" target="_blank" aria-label="Instagram">
                            <i class="uil uil-instagram"></i>
                        </a>
                        <a href="https://www.youtube.com/@riva_aqar" target="_blank" aria-label="YouTube">
                            <i class="uil uil-youtube"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Column 2: Quick Links (Company) -->
            <div class="col-lg-2 col-md-4 col-6 text-start">
                <div class="widget">
                    <h4 class="widget-title mb-4">@lang('public.common.site_title')</h4>
                    <ul class="list-unstyled footer-links mb-0">
                        <li class="mb-2">
                            <a href="{{ route('frontend.home') }}">@lang('public.nav.home')</a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('frontend.about') }}">@lang('public.nav.about')</a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('frontend.careers') }}">@lang('public.nav.careers')</a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('frontend.blog') }}">@lang('public.nav.events')</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Column 3: Discover More -->
            <div class="col-lg-3 col-md-4 col-6 text-start">
                <div class="widget">
                    <h4 class="widget-title mb-4">@lang('public.nav.discover_more')</h4>
                    <ul class="list-unstyled footer-links mb-0">
                        <li class="mb-2">
                            <a href="{{ route('frontend.projects') }}">@lang('public.nav.browse_projects')</a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('frontend.projects.map') }}">@lang('public.nav.project_map')</a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('broker.login') }}">@lang('public.nav.broker')</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Column 4: Contact Info -->
            <div class="col-lg-3 col-md-4 col-12 text-start">
                <div class="widget">
                    <h4 class="widget-title mb-4">@lang('public.nav.contact')</h4>
                    <ul class="list-unstyled footer-contact-info mb-0">
                        @if(setting('site_address'))
                            <li class="mb-3 d-flex align-items-start">
                                <i class="uil uil-map-marker mt-1 fs-16 opacity-75"></i>
                                <span class="fs-14 lh-sm">{{ setting('site_address') }}</span>
                            </li>
                        @endif
                        @if(setting('site_phone'))
                            <li class="mb-2 d-flex align-items-center">
                                <i class="uil uil-phone fs-16 opacity-75"></i>
                                <a href="tel:{{ setting('site_phone') }}" class="fs-14">{{ setting('site_phone') }}</a>
                            </li>
                        @endif
                        @if(setting('site_email'))
                            <li class="mb-4 d-flex align-items-center">
                                <i class="uil uil-envelope fs-16 opacity-75"></i>
                                <a href="mailto:{{ setting('site_email') }}" class="fs-14">{{ setting('site_email') }}</a>
                            </li>
                        @endif
                        <li>
                            <a href="{{ route('frontend.contactus') }}" class="btn btn-sm btn-outline-white rounded-pill px-4 py-2 hover-lift">
                                @lang('public.nav.contact')
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Divider Line -->
        <hr class="mt-2 mb-6 footer-divider" />

        <!-- Bottom Row: Copyright and Legal Links -->
        <div class="row align-items-center justify-content-between footer-bottom text-start">
            <div class="col-md-6 mb-3 mb-md-0">
                <p class="mb-0 fs-14 copyright">
                    @lang('public.footer.copyright')
                </p>
            </div>
            <div class="col-md-6 text-md-end footer-bottom-links">
                <a href="{{ route('frontend.privacy') }}">@lang('public.footer.privacy')</a>
                <a href="{{ route('frontend.terms') }}">@lang('public.footer.terms')</a>
            </div>
        </div>
    </div>

    <style>
    /* Footer Custom Design System */
    .custom-footer {
        background-color: #112616; /* Brand Sophisticated Forest Green */
        color: #cbd5e1;
        font-family: 'IBM Plex Sans Arabic', 'Rubik', sans-serif;
        position: relative;
    }

    .custom-footer .footer-logo {
        transition: transform 0.3s ease;
    }

    .custom-footer .footer-logo:hover {
        transform: scale(1.05);
    }

    .custom-footer .footer-desc {
        color: #94a3b8;
        line-height: 1.6;
        font-size: 0.9rem;
        max-width: 320px;
    }

    .custom-footer .widget-title {
        color: #ffffff !important;
        font-weight: 600;
        font-size: 1.05rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    /* Links and Hover micro-animations */
    .custom-footer .footer-links a {
        color: #a9b7caff;
        text-decoration: none;
        font-size: 0.65rem;
        transition: all 0.25s ease;
        display: inline-block;
    }

    .custom-footer .footer-links a:hover {
        color: #ffffff;
        transform: translateX(4px);
    }

    [dir="rtl"] .custom-footer .footer-links a:hover {
        transform: translateX(-4px);
    }

    .custom-footer .footer-contact-info {
        color: #94a3b8;
    }

    .custom-footer .footer-contact-info a {
        color: #94a3b8;
        text-decoration: none;
        transition: color 0.25s ease;
    }

    .custom-footer .footer-contact-info a:hover {
        color: #ffffff;
    }

    /* Social Icons styling with micro-animations */
    .custom-footer .social-icons a {
        width: 36px;
        height: 36px;
        line-height: 36px;
        text-align: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.04);
        color: #cbd5e1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .custom-footer .social-icons a svg {
        transition: transform 0.3s ease;
    }

    .custom-footer .social-icons a:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        transform: translateY(-3px);
    }

    .custom-footer .social-icons a:hover svg {
        transform: scale(1.1);
    }

    .custom-footer .footer-divider {
        border-color: rgba(255, 255, 255, 0.08);
    }

    .custom-footer .copyright {
        color: #64748b;
    }

    .custom-footer .footer-bottom-links a {
        color: #64748b;
        text-decoration: none;
        font-size: 0.85rem;
        transition: color 0.25s ease;
    }

    .custom-footer .footer-bottom-links a:hover {
        color: #ffffff;
    }

    /* Custom button border white */
    .custom-footer .btn-outline-white {
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        background: transparent;
        transition: all 0.25s ease;
    }

    .custom-footer .btn-outline-white:hover {
        background: #ffffff;
        color: #112616 !important;
        border-color: #ffffff;
    }

    /* Hover-lift effect */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-lift:hover {
        transform: translateY(-2px);
    }

    /* Safeguards for list padding in both directions */
    .custom-footer ul.list-unstyled {
        padding-left: 0 !important;
        padding-right: 0 !important;
    }

    /* Direction-aware margins and spacing */
    .custom-footer .social-icons a:not(:last-child) {
        margin-right: 0.75rem;
    }
    .custom-footer .footer-contact-info li i {
        margin-right: 0.5rem;
    }
    .custom-footer .footer-bottom-links a:not(:last-child) {
        margin-right: 1.5rem;
    }

    /* RTL specific overrides for hardcoded theme styles */
    [dir="rtl"] .custom-footer {
        text-align: right !important;
    }
    [dir="rtl"] .custom-footer .text-start {
        text-align: right !important;
    }
    [dir="rtl"] .custom-footer .text-md-end {
        text-align: left !important;
    }
    [dir="rtl"] .custom-footer .social-icons a:not(:last-child) {
        margin-right: 0 !important;
        margin-left: 0.75rem !important;
    }
    [dir="rtl"] .custom-footer .footer-contact-info li i {
        margin-right: 0 !important;
        margin-left: 0.5rem !important;
    }
    [dir="rtl"] .custom-footer .footer-bottom-links a:not(:last-child) {
        margin-right: 0 !important;
        margin-left: 1.5rem !important;
    }
    </style>
</footer>
