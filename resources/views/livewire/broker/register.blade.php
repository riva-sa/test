<div class="min-h-screen flex items-center justify-center p-4 py-10">
    <div class="w-full max-w-2xl">
        <div class="text-center mb-8">
            <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-14 w-auto mx-auto mb-4" alt="Logo">
            <h1 class="text-2xl font-black text-gray-900">التسجيل كوسيط عقاري</h1>
            <p class="text-sm text-gray-500 mt-2">انضم لشبكة وسطاء ريفا العقارية وابدأ بإرسال عملائك</p>
        </div>

        {{-- Progress steps --}}
        @if ($step < 4)
            <div class="flex items-center justify-center gap-2 mb-8">
                @foreach ([1 => 'الحساب', 2 => 'البيانات', 3 => 'الوثائق'] as $s => $label)
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-2">
                            <span class="h-8 w-8 rounded-full flex items-center justify-center text-xs font-black
                                {{ $step > $s ? 'bg-green-500 text-white' : ($step == $s ? 'bg-gray-900 text-white' : 'bg-gray-200 text-gray-500') }}">
                                @if($step > $s) <i class="fas fa-check"></i> @else {{ $s }} @endif
                            </span>
                            <span class="text-xs font-bold {{ $step >= $s ? 'text-gray-900' : 'text-gray-400' }}">{{ $label }}</span>
                        </div>
                        @if ($s < 3)
                            <span class="w-8 h-0.5 {{ $step > $s ? 'bg-green-500' : 'bg-gray-200' }}"></span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">

            {{-- Step 1: Account type & credentials --}}
            @if ($step === 1)
                <h2 class="text-lg font-black text-gray-900 mb-6">إنشاء الحساب</h2>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button type="button" wire:click="$set('broker_type', 'individual')"
                            class="p-4 rounded-xl border-2 text-right transition-all {{ $broker_type === 'individual' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300' }}">
                        <i class="fas fa-user text-lg mb-2 {{ $broker_type === 'individual' ? 'text-gray-900' : 'text-gray-400' }}"></i>
                        <div class="text-sm font-black text-gray-900">وسيط فرد</div>
                        <div class="text-[11px] text-gray-500 mt-1">للوسطاء العقاريين الأفراد</div>
                    </button>
                    <div class="p-4 rounded-xl border-2 border-gray-100 bg-gray-50/50 text-right opacity-60 cursor-not-allowed relative">
                        <span class="absolute top-2 left-2 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-[9px] font-black rounded-full">قريباً</span>
                        <i class="fas fa-building text-lg mb-2 text-gray-300"></i>
                        <div class="text-sm font-black text-gray-400">شركة وساطة</div>
                        <div class="text-[11px] text-gray-400 mt-1">غير متاح حالياً</div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني <span class="text-red-500">*</span></label>
                        <input type="email" wire:model="email" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm" placeholder="example@email.com">
                        @error('email') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" wire:model="password" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm" placeholder="8 أحرف على الأقل">
                        @error('password') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">تأكيد كلمة المرور <span class="text-red-500">*</span></label>
                        <input type="password" wire:model="password_confirmation" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                    </div>
                </div>

                <button type="button" wire:click="nextStep" class="w-full mt-8 py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all">
                    التالي <i class="fas fa-arrow-left mr-2 text-xs"></i>
                </button>
            @endif

            {{-- Step 2: Profile information --}}
            @if ($step === 2)
                <h2 class="text-lg font-black text-gray-900 mb-6">استكمال البيانات</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">الاسم الكامل <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                        @error('name') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهوية / الإقامة <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="national_id" dir="ltr" inputmode="numeric" data-latin-digits class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm text-right">
                        @error('national_id') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الواتساب <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="whatsapp" dir="ltr" inputmode="numeric" data-latin-digits class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm text-right" placeholder="05xxxxxxxx">
                        @error('whatsapp') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">المدينة <span class="text-red-500">*</span></label>
                        <select wire:model="city" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                            <option value="">اختر المدينة</option>
                            @foreach ($cities as $cityName)
                                <option value="{{ $cityName }}">{{ $cityName }}</option>
                            @endforeach
                        </select>
                        @error('city') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الآيبان <span class="text-red-500">*</span></label>
                        <div class="flex items-stretch" dir="ltr">
                            <span class="shrink-0 inline-flex items-center px-4 rounded-r-none rounded-l-xl border border-l-0 border-gray-200 bg-gray-100 text-sm font-black text-gray-600">SA</span>
                            <input type="text" wire:model="iban_number" dir="ltr" inputmode="numeric" maxlength="22"
                                   data-latin-digits
                                   class="w-full px-4 py-3 rounded-l-none rounded-r-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm text-left tracking-wide"
                                   placeholder="22 رقماً">
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5">أدخل 22 رقماً بعد SA بالأرقام الإنجليزية فقط</p>
                        @error('iban_number') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">الحالة الوظيفية <span class="text-red-500">*</span></label>
                        <select wire:model="employment_status" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                            <option value="">اختر</option>
                            @foreach ($employmentStatuses as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('employment_status') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">كيف سمعت عنا؟ <span class="text-red-500">*</span></label>
                        <select wire:model="heard_about_us" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                            <option value="">اختر</option>
                            @foreach ($heardAboutUsOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('heard_about_us') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" wire:click="previousStep" class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-black rounded-xl transition-all">
                        <i class="fas fa-arrow-right ml-2 text-xs"></i> السابق
                    </button>
                    <button type="button" wire:click="nextStep" class="flex-1 py-3.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all">
                        التالي <i class="fas fa-arrow-left mr-2 text-xs"></i>
                    </button>
                </div>
            @endif

            {{-- Step 3: Documents --}}
            @if ($step === 3)
                <h2 class="text-lg font-black text-gray-900 mb-2">رفع الوثائق</h2>
                <p class="text-xs text-gray-500 mb-6">الملفات المقبولة: PDF أو صور (JPG / PNG) بحد أقصى 5 ميجا</p>

                <div class="space-y-5">
                    <div class="p-4 border border-gray-200 rounded-xl">
                        <label class="block text-sm font-bold text-gray-700 mb-2">الهوية الوطنية أو الإقامة <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="national_id_file" class="w-full text-sm" accept=".pdf,.jpg,.jpeg,.png">
                        <div wire:loading wire:target="national_id_file" class="text-xs text-gray-500 mt-1">جاري الرفع...</div>
                        @if ($national_id_file) <div class="text-xs text-green-600 font-bold mt-1"><i class="fas fa-check ml-1"></i> تم اختيار الملف</div> @endif
                        @error('national_id_file') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 border border-gray-200 rounded-xl">
                        <label class="block text-sm font-bold text-gray-700 mb-2">رخصة فال <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="fal_license_file" class="w-full text-sm" accept=".pdf,.jpg,.jpeg,.png">
                        <div wire:loading wire:target="fal_license_file" class="text-xs text-gray-500 mt-1">جاري الرفع...</div>
                        @if ($fal_license_file) <div class="text-xs text-green-600 font-bold mt-1"><i class="fas fa-check ml-1"></i> تم اختيار الملف</div> @endif
                        @error('fal_license_file') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="p-4 border border-gray-100 rounded-xl bg-gray-50/50">
                        <label class="block text-sm font-bold text-gray-700 mb-2">ملف الآيبان <span class="text-gray-400 text-xs font-medium">(اختياري)</span></label>
                        <input type="file" wire:model="iban_file" class="w-full text-sm" accept=".pdf,.jpg,.jpeg,.png">
                        <div wire:loading wire:target="iban_file" class="text-xs text-gray-500 mt-1">جاري الرفع...</div>
                        @if ($iban_file) <div class="text-xs text-green-600 font-bold mt-1"><i class="fas fa-check ml-1"></i> تم اختيار الملف</div> @endif
                        @error('iban_file') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex gap-3 mt-8">
                    <button type="button" wire:click="previousStep" class="px-6 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-black rounded-xl transition-all">
                        <i class="fas fa-arrow-right ml-2 text-xs"></i> السابق
                    </button>
                    <button type="button" wire:click="submit" wire:loading.attr="disabled" class="flex-1 py-3.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-sm font-black rounded-xl transition-all">
                        <span wire:loading.remove wire:target="submit">إرسال طلب التسجيل</span>
                        <span wire:loading wire:target="submit">جاري الإرسال...</span>
                    </button>
                </div>
            @endif

            {{-- Step 4: Pending review --}}
            @if ($step === 4)
                <div class="text-center py-8">
                    <div class="h-20 w-20 mx-auto rounded-full bg-yellow-50 border border-yellow-200 flex items-center justify-center mb-6">
                        <i class="fas fa-hourglass-half text-3xl text-yellow-500"></i>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 mb-3">تم استلام طلب التسجيل</h2>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-md mx-auto">
                        تم استلام طلب التسجيل وسيتم مراجعة البيانات من قبل الإدارة.
                        <br>
                        سيصلك إشعار عبر البريد الإلكتروني فور اعتماد حسابك.
                    </p>
                    <a href="{{ route('broker.login') }}" class="inline-block mt-8 px-8 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all">
                        العودة لتسجيل الدخول
                    </a>
                </div>
            @endif
        </div>

        @if ($step < 4)
            <p class="text-center text-sm text-gray-500 mt-6">
                لديك حساب بالفعل؟
                <a href="{{ route('broker.login') }}" class="font-black text-gray-900 hover:underline">تسجيل الدخول</a>
            </p>
        @endif
    </div>
</div>
