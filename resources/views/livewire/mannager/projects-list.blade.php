<div class="min-h-full bg-gray-50 p-4" x-data="{ unitModalOpen: false, selectedUnit: null }">
    <div class="max-w-7xl mx-auto space-y-4">
        
        <!-- Header, Search, & Filters -->
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 space-y-3">
            <!-- Top row: Title + Search -->
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    المشاريع
                </h2>
                <div class="relative w-full sm:w-80">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="ابحث بالاسم، العنوان، أو رقم الوحدة..." class="w-full pl-10 pr-4 py-2 bg-gray-50 border-none rounded-xl focus:ring-1 focus:ring-gray-300 text-sm font-medium">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Primary Filters Row -->
            <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-gray-50">
                <select wire:model.live="city_id" class="bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-3">
                    <option value="">كل المدن</option>
                    @foreach($cities as $city)
                        <option value="{{ $city->id }}">{{ $city->name }}</option>
                    @endforeach
                </select>

                @if($states->count() > 0)
                <select wire:model.live="state_id" class="bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-3">
                    <option value="">كل الأحياء</option>
                    @foreach($states as $state)
                        <option value="{{ $state->id }}">{{ $state->name }}</option>
                    @endforeach
                </select>
                @endif
                
                <select wire:model.live="project_type_id" class="bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-3">
                    <option value="">نوع المشروع</option>
                    @foreach($projectTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
                
                <select wire:model.live="developer_id" class="bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-3">
                    <option value="">كل المطورين</option>
                    @foreach($developers as $developer)
                        <option value="{{ $developer->id }}">{{ $developer->name }}</option>
                    @endforeach
                </select>

                <select wire:model.live="sort_by" class="bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-3">
                    <option value="latest">الأحدث أولاً</option>
                    <option value="name_asc">الاسم (أ-ي)</option>
                    <option value="units_desc">الأكثر وحدات</option>
                    <option value="price_asc">السعر: من الأقل</option>
                    <option value="price_desc">السعر: من الأعلى</option>
                </select>

                <!-- Advanced Filters Toggle -->
                <button wire:click="toggleAdvancedFilters" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-bold transition-colors {{ $showAdvancedFilters ? 'bg-primary-50 border-primary-200 text-primary-700' : 'bg-white border-gray-200 text-gray-500 hover:bg-gray-50' }}">
                    <i class="fas fa-sliders-h text-[10px]"></i>
                    فلاتر متقدمة
                    @php
                        $activeCount = collect([
                            $sales_manager_id, $commission_type, $min_price, $max_price,
                            $min_area, $max_area, $bedrooms
                        ])->filter()->count() + ($has_available_units ? 1 : 0) + ($has_virtual_tour ? 1 : 0) + ($has_ad_license ? 1 : 0);
                    @endphp
                    @if($activeCount > 0)
                        <span class="bg-primary-600 text-white rounded-full w-4 h-4 flex items-center justify-center text-[9px] font-black">{{ $activeCount }}</span>
                    @endif
                </button>

                <!-- Reset Filters -->
                @if($search || $city_id || $state_id || $developer_id || $project_type_id || $commission_type || $sales_manager_id || $min_price || $max_price || $min_area || $max_area || $bedrooms || $has_available_units || $has_virtual_tour || $has_ad_license)
                <button wire:click="resetFilters" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-600 text-xs font-bold hover:bg-red-100 transition-colors">
                    <i class="fas fa-times text-[10px]"></i>
                    مسح الكل
                </button>
                @endif
            </div>

            <!-- Advanced Filters Panel (Collapsible) -->
            @if($showAdvancedFilters)
            <div class="pt-3 border-t border-gray-100 space-y-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                    <!-- Sales Manager -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">مدير المبيعات</label>
                        <select wire:model.live="sales_manager_id" class="w-full bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-2">
                            <option value="">الكل</option>
                            @foreach($salesManagers as $manager)
                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Min Price -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">السعر من (ر.س)</label>
                        <input type="number" wire:model.live.debounce.500ms="min_price" placeholder="0" class="w-full bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-2">
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">السعر إلى (ر.س)</label>
                        <input type="number" wire:model.live.debounce.500ms="max_price" placeholder="∞" class="w-full bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-2">
                    </div>

                    <!-- Min Area -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">المساحة من (م²)</label>
                        <input type="number" wire:model.live.debounce.500ms="min_area" placeholder="0" class="w-full bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-2">
                    </div>

                    <!-- Max Area -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1">المساحة إلى (م²)</label>
                        <input type="number" wire:model.live.debounce.500ms="max_area" placeholder="∞" class="w-full bg-white border border-gray-200 text-gray-600 rounded-lg text-xs font-bold focus:ring-0 focus:border-gray-300 py-1.5 px-2">
                    </div>
                </div>

                <!-- Bedrooms + Toggle Switches -->
                <div class="flex flex-wrap items-end gap-4">
                    <!-- Bedrooms -->
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 mb-1.5">عدد غرف النوم</label>
                        <div class="flex gap-1">
                            @foreach(['', '1', '2', '3', '4', '5+'] as $val)
                            <button wire:click="$set('bedrooms', '{{ $val }}')" class="px-2.5 py-1 rounded-lg text-xs font-bold border transition-colors {{ $bedrooms === $val ? 'bg-gray-900 text-white border-gray-900' : 'bg-white text-gray-500 border-gray-200 hover:bg-gray-50' }}">
                                {{ $val === '' ? 'الكل' : $val }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="h-8 w-px bg-gray-200 mx-2 hidden sm:block"></div>

                    <!-- Toggle: Available Units -->
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" wire:model.live="has_available_units" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-green-500 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">وحدات متاحة فقط</span>
                    </label>

                    <!-- Toggle: Virtual Tour -->
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" wire:model.live="has_virtual_tour" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-purple-500 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">جولة افتراضية</span>
                    </label>

                    <!-- Toggle: Ad License -->
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" wire:model.live="has_ad_license" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-blue-500 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <span class="text-xs font-bold text-gray-600">رخصة إعلان (فال)</span>
                    </label>
                </div>
            </div>
            @endif
        </div>

        <!-- Projects List -->
        <div class="space-y-3">
            @forelse($projects as $project)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ expanded: false, tab: 'overview' }">
                    <!-- Project Header (Compact & Clickable) -->
                    <div class="p-3 cursor-pointer hover:bg-gray-50 transition-colors" @click="expanded = !expanded">
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
                            <div class="flex items-center gap-3">
                                @php
                                    $mainImage = $project->projectMedia->where('main', 1)->first() ?? $project->projectMedia->where('media_type', 'image')->first();
                                @endphp
                                @if($mainImage)
                                    <img loading="lazy" src="{{ str_starts_with($mainImage->media_url, 'http') ? $mainImage->media_url : Storage::disk('public')->url($mainImage->media_url) }}" onerror="this.src='https://placehold.co/100x100?text=No+Image'" alt="{{ $project->name }}" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shadow-sm">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200 shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                
                                <div>
                                    <p class="text-[10px] text-gray-500 mb-0.5 font-medium flex items-center gap-1">
                                        {{ $project->city?->name ?? 'مدينة غير محددة' }} · {{ $project->state?->name ?? 'حي غير محدد' }} · {{ $project->projectType?->name ?? 'مشروع' }}
                                    </p>
                                    <h3 class="text-base font-bold text-gray-900 leading-tight">
                                        {{ $project->name }}
                                    </h3>
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div class="text-center px-4 border-l border-gray-100">
                                    <span class="block text-lg font-black text-gray-800 leading-none">{{ $project->units->count() }}</span>
                                    <span class="block text-[10px] font-bold text-gray-400 mt-0.5">وحدة</span>
                                </div>
                                <div class="text-gray-400 transform transition-transform duration-200" :class="{ 'rotate-180': expanded }">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Project Details (Expandable) -->
                    <div x-show="expanded" x-collapse class="border-t border-gray-100 bg-white">
                        
                        <!-- Tabs Header -->
                        <div class="flex items-center justify-center gap-6 border-b border-gray-100 px-6 pt-2">
                            <button @click="tab = 'overview'" class="pb-3 text-sm font-bold transition-colors relative" :class="tab === 'overview' ? 'text-gray-900' : 'text-gray-400 hover:text-gray-600'">
                                نظرة عامة
                                <div x-show="tab === 'overview'" class="absolute bottom-0 left-0 w-full h-0.5 bg-gray-900 rounded-t-full"></div>
                            </button>
                            <button @click="tab = 'units'" class="pb-3 text-sm font-bold transition-colors relative" :class="tab === 'units' ? 'text-gray-900' : 'text-gray-400 hover:text-gray-600'">
                                الوحدات والمخطط
                                <div x-show="tab === 'units'" class="absolute bottom-0 left-0 w-full h-0.5 bg-gray-900 rounded-t-full"></div>
                            </button>
                            <button @click="tab = 'marketing'" class="pb-3 text-sm font-bold transition-colors relative" :class="tab === 'marketing' ? 'text-gray-900' : 'text-gray-400 hover:text-gray-600'">
                                وسائط وتسويق
                                <div x-show="tab === 'marketing'" class="absolute bottom-0 left-0 w-full h-0.5 bg-gray-900 rounded-t-full"></div>
                            </button>
                        </div>

                        <!-- Tab Content: Overview -->
                        <div x-show="tab === 'overview'" class="p-6 space-y-8">
                            
                            <!-- Stats Boxes -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center shadow-sm">
                                    <span class="block text-[11px] text-gray-500 font-medium mb-1">المدينة</span>
                                    <span class="block font-bold text-gray-900">{{ $project->city?->name ?? '-' }}</span>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center shadow-sm">
                                    <span class="block text-[11px] text-gray-500 font-medium mb-1">الحي</span>
                                    <span class="block font-bold text-gray-900">{{ $project->state?->name ?? '-' }}</span>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center shadow-sm">
                                    <span class="block text-[11px] text-gray-500 font-medium mb-1">نوع المشروع</span>
                                    <span class="block font-bold text-gray-900">{{ $project->projectType?->name ?? '-' }}</span>
                                </div>
                                <div class="bg-white border border-gray-200 rounded-2xl p-4 text-center shadow-sm">
                                    <span class="block text-[11px] text-gray-500 font-medium mb-1">وحدات متاحة الآن</span>
                                    <span class="block font-bold text-gray-900" dir="ltr">
                                        @php
                                            $total = $project->units->count();
                                            $available = $project->units->where('case', 0)->count();
                                        @endphp
                                        {{ $available }} / {{ $total }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($project->description)
                            <div class="relative">
                                <h4 class="text-[11px] font-bold text-gray-400 mb-2 flex items-center gap-2">
                                    <span class="w-8 h-px bg-gray-200"></span>
                                    نبذة عن المشروع
                                    <span class="flex-1 h-px bg-gray-200"></span>
                                </h4>
                                <div class="text-sm text-gray-700 leading-relaxed font-medium px-4 text-justify">
                                    {!! strip_tags($project->description) !!}
                                </div>
                            </div>
                            @endif

                            <!-- Features (Why this project) -->
                            @if($project->features->count() > 0 || $project->guarantees->count() > 0)
                            <div class="relative">
                                <h4 class="text-[11px] font-bold text-gray-400 mb-4 flex items-center gap-2">
                                    <span class="w-8 h-px bg-gray-200"></span>
                                    لماذا هذا المشروع
                                    <span class="flex-1 h-px bg-gray-200"></span>
                                </h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-4">
                                    @foreach($project->features->take(4) as $feature)
                                    <div class="border border-gray-200 rounded-2xl p-4 text-center flex flex-col items-center justify-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                            <i class="fas fa-check"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-800">{{ $feature->name }}</span>
                                    </div>
                                    @endforeach
                                    @foreach($project->guarantees->take(4 - $project->features->count()) as $guarantee)
                                    <div class="border border-gray-200 rounded-2xl p-4 text-center flex flex-col items-center justify-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <span class="text-[11px] font-bold text-gray-800">{{ $guarantee->name }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Tab Content: Units -->
                        <div x-show="tab === 'units'" class="p-4 sm:p-6" style="display: none;">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-bold text-gray-800">وحدات المشروع</h4>
                                <a href="{{ route('manager.projects.pdf', $project->id) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-xl text-xs font-bold hover:bg-gray-800 transition-colors shadow-sm">
                                    <i class="fas fa-file-pdf text-red-400"></i>
                                    تحميل ملف الاسعار (PDF)
                                </a>
                            </div>
                            @if($project->units->count() > 0)
                                <div class="border border-gray-200 rounded-2xl overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 text-right text-xs">
                                            <thead class="bg-gray-50/80">
                                                <tr>
                                                    <th class="px-4 py-3 font-bold text-gray-500">رقم الوحدة</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500">العمارة / الدور</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500">المساحة</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500">السعر</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500 text-center">غرف / حمامات</th>
                                                    <th class="px-4 py-3 font-bold text-gray-500 text-center">الحالة</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100">
                                                @foreach($project->units as $unit)
                                                <tr class="hover:bg-gray-100 transition-colors cursor-pointer" @click="selectedUnit = {{ json_encode($unit) }}; unitModalOpen = true;">
                                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $unit->unit_number ?? $unit->title }}</td>
                                                    <td class="px-4 py-3 text-gray-500">
                                                        {{ $unit->building_number ? 'عمارة '.$unit->building_number : '-' }} 
                                                        <span class="text-gray-300 mx-1">|</span> 
                                                        {{ $unit->floor ? 'دور '.$unit->floor : '-' }}
                                                    </td>
                                                    <td class="px-4 py-3 font-medium text-gray-600">{{ $unit->unit_area }} م²</td>
                                                    <td class="px-4 py-3">
                                                        @if($unit->show_price)
                                                            <span class="font-bold text-gray-900">{{ number_format($unit->unit_price) }} ر.س</span>
                                                        @else
                                                            <span class="text-gray-400">مخفي</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-center text-gray-500">
                                                        <div class="flex items-center justify-center gap-3">
                                                            <span class="flex items-center gap-1 font-medium" title="غرف نوم">
                                                                <i class="fas fa-bed text-gray-300"></i> {{ $unit->beadrooms ?? 0 }}
                                                            </span>
                                                            <span class="flex items-center gap-1 font-medium" title="حمامات">
                                                                <i class="fas fa-bath text-gray-300"></i> {{ $unit->bathrooms ?? 0 }}
                                                            </span>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-3 text-center">
                                                        @if($unit->case == 0)
                                                            <span class="px-2 py-0.5 bg-green-50 text-green-700 rounded text-[10px] font-bold border border-green-100">متاح</span>
                                                        @elseif($unit->case == 1)
                                                            <span class="px-2 py-0.5 bg-yellow-50 text-yellow-700 rounded text-[10px] font-bold border border-yellow-100">محجوز</span>
                                                        @elseif($unit->case == 2)
                                                            <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded text-[10px] font-bold border border-red-100">مباع</span>
                                                        @else
                                                            <span class="px-2 py-0.5 bg-gray-50 text-gray-700 rounded text-[10px] font-bold border border-gray-200">تحت الإنشاء</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @else
                                <div class="bg-gray-50 p-8 rounded-2xl border border-dashed border-gray-200 text-center flex flex-col items-center justify-center text-gray-400">
                                    <i class="fas fa-building text-3xl mb-3 text-gray-300"></i>
                                    <span class="text-sm font-bold text-gray-700">لا يوجد وحدات</span>
                                    <span class="text-xs">لم يتم إضافة وحدات لهذا المشروع حتى الآن.</span>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Content: Marketing & Media -->
                        <div x-show="tab === 'marketing'" class="p-4 sm:p-6 space-y-6" style="display: none;">
                            
                            <!-- Actions / Links -->
                            @if($project->latitude && $project->longitude || $project->virtualTour)
                            <div class="flex flex-wrap gap-3">
                                @if($project->latitude && $project->longitude)
                                <a href="https://maps.google.com/?q={{ $project->latitude }},{{ $project->longitude }}" target="_blank" class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-800 rounded-xl text-xs font-bold border border-gray-200 shadow-sm flex items-center gap-2 transition-colors w-max">
                                    <i class="fas fa-map-marker-alt text-red-500 text-lg"></i>
                                    الموقع على الخريطة
                                </a>
                                @endif
                                @if($project->virtualTour)
                                <a href="{{ $project->virtualTour }}" target="_blank" class="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-800 rounded-xl text-xs font-bold border border-gray-200 shadow-sm flex items-center gap-2 transition-colors w-max">
                                    <i class="fas fa-vr-cardboard text-purple-500 text-lg"></i>
                                    الجولة الافتراضية 360°
                                </a>
                                @endif
                            </div>
                            @endif

                            <!-- Media -->
                            @php
                                $validMedia = $project->projectMedia->filter(fn($m) => !empty($m->media_url) || !empty($m->youtube_url));
                                $visualMedia = $validMedia->filter(fn($m) => in_array($m->media_type, ['image', 'video']) || !empty($m->youtube_url));
                                $fileMedia = $validMedia->filter(fn($m) => !in_array($m->media_type, ['image', 'video']) && empty($m->youtube_url));
                            @endphp
                            
                            @if($visualMedia->count() > 0)
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-4">صور وفيديوهات المشروع</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    @foreach($visualMedia as $media)
                                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 aspect-square">
                                            @if($media->media_type === 'image' && !empty($media->media_url))
                                                <img loading="lazy" src="{{ str_starts_with($media->media_url, 'http') ? $media->media_url : Storage::disk('public')->url($media->media_url) }}" onerror="this.src='https://placehold.co/150x150?text=Error'" class="w-full h-full object-cover">
                                            @elseif($media->media_type === 'video' || !empty($media->youtube_url))
                                                <div class="w-full h-full flex items-center justify-center text-red-500 bg-gray-100">
                                                    <i class="fab fa-youtube text-3xl"></i>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($media->media_url) || !empty($media->youtube_url))
                                            <a href="{{ $media->youtube_url ?? (str_starts_with($media->media_url, 'http') ? $media->media_url : Storage::disk('public')->url($media->media_url)) }}" target="_blank" class="absolute inset-0 bg-gray-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white backdrop-blur-[2px]">
                                                <i class="fas fa-external-link-alt text-lg"></i>
                                            </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            @if($fileMedia->count() > 0)
                            <div>
                                <h4 class="text-sm font-bold text-gray-800 mb-4 mt-6">ملفات المشروع</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    @foreach($fileMedia as $media)
                                        <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-gray-50 aspect-square">
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-100">
                                                <i class="fas fa-file-pdf text-2xl mb-2 text-red-400"></i>
                                                <span class="text-[10px] font-bold">ملف</span>
                                            </div>
                                            
                                            @if(!empty($media->media_url))
                                            <a href="{{ str_starts_with($media->media_url, 'http') ? $media->media_url : Storage::disk('public')->url($media->media_url) }}" target="_blank" class="absolute inset-0 bg-gray-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white backdrop-blur-[2px]">
                                                <i class="fas fa-download text-lg"></i>
                                            </a>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
            @empty
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="fas fa-search text-2xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-1">لا توجد مشاريع</h3>
                    <p class="text-sm text-gray-500">لم يتم العثور على أي مشاريع تطابق الفلاتر المحددة.</p>
                </div>
            @endforelse
        </div>

        @if($projects->hasPages())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="text-xs font-bold text-gray-500">
                عرض {{ $projects->firstItem() }} - {{ $projects->lastItem() }} من {{ $projects->total() }} مشروع
            </div>
            <div class="flex items-center gap-1">
                {{-- Previous --}}
                @if($projects->onFirstPage())
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </span>
                @else
                    <button wire:click="previousPage" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                    @if($page == $projects->currentPage())
                        <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-900 text-white text-xs font-black">{{ $page }}</span>
                    @elseif($page == 1 || $page == $projects->lastPage() || abs($page - $projects->currentPage()) <= 1)
                        <button wire:click="gotoPage({{ $page }})" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 text-xs font-bold transition-colors">{{ $page }}</button>
                    @elseif(abs($page - $projects->currentPage()) == 2)
                        <span class="w-6 h-8 flex items-center justify-center text-gray-300 text-xs">…</span>
                    @endif
                @endforeach

                {{-- Next --}}
                @if($projects->hasMorePages())
                    <button wire:click="nextPage" class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                @else
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg text-gray-300 cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </span>
                @endif
            </div>
        </div>
        @endif
    </div>

    <!-- Unit Details Modal Popup -->
    <div x-show="unitModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4" style="display: none;">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="unitModalOpen = false"></div>
        
        <!-- Modal Content -->
        <div x-show="unitModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
             
            <!-- Header -->
            <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-50 flex items-center justify-center text-primary-600">
                        <i class="fas fa-home"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">تفاصيل الوحدة <span x-text="selectedUnit?.unit_number || selectedUnit?.title"></span></h3>
                        <p class="text-xs text-gray-500 font-medium" x-show="selectedUnit?.building_number || selectedUnit?.floor">
                            عمارة <span x-text="selectedUnit?.building_number || '-'"></span> 
                            <span class="mx-1">·</span> 
                            دور <span x-text="selectedUnit?.floor || '-'"></span>
                        </p>
                    </div>
                </div>
                <button @click="unitModalOpen = false" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-6 overflow-y-auto" x-data="{ formatPrice(price) { return price ? new Intl.NumberFormat('en-US').format(price) : '-' } }">
                
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <!-- Status -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center">
                        <span class="block text-[10px] text-gray-500 font-bold mb-1 uppercase">الحالة</span>
                        <template x-if="selectedUnit?.case == 0">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-green-600"><i class="fas fa-check-circle"></i> متاح</span>
                        </template>
                        <template x-if="selectedUnit?.case == 1">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-yellow-600"><i class="fas fa-clock"></i> محجوز</span>
                        </template>
                        <template x-if="selectedUnit?.case == 2">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-red-600"><i class="fas fa-times-circle"></i> مباع</span>
                        </template>
                        <template x-if="selectedUnit?.case == 3">
                            <span class="inline-flex items-center gap-1 text-sm font-bold text-gray-600"><i class="fas fa-hard-hat"></i> تحت الإنشاء</span>
                        </template>
                    </div>
                    
                    <!-- Area -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center">
                        <span class="block text-[10px] text-gray-500 font-bold mb-1 uppercase">المساحة</span>
                        <span class="block font-bold text-gray-900 text-sm"><span x-text="selectedUnit?.unit_area"></span> م²</span>
                    </div>

                    <!-- Price -->
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center col-span-2 md:col-span-2">
                        <span class="block text-[10px] text-gray-500 font-bold mb-1 uppercase">السعر</span>
                        <template x-if="selectedUnit?.show_price">
                            <span class="block font-black text-primary-600 text-lg"><span x-text="formatPrice(selectedUnit?.unit_price)"></span> ر.س</span>
                        </template>
                        <template x-if="!selectedUnit?.show_price">
                            <span class="block font-bold text-gray-400 text-sm mt-1">مخفي بناءً على إعدادات الوحدة</span>
                        </template>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Unit Configuration -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">تفاصيل وتقسيم الوحدة</h4>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-bed w-4 text-center text-gray-300"></i> غرف النوم</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.beadrooms || 0"></span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-bath w-4 text-center text-gray-300"></i> الحمامات</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.bathrooms || 0"></span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-utensils w-4 text-center text-gray-300"></i> المطابخ</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.kitchen || 0"></span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-couch w-4 text-center text-gray-300"></i> الصالات</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.hall || 0"></span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-door-open w-4 text-center text-gray-300"></i> غرف الخادمة</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.maid_room || 0"></span>
                            </li>
                        </ul>
                    </div>

                    <!-- Additional Details -->
                    <div>
                        <h4 class="text-xs font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2">معلومات إضافية</h4>
                        <ul class="space-y-3">
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-map-signs w-4 text-center text-gray-300"></i> الاتجاه</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.direction || 'غير محدد'"></span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-car w-4 text-center text-gray-300"></i> مواقف السيارات</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.parking || 0"></span>
                            </li>
                            <li class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 flex items-center gap-2"><i class="fas fa-street-view w-4 text-center text-gray-300"></i> عرض الشارع</span>
                                <span class="font-bold text-gray-900" x-text="selectedUnit?.street_width ? selectedUnit?.street_width + ' م' : 'غير محدد'"></span>
                            </li>
                            <li class="flex flex-col gap-1 mt-4 pt-3 border-t border-gray-50">
                                <span class="text-gray-500 text-xs font-bold">وصف الوحدة</span>
                                <div class="text-sm font-medium text-gray-800 leading-relaxed" x-html="selectedUnit?.description || 'لا يوجد وصف مضاف.'"></div>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Unit Images and Floor Plan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 border-t border-gray-100 pt-6" x-show="selectedUnit?.image || (Array.isArray(selectedUnit?.images) && selectedUnit.images.length > 0) || selectedUnit?.floor_plan" style="display: none;">
                    
                    <!-- Main & Additional Images -->
                    <div x-show="selectedUnit?.image || (Array.isArray(selectedUnit?.images) && selectedUnit.images.length > 0)">
                        <h4 class="text-xs font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <i class="fas fa-images text-gray-400"></i> صور الوحدة
                        </h4>
                        <div class="space-y-3">
                            <template x-if="selectedUnit?.image">
                                <a :href="'/storage/' + selectedUnit.image" target="_blank" class="block rounded-xl overflow-hidden border border-gray-200 shadow-sm relative group bg-gray-50">
                                    <img :src="'/storage/' + selectedUnit.image" class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500" alt="صورة الوحدة">
                                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300 flex items-center justify-center">
                                        <i class="fas fa-search-plus text-white opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 text-2xl drop-shadow-md"></i>
                                    </div>
                                </a>
                            </template>
                            
                            <template x-if="Array.isArray(selectedUnit?.images) && selectedUnit.images.length > 0">
                                <div class="grid grid-cols-3 gap-2">
                                    <template x-for="img in selectedUnit.images" :key="img">
                                        <a :href="'/storage/' + img" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 shadow-sm relative group bg-gray-50 aspect-square">
                                            <img :src="'/storage/' + img" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="صورة إضافية">
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors duration-300"></div>
                                        </a>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Floor Plan -->
                    <div x-show="selectedUnit?.floor_plan">
                        <h4 class="text-xs font-bold text-gray-800 mb-3 border-b border-gray-100 pb-2 flex items-center gap-2">
                            <i class="fas fa-vector-square text-gray-400"></i> مخطط الوحدة
                        </h4>
                        <a :href="'/storage/' + selectedUnit?.floor_plan" target="_blank" class="block rounded-xl overflow-hidden border border-gray-200 bg-gray-50/50 flex items-center justify-center p-4 relative group shadow-sm h-[calc(100%-2.5rem)]">
                            <img :src="'/storage/' + selectedUnit?.floor_plan" class="max-w-full max-h-48 object-contain group-hover:scale-105 transition-transform duration-500" alt="مخطط الوحدة">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300 flex items-center justify-center">
                                <i class="fas fa-search-plus text-gray-700 opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300 text-2xl drop-shadow-sm bg-white/80 p-3 rounded-full"></i>
                            </div>
                        </a>
                    </div>

                </div>

            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-end gap-3">
                <button @click="unitModalOpen = false" class="px-6 py-2 bg-white border border-gray-200 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-50 transition-colors">إغلاق</button>
            </div>
        </div>
    </div>

</div>
