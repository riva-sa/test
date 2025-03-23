<div class="unit-sheet">
    <div class="side-sheet border {{ $showSideSheet ? 'active' : '' }}" style="max-width: 96% !important;height: fit-content !important;">
        @if($selectedUnit)
            <div class="d-lg-flex flex-row align-items-lg-center p-4">
                <a class="btn btn-circle btn-soft-primary closeSideSheet side-sheet-close" wire:click="closeSideSheet"><i class="uil uil-multiply"></i></a>
                <h6 class="mb-0"><i class="uil uil-wall fs-20 ms-1"></i> تفاصيل الوحدة</h6>
            </div>

            <section class="wrapper bg-light">
                <div class="container-fluid px-md-4">
                    <div class="d-flex gap-2">
                        <div class="w-100">
                            @if($currentStep == 1)
                            <figure class="card-img-top rounded" style="background-image: url('@if($selectedUnit->image) {{ asset('storage/' .$selectedUnit->image ) }} @else (){{ asset('storage/' .$selectedUnit->project->getMainImages()->media_url ) }} @endif');background-size:cover;height: 240px;">
                                {{-- <img src="{{ asset('storage/' .$selectedUnit->image ) }}" class="rounded w-100" style="max-height: 400px" alt="Riva - ريفا" /> --}}
                            </figure>
                            @endif
                            <!-- Step 1: Show Unit Data -->
                            @if($currentStep == 1)

                                <div class="post-header mb-5 mt-5">
                                    <h4 class="post-title"> {{ $selectedUnit->title }} <span class="badge bg-pale-ash text-dark rounded-pill">{{ $selectedUnit->unit_type }}</span> </h4>

                                    <!-- Unit Details -->
                                    <div class="p-2 shadow mt-2 rounded" style="background: #f1f1f1da !important;">
                                        <ul class="post-meta mb-0">
                                            <li class="post-date">
                                                <img src="{{ asset('frontend/img/icons/bathtub-01.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->bathrooms }}</span>
                                            </li>
                                            <li class="post-author">
                                                <img src="{{ asset('frontend/img/icons/bed.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->beadrooms }}</span>
                                            </li>
                                            <li class="post-comments">
                                                <img src="{{ asset('frontend/img/icons/move.png') }}" class="dark-image" style="width: 20px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->unit_area . ' م²' }}</span>
                                            </li>
                                            <li class="post-comments">
                                                <img src="{{ asset('frontend/img/icons/pan-03(1).png') }}" class="dark-image" style="width: 17px;" alt="Riva - ريفا">
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->kitchen}}</span>
                                            </li>
                                            <li class="post-comments">
                                                <span class="text-dark fs-15">الدور :</span>
                                                <span class="me-1 fs-15 text-gray-800">{{ $selectedUnit->floor}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <p class="mt-2">
                                        {!! Str::limit(strip_tags($selectedUnit->description), 150) !!}
                                    </p>
                                    <div class="mb-3">
                                        <div class="text-right">
                                            <h2 class="text-uppercase fs-20 mb-3">المميزات</h2>
                                        </div>
                                        <div class="d-flex gap-4">
                                            @foreach ($selectedUnit->features as $features)
                                                <div class="d-flex flex-row" style="flex-wrap: wrap;" wire:key="{{$features->id}}">
                                                    <div>
                                                        <img src="{{ asset('storage/' . $features->icon) }}" style="width:50px" class="text-purple" alt="Riva - ريفا" />
                                                    </div>
                                                    <div class="me-2">
                                                        <h4 class="mb-0 fs-15">{{$features->name}}</h4>
                                                        <p class="mb-0 fs-14">{{ $features->description }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                                <hr style="margin: 10px;">
                                @if ($selectedUnit->show_price)
                                    <h3 class="h3 mb-3 text-success px-2">
                                        <span class="text-muted small fs-15">السعر</span>
                                        {{ number_format($selectedUnit->unit_price) . ' ريال' }}
                                    </h3>
                                @endif
                                @if ($selectedUnit->case == 0)
                                    <div class="mb-4">
                                        <a wire:click="goToFormStep" class="btn btn-primary btn-icon btn-sm btn-icon-start rounded w-100">تسجيل اهتمام بالوحدة <i class="uil uil-fire"></i></a>
                                    </div>
                                @else
                                    <div class="mb-4">
                                        <a class="btn btn-soft-orange btn-icon btn-sm btn-icon-start rounded w-100 disabled" disabled>
                                            @if ($selectedUnit->case == 0)
                                                الوحدة محجوزة
                                            @else
                                                الوحدة مباعة
                                            @endif
                                        </a>
                                    </div>
                                @endif

                            @endif

                            <!-- Step 2: Show Interest Form -->
                            @if($currentStep == 2)
                                <div class="post-header mb-5 mt-5">
                                    <h4 class="post-title">تقديم اهتمام لشراء الوحدة</h4>
                                    <form wire:submit.prevent="submitInterest">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label text-gray-900">اسمك</label>
                                                <input type="text"
                                                       id="name"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       wire:model="name">
                                                @error('name')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label text-gray-900">بريد الكتروني</label>
                                                <input type="email"
                                                       id="email"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       wire:model="email">
                                                @error('email')
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="phone" class="form-label text-gray-900">رقم الهاتف</label>
                                            <input type="text"
                                                   id="phone"
                                                   class="form-control @error('phone') is-invalid @enderror"
                                                   wire:model="phone">
                                            @error('phone')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        <div class="row mb-6">
                                            <div class="col-12 mb-3">
                                                <label class="form-label text-gray-900 mb-3">طريقة الشراء</label>
                                                <div class="custom-radio-group">
                                                    @foreach($purchaseTypes as $value => $label)
                                                        <div class="custom-radio-item">
                                                            <input type="radio"
                                                                   name="purchaseType"
                                                                   id="purchaseType_{{ $value }}"
                                                                   value="{{ $value }}"
                                                                   wire:model="purchaseType"
                                                                   class="custom-radio-input">
                                                            <label for="purchaseType_{{ $value }}" class="custom-radio-label">
                                                                <span class="radio-icon"></span>
                                                                <span class="radio-text">{{ $label }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('purchaseType')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label text-gray-900 mb-3">الغرض من الشراء</label>
                                                <div class="custom-radio-group">
                                                    @foreach($purchasePurposes as $value => $label)
                                                        <div class="custom-radio-item">
                                                            <input type="radio"
                                                                   name="purchasePurpose"
                                                                   id="purchasePurpose_{{ $value }}"
                                                                   value="{{ $value }}"
                                                                   wire:model="purchasePurpose"
                                                                   class="custom-radio-input">
                                                            <label for="purchasePurpose_{{ $value }}" class="custom-radio-label">
                                                                <span class="radio-icon"></span>
                                                                <span class="radio-text">{{ $label }}</span>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                @error('purchasePurpose')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="mb-5">
                                            <label for="message" class="form-label text-gray-900">رسالة</label>
                                            <textarea id="message"
                                                      class="form-control @error('message') is-invalid @enderror"
                                                      wire:model="message"></textarea>
                                            @error('message')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div> --}}

                                        <button type="submit" class="btn btn-primary btn-icon btn-sm btn-icon-start rounded w-100" wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="submitInterest">
                                                إرسال
                                            </span>
                                            <div wire:loading wire:target="submitInterest">
                                                <div class="spinner-border spinner-border-sm me-4 mb-2" role="status">
                                                    <span class="visually-hidden">جاري الإرسال...</span>
                                                </div>
                                                جاري الإرسال...
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </section>
        @endif

    </div>

    <div class="side-sheet-overlay {{ $showSideSheet ? 'active' : '' }}" wire:click="closeSideSheet"></div>
    @if (session()->has('message'))
        <div class="alert alert-success">
            {{ session('message') }}
        </div>
    @endif

</div>
