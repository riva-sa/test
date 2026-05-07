<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إرسال الإشعارات</h1>
            <p class="text-sm text-gray-500 mt-1">أرسل إشعارات فردية أو جماعية للفريق</p>
        </div>
        <a href="{{ route('manager.announcements') }}"
           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fas fa-inbox"></i>
            صندوق الوارد
        </a>
    </div>

    @if (session('success'))
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-800 text-sm">
            <i class="fas fa-check-circle text-green-500"></i>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Compose Form --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">إنشاء إشعار جديد</h2>
            </div>
            <div class="p-6 space-y-5">
                {{-- Type Selector --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع الإشعار</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach(['individual' => ['label' => 'فردي', 'icon' => 'fa-user'], 'group' => ['label' => 'مجموعة', 'icon' => 'fa-users'], 'announcement' => ['label' => 'إعلان عام', 'icon' => 'fa-bullhorn'], 'task' => ['label' => 'مهمة', 'icon' => 'fa-tasks']] as $value => $meta)
                            <button wire:click="$set('type', '{{ $value }}')" type="button"
                                class="flex flex-col items-center gap-1.5 px-3 py-3 rounded-lg border text-xs font-medium transition-all
                                    {{ $type === $value ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                                <i class="fas {{ $meta['icon'] }} text-base"></i>
                                {{ $meta['label'] }}
                            </button>
                        @endforeach
                    </div>
                    @error('type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Individual recipient picker --}}
                @if($type === 'individual' || $type === 'task')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            المستلمون <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="selectedUserIds" multiple
                            class="custom-multiselect w-full border border-gray-200 rounded-lg text-sm">
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">اضغط مع الضغط على Ctrl لاختيار أكثر من شخص</p>
                        @error('selectedUserIds') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                @elseif($type === 'group')
                    <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg bg-blue-50 border border-blue-100 text-sm text-blue-700">
                        <i class="fas fa-info-circle"></i>
                        سيُرسل هذا الإشعار لجميع موظفي المبيعات النشطين
                    </div>
                @elseif($type === 'announcement')
                    <div class="flex items-center gap-2 px-3 py-2.5 rounded-lg bg-amber-50 border border-amber-100 text-sm text-amber-700">
                        <i class="fas fa-bullhorn"></i>
                        سيُرسل هذا الإشعار لجميع مستخدمي النظام
                    </div>
                @endif

                {{-- Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان الإشعار <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="title" type="text" placeholder="اكتب عنوان الإشعار..."
                        class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-gray-900/10 focus:border-gray-400 outline-none transition-all">
                    @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Rich Text Content --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        المحتوى <span class="text-red-500">*</span>
                    </label>
                    <div wire:ignore x-data="{}" @clear-trix.window="$refs.trixEditor.editor?.loadHTML('')">
                        <input id="trix-content-{{ $this->getId() }}" type="hidden" wire:model="content">
                        <trix-editor
                            x-ref="trixEditor"
                            input="trix-content-{{ $this->getId() }}"
                            x-on:trix-change="$wire.set('content', $event.target.value)"
                            class="trix-content border border-gray-200 rounded-lg min-h-[160px] text-sm"
                            placeholder="اكتب محتوى الإشعار هنا...">
                        </trix-editor>
                    </div>
                    @error('content') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Send Button --}}
                <div class="flex justify-end pt-2">
                    <button wire:click="send" wire:loading.attr="disabled"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 disabled:opacity-60 transition-all">
                        <span wire:loading.remove wire:target="send"><i class="fas fa-paper-plane"></i> إرسال</span>
                        <span wire:loading wire:target="send"><i class="fas fa-circle-notch fa-spin"></i> جاري الإرسال...</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Sent Notifications Log --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-base font-semibold text-gray-900">الإشعارات المُرسلة</h2>
            </div>
            <div class="divide-y divide-gray-100 max-h-[600px] overflow-y-auto">
                @forelse($sentNotifications as $notif)
                    <div x-data="{ open: false }" class="px-5 py-4">
                        <button @click="open = !open" class="w-full text-right">
                            <div class="flex justify-between items-start gap-2">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $notif->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }} · {{ $notif->recipients->count() }} مستلم</p>
                                </div>
                                <span class="shrink-0 text-[10px] font-medium px-2 py-0.5 rounded-full
                                    {{ match($notif->type) {
                                        'group'        => 'bg-blue-50 text-blue-700',
                                        'announcement' => 'bg-amber-50 text-amber-700',
                                        'task'         => 'bg-purple-50 text-purple-700',
                                        default        => 'bg-gray-100 text-gray-600',
                                    } }}">
                                    {{ match($notif->type) { 'group' => 'مجموعة', 'announcement' => 'إعلان', 'task' => 'مهمة', default => 'فردي' } }}
                                </span>
                            </div>
                        </button>
                        <div x-show="open" x-collapse class="mt-3 space-y-1">
                            @foreach($notif->recipients->take(8) as $recipient)
                                <div class="flex items-center justify-between text-xs py-1">
                                    <span class="text-gray-700">{{ $recipient->user?->name ?? 'مستخدم محذوف' }}</span>
                                    @if($recipient->read_at)
                                        <span class="text-green-600 flex items-center gap-1"><i class="fas fa-check-double text-[10px]"></i> مقروء</span>
                                    @else
                                        <span class="text-gray-400">غير مقروء</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($notif->recipients->count() > 8)
                                <p class="text-xs text-gray-400">و {{ $notif->recipients->count() - 8 }} آخرين...</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-10 text-center text-gray-400 text-sm">
                        <i class="fas fa-paper-plane text-2xl mb-2 block opacity-30"></i>
                        لم ترسل أي إشعارات بعد
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
