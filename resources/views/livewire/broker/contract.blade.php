<div class="min-h-screen flex items-center justify-center p-4 py-10">
    <div class="w-full max-w-xl">
        <div class="text-center mb-8">
            <img src="{{ asset('frontend/img/logoyy.png') }}" class="h-14 w-auto mx-auto mb-4" alt="Logo">
            <h1 class="text-2xl font-black text-gray-900">اعتماد عقد الوساطة</h1>
            <p class="text-sm text-gray-500 mt-2">خطوة أخيرة قبل تفعيل بوابتك — راجع العقد ووقّعه وارفع النسخة الموقعة</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            @if (session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-bold rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            @if (! $broker->contractSent())
                {{-- العقد لم يُرسل بعد --}}
                <div class="text-center py-8">
                    <div class="h-20 w-20 mx-auto rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center mb-6">
                        <i class="fas fa-file-contract text-3xl text-blue-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 mb-3">تم اعتماد حسابك 🎉</h2>
                    <p class="text-sm text-gray-500 leading-relaxed max-w-sm mx-auto">
                        جاري تجهيز عقد الوساطة الخاص بك من قبل الإدارة.
                        <br>
                        سيصلك إشعار عبر البريد الإلكتروني فور إرسال العقد لتوقيعه.
                    </p>
                </div>
            @else
                {{-- تحميل العقد --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-red-50 flex items-center justify-center">
                                <i class="fas fa-file-pdf text-red-500"></i>
                            </div>
                            <div>
                                <div class="text-[13px] font-black text-gray-900">عقد الوساطة</div>
                                <div class="text-[10px] text-gray-400">أُرسل بتاريخ {{ $broker->contract_sent_at->format('Y-m-d') }}</div>
                            </div>
                        </div>
                        <button type="button" wire:click="downloadContract" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-[11px] font-black rounded-lg transition-all">
                            <i class="fas fa-download ml-1"></i> تحميل العقد
                        </button>
                    </div>
                    <p class="text-[11px] text-gray-400 mt-2 leading-relaxed">
                        راجع العقد بالأسفل أو حمّله، وقّعه، ثم ارفع النسخة الموقعة بصيغة PDF.
                    </p>

                    {{-- معاينة العقد داخل الصفحة --}}
                    <div class="mt-4 rounded-xl overflow-hidden border border-gray-100 bg-gray-50">
                        <iframe src="{{ route('broker.contract.view') }}#toolbar=0" class="w-full h-[55vh]" title="عقد الوساطة"></iframe>
                    </div>
                </div>

                <form wire:submit="submit" class="space-y-5">
                    <div class="p-4 border border-gray-200 rounded-xl">
                        <label class="block text-sm font-bold text-gray-700 mb-2">النسخة الموقعة من العقد <span class="text-red-500">*</span></label>
                        <input type="file" wire:model="signed_contract" accept=".pdf" class="w-full text-sm">
                        <div wire:loading wire:target="signed_contract" class="text-xs text-gray-500 mt-1">جاري الرفع...</div>
                        @if ($signed_contract) <div class="text-xs text-green-600 font-bold mt-1"><i class="fas fa-check ml-1"></i> تم اختيار الملف</div> @endif
                        @error('signed_contract') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <label class="flex items-start gap-3 p-4 bg-gray-50 rounded-xl cursor-pointer">
                        <input type="checkbox" wire:model="agreed" class="mt-0.5 rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        <span class="text-[13px] text-gray-700 leading-relaxed">
                            أقر بأنني قرأت عقد الوساطة وفهمت جميع بنوده، <strong>وأوافق عليه</strong>، وأن النسخة المرفوعة موقعة مني.
                        </span>
                    </label>
                    @error('agreed') <p class="text-xs text-red-600 font-bold">{{ $message }}</p> @enderror

                    <button type="submit" wire:loading.attr="disabled"
                            class="w-full py-3.5 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-sm font-black rounded-xl transition-all">
                        <span wire:loading.remove wire:target="submit">اعتماد العقد وتفعيل البوابة</span>
                        <span wire:loading wire:target="submit">جاري الاعتماد...</span>
                    </button>
                </form>
            @endif

            <form method="POST" action="{{ route('broker.logout') }}" class="mt-6 text-center">
                @csrf
                <button type="submit" class="text-xs font-bold text-gray-400 hover:text-red-600 transition-colors">تسجيل الخروج</button>
            </form>
        </div>
    </div>
</div>
