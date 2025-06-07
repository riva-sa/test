<section class="section-frame mx-xxl-5" dir="rtl">
    <div class="container-fluid py-14 py-md-10">
        <div class="d-md-flex justify-content-between align-items-center mb-10">
            <div class="d-flex align-items-center">
                <h3 class="display-4 text-main">اسكتشف مشاريعُنا</h3>
                <div wire:loading wire:target="setActiveTab">
                    <div class="spinner-border spinner-border-sm me-4 mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

            <!-- /column -->
            <ul class="nav nav-tabs nav-pills tab-box" dir="rtl">

                <li class="nav-item" wire:click="setActiveTab('all')">
                    <a wire:click="setActiveTab('all')" class="nav-link px-4 {{ $activeTab ==='all' ? 'active noise-container' : '' }}">
                        الكل
                    </a>
                </li>

                @foreach($projectTypes as $type)
                    <li class="nav-item">
                        <a wire:click="setActiveTab('{{ $type->slug }}')" class="nav-link px-4 {{ $activeTab === $type->slug ? 'active noise-container' : '' }}">
                            {{ $type->name }}
                        </a>
                    </li>
                @endforeach

            </ul>
        </div>
        <!-- /.row -->
        <div class="row">
            @foreach($projects as $project)
                <div class="col-md-4 mb-3" dir="rtl">
                    <figure class="overlay caption caption-overlay rounded-xl mb-0">
                        <div class="d-flex mainCardHeader">

                            <h3 class="post-title h3">
                                <a href="{{ route('frontend.projects.single', $project->slug) }}" class="d-flex align-content-center">{{ $project->name }} <span class="badge noise-container mb-0 text-dark rounded-pill fs-15 me-2">متاح</span></a>
                            </h3>

                            <a href="{{ route('frontend.projects.single', $project->slug) }}" class="noise-container arrowToSingle"><i class="uil uil-arrow-up-left"></i></a>
                        </div>
                        <a href="{{ route('frontend.projects.single', $project->slug) }}"
                            style="height: 500px; background:url('@if($project->getMainImages() !== null ) {{ App\Helpers\MediaHelper::getUrl($project->getMainImages()->media_url) }} @else {{ App\Helpers\MediaHelper::getUrl($project->projectMedia()->first()->media_url) }} @endif');background-size: cover;background-position: center;"></a>
                        {{-- <a href="{{ route('frontend.projects.single', $project->slug) }}"
                            style="height: 500px; background:url('@if($project->getMainImages() !== null ) {{ Storage::disk('public')->url($project->getMainImages()->media_url) }} @else {{ Storage::disk('public')->url($project->projectMedia()->first()->media_url) }} @endif');background-size: cover;background-position: center;"></a> --}}
                            <figcaption class="noise-container text-right tap" dir="rtl">

                            <div class="d-flex align-content-start justify-content-between w-100">
                                <h2 class="post-title h4 mt-1 mb-3">
                                    <a href="{{ route('frontend.projects.single', $project->slug) }}">نظرة عامة</a>
                                </h2>
                                <div>
                                    <img src="{{ App\Helpers\MediaHelper::getUrl($project->developer->logo) }}" style="width: 50px !important;max-height:50px" alt="Logo">
                                </div>
                            </div>
                            <ul class="post-meta text-white mb-3">
                                <li class="post-date">
                                    <img src="{{ asset('frontend/img/icons/money-03.png') }}" style="width: 20px;" alt="Riva - ريفا">
                                    <span class="me-1">{{ $project->price_range }}</span>
                                </li>
                            </ul>
                            <ul class="post-meta text-white mb-0">
                                <li class="post-date">
                                    <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}" style="width: 20px;" alt="Riva - ريفا">
                                    <span class="me-1">{{ $project->bathroom_range }}</span>
                                </li>
                                <li class="post-author">
                                    <img src="{{ asset('frontend/img/icons/bed.png') }}" style="width: 20px;" alt="Riva - ريفا">
                                    <span class="me-1">{{ $project->bedroom_range }}</span>
                                </li>
                                <li class="post-comments">
                                    <img src="{{ asset('frontend/img/icons/move.png') }}" style="width: 20px;" alt="Riva - ريفا">
                                    <span class="me-1">{{ $project->space_range }}</span>
                                </li>
                            </ul>
                        </figcaption>

                    </figure>
                </div>
            @endforeach
        </div>
        <!-- /.swiper-container -->
    </div>
    <!-- /.container -->
    <div class="text-center mb-10">
        <a href="{{ route('frontend.projects') }}" class="btn btn-expand btn-soft-primary rounded-pill">
            <span>عرض كل المشاريع</span>
            <i class="uil uil-arrow-left"></i>
        </a>
    </div>
</section>
