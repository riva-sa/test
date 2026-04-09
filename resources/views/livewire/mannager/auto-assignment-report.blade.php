<div class="p-4 bg-white min-h-screen" dir="rtl">
    <!-- رسالة النجاح (اختياري) -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- 1. العنوان الرئيسي والفلاتر -->
    <div class="mb-6 border-b border-gray-200 pb-4">
        <div class="flex flex-wrap items-center justify-between gap-y-4 mb-3">
            <h1 class="text-2xl font-bold text-gray-800">التوزيع التلقائي للطلبات</h1>
        </div>
        
        <!-- منطقة الفلاتر -->
        <div class="bg-white p-4 rounded-lg border border-gray-200 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center">
                <span class="text-sm font-medium text-gray-500 ml-3">نطاق التاريخ:</span>
                <div class="flex items-center border border-gray-200 rounded-lg p-1">
                    <select wire:model.live="dateRange" class="border-0 bg-transparent focus:outline-none text-sm font-medium py-1">
                        <option value="today">اليوم</option>
                        <option value="yesterday">الأمس</option>
                        <option value="this_week">هذا الأسبوع</option>
                        <option value="this_month">هذا الشهر</option>
                    </select>
                </div>
            </div>
            
            <div class="flex space-x-2 space-x-reverse">
                <button wire:click="$refresh" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    تحديث
                </button>
            </div>
        </div>
    </div>

    <!-- 2. شريط الملخص التنفيذي (Executive Summary) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        
        <!-- إجمالي الموزعة -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-blue-100 p-3 rounded-lg ml-4">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">إجمالي الطلبات الموزعة</p>
                <div class="flex items-baseline">
                    <p class="text-2xl font-bold text-gray-800">{{ number_format($totalAssigned) }}</p>
                </div>
            </div>
        </div>
        
        <!-- المتاحين -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-green-100 p-3 rounded-lg ml-4">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">المندوبين المتاحين (نشط)</p>
                <p class="text-2xl font-bold text-gray-800">{{ $activeRepsCount }} <span class="text-sm text-gray-400 font-normal">/ {{ count($salesReps) }}</span></p>
            </div>
        </div>
        
        <!-- متوسط لكل مندوب -->
        <div class="bg-white border border-gray-200 rounded-lg p-4 flex items-center">
            <div class="bg-purple-100 p-3 rounded-lg ml-4">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">متوسط لكل مندوب</p>
                <p class="text-2xl font-bold text-gray-800">{{ $activeRepsCount > 0 ? round($totalAssigned / $activeRepsCount, 1) : 0 }}</p>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <!-- 3. جدول توزيع المندوبين -->
        <div class="xl:col-span-1 border border-gray-200 rounded-lg overflow-hidden bg-white max-h-[700px] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-800">توزيع الطلبات على المندوبين</h3>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @foreach($salesReps as $rep)
                    <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                    src="{{ $rep->avatar_url ?? asset('images/default-avatar.png') }}"
                                    alt="{{ $rep->name }}"
                                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($rep->name) }}&background=2563EB&color=fff'">
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-800 block truncate max-w-[150px]">{{ $rep->name }}</span>
                                <div class="mt-1">
                                    @if($rep->is_active && !$rep->on_vacation)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">نشط</span>
                                    @elseif($rep->on_vacation)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">إجازة</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">غير نشط</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col items-center justify-center p-2 bg-blue-50 rounded-lg border border-blue-100 min-w-[3rem]">
                            <span class="text-lg font-bold text-blue-700 font-mono">{{ $rep->total_assigned }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- 4. سجل العمليات الحديثة -->
        <div class="xl:col-span-2 border border-gray-200 rounded-lg overflow-hidden bg-white max-h-[700px] flex flex-col">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">سجل التوزيع الحديث</h3>
            </div>
            
            <div class="flex-1 overflow-y-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"># الطلب</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المندوب المستلم</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العميل والمشروع</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المصدر</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوقت</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentAssignments as $assignment)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="{{ route('manager.order-details', $assignment->id) }}" class="text-indigo-600 font-bold hover:text-indigo-900 border-b border-indigo-200 hover:border-indigo-600 transition-colors">
                                        {{ $assignment->id }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <img class="h-6 w-6 rounded-full object-cover border border-gray-200"
                                            src="{{ optional($assignment->assignedSalesUser)->avatar_url ?? asset('images/default-avatar.png') }}"
                                            alt="{{ optional($assignment->assignedSalesUser)->name }}"
                                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(optional($assignment->assignedSalesUser)->name ?? 'User') }}&background=2563EB&color=fff'">
                                        <span class="text-gray-900 font-medium">{{ optional($assignment->assignedSalesUser)->name ?? 'غير محدد' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-gray-900">{{ $assignment->name }}</span>
                                        <span class="text-xs text-gray-500">{{ $assignment->project->name ?? 'غير محدد' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                        {!! is_array($assignment->formattedMarketingSource()) ? $assignment->formattedMarketingSource()['icon'] : '<i class="fas fa-globe text-gray-400"></i>' !!}
                                        <span class="ml-1 mr-1">{{ is_array($assignment->formattedMarketingSource()) ? $assignment->formattedMarketingSource()['label'] : 'غير معروف' }}</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $assignment->created_at->format('Y-m-d H:i') }}
                                    <span class="block text-gray-400 font-light mt-0.5">{{ $assignment->created_at->diffForHumans() }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    لا توجد طلبات موزعة في هذه الفترة الزمنيّة.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($recentAssignments->hasPages())
                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $recentAssignments->links(data: ['scrollTo' => false]) }}
                </div>
            @endif
        </div>
    </div>


    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-gray-900 bg-opacity-50 z-50 items-center justify-center backdrop-blur-sm hidden">
        <div class="bg-white rounded-xl p-8 shadow-2xl max-w-sm mx-4" style="position: absolute; transform: translate(-50%,-50%); top: 50%; left: 50%;">
            <div class="flex flex-col items-center space-y-4">
                <svg class="animate-spin w-8 h-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900">جاري تحميل التقرير</h3>
                    <p class="text-sm text-gray-600 mt-1">يرجى الانتظار...</p>
                </div>
            </div>
        </div>
    </div>
</div>
