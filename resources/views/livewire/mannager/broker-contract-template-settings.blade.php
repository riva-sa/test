<div class="p-4 md:p-6 min-h-screen bg-gray-50/50">

    {{-- PDF.js loaded only as fallback when Ghostscript is unavailable.
         Self-hosted from /public so it works on hosts that block CDNs (e.g.
         Laravel Cloud) — and the bundled cMaps ensure Arabic renders correctly. --}}
    @if (empty($pageImages) && $tempPdfUrl)
    <div wire:ignore>
        <script src="{{ asset('vendor/pdfjs/pdf.min.js') }}"></script>
    </div>
    @endif

<div
     x-data="{
        activeField: '',
        fieldsConfig: @entangle('fieldsConfig'),
        pdfUrl:       @entangle('tempPdfUrl'),
        pageImages:   @entangle('pageImages'),

        initPdf() {
            if (!this.pdfUrl || this.pageImages.length > 0) return;
            // The pdf.js library is loaded async via a <script> tag; if it hasn't
            // finished loading yet, retry shortly instead of bailing out (which
            // previously left the preview area blank).
            if (typeof pdfjsLib === 'undefined') {
                setTimeout(() => this.initPdf(), 200);
                return;
            }

            pdfjsLib.GlobalWorkerOptions.workerSrc = '{{ asset('vendor/pdfjs/pdf.worker.min.js') }}';

            const container = document.getElementById('pdf-js-container');
            if (!container) return;
            container.innerHTML = '<div class=\'text-center py-12\'><i class=\'fas fa-circle-notch fa-spin text-3xl text-gray-300\'></i><p class=\'text-xs text-gray-400 mt-2\'>جاري تحميل صفحات العقد...</p></div>';

            const loadingTask = pdfjsLib.getDocument({
                url: this.pdfUrl,
                cMapUrl: '{{ asset('vendor/pdfjs/cmaps/') }}/',
                cMapPacked: true,
                standardFontDataUrl: '{{ asset('vendor/pdfjs/standard_fonts/') }}/',
                fontExtraProperties: true,
            });

            loadingTask.promise.then((pdf) => {
                container.innerHTML = '';
                for (let pageNum = 1; pageNum <= pdf.numPages; pageNum++) {
                    const pageWrapper = document.createElement('div');
                    pageWrapper.className = 'relative border border-gray-200 bg-white rounded-2xl shadow-sm overflow-hidden mb-6 mx-auto';
                    pageWrapper.style.maxWidth = '100%';
                    pageWrapper.style.width = '720px';
                    pageWrapper.id = 'page-wrapper-' + pageNum;

                    const canvas = document.createElement('canvas');
                    canvas.className = 'w-full h-auto block';
                    pageWrapper.appendChild(canvas);

                    const overlay = document.createElement('div');
                    overlay.className = 'absolute inset-0 cursor-crosshair select-none';
                    overlay.id = 'page-overlay-' + pageNum;
                    overlay.dataset.page = pageNum;
                    pageWrapper.appendChild(overlay);

                    container.appendChild(pageWrapper);

                    pdf.getPage(pageNum).then((page) => {
                        const viewport = page.getViewport({ scale: 1.5 });
                        const context  = canvas.getContext('2d');
                        canvas.height  = viewport.height;
                        canvas.width   = viewport.width;
                        page.render({ canvasContext: context, viewport: viewport, intent: 'print' })
                            .promise.then(() => this.drawMarkers());
                    });

                    overlay.addEventListener('click', (e) => {
                        if (!this.activeField) {
                            alert('يرجى تحديد حقل من القائمة الجانبية أولاً قبل الضغط على المستند.');
                            return;
                        }
                        const rect = overlay.getBoundingClientRect();
                        const x = ((e.clientX - rect.left) / rect.width)  * 100;
                        const y = ((e.clientY - rect.top)  / rect.height) * 100;
                        $wire.setFieldCoordinates(this.activeField, pageNum, x, y);
                    });
                }
            }).catch(() => {
                container.innerHTML = '<div class=\'text-center py-12 text-red-500\'><i class=\'fas fa-exclamation-triangle text-3xl\'></i><p class=\'text-xs mt-2\'>فشل تحميل ملف الـ PDF.</p></div>';
            });
        },

        drawMarkers() {
            document.querySelectorAll('.field-marker').forEach(el => el.remove());
            const config = this.fieldsConfig;
            if (!config) return;

            const labels = {
                'name':             'الاسم',
                'reference_number': 'رقم العضوية',
                'national_id':      'الهوية / السجل',
                'phone':            'الواتساب',
                'email':            'البريد الإلكتروني',
                'iban':             'الآيبان',
                'date':             'تاريخ الاعتماد',
                'signature':        'التوقيع'
            };

            Object.keys(config).forEach(key => {
                const field = config[key];
                if (!field || !field.page) return;

                const overlay = document.getElementById('page-overlay-' + field.page);
                if (!overlay) return;

                const marker = document.createElement('div');
                marker.className = 'field-marker absolute bg-zinc-900 border border-zinc-700 text-white px-2.5 py-1 rounded-lg text-[10px] font-black shadow-lg pointer-events-none transform -translate-x-1/2 -translate-y-1/2 flex items-center gap-1.5 z-10';
                marker.style.left = field.x + '%';
                marker.style.top  = field.y + '%';

                let icon = 'fa-font';
                if (key === 'signature') icon = 'fa-pen-fancy text-yellow-400';
                if (key === 'date')      icon = 'fa-calendar-alt text-indigo-400';

                marker.innerHTML = '<i class=\'fas ' + icon + '\'></i> ' + (labels[key] || key);
                overlay.appendChild(marker);
            });
        }
     }"
     x-init="
        $nextTick(() => {
            if (pageImages.length > 0) {
                drawMarkers();
            } else {
                initPdf();
            }
            $watch('pdfUrl',       ()     => { if (pageImages.length === 0) initPdf(); });
            $watch('pageImages',   (imgs) => { $nextTick(() => { if (imgs.length > 0) drawMarkers(); else initPdf(); }); });
            $watch('fieldsConfig', ()     => drawMarkers());
        });
     ">

    {{-- Breadcrumb / Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 font-bold mb-1">
                <a href="{{ route('manager.broker-applications') }}" class="hover:text-gray-900 transition-colors">طلبات الوسطاء</a>
                <i class="fas fa-chevron-left text-[9px]"></i>
                <span class="text-gray-900">قالب عقد الوساطة</span>
            </div>
            <h1 class="text-xl font-black text-gray-900">إعداد قالب عقد الوساطة</h1>
            <p class="text-xs text-gray-500 mt-1">قم برفع العقد الموحد للوساطة وتحديد أماكن تعبئة البيانات تلقائياً</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('manager.broker-applications') }}"
               class="px-4 py-2 border border-gray-200 text-gray-700 hover:bg-gray-50 text-xs font-black rounded-xl transition-all">
                إلغاء العودة
            </a>
            <button wire:click="saveSettings"
                    class="px-5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white text-xs font-black rounded-xl transition-all flex items-center gap-1.5">
                <i class="fas fa-save"></i> حفظ الإعدادات
            </button>
        </div>
    </div>

    @if (session('message'))
        <div class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 text-sm font-bold rounded-xl shadow-sm">
            {{ session('message') }}
        </div>
    @endif

    {{-- Main grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Sidebar Panel (Toolbox & Upload) --}}
        <div class="space-y-6 lg:col-span-1">

            {{-- Upload section --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100">
                <h3 class="text-xs font-black text-gray-800 mb-3 flex items-center gap-1.5">
                    <i class="fas fa-file-upload text-gray-400"></i> رفع ملف القالب
                </h3>
                <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 text-center hover:border-gray-400 transition-colors bg-gray-50/50">
                    <input type="file" wire:model="pdfFile" accept="application/pdf"
                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div class="space-y-1">
                        <i class="fas fa-file-pdf text-3xl text-gray-300"></i>
                        <div class="text-[11px] font-bold text-gray-600">اضغط لرفع ملف PDF</div>
                        <p class="text-[9px] text-gray-400">الحد الأقصى للحجم 10 ميجابايت</p>
                    </div>
                </div>
                @error('pdfFile')
                    <p class="text-xs text-red-600 font-bold mt-2">{{ $message }}</p>
                @enderror

                {{-- Rendering quality indicator --}}
                @if ($tempPdfUrl)
                    <div class="mt-3 flex items-center gap-1.5 text-[10px] font-bold {{ count($pageImages) > 0 ? 'text-green-600' : 'text-blue-600' }}">
                        <i class="fas {{ count($pageImages) > 0 ? 'fa-check-circle' : 'fa-eye' }}"></i>
                        @if (count($pageImages) > 0)
                            عرض عالي الجودة ({{ count($pageImages) }} {{ count($pageImages) === 1 ? 'صفحة' : 'صفحات' }})
                        @else
                            عرض داخل المتصفح
                        @endif
                    </div>
                @endif

                <div wire:loading wire:target="pdfFile" class="mt-3 flex items-center gap-1.5 text-[10px] text-indigo-600 font-bold">
                    <i class="fas fa-circle-notch fa-spin"></i> جاري معالجة الملف...
                </div>
            </div>

            {{-- Fields Selector Toolbox --}}
            <div class="bg-white p-5 rounded-2xl border border-gray-100 space-y-4">
                <div>
                    <h3 class="text-xs font-black text-gray-800 mb-1 flex items-center gap-1.5">
                        <i class="fas fa-map-marker-alt text-indigo-500"></i> حقول التعبئة التلقائية
                    </h3>
                    <p class="text-[10px] text-gray-400">اختر حقلاً ثم اضغط على موقع كتابته في المستند</p>
                </div>

                <div class="space-y-2">
                    @foreach ($mappableFields as $key => $label)
                        <div class="flex items-center justify-between p-2 rounded-xl border transition-all"
                             :class="activeField === '{{ $key }}' ? 'border-indigo-600 bg-indigo-50/30' : 'border-gray-100 hover:bg-gray-50/50'">

                            <button type="button" @click="activeField = '{{ $key }}'"
                                    class="flex-1 text-right text-xs font-bold flex items-center gap-2"
                                    :class="activeField === '{{ $key }}' ? 'text-indigo-900' : 'text-gray-700'">
                                <span class="h-2 w-2 rounded-full"
                                      :class="fieldsConfig['{{ $key }}'] ? 'bg-green-500' : 'bg-gray-300'"></span>
                                {{ $label }}
                            </button>

                            <div class="flex items-center gap-1.5">
                                @if (isset($fieldsConfig[$key]))
                                    <span class="text-[9px] font-bold bg-green-50 text-green-700 px-1.5 py-0.5 rounded-md border border-green-100">
                                        ص {{ $fieldsConfig[$key]['page'] }}
                                    </span>
                                    <button type="button" wire:click="clearField('{{ $key }}')"
                                            class="text-gray-300 hover:text-red-500 text-[10px] transition-colors p-1">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                @else
                                    <span class="text-[9px] font-bold bg-gray-50 text-gray-400 px-1.5 py-0.5 rounded-md border border-gray-100">
                                        غير محدد
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- PDF Preview & Click-Position Area --}}
        <div class="lg:col-span-3">
            <div class="bg-gray-100/50 rounded-2xl border border-gray-200/50 p-6 min-h-[60vh] relative overflow-hidden">

                @if (count($pageImages) > 0)
                    {{-- ═══ HIGH-QUALITY MODE: server-rendered PNG pages (Ghostscript) ═══
                         Arabic text renders perfectly. Each page is a transparent-overlay
                         PNG served by PHP. Overlay div captures click coordinates. --}}
                    <div class="w-full max-w-full space-y-6">
                        @foreach ($pageImages as $i => $src)
                            @php $p = $i + 1; @endphp
                            <div id="page-wrapper-{{ $p }}"
                                 class="relative border border-gray-200 bg-white rounded-2xl shadow-sm overflow-hidden mx-auto"
                                 style="max-width: 100%; width: 720px;">
                                <img src="{{ $src }}" class="w-full h-auto block select-none" draggable="false" />
                                <div id="page-overlay-{{ $p }}"
                                     data-page="{{ $p }}"
                                     class="absolute inset-0 cursor-crosshair select-none"
                                     @click="
                                         if (!activeField) {
                                             alert('يرجى تحديد حقل من القائمة الجانبية أولاً قبل الضغط على المستند.');
                                             return;
                                         }
                                         const rect = $el.getBoundingClientRect();
                                         const x = (($event.clientX - rect.left)  / rect.width)  * 100;
                                         const y = (($event.clientY - rect.top)   / rect.height) * 100;
                                         $wire.setFieldCoordinates(activeField, {{ $p }}, x, y);
                                     "></div>
                            </div>
                        @endforeach
                    </div>

                @elseif ($tempPdfUrl)
                    {{-- ═══ PDF.JS FALLBACK MODE (Ghostscript not installed) ═══
                         Arabic may not render correctly. Install Ghostscript to fix. --}}
                    <div id="pdf-js-container" class="w-full max-w-full" wire:ignore></div>

                @else
                    {{-- ═══ EMPTY STATE ═══ --}}
                    <div class="flex flex-col items-center justify-center min-h-[40vh] text-center p-8 max-w-sm mx-auto">
                        <div class="h-16 w-16 mx-auto rounded-2xl bg-white border border-gray-100 flex items-center justify-center shadow-sm mb-4">
                            <i class="fas fa-file-pdf text-3xl text-gray-300 animate-pulse"></i>
                        </div>
                        <h4 class="text-sm font-black text-gray-700">لم يتم رفع قالب عقد حتى الآن</h4>
                        <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                            قم برفع ملف PDF الخاص بعقد الوساطة لتبدأ بضبط الحقول.
                        </p>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>
</div>
