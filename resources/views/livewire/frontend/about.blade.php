<div>
    <section class="wrapper bg-gray" >
        <div class="container pt-10 pt-md-14 pb-13 text-center">
          <div class="row">
            <div class="col-xl-6 mx-auto">
                <h1 class="display-1 mb-4">{!! \App\Helpers\ContentHelper::get('main_heading', 'ريفا العقارية .. تفهمك') !!}</h1>
                <p class="lead fs-lg mb-0">
                    {!! \App\Helpers\ContentHelper::get('tagline', 'شركة ريفا العقارية متخصصة في إدارة وتسويق المبيعات العقارية') !!}
                </p>
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container -->
        {{-- <figure class="position-absoute" style="bottom: 0; left: 0; z-index: 2;"><img src="{{asset('frontend/img/cta.png')}}" alt="" /></figure> --}}
      </section>

      <section class="py-5 bg-light mt-10">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-dark">
                    {!! \App\Helpers\ContentHelper::get('services_title', 'خدماتنا') !!}
                </h2>
                <p class="lead text-muted">
                    {{ \App\Helpers\ContentHelper::get('services_subtitle', 'نقدم حلولاً عقارية متكاملة بمعايير عالمية') }}
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-5 text-center">
                            <div class="icon-box icon-box-lg bg-primary rounded-circle mb-4 mx-auto">
                                <i class="fas fa-building text-white"></i>
                            </div>
                            <h4 class="mb-3">{{ \App\Helpers\ContentHelper::get('service_1_title', 'إدارة المشاريع العقارية') }}</h4>
                            <p class="mb-0">{{ \App\Helpers\ContentHelper::get('service_1_content', 'نقدم خدمات متكاملة لإدارة المشاريع العقارية بكفاءة عالية وخبرة متميزة') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-5 text-center">
                            <div class="icon-box icon-box-lg bg-primary rounded-circle mb-4 mx-auto">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <h4 class="mb-3">{{ \App\Helpers\ContentHelper::get('service_2_title', 'التسويق العقاري') }}</h4>
                            <p class="mb-0">{{ \App\Helpers\ContentHelper::get('service_2_content', 'استراتيجيات تسويقية مبتكرة لتحقيق أعلى عائد استثماري لعملائنا') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm hover-lift">
                        <div class="card-body p-5 text-center">
                            <div class="icon-box icon-box-lg bg-primary rounded-circle mb-4 mx-auto">
                                <i class="fas fa-handshake text-white"></i>
                            </div>
                            <h4 class="mb-3">{{ \App\Helpers\ContentHelper::get('service_3_title', 'الاستشارات العقارية') }}</h4>
                            <p class="mb-0">{{ \App\Helpers\ContentHelper::get('service_3_content', 'خبراء متخصصون لتقديم الاستشارات العقارية المناسبة لاحتياجاتك') }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
      <!-- /section -->
      <section class="wrapper bg-light mb-10" dir="rtl">
        <div class="container py-14 py-md-16">
          <div class="row gx-lg-8 gx-xl-12 gy-10 mb-14 mb-md-17 align-items-center">
            <div class="col-lg-6 position-relative order-lg-2">
                <div class="overlap-grid overlap-grid-1">
                    <div class="item">
                        <figure class="rounded shadow">
                            <img src="{{asset('frontend/img/baner.jpg')}}" srcset="{{asset('frontend/img/baner.jpg')}} 2x" alt="">
                        </figure>
                    </div>
                </div>
            </div>
            <!--/column -->
            <div class="col-lg-6">

                <h2 class="display-4 mb-3">{!! \App\Helpers\ContentHelper::get('management_title', 'ريفا العقارية .. تفهمك') !!}</h2>
                <p class="lead fs-lg">{!! \App\Helpers\ContentHelper::get('management_content', 'ريفا العقارية .. تفهمك') !!} </p>

                <h2 class="display-4 mb-3">{!! \App\Helpers\ContentHelper::get('marketing_title', 'ريفا العقارية .. تفهمك') !!} </h2>
                <p class="lead fs-lg">{!! \App\Helpers\ContentHelper::get('marketing_content', 'ريفا العقارية .. تفهمك') !!}</p>

            </div>
            <!--/column -->
          </div>
          <!--/.row -->
          <div class="row mb-5">
            <div class="col-md-10 col-xl-8 col-xxl-7 mx-auto text-center">
              <h2 class="display-4 mb-4 px-lg-14"> {!! \App\Helpers\ContentHelper::get('company_footer', 'ريفا العقارية .. تفهمك') !!}</h2>
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
          <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <div class="col-lg-12 order-lg-2">
              <div class="card me-lg-6">
                <div class="card-body p-6">
                  <div class="d-flex flex-row">
                    <div>
                      <span class="icon btn btn-circle btn-lg btn-soft-primary ps-none ms-4"><span class="number">01</span></span>
                    </div>
                    <div>
                      <h4 class="mb-1">{!! \App\Helpers\ContentHelper::get('mission_title', 'ريفا العقارية .. تفهمك') !!}</h4>
                      <p class="mb-0">{!! \App\Helpers\ContentHelper::get('mission_content', 'ريفا العقارية .. تفهمك') !!}</p>
                    </div>
                  </div>
                </div>
                <!--/.card-body -->
              </div>
              <!--/.card -->
              <div class="card ms-lg-13 mt-6">
                <div class="card-body p-6">
                  <div class="d-flex flex-row">
                    <div>
                      <span class="icon btn btn-circle btn-lg btn-soft-primary ps-none ms-4"><span class="number">02</span></span>
                    </div>
                    <div>
                      <h4 class="mb-1">{!! \App\Helpers\ContentHelper::get('vision_title', 'ريفا العقارية .. تفهمك') !!}</h4>
                      <p class="mb-0">{!! \App\Helpers\ContentHelper::get('vision_content', 'ريفا العقارية .. تفهمك') !!}</p>
                    </div>
                  </div>
                </div>
                <!--/.card-body -->
              </div>
              <!--/.card -->
              <div class="card mx-lg-6 mt-6">
                <div class="card-body p-6">
                  <div class="d-flex flex-row">
                    <div>
                      <span class="icon btn btn-circle btn-lg btn-soft-primary ps-none ms-4"><span class="number">03</span></span>
                    </div>
                    <div>
                      <h4 class="mb-1">{!! \App\Helpers\ContentHelper::get('values_title', 'ريفا العقارية .. تفهمك') !!}</h4>
                      <p class="mb-0">{!! \App\Helpers\ContentHelper::get('values_content', 'ريفا العقارية .. تفهمك') !!}</p>
                    </div>
                  </div>
                </div>
                <!--/.card-body -->
              </div>
              <!--/.card -->
            </div>
          </div>
          <!--/.row -->
        </div>
        <!-- /.container -->
      </section>

</div>
