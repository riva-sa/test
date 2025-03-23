<section class="wrapper bg-light twons" dir="rtl">
    <div class="container py-10 py-md-10 mt-md-15">
        <div class="row">
            <div class="col-md-11 col-lg-10 col-xl-9 col-xxl-8 mx-auto text-center position-relative">
                <h3 class="display-3 mb-11 px-xl-10 px-xxl-13 text-main">وإلى سكان المملكة</h3>
            </div>
        </div>

        <ul class="list-inline mb-0 p-0">
            @foreach($states as $state)
                <li class="list-inline-item mb-2">
                    <a href="{{ url('/projects?' . http_build_query(['selected_cities' => $state['city_id'], 'selected_states' => $state['id']])) }}" class=" btn btn-sm rounded border border-1 border-primary text-main">
                        <div class="icon btn btn-circle btn-sm pe-none mx-auto ms-2 mb-xl-0"> <img src="{{ $state['photo'] }}" width="100%" alt=""> </div>
                        {{ $state['name'] }}
                        {{-- @if ($state['projects_count'] > 0) --}}
                            <span class="badge text-dark rounded-pill mb-0 me-1">{{ $state['projects_count'] }} مشروع</span>
                            <i class="uil uil-arrow-up-left me-3"></i>
                        {{-- @endif --}}
                    </a>
                </li>
            @endforeach
        </ul>
        {{-- <div class="grid mb-12">
            <div class="row isotope gy-6">
                @foreach($states as $state)
                    @if($loop->index === 3)
                        <div class="item col-md-6 col-xl-4">
                            <div class="shadow-none rounded-xl mt-4" style="height:200px">
                                <div class="card-body p-5">
                                    <div class="">
                                        <div class="info ps-0 text-center">
                                            <h2 class="mb-2">ريڤا العقارية</h2>
                                            <h2 class="mb-4 fw-bold">تـــــــفــهـمــــــــك</h2>
                                            <a class="btn btn-primary btn-icon btn-icon-start rounded">
                                                <i class="uil uil-file ms-1"></i> حمل الملف التعريفي للشركة
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="item col-md-6 col-xl-4">
                        <div class="card shadow-none rounded-xl" style="background: url('{{ $state['photo'] }}');background-size:cover;height:{{ $state['height'] }};background-position: center;">
                            <div class="card-body p-5">
                                <div class="d-flex align-content-center justify-content-between">
                                    <div class="info ps-0 text-white">
                                        <h5 class="mb-1 text-white">{{ $state['name'] }}</h5>
                                        <p class="mb-0">{{ $state['projects_count'] }} مشروع</p>
                                    </div>
                                    <a href="{{ url('/projects?' . http_build_query(['selected_cities' => $state['city_id'], 'selected_states' => $state['id']])) }}"
                                       class="noise-container btn btn-circle text-white btn-lg">
                                        <i class="uil uil-arrow-up-left"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div> --}}
    </div>
</section>
