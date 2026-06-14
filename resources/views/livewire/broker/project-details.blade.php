<div>
    <a href="{{ route('broker.projects') }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-400 hover:text-gray-900 mb-4 transition-colors">
        <i class="fas fa-arrow-right"></i> العودة للمشاريع
    </a>

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden mb-6">
        @php
            $images = $project->projectMedia->where('media_type', 'image')->values();
        @endphp
        @if ($images->isNotEmpty())
            <div class="grid grid-cols-2 md:grid-cols-4 gap-1 h-56">
                @foreach ($images->take(4) as $i => $media)
                    <div class="{{ $i === 0 ? 'col-span-2 row-span-2' : '' }} bg-gray-100 overflow-hidden {{ $i > 0 && $loop->remaining >= 0 ? '' : '' }}">
                        <img src="{{ \App\Helpers\MediaHelper::getUrl($media->media_url) }}" class="w-full h-full object-cover" alt="{{ $project->name }}">
                    </div>
                @endforeach
            </div>
        @endif
        <div class="p-6">
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div>
                    <h1 class="text-xl font-black text-gray-900">{{ $project->name }}</h1>
                    <div class="flex flex-wrap items-center gap-4 mt-2 text-[12px] text-gray-500 font-bold">
                        <span><i class="fas fa-location-dot ml-1 text-gray-300"></i>{{ $project->city->name ?? '—' }} {{ $project->state?->name ? '· '.$project->state->name : '' }}</span>
                        <span><i class="fas fa-helmet-safety ml-1 text-gray-300"></i>{{ $project->developer->name ?? '—' }}</span>
                        @if ($project->projectType)
                            <span><i class="fas fa-tag ml-1 text-gray-300"></i>{{ $project->projectType->name }}</span>
                        @endif
                        @if ($project->AdLicense)
                            <span><i class="fas fa-certificate ml-1 text-gray-300"></i>رخصة إعلان: {{ $project->AdLicense }}</span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('broker.leads.create', ['project' => $project->id]) }}"
                   class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white text-sm font-black rounded-xl transition-all whitespace-nowrap">
                    <i class="fas fa-user-plus ml-2"></i> إرسال عميل لهذا المشروع
                </a>
            </div>

            @if ($project->description)
                <p class="text-sm text-gray-600 leading-relaxed mt-4">{!! nl2br(e(strip_tags($project->description))) !!}</p>
            @endif

            {{-- Features & Guarantees --}}
            @if ($project->features->isNotEmpty() || $project->guarantees->isNotEmpty())
                <div class="flex flex-wrap gap-2 mt-5">
                    @foreach ($project->features as $feature)
                        <span class="px-3 py-1.5 bg-gray-50 border border-gray-100 text-gray-600 text-[11px] font-bold rounded-full">{{ $feature->name }}</span>
                    @endforeach
                    @foreach ($project->guarantees as $guarantee)
                        <span class="px-3 py-1.5 bg-green-50 border border-green-100 text-green-700 text-[11px] font-bold rounded-full">
                            <i class="fas fa-shield-halved ml-1"></i>{{ $guarantee->name }}
                        </span>
                    @endforeach
                </div>
            @endif

            {{-- Attachments --}}
            @php
                $attachments = $project->projectMedia->whereIn('media_type', ['pdf', 'file', 'brochure'])->values();
            @endphp
            @if ($attachments->isNotEmpty())
                <div class="mt-5 pt-5 border-t border-gray-50">
                    <div class="text-[11px] font-black text-gray-400 uppercase mb-3">المرفقات</div>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($attachments as $attachment)
                            <a href="{{ \App\Helpers\MediaHelper::getUrl($attachment->media_url) }}" target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-100 text-gray-700 text-[12px] font-bold rounded-xl transition-all">
                                <i class="fas fa-file-pdf text-red-400"></i> تحميل المرفق
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Units --}}
    <div class="bg-white rounded-2xl border border-gray-100 p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
            <h2 class="text-base font-black text-gray-900">الوحدات</h2>
            <div class="flex flex-wrap gap-2">
                <select wire:model.live="unitTypeFilter" class="px-3 py-2 rounded-xl border border-gray-200 focus:border-gray-900 focus:ring-0 text-xs font-bold">
                    <option value="">كل الأنواع</option>
                    @foreach ($unitTypes as $type)
                        <option value="{{ $type }}">{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($units as $unit)
                <div class="border border-gray-100 rounded-2xl overflow-hidden hover:border-gray-200 transition-all">
                    <div class="h-32 bg-gray-100 relative">
                        @if ($unit->image)
                            <img src="{{ \App\Helpers\MediaHelper::getUrl($unit->image) }}" class="w-full h-full object-cover" alt="{{ $unit->title }}">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300"><i class="fas fa-house text-2xl"></i></div>
                        @endif
                        <span class="absolute top-2 right-2 px-2.5 py-1 text-[10px] font-black rounded-full text-white
                            {{ $unit->case == 0 ? 'bg-green-500' : ($unit->case == 1 ? 'bg-yellow-500' : 'bg-red-500') }}">
                            {{ $unit->case == 0 ? 'متاحة' : ($unit->case == 1 ? 'محجوزة' : 'مباعة') }}
                        </span>
                    </div>
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-[13px] font-black text-gray-900">{{ $unit->title }}</h3>
                            <span class="text-[10px] font-bold text-gray-400">{{ $unit->unit_type }}</span>
                        </div>
                        <div class="flex items-center gap-3 text-[10px] text-gray-400 font-bold mb-3">
                            @if ($unit->unit_area) <span><i class="fas fa-ruler-combined ml-1"></i>{{ $unit->unit_area }} م²</span> @endif
                            @if ($unit->beadrooms) <span><i class="fas fa-bed ml-1"></i>{{ $unit->beadrooms }}</span> @endif
                            @if ($unit->bathrooms) <span><i class="fas fa-bath ml-1"></i>{{ $unit->bathrooms }}</span> @endif
                            @if ($unit->floor) <span><i class="fas fa-stairs ml-1"></i>دور {{ $unit->floor }}</span> @endif
                        </div>
                        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                            @if ($unit->show_price && $unit->unit_price)
                                <span class="text-[14px] font-black text-gray-900">{{ number_format((float) $unit->unit_price) }} ر.س</span>
                            @else
                                <span class="text-[11px] font-bold text-gray-400">السعر عند الطلب</span>
                            @endif
                            @if ($unit->case == 0)
                                <a href="{{ route('broker.leads.create', ['project' => $project->id, 'unit' => $unit->id]) }}"
                                   class="px-3 py-1.5 bg-gray-900 hover:bg-gray-800 text-white text-[10px] font-black rounded-lg transition-all">
                                    إرسال عميل
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-10 text-center text-sm text-gray-400">لا توجد وحدات مطابقة</div>
            @endforelse
        </div>

        <div class="mt-5">
            {{ $units->links() }}
        </div>
    </div>
</div>
