<div>
    <section class="wrapper image-wrapper bg-image bg-overlay bg-overlay-400 text-white" data-image-src="{{asset('frontend/img/baner.jpg')}}">
        <div class="container pt-17 pb-20 pt-md-19 pb-md-21 text-center">
          <div class="row">
            <div class="col-lg-8 mx-auto">
              <h1 class="display-1 mb-3 text-white">تواصل معنا</h1>
              <!-- /nav -->
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>

    <section class="wrapper bg-light">
        <div class="container pb-11">
          <div class="row mb-14 mb-md-16">
            <div class="col-xl-10 mx-auto mt-n19">
              <div class="card">
                <div class="row gx-0">
                  <div class="col-lg-6 align-self-stretch">
                    <div class="map map-full rounded-top rounded-lg-start">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3622.129610651099!2d46.587956!3d24.791015!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3e2ee5b35674d549%3A0x648052f4250b214!2z2LTYsdmD2Kkg2LHZitmB2Kcg2KfZhNi52YLYp9ix2YrYqQ!5e0!3m2!1sar!2ssa!4v1739709043432!5m2!1sar!2ssa" loading="lazy"  style="width:100%; height: 100%; border:0" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <!-- /.map -->
                  </div>
                  <!--/column -->
                  <div class="col-lg-6">
                    <div class="p-10 p-md-11 p-lg-14" dir="rtl">
                      <div class="d-flex flex-row">
                        <div>
                          <div class="icon text-primary fs-28 ms-4 mt-n1"> <i class="uil uil-location-pin-alt"></i> </div>
                        </div>
                        <div class="align-self-start justify-content-start">
                          <h5 class="mb-1">العنوان</h5>
                          <address>{{ setting('site_address') }}</address>
                        </div>
                      </div>
                      <!--/div -->
                      <div class="d-flex flex-row">
                        <div>
                          <div class="icon text-primary fs-28 ms-4 mt-n1"> <i class="uil uil-phone-volume"></i> </div>
                        </div>
                        <div>
                          <h5 class="mb-1">الهاتف</h5>
                          <p>{{ setting('site_phone') }}</p>
                        </div>
                      </div>
                      <!--/div -->
                      <div class="d-flex flex-row">
                        <div>
                          <div class="icon text-primary fs-28 ms-4 mt-n1"> <i class="uil uil-envelope"></i> </div>
                        </div>
                        <div>
                          <h5 class="mb-1">البريد الالكتروني</h5>
                          <p class="mb-0"><a href="mailto:{{ setting('site_email') }}" class="link-body">{{ setting('site_email') }}</a></p>
                        </div>
                      </div>
                      <!--/div -->
                    </div>
                    <!--/div -->
                  </div>
                  <!--/column -->
                </div>
                <!--/.row -->
              </div>
              <!-- /.card -->
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
          <div class="row">
            <div class="col-lg-10 offset-lg-1 col-xl-8 offset-xl-2" dir="rtl">
                <h2 class="display-4 mb-3 text-center">أرسل لنا رسالة</h2>
                <p class="lead text-center mb-10">تواصل معنا من خلال نموذج الاتصال الخاص بنا وسنقوم بالرد عليك قريبًا.</p>
                @if ($success)
                    <div class="alert alert-success text-center mb-4" role="alert">
                        تم إرسال رسالتك بنجاح. سنتواصل معك قريباً.
                    </div>
                @endif

                <form wire:submit.prevent="submit">
                    <div class="messages">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="row gx-4">
                        <div class="col-md-4">
                            <div class="form-floating mb-4">
                                <input id="form_name" type="text" wire:model="name" class="form-control" placeholder="">
                                <label for="form_name">الاسم *</label>
                                @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-floating mb-4">
                                <input id="form_email" type="email" wire:model="email" class="form-control" placeholder="">
                                <label for="form_email">البريد الالكتروني *</label>
                                @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-select-wrapper mb-4">
                                <select class="form-select" id="form-select" wire:model="department">
                                    <option selected disabled value="">حدد القسم</option>
                                    <option value="Sales">مبيعات</option>
                                    <option value="Marketing">تسويق</option>
                                    <option value="Customer Support">خدمة عملاء</option>
                                </select>
                                @error('department') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating mb-4">
                                <textarea id="form_message" wire:model="message" class="form-control" placeholder="Your message" style="height: 150px"></textarea>
                                <label for="form_message">الرسالة *</label>
                                @error('message') <div class="text-danger mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary rounded-pill btn-send mb-3">ارسال</button>
                        </div>
                    </div>
                </form>
              <!-- /form -->
            </div>
            <!-- /column -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
    {{-- <span id="fullscreen-btn" class="fullscreen-btn">....</span> --}}

</div>
