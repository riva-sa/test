<div class="antialiased text-slate-900 bg-white min-h-screen">
    {{-- Header --}}
    <div class="border-b border-slate-200 bg-white sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-semibold tracking-tight">مندوبي المبيعات</h1>
                    <p class="text-sm text-slate-500">إدارة حسابات الفريق ووضع التوفر.</p>
                </div>
                <button wire:click="startAdding" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 bg-slate-900 text-slate-50 shadow hover:bg-slate-900/90 h-9 px-4 py-2">
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    إضافة مندوب
                </button>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session()->has('status'))
            <div class="mb-6 p-4 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center">
                <svg class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Add / Edit Form (Dialog Style) --}}
        @if ($isAdding || $isEditing)
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-white/80 backdrop-blur-sm animate-in fade-in duration-200">
                <div class="w-full max-w-lg bg-white rounded-lg border border-slate-200 shadow-lg animate-in zoom-in-95 duration-200">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold tracking-tight">{{ $isAdding ? 'إضافة مندوب جديد' : 'تعديل بيانات المندوب' }}</h2>
                            <p class="text-sm text-slate-500">أدخل المعلومات المطلوبة أدناه.</p>
                        </div>
                        <button wire:click="{{ $isAdding ? 'cancelAdding' : 'cancelEditing' }}" class="rounded-sm opacity-70 transition-opacity hover:opacity-100 focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <form wire:submit.prevent="{{ $isAdding ? 'saveNewUser' : 'saveEdit('.$editingUser.')' }}" class="p-6 space-y-4">
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">الاسم الكامل</label>
                            <input type="text" wire:model.defer="{{ $isAdding ? 'newFields.name' : 'editFields.name' }}"
                                class="flex h-9 w-full rounded-md border border-slate-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:cursor-not-allowed disabled:opacity-50">
                            @error($isAdding ? 'newFields.name' : 'editFields.name') <p class="text-[0.8rem] font-medium text-destructive text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">البريد الإلكتروني</label>
                            <input type="email" wire:model.defer="{{ $isAdding ? 'newFields.email' : 'editFields.email' }}"
                                class="flex h-9 w-full rounded-md border border-slate-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:opacity-50">
                            @error($isAdding ? 'newFields.email' : 'editFields.email') <p class="text-[0.8rem] font-medium text-destructive text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">رقم الهاتف (اختياري)</label>
                            <input type="text" wire:model.defer="{{ $isAdding ? 'newFields.phone' : 'editFields.phone' }}"
                                class="flex h-9 w-full rounded-md border border-slate-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:opacity-50">
                        </div>

                        @if($isAdding)
                        <div class="space-y-2">
                            <label class="text-sm font-medium leading-none">كلمة المرور</label>
                            <input type="password" wire:model.defer="newFields.password"
                                class="flex h-9 w-full rounded-md border border-slate-200 bg-transparent px-3 py-1 text-sm shadow-sm transition-colors placeholder:text-slate-500 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-slate-950 disabled:opacity-50">
                            @error('newFields.password') <p class="text-[0.8rem] font-medium text-destructive text-red-600">{{ $message }}</p> @enderror
                        </div>
                        @else
                        {{-- Toggles for Editing --}}
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="flex flex-col space-y-3">
                                <label class="text-sm font-medium">حالة الحساب</label>
                                <button type="button" 
                                        dir="ltr"
                                        wire:click="$set('editFields.is_active', {{ $editFields['is_active'] ? 'false' : 'true' }})"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 {{ $editFields['is_active'] ? 'bg-slate-900' : 'bg-slate-200' }}">
                                    <span class="pointer-events-none block h-5 w-5 rounded-full bg-white shadow-lg ring-0 transition-transform {{ $editFields['is_active'] ? 'translate-x-[20px]' : 'translate-x-[2px]' }}"></span>
                                </button>
                                <span class="text-xs text-slate-500">{{ $editFields['is_active'] ? 'نشط' : 'معطل' }}</span>
                            </div>
                            <div class="flex flex-col space-y-3">
                                <label class="text-sm font-medium">وضع الإجازة</label>
                                <button type="button" 
                                        dir="ltr"
                                        wire:click="$set('editFields.on_vacation', {{ $editFields['on_vacation'] ? 'false' : 'true' }})"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-slate-950 focus-visible:ring-offset-2 {{ $editFields['on_vacation'] ? 'bg-slate-900' : 'bg-slate-200' }}">
                                    <span class="pointer-events-none block h-5 w-5 rounded-full bg-white shadow-lg ring-0 transition-transform {{ $editFields['on_vacation'] ? 'translate-x-[20px]' : 'translate-x-[2px]' }}"></span>
                                </button>
                                <span class="text-xs text-slate-500">{{ $editFields['on_vacation'] ? 'في إجازة' : 'متوفر' }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="pt-6 flex justify-end gap-2">
                            <button type="button" wire:click="{{ $isAdding ? 'cancelAdding' : 'cancelEditing' }}"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring border border-slate-200 bg-transparent shadow-sm hover:bg-slate-100 h-9 px-4 py-2">
                                إلغاء
                            </button>
                            <button type="submit"
                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-slate-900 text-slate-50 shadow hover:bg-slate-900/90 h-9 px-4 py-2">
                                {{ $isAdding ? 'إنشاء الحساب' : 'حفظ التغييرات' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Table Section --}}
        <div class="rounded-md border border-slate-200 overflow-hidden bg-white shadow-sm">
            <table class="w-full text-right text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="h-10 px-4 text-slate-500 font-medium text-xs">المندوب</th>
                        <th class="h-10 px-4 text-slate-500 font-medium text-xs">البريد</th>
                        <th class="h-10 px-4 text-slate-500 font-medium text-xs">الحالة</th>
                        <th class="h-10 px-4 text-slate-500 font-medium text-xs">التوفر</th>
                        <th class="h-10 px-4 text-slate-500 font-medium text-xs text-left">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse ($salesUsers as $user)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="p-4 align-middle font-medium">{{ $user->name }}</td>
                            <td class="p-4 align-middle text-slate-500">{{ $user->email }}</td>
                            <td class="p-4 align-middle">
                                @if ($user->is_active)
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-slate-900 text-slate-50 shadow">نشط</div>
                                @else
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-slate-100 text-slate-900">معطل</div>
                                @endif
                            </td>
                            <td class="p-4 align-middle">
                                @if ($user->on_vacation)
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-slate-200 text-slate-500">في إجازة</div>
                                @else
                                    <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-transparent bg-slate-100 text-slate-900">متوفر</div>
                                @endif
                            </td>
                            <td class="p-4 align-middle text-left">
                                <button wire:click="startEditing({{ $user->id }})" 
                                        class="inline-flex items-center justify-center rounded-md text-xs font-medium transition-colors border border-slate-200 hover:bg-slate-100 h-8 px-3">
                                    تعديل
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-slate-500 italic">لا يوجد مندوبي مبيعات حالياً.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
        .antialiased { -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
    </style>
</div>