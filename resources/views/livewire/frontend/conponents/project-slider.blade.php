<section class="section-frame mx-xxl-5 overflow-hidden rounded-xl">
    <div class="swiper-container blog grid-view" data-margin="30" data-dots="false" data-loop="true" data-autoplay="false" data-autoplaytime="5000" data-drag="false" data-speed="700" data-items-xxl="1" data-items-xl="1" data-items-lg="1" data-items-md="1" data-items-xs="1">
        <div class="swiper">
            <div class="swiper-wrapper">
                @forelse($projects as $project)

                    <div class="swiper-slide" wire:key="{{$project->id}}">
                        <figure class="overlay caption caption-overlay rounded-xl mb-0">
                            <a class="wrapper image-wrapper bg-image bg-cover bg-overlay"
                               style="height: 700px"
                               href="{{ route('frontend.projects.single', $project->slug) }}"
                               data-image-src="@if($project->getMainImages()) {{ App\Helpers\MediaHelper::getUrl($project->getMainImages()->media_url) }} @else {{ App\Helpers\MediaHelper::getUrl($project->projectMedia->first()->media_url) }} @endif">
                            </a>

                            <figcaption class="noise-container text-right hero" dir="rtl">
                                <div class="d-flex align-content-start justify-content-between w-100">
                                    <h2 class="post-title h3 mt-1 mb-3">
                                        <a href="{{ route('frontend.projects.single', $project->slug) }}">{{ $project->name }}</a>
                                    </h2>
                                    <div>
                                        <img src="{{ App\Helpers\MediaHelper::getUrl($project->developer->logo) }}"
                                             style="width: 50px !important;max-height:50px"
                                             alt="Logo">
                                    </div>
                                </div>
                                <p class="fs-15 text-muted fw-light mt-2 mb-0">
                                    {!! Str::limit(strip_tags($project->description), 150) !!}
                                </p>
                                <span class="badge badge-lg text-white mt-3 d-flex align-content-center">
                                    عرض كل التفاصيل
                                    <i class="uil uil-arrow-up-left fs-15 me-2"></i>
                                </span>
                            </figcaption>

                            @php
                                $pdfUrl = $project->getFirstPdfUrl();
                            @endphp
                            @if($pdfUrl)

                            <div class="text-right" style="position: absolute;z-index: 4;top: 1rem;left: 1rem;" dir="rtl">
                                <a href="{{ $pdfUrl }}" download class="noise-container badge badge-lg text-white d-flex align-content-center align-items-center p-2" dir="rtl">
                                    حمل الملف التعريفي للمشروع
                                    <i class="uil uil-file-download-alt fs-25 me-2"></i>
                                </a>
                            </div>

                            @endif


                        </figure>
                    </div>

                @empty
                    <div class="swiper-slide">
                        <p>لا توجد مشاريع متاحة</p>
                    </div>
                @endforelse
            </div>
            <div class="swiper-controls"><div class="swiper-navigation"><div class="swiper-button swiper-button-prev swiper-button-disabled" tabindex="-1" role="button" aria-label="Previous slide" aria-controls="swiper-wrapper-797a0bbed7b6bf81" aria-disabled="true"></div><div class="swiper-button swiper-button-next" tabindex="0" role="button" aria-label="Next slide" aria-controls="swiper-wrapper-797a0bbed7b6bf81" aria-disabled="false"></div></div><div class="swiper-pagination swiper-pagination-clickable swiper-pagination-bullets swiper-pagination-horizontal"><span class="swiper-pagination-bullet swiper-pagination-bullet-active" tabindex="0" role="button" aria-label="Go to slide 1" aria-current="true"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 2"></span><span class="swiper-pagination-bullet" tabindex="0" role="button" aria-label="Go to slide 3"></span></div></div>
        </div>
    </div>
</section>
