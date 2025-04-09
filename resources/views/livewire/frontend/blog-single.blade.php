<div>
    <!-- /header -->
    <section class="wrapper bg-soft-primary">
        <div class="container pt-10 pb-19 pt-md-14 pb-md-20 text-center">
            <div class="row">
                <div class="col-md-10 col-xl-8 mx-auto">
                    <div class="post-header">
                        <div class="post-category text-line">
                            <a href="#" class="hover" rel="category">{{ $post->categories->first()->name }}</a>
                        </div>
                        <!-- /.post-category -->
                        <h1 class="display-1 mb-4">{{ $post->title }}</h1>
                        <ul class="post-meta mb-5">
                            <li class="post-date"><i class="uil uil-calendar-alt"></i> <span>{{ $post->created_at->format('T M D') }}</span></li>
                        </ul>
                        <!-- /.post-meta -->
                    </div>
                    <!-- /.post-header -->
                </div>
                <!-- /column -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
        </section>
        <!-- /section -->
        <section class="wrapper bg-light" dir="rtl">
        <div class="container pb-14 pb-md-16">
            <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="blog single mt-n17">
                <div class="card">
                    <figure class="card-img-top">
                        {{-- <img src="{{ asset('storage/' . $post->cover_photo_path) }}" alt="" /> --}}
                        <img src="{{ App\Helpers\MediaHelper::getUrl($post->cover_photo_path) }}" alt="">
                    </figure>
                    <div class="card-body">
                    <div class="classic-view">
                        <article class="post">
                        <div class="post-content mb-5 text-end">
                            {!! $post->body !!}
                        </div>
                        <!-- /.post-content -->
                        <div class="post-footer d-md-flex flex-md-row justify-content-md-between align-items-center mt-8">
                            <div>
                                <ul class="list-unstyled tag-list mb-0 pe-0">
                                    @foreach ($post->categories as $category)
                                        <li><a href="#" class="btn btn-soft-ash btn-sm rounded-pill mb-0">{{$category->name}}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <div class="mb-0 mb-md-2">
                                <div class="dropdown share-dropdown btn-group">
                                    <button class="btn btn-sm btn-red rounded-pill btn-icon btn-icon-start dropdown-toggle mb-0 me-0" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="uil uil-share-alt"></i> مشاركة </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#"><i class="uil uil-twitter"></i>Twitter</a>
                                        <a class="dropdown-item" href="#"><i class="uil uil-facebook-f"></i>Facebook</a>
                                        <a class="dropdown-item" href="#"><i class="uil uil-linkedin"></i>Linkedin</a>
                                    </div>
                                    <!--/.dropdown-menu -->
                                </div>
                            <!--/.share-dropdown -->
                            </div>
                        </div>
                        <!-- /.post-footer -->
                        </article>
                        <!-- /.post -->
                    </div>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
    </section>
</div>
