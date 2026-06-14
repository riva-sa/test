<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-xl font-black text-gray-900">إرسال عميل جديد</h1>
        <p class="text-sm text-gray-500 mt-1">سيصل العميل مباشرة لفريق المبيعات وستتمكن من متابعة حالته أولاً بأول</p>
    </div>

    <form wire:submit="submit" class="space-y-6">

        {{-- بيانات العميل --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-900 mb-5"><span class="text-gray-300 ml-2">01</span> بيانات العميل</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">اسم العميل <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                    @error('name') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="phone" dir="ltr" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm text-right" placeholder="05xxxxxxxx">
                    @error('phone') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- بيانات الاهتمام --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-900 mb-5"><span class="text-gray-300 ml-2">02</span> المشاريع والوحدات محل الاهتمام</h2>

            <label class="block text-sm font-bold text-gray-700 mb-2">المشاريع <span class="text-red-500">*</span> <span class="text-gray-400 text-xs font-medium">(يمكن اختيار أكثر من مشروع)</span></label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-1 mb-2">
                @foreach ($projects as $project)
                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all {{ in_array((string) $project->id, array_map('strval', $selectedProjects)) ? 'border-gray-900 bg-gray-50' : 'border-gray-100 hover:border-gray-200' }}">
                        <input type="checkbox" wire:model.live="selectedProjects" value="{{ $project->id }}" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                        <span class="text-[13px] font-bold text-gray-800">{{ $project->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('selectedProjects') <p class="text-xs text-red-600 font-bold mb-3">{{ $message }}</p> @enderror

            @if (! empty($availableUnits))
                <label class="block text-sm font-bold text-gray-700 mb-2 mt-4">الوحدات <span class="text-gray-400 text-xs font-medium">(اختياري — يمكن اختيار أكثر من وحدة)</span></label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 max-h-52 overflow-y-auto p-1">
                    @foreach ($availableUnits as $unit)
                        <label class="flex items-center justify-between gap-3 p-3 rounded-xl border cursor-pointer transition-all {{ in_array((string) $unit['id'], array_map('strval', $selectedUnits)) ? 'border-gray-900 bg-gray-50' : 'border-gray-100 hover:border-gray-200' }}">
                            <div class="flex items-center gap-3">
                                <input type="checkbox" wire:model.live="selectedUnits" value="{{ $unit['id'] }}" class="rounded border-gray-300 text-gray-900 focus:ring-gray-900">
                                <div>
                                    <div class="text-[13px] font-bold text-gray-800">{{ $unit['title'] }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $unit['project']['name'] ?? '' }} · {{ $unit['unit_type'] }}</div>
                                </div>
                            </div>
                            @if ($unit['unit_price'])
                                <span class="text-[11px] font-black text-gray-600 whitespace-nowrap">{{ number_format((float) $unit['unit_price']) }} ر.س</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            @elseif (! empty($selectedProjects))
                <p class="text-xs text-gray-400 mt-2">لا توجد وحدات متاحة في المشاريع المختارة — سيتم تسجيل الاهتمام على مستوى المشروع.</p>
            @endif
        </div>

        {{-- بيانات الطلب --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h2 class="text-sm font-black text-gray-900 mb-5"><span class="text-gray-300 ml-2">03</span> بيانات الطلب</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع العقار <span class="text-red-500">*</span></label>
                    <select wire:model="property_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                        <option value="">اختر</option>
                        @foreach ($propertyTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('property_type') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">طريقة الشراء <span class="text-red-500">*</span></label>
                    <select wire:model.live="PurchaseType" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                        <option value="">اختر</option>
                        @foreach ($purchaseTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('PurchaseType') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الغرض من الشراء <span class="text-red-500">*</span></label>
                    <select wire:model="PurchasePurpose" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                        <option value="">اختر</option>
                        @foreach ($purchasePurposes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('PurchasePurpose') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع الدعم <span class="text-red-500">*</span></label>
                    <select wire:model="support_type" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                        <option value="">اختر</option>
                        @foreach ($supportTypes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('support_type') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">الميزانية <span class="text-gray-400 text-xs font-medium">(اختياري)</span></label>
                    <input type="text" wire:model="budget" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm" placeholder="مثال: 800,000 - 1,000,000">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">المدينة <span class="text-gray-400 text-xs font-medium">(اختياري)</span></label>
                    <input type="text" wire:model="city" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                </div>
                @if ($PurchaseType === 'bank')
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم البنك <span class="text-gray-400 text-xs font-medium">(اختياري)</span></label>
                        <input type="text" wire:model="bank_name" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm">
                    </div>
                @endif
                <div class="md:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">ملاحظات <span class="text-gray-400 text-xs font-medium">(اختياري)</span></label>
                    <textarea wire:model="message" rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-sm" placeholder="أي تفاصيل إضافية عن احتياج العميل..."></textarea>
                    @error('message') <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-4 bg-gray-900 hover:bg-gray-800 disabled:opacity-50 text-white text-sm font-black rounded-2xl transition-all">
            <span wire:loading.remove wire:target="submit"><i class="fas fa-paper-plane ml-2"></i> إرسال العميل</span>
            <span wire:loading wire:target="submit">جاري الإرسال...</span>
        </button>
    </form>
</div>
