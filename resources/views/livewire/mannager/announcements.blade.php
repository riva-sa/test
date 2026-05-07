<div class="p-6 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الإشعارات والإعلانات</h1>
            <p class="text-sm text-gray-500 mt-1">جميع الإشعارات الواردة إليك ({{ $totalCount }})</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="markAllRead"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-check-double"></i>
                تعليم الكل كمقروء
            </button>
            @if(auth()->user()->hasRole(['sales_manager', 'Admin']))
                <a href="{{ route('manager.notifications') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-900 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-paper-plane"></i>
                    إرسال إشعار
                </a>
            @endif
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($recipients as $recipient)
                @php $notif = $recipient->notification; @endphp
                <div
                    wire:click="markRead({{ $notif->id }})"
                    class="px-6 py-5 cursor-pointer transition-colors {{ $recipient->read_at ? 'bg-white' : 'bg-blue-50/40' }} hover:bg-gray-50">
                    <div class="flex items-start gap-4">
                        {{-- Unread dot --}}
                        <div class="mt-1.5 shrink-0">
                            @if(!$recipient->read_at)
                                <span class="block h-2.5 w-2.5 rounded-full bg-blue-500"></span>
                            @else
                                <span class="block h-2.5 w-2.5 rounded-full bg-transparent border border-gray-200"></span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2">
                                    <h3 class="text-sm font-semibold text-gray-900 {{ !$recipient->read_at ? '' : 'font-medium' }}">
                                        {{ $notif->title }}
                                    </h3>
                                    <span class="text-[10px] font-medium px-2 py-0.5 rounded-full
                                        {{ match($notif->type) {
                                            'group'        => 'bg-blue-50 text-blue-700',
                                            'announcement' => 'bg-amber-50 text-amber-700',
                                            'task'         => 'bg-purple-50 text-purple-700',
                                            default        => 'bg-gray-100 text-gray-600',
                                        } }}">
                                        {{ match($notif->type) { 'group' => 'مجموعة', 'announcement' => 'إعلان', 'task' => 'مهمة', default => 'فردي' } }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-400 shrink-0">{{ $notif->created_at->diffForHumans() }}</span>
                            </div>

                            {{-- Rich text body --}}
                            <div class="prose prose-sm max-w-none text-gray-600 trix-content"
                                 x-data="{ expanded: false }"
                                 @click.stop>
                                <div :class="expanded ? '' : 'line-clamp-3'">
                                    {!! $notif->content !!}
                                </div>
                                @if(strlen(strip_tags($notif->content)) > 200)
                                    <button @click="expanded = !expanded"
                                        class="mt-1 text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        <span x-text="expanded ? 'عرض أقل' : 'عرض المزيد'"></span>
                                    </button>
                                @endif
                            </div>

                            <p class="mt-2 text-xs text-gray-400">
                                من: <span class="font-medium text-gray-600">{{ $notif->sender?->name ?? 'النظام' }}</span>
                                @if($recipient->read_at)
                                    · <span class="text-green-600"><i class="fas fa-check-double text-[10px]"></i> مقروء {{ $recipient->read_at->diffForHumans() }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-16 text-center">
                    <i class="fas fa-bell-slash text-4xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">لا توجد إشعارات بعد</p>
                </div>
            @endforelse
        </div>

        {{-- Load More --}}
        @if($hasMore)
            <div class="px-6 py-4 border-t border-gray-100 text-center">
                <button wire:click="loadMore" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium text-gray-700 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors">
                    <span wire:loading.remove wire:target="loadMore"><i class="fas fa-chevron-down"></i> تحميل المزيد</span>
                    <span wire:loading wire:target="loadMore"><i class="fas fa-circle-notch fa-spin"></i> جاري التحميل...</span>
                </button>
            </div>
        @endif
    </div>
</div>
