<div class="unit-sheet">
    <div class="side-sheet border {{ $showOrderSheet ? 'active' : '' }}" style="max-width: 91% !important;max-height: 92vh;overflow-y: scroll;">
        <div class="d-lg-flex flex-row align-items-lg-center p-4">
            <a class="btn btn-circle btn-soft-primary closeSideSheet side-sheet-close" wire:click="closeSideSheet"><i class="uil uil-multiply"></i></a>
            <h6 class="mb-0"> تقديم اهتمام لشراء الوحدة </h6>
        </div>

        <section class="wrapper bg-light">
            <div class="container-fluid px-md-4">
                <div class="d-flex gap-2">
                    <div class="w-100">
                        <div class="post-header mb-5 mt-md-5">
                            <form wire:submit.prevent="submitOrderUnit">
                                <div class="form-select-wrapper mb-4">
                                    <label for="unit_id" class="form-label text-gray-900">اختر الوحدة</label>
                                    <select wire:model="unit_id" class="form-select" id="unit_id">
                                        <option selected>اختر الوحدة</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->title . ' ' . $unit->building_number . '-' .  $unit->unit_number }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6 mb-4">
                                        <label for="firstName" class="form-label text-gray-900">اسمك الاول</label>
                                        <input type="text"
                                                id="firstName"
                                                class="form-control @error('firstName') is-invalid @enderror"
                                                wire:model="firstName">
                                        @error('firstName')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-6 mb-4">
                                        <label for="lastName" class="form-label text-gray-900">الاسم الاخير</label>
                                        <input type="text"
                                                id="lastName"
                                                class="form-control @error('lastName') is-invalid @enderror"
                                                wire:model="lastName">
                                        @error('lastName')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label text-gray-900">رقم الهاتف</label>
                                        {{-- <input type="text"
                                                id="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                wire:model="phone"> --}}
                                            <div class="input-group" dir="rtl">
                                                <input type="text"
                                                    id="phone"
                                                    name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    wire:model="phone"
                                                    placeholder="5xxxxxxxx"
                                                    maxlength="9"
                                                    pattern="5[0-9]{8}"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^[^5]/, '5')">
                                                <span class="input-group-text">+966</span>

                                            </div>
                                            <small class="text-muted">أدخل رقم الجوال بدون رمز الدولة (يبدأ بـ 5)</small>
                                        @error('phone')
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
                                                            wire:model.live="purchaseType"
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

                                    @if($purchaseType === 'bank')
                                        <div class="col-12 mb-3">
                                            <label for="support_type" class="form-label text-gray-900">نوع الدعم</label>
                                            <select wire:model="support_type" class="form-select" id="support_type">
                                                <option value="مدعوم">مدعوم</option>
                                                <option value="غير مدعوم">غير مدعوم</option>
                                            </select>
                                            @error('support_type')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    @endif

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
                    </div>
                </div>
            </div>
        </section>

    </div>
    <div class="side-sheet-overlay {{ $showOrderSheet ? 'active' : '' }}" wire:click="closeSideSheet"></div>
</div>
