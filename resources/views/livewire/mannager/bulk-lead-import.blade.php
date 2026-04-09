<div class="p-4 sm:p-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">استيراد عملاء محتملين (Excel)</h1>
        <p class="text-sm text-gray-600 mb-6">
            الأعمدة المطلوبة في الصف الأول: <strong>Client Name</strong>، <strong>Client Phone Number</strong>، <strong>Project Name</strong>
            (يمكنك استخدام العناوين العربية المكافئة).
        </p>

        @if (session()->has('bulk_import_message'))
            <div class="mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
                {{ session('bulk_import_message') }}
            </div>
        @endif

        <form wire:submit.prevent="import" class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ملف Excel</label>
                <input type="file" wire:model="file" accept=".xlsx,.xls,.csv"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                @error('file') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                <div wire:loading wire:target="file" class="text-sm text-gray-500 mt-2">جاري تحميل الملف...</div>
            </div>

            <button type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 disabled:opacity-50">
                <span wire:loading.remove wire:target="import">معالجة وتوزيع</span>
                <span wire:loading wire:target="import">جاري المعالجة...</span>
            </button>
        </form>

        @if($lastResult)
            <div class="mt-8 bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">النتيجة</h2>
                <dl class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <dt class="text-xs text-gray-500">تم الإنشاء</dt>
                        <dd class="text-2xl font-bold text-gray-900">{{ $lastResult['imported'] ?? 0 }}</dd>
                    </div>
                    <div class="bg-amber-50 rounded-lg p-4">
                        <dt class="text-xs text-amber-800">تم التخطي</dt>
                        <dd class="text-2xl font-bold text-amber-900">{{ count($lastResult['skipped'] ?? []) }}</dd>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <dt class="text-xs text-red-800">أخطاء</dt>
                        <dd class="text-2xl font-bold text-red-900">{{ count($lastResult['failed'] ?? []) }}</dd>
                    </div>
                </dl>

                @if(!empty($lastResult['batch_id']))
                    <p class="text-xs text-gray-500 mb-4">معرف الدفعة: <code dir="ltr">{{ $lastResult['batch_id'] }}</code></p>
                @endif

                @if(!empty($lastResult['skipped']))
                    <h3 class="text-sm font-medium text-gray-700 mb-2">التخطي</h3>
                    <ul class="text-sm text-gray-600 space-y-1 max-h-40 overflow-y-auto mb-4">
                        @foreach($lastResult['skipped'] as $item)
                            <li>صف {{ $item['row'] ?? '—' }}: {{ $item['reason'] ?? '' }}</li>
                        @endforeach
                    </ul>
                @endif

                @if(!empty($lastResult['failed']))
                    <h3 class="text-sm font-medium text-gray-700 mb-2">الأخطاء</h3>
                    <ul class="text-sm text-red-700 space-y-1 max-h-40 overflow-y-auto">
                        @foreach($lastResult['failed'] as $item)
                            <li>صف {{ $item['row'] ?? '—' }}: {{ $item['reason'] ?? '' }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        @endif
    </div>
</div>
