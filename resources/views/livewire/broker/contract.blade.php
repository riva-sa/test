<div class="min-h-screen flex items-center justify-center p-4 py-10 bg-gray-50/60">
    <div class="w-full max-w-2xl">

        {{-- Logo + heading --}}
        <div class="text-center mb-8">
            <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-14 w-auto mx-auto mb-4" alt="Logo">
            <h1 class="text-2xl font-black text-gray-900">اعتماد عقد الوساطة</h1>
            <p class="text-sm text-gray-500 mt-2">راجع العقد المُعدّ خصيصاً لك، ووقّعه لتفعيل حسابك</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">

            {{-- Flash messages --}}
            @if (session('error'))
                <div class="mx-6 mt-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-bold rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            {{-- ══════════ Contract not ready yet ══════════ --}}
            @if (! $broker->contractSent())
                <div class="p-10 text-center">
                    <div class="h-20 w-20 mx-auto rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center mb-6">
                        <i class="fas fa-file-contract text-3xl text-blue-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 mb-3">تم اعتماد حسابك 🎉</h2>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-sm mx-auto">
                        جاري إعداد عقد الوساطة الخاص بك.<br>
                        ستتلقى إشعاراً عبر البريد الإلكتروني فور اكتمال العقد.
                    </p>
                </div>

            {{-- ══════════ Contract ready ══════════ --}}
            @else

                {{-- Broker info strip --}}
                <div class="px-6 pt-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100 mb-5">
                        <div class="text-center">
                            <div class="text-[9px] font-bold text-gray-400 uppercase mb-1">الاسم</div>
                            <div class="text-[12px] font-black text-gray-900 truncate">{{ $broker->name }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[9px] font-bold text-gray-400 uppercase mb-1">رقم العضوية</div>
                            <div class="text-[12px] font-black text-indigo-600">{{ $broker->reference_number }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[9px] font-bold text-gray-400 uppercase mb-1">رقم الهوية</div>
                            <div class="text-[12px] font-black text-gray-900">{{ $broker->national_id ?? '—' }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-[9px] font-bold text-gray-400 uppercase mb-1">المدينة</div>
                            <div class="text-[12px] font-black text-gray-900">{{ $broker->city ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Tab switcher (3 tabs) --}}
                <div class="px-6 mb-0">
                    <div class="flex bg-gray-100 p-1 rounded-xl gap-1">
                        <button type="button" wire:click="switchTab('view')"
                                class="flex-1 py-2 text-[11px] font-black rounded-lg transition-all
                                       {{ $activeTab === 'view' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            <i class="fas fa-eye ml-1"></i>عرض العقد
                        </button>
                        <button type="button" wire:click="switchTab('sign_online')"
                                class="flex-1 py-2 text-[11px] font-black rounded-lg transition-all
                                       {{ $activeTab === 'sign_online' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            <i class="fas fa-pen-nib ml-1"></i>توقيع إلكتروني
                        </button>
                        <button type="button" wire:click="switchTab('upload')"
                                class="flex-1 py-2 text-[11px] font-black rounded-lg transition-all
                                       {{ $activeTab === 'upload' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                            <i class="fas fa-upload ml-1"></i>رفع موقّع
                        </button>
                    </div>
                </div>

                {{-- ── Tab: View contract ── --}}
                @if ($activeTab === 'view')
                    <div class="p-6 space-y-4">
                        {{-- PDF preview iframe --}}
                        <div class="rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                            <iframe src="{{ route('broker.contract.view') }}#toolbar=0"
                                    class="w-full h-[62vh]"
                                    title="عقد الوساطة">
                            </iframe>
                        </div>

                        {{-- Download + proceed --}}
                        <div class="flex gap-3">
                            <button type="button" wire:click="downloadContract"
                                    class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-black rounded-xl transition-all">
                                <i class="fas fa-download text-gray-400"></i> تحميل العقد
                            </button>
                            <button type="button" wire:click="switchTab('sign_online')"
                                    class="flex-1 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-xs font-black rounded-xl transition-all">
                                التوقيع الإلكتروني <i class="fas fa-pen-nib mr-1"></i>
                            </button>
                            <button type="button" wire:click="switchTab('upload')"
                                    class="flex-1 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black rounded-xl transition-all">
                                رفع نسخة موقّعة <i class="fas fa-upload mr-1"></i>
                            </button>
                        </div>

                        {{-- Info banner for offline signing --}}
                        <div class="flex items-start gap-3 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                            <i class="fas fa-info-circle text-indigo-500 mt-0.5 flex-shrink-0"></i>
                            <p class="text-[11px] text-indigo-700 leading-relaxed">
                                يمكنك <strong>تحميل العقد</strong> وطباعته، وبعد التوقيع عليه يدوياً
                                قم برفعه مباشرةً عبر تبويب <strong>"رفع موقّع"</strong>، أو يمكنك التوقيع إلكترونياً الآن.
                            </p>
                        </div>
                    </div>
                @endif

                {{-- ── Tab: Sign Online ── --}}
                @if ($activeTab === 'sign_online')
                    <form wire:submit="submit" class="p-6 space-y-5">

                        {{-- Signature pad --}}
                        <div>
                            <label class="block text-sm font-black text-gray-800 mb-2">
                                توقيعك الإلكتروني <span class="text-red-500">*</span>
                            </label>
                            <p class="text-[11px] text-gray-400 mb-3">
                                ارسم توقيعك في المنطقة أدناه باستخدام الماوس أو إصبعك على الشاشة
                            </p>

                            <div x-data="signaturePad()"
                                 class="relative border-2 border-dashed border-gray-200 rounded-xl overflow-hidden bg-gray-50 hover:border-gray-400 transition-colors"
                                 style="height: 180px;">
                                <canvas x-ref="canvas"
                                        class="absolute inset-0 w-full h-full touch-none cursor-crosshair"
                                        style="touch-action: none;">
                                </canvas>
                                <div x-show="empty"
                                     class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none select-none">
                                    <i class="fas fa-pen-nib text-3xl text-gray-200 mb-2"></i>
                                    <span class="text-[11px] text-gray-300 font-bold">ارسم توقيعك هنا</span>
                                </div>

                                <button type="button" x-show="!empty" @click="clearPad()"
                                        class="absolute top-2 left-2 z-10 text-[11px] text-gray-400 hover:text-red-500 font-bold transition-colors flex items-center gap-1 bg-white/80 px-2 py-1 rounded-lg">
                                    <i class="fas fa-eraser"></i> مسح التوقيع
                                </button>
                            </div>

                            <div class="mt-2">
                                @error('signatureData')
                                    <p class="text-xs text-red-600 font-bold">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Agreement checkbox --}}
                        <label class="flex items-start gap-3 p-4 bg-gray-50 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-100/70 transition-colors">
                            <input type="checkbox" wire:model="agreed"
                                   class="mt-0.5 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                            <span class="text-[13px] text-gray-700 leading-relaxed">
                                أقر بأنني قرأت عقد الوساطة وفهمت جميع بنوده،
                                <strong>وأوافق عليه</strong>، وأن هذا التوقيع الإلكتروني يمثلني
                                قانونياً بما يساوي التوقيع الخطّي.
                            </span>
                        </label>
                        @error('agreed') <p class="text-xs text-red-600 font-bold -mt-2">{{ $message }}</p> @enderror

                        {{-- Submit --}}
                        <button type="submit" id="submitBtn"
                                wire:loading.attr="disabled"
                                class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-sm font-black rounded-xl transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="submit">
                                <i class="fas fa-check-circle ml-1"></i>
                                اعتماد العقد وتفعيل البوابة
                            </span>
                            <span wire:loading wire:target="submit" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                جاري الحفظ...
                            </span>
                        </button>

                        <button type="button" wire:click="switchTab('view')"
                                class="w-full py-2 text-xs font-bold text-gray-400 hover:text-gray-700 transition-colors">
                            <i class="fas fa-arrow-right ml-1"></i> العودة لمعاينة العقد
                        </button>
                    </form>
                @endif

                {{-- ── Tab: Upload Signed PDF ── --}}
                @if ($activeTab === 'upload')
                    <form wire:submit="submitUpload" class="p-6 space-y-5">

                        {{-- Step guide --}}
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="p-3 bg-blue-50 rounded-xl border border-blue-100">
                                <div class="h-8 w-8 rounded-full bg-blue-600 text-white text-xs font-black flex items-center justify-center mx-auto mb-2">1</div>
                                <p class="text-[10px] font-bold text-blue-800">قم بتحميل العقد وطباعته</p>
                            </div>
                            <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                                <div class="h-8 w-8 rounded-full bg-amber-500 text-white text-xs font-black flex items-center justify-center mx-auto mb-2">2</div>
                                <p class="text-[10px] font-bold text-amber-800">وقّعه يدوياً وامسحه ضوئياً</p>
                            </div>
                            <div class="p-3 bg-green-50 rounded-xl border border-green-100">
                                <div class="h-8 w-8 rounded-full bg-green-600 text-white text-xs font-black flex items-center justify-center mx-auto mb-2">3</div>
                                <p class="text-[10px] font-bold text-green-800">ارفعه هنا لتفعيل حسابك</p>
                            </div>
                        </div>

                        {{-- Download button --}}
                        <div class="flex items-center justify-center">
                            <button type="button" wire:click="downloadContract"
                                    class="flex items-center gap-2 px-5 py-2.5 border-2 border-dashed border-gray-300 hover:border-gray-500 text-gray-600 hover:text-gray-900 text-xs font-black rounded-xl transition-all">
                                <i class="fas fa-download text-indigo-500"></i>
                                تحميل نسخة العقد للتوقيع
                            </button>
                        </div>

                        {{-- File upload area --}}
                        <div>
                            <label class="block text-sm font-black text-gray-800 mb-2">
                                رفع نسخة العقد الموقّعة <span class="text-red-500">*</span>
                            </label>
                            <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-indigo-400 transition-colors bg-gray-50"
                                 :class="{ 'border-indigo-500 bg-indigo-50/30': $wire.signedPdfFile }">
                                <input type="file" wire:model="signedPdfFile" accept="application/pdf"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" id="signedPdfInput">

                                @if ($signedPdfFile)
                                    {{-- File selected state --}}
                                    <div class="space-y-2">
                                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                        <p class="text-[12px] font-black text-gray-900">{{ $signedPdfFile->getClientOriginalName() }}</p>
                                        <p class="text-[10px] text-gray-400">{{ round($signedPdfFile->getSize() / 1024, 1) }} كيلوبايت</p>
                                    </div>
                                @else
                                    {{-- Empty upload state --}}
                                    <div class="space-y-2">
                                        <i class="fas fa-file-upload text-3xl text-gray-300"></i>
                                        <p class="text-[12px] font-bold text-gray-600">اضغط لاختيار ملف PDF الموقّع</p>
                                        <p class="text-[10px] text-gray-400">بصيغة PDF فقط · الحد الأقصى 20 ميجابايت</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Upload loading --}}
                            <div wire:loading wire:target="signedPdfFile" class="flex items-center gap-2 mt-2">
                                <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                <span class="text-[11px] text-indigo-600 font-bold">جاري رفع الملف...</span>
                            </div>

                            @error('signedPdfFile')
                                <p class="text-xs text-red-600 font-bold mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Agreement checkbox for upload --}}
                        <label class="flex items-start gap-3 p-4 bg-gray-50 border border-gray-100 rounded-xl cursor-pointer hover:bg-gray-100/70 transition-colors">
                            <input type="checkbox" wire:model="uploadAgreed"
                                   class="mt-0.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="text-[13px] text-gray-700 leading-relaxed">
                                أقر بأن الملف المرفوع هو نسخة المستند الأصلي الموقّع من قِبَلي،
                                <strong>وأوافق</strong> على جميع بنود عقد الوساطة المُرسَل إليّ.
                            </span>
                        </label>
                        @error('uploadAgreed') <p class="text-xs text-red-600 font-bold -mt-2">{{ $message }}</p> @enderror

                        {{-- Submit --}}
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white text-sm font-black rounded-xl transition-all flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="submitUpload">
                                <i class="fas fa-paper-plane ml-1"></i>
                                إرسال العقد الموقّع وتفعيل الحساب
                            </span>
                            <span wire:loading wire:target="submitUpload" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                جاري الإرسال...
                            </span>
                        </button>

                        <button type="button" wire:click="switchTab('view')"
                                class="w-full py-2 text-xs font-bold text-gray-400 hover:text-gray-700 transition-colors">
                            <i class="fas fa-arrow-right ml-1"></i> العودة لمعاينة العقد
                        </button>
                    </form>
                @endif

            @endif

            {{-- Logout --}}
            <div class="px-6 pb-5 text-center">
                <form method="POST" action="{{ route('broker.logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-gray-300 hover:text-red-500 transition-colors">
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Signature Pad: library loaded once via @assets (survives Livewire morphs & wire:navigate). --}}
@assets
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script>
@endassets

{{-- Alpine component registered once; re-initialises reliably whenever the canvas enters the DOM. --}}
@script
<script>
    Alpine.data('signaturePad', () => ({
        pad: null,
        empty: true,

        init() {
            const canvas = this.$refs.canvas;
            if (!canvas || typeof SignaturePad === 'undefined') return;

            this.pad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(0,0,0,0)',
                penColor: '#111827',
                minWidth: 1.2,
                maxWidth: 3.0,
            });

            const resize = () => {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                const data  = this.pad.toData();
                canvas.width  = canvas.offsetWidth  * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
                this.pad.clear();
                if (data.length) this.pad.fromData(data);
            };
            // Wait a tick so the element has its final layout size before sizing the canvas.
            this.$nextTick(resize);
            this._resize = resize;
            window.addEventListener('resize', resize);

            this.pad.addEventListener('beginStroke', () => { this.empty = false; });
            this.pad.addEventListener('endStroke', () => {
                this.empty = this.pad.isEmpty();
                // Defer (no extra round-trip); value is synced with the next submit request.
                this.$wire.set('signatureData', this.pad.isEmpty() ? '' : this.pad.toDataURL('image/png'), false);
            });
        },

        clearPad() {
            if (this.pad) this.pad.clear();
            this.empty = true;
            this.$wire.set('signatureData', '', false);
        },

        destroy() {
            if (this._resize) window.removeEventListener('resize', this._resize);
        },
    }));
</script>
@endscript
