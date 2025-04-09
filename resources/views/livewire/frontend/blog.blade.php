<div>
    <section class="section-frame overflow-hidden mt-5">
        <div class="wrapper bg-soft-primary">
            <div class="container-fluid py-10 py-md-10 text-center">
                <div class="row">
                    <div class="col-md-7 col-lg-6 col-xl-5 mx-auto">
                        <h1 class="display-1 mb-0">الاحداث العقارية</h1>
                    </div>
                </div>
            </div>
        </div>
      </section>
      <!-- /section -->
      <section class="wrapper bg-light">
        <div class="container-fluid py-8 px-7 py-md-8">
          <div class="row">
            <div class="col-lg-9" dir="rtl">
                <div class="blog">
                    <div class="row gx-md-8 gy-8 mb-8" >
                        @if($posts->count() > 0)
                            @foreach($posts as $post)
                                <article class="col-md-6">
                                    <div class="card">
                                        <figure class="card-img-top overlay overlay-1 hover-scale"><a href="{{ route('frontend.blog.single', $post->slug) }}"> <img src="{{ App\Helpers\MediaHelper::getUrl($post->cover_photo_path) }}" alt="" /></a>
                                            <figcaption>
                                                <h5 class="from-top mb-0">تابع</h5>
                                            </figcaption>
                                        </figure>
                                        <div class="card-body">
                                            <div class="post-header">
                                                <div class="post-category text-line">
                                                    <a href="{{ route('frontend.blog.single', $post->slug) }}" class="hover" rel="category">{{ $post->categories->first()->name }}</a>
                                                </div>
                                                <!-- /.post-category -->
                                                <h2 class="post-title h3 mt-1 mb-3"><a class="link-dark" href="{{ route('frontend.blog.single', $post->slug) }}">{{ $post->title }}</a></h2>
                                            </div>
                                            <!-- /.post-header -->
                                            <div class="post-content">
                                                <p>{{ \Illuminate\Support\Str::limit($post->sub_title, 150) }}</p>
                                            </div>
                                            <!-- /.post-content -->
                                        </div>
                                        <!--/.card-body -->
                                        <div class="card-footer">
                                            <ul class="post-meta d-flex mb-0">
                                                <li class="post-date"><i class="uil uil-calendar-alt"></i><span>{{ $post->created_at->format('Y M d') }}</span></li>
                                            </ul>
                                            <!-- /.post-meta -->
                                        </div>
                                        <!-- /.card-footer -->
                                    </div>
                                    <!-- /.card -->
                                </article>
                            @endforeach
                        @else
                            <p>No posts found.</p>
                        @endif

                    </div>
                    <!-- /.row -->
                </div>
            </div>
            <!-- /column -->
            <aside class="col-lg-3 sidebar" dir="rtl">
              <div class="widget">
                <form class="search-form">
                  <div class="form-floating mb-0">
                    <input id="search-form" wire:model.debounce.300ms="searchTerm" type="text" class="form-control" placeholder="Search">
                    <label for="search-form">بحث</label>
                  </div>
                </form>
                <!-- /.search-form -->
              </div>
              <!-- /.widget -->
              <div class="widget">
                <h4 class="widget-title mb-3">Categories</h4>
                <ul class="unordered-list bullet-primary text-reset pe-0">
                    @foreach($categories as $category)
                        <li><a href="{{ $category->id }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
              </div>
              <!-- /.widget -->
              <div class="widget">
                <h4 class="widget-title mb-3">Tags</h4>
                <ul class="list-unstyled tag-list pe-0">
                    @foreach($tags as $tag)
                        <li><a href="#" class="btn btn-soft-ash btn-sm rounded-pill">{{ $tag->name }}</a></li>
                    @endforeach
                </ul>
              </div>
            </aside>
            <!-- /column .sidebar -->
          </div>
          <!-- /.row -->
        </div>
        <!-- /.container -->
    </section>
</div>
