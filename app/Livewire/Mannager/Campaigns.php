<?php

namespace App\Livewire\Mannager;

use App\Models\Campaign;
use App\Models\Project;
use App\Services\TrackingService;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\EnhancedTrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class Campaigns extends Component
{
    use WithPagination;

    // View Mode Properties
    public $viewMode = 'overview'; // 'overview', 'detailed', 'management', 'comparison'
    public $selectedCampaignId = null;
    public $comparisonCampaignIds = [];

    // Enhanced Filter Properties
    public $searchTerm = '';
    public $selectedProjects = [];
    public $selectedSources = [];
    public $selectedEventTypes = [];
    public $selectedDeviceTypes = [];
    public $datePreset = 'last_30_days';
    public $customStartDate = '';
    public $customEndDate = '';
    public $useCustomDate = false;

    // Enhanced Data Properties
    public $dashboardData = [];
    public $campaignAnalytics = [];
    public $realTimeData = [];
    public $comparisonData = [];
    public $filterOptions = [];

    // Enhanced Modal Properties
    public $showCampaignModal = false;
    public $showExportModal = false;
    public $showComparisonModal = false;
    public $isEditMode = false;
    public $campaignIdToEdit = null;

    // Enhanced Campaign Form Properties
    public $name = '';
    public $description = '';
    public $project_id = '';
    public $source = '';
    public $start_date = '';
    public $end_date = '';
    public $budget = '';
    public $status = 'active';
    public $target_audience = '';
    public $goals = [];
    public $metadata = [];

    // Enhanced Export Properties
    public $exportFormat = 'pdf';
    public $exportDateRange = 'current';
    public $exportFields = [];

    // Enhanced Real-time Properties
    public $enableRealTime = false;
    public $lastUpdateTime = '';
    public $autoRefreshInterval = 30; // seconds

    // Enhanced Available Sources with Arabic translations
    public array $availableSources = [
        'facebook' => 'فيسبوك',
        'instagram' => 'إنستغرام',
        'google' => 'إعلانات جوجل',
        'tiktok' => 'تيك توك',
        'snapchat' => 'سناب شات',
        'newsletter' => 'النشرة الإخبارية',
        'sms' => 'حملة الرسائل النصية',
        'direct' => 'مباشر',
        'youtube' => 'يوتيوب',
        'linkedin' => 'لينكد إن',
        'twitter' => 'تويتر',
        'whatsapp' => 'واتساب',
        'telegram' => 'تليجرام',
        'email' => 'البريد الإلكتروني',
        'other' => 'أخرى',
    ];

    // Enhanced Campaign Statuses with Arabic translations
    public array $campaignStatuses = [
        'active' => 'نشطة',
        'paused' => 'متوقفة مؤقتاً',
        'completed' => 'مكتملة',
        'draft' => 'مسودة',
        'cancelled' => 'ملغاة',
        'scheduled' => 'مجدولة',
    ];

    // Enhanced Event Types with Arabic translations
    public array $eventTypes = [
        'visit' => 'زيارات',
        'view' => 'مشاهدات',
        'show' => 'عروض',
        'order' => 'طلبات',
        'whatsapp' => 'رسائل واتساب',
        'call' => 'مكالمات',
        'email' => 'رسائل إلكترونية',
        'download' => 'تحميلات',
        'registration' => 'تسجيلات',
        'subscription' => 'اشتراكات',
    ];

    // Enhanced Device Types with Arabic translations
    public array $deviceTypes = [
        'desktop' => 'سطح المكتب',
        'mobile' => 'الهاتف المحمول',
        'tablet' => 'الجهاز اللوحي',
        'smart_tv' => 'التلفزيون الذكي',
        'other' => 'أخرى',
    ];

    protected $listeners = [
        'deleteConfirmed' => 'deleteCampaign',
        'refreshData' => 'refreshDashboardData',
        'exportConfirmed' => 'exportData',
        'campaignUpdated' => 'refreshDashboardData',
        'bulkActionConfirmed' => 'performBulkAction',
        'duplicateConfirmed' => 'duplicateCampaign',
    ];

    protected function rules()
    {
        // القاعدة الأساسية لتاريخ البدء
        $startDateRules = ['required', 'date'];

        // أضف شرط "بعد أو يساوي اليوم" فقط عند إنشاء حملة جديدة
        if (!$this->isEditMode) {
            $startDateRules[] = 'after_or_equal:today';
        }

        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                Rule::unique('campaigns', 'name')->ignore($this->campaignIdToEdit),
            ],
            'project_id' => 'required|exists:projects,id',
            'source' => 'required|string|max:100|in:' . implode(',', array_keys($this->availableSources)),
            
            // استخدم القاعدة الديناميكية هنا
            'start_date' => $startDateRules, 
            
            'end_date' => 'nullable|date|after:start_date',
            'budget' => 'nullable|numeric|min:0|max:999999999.99',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:' . implode(',', array_keys($this->campaignStatuses)),
            'target_audience' => 'nullable|string|max:500',
            'goals' => 'nullable|array',
            'goals.*' => 'string|max:255',
            'metadata' => 'nullable|array',
        ];
    }


    // Enhanced Validation Messages in Arabic
    protected $messages = [
        'name.required' => 'اسم الحملة مطلوب',
        'name.min' => 'اسم الحملة يجب أن يكون على الأقل 3 أحرف',
        'name.max' => 'اسم الحملة يجب ألا يزيد عن 255 حرف',
        'name.unique' => 'اسم الحملة موجود بالفعل، يرجى اختيار اسم آخر',
        'project_id.required' => 'يجب اختيار مشروع',
        'project_id.exists' => 'المشروع المحدد غير موجود',
        'source.required' => 'مصدر الحملة مطلوب',
        'source.in' => 'مصدر الحملة المحدد غير صالح',
        'start_date.required' => 'تاريخ البدء مطلوب',
        'start_date.date' => 'تاريخ البدء يجب أن يكون تاريخاً صالحاً',
        'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو في المستقبل',
        'end_date.date' => 'تاريخ الانتهاء يجب أن يكون تاريخاً صالحاً',
        'end_date.after' => 'تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء',
        'budget.numeric' => 'الميزانية يجب أن تكون رقماً',
        'budget.min' => 'الميزانية يجب أن تكون أكبر من أو تساوي صفر',
        'budget.max' => 'الميزانية كبيرة جداً',
        'description.max' => 'الوصف يجب ألا يزيد عن 2000 حرف',
        'status.required' => 'حالة الحملة مطلوبة',
        'status.in' => 'حالة الحملة المحددة غير صالحة',
        'target_audience.max' => 'الجمهور المستهدف يجب ألا يزيد عن 500 حرف',
        'goals.array' => 'الأهداف يجب أن تكون قائمة',
        'goals.*.string' => 'كل هدف يجب أن يكون نصاً',
        'goals.*.max' => 'كل هدف يجب ألا يزيد عن 255 حرف',
        'metadata.array' => 'البيانات الإضافية يجب أن تكون قائمة',
    ];

    public function mount()
    {
        $this->initializeComponent();
    }

    // Enhanced Initialization Methods
    public function initializeComponent()
    {
        try {
            $this->setDefaultDates();
            $this->loadFilterOptions();
            $this->refreshDashboardData();
            $this->lastUpdateTime = now()->format('H:i:s');
            
            // Initialize export fields
            $this->exportFields = ['name', 'project', 'source', 'status', 'budget', 'start_date', 'end_date'];
            
        } catch (\Exception $e) {
            Log::error('Error initializing Campaigns component: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تحميل البيانات: ' . $e->getMessage()
            ]);
        }
    }

    private function setDefaultDates()
    {
        $this->customStartDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->customEndDate = Carbon::now()->format('Y-m-d');
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = Carbon::now()->addDays(30)->format('Y-m-d');
    }

    private function loadFilterOptions()
    {
        try {
            $cacheKey = 'campaigns_filter_options_' . auth()->id();
            
            $this->filterOptions = Cache::remember($cacheKey, 300, function () {
                $service = app(EnhancedTrackingService::class);
                return $service->getAdvancedFilters();
            });
            
        } catch (\Exception $e) {
            Log::error('Error loading filter options: ' . $e->getMessage());
            $this->filterOptions = [
                'date_presets' => $this->getDefaultDatePresets(),
                'projects' => [],
                'sources' => $this->availableSources,
                'event_types' => $this->eventTypes,
                'device_types' => $this->deviceTypes,
            ];
        }
    }

    private function getDefaultDatePresets()
    {
        return [
            'today' => [
                'label' => 'اليوم',
                'start' => Carbon::today(),
                'end' => Carbon::today()->endOfDay(),
            ],
            'yesterday' => [
                'label' => 'أمس',
                'start' => Carbon::yesterday(),
                'end' => Carbon::yesterday()->endOfDay(),
            ],
            'last_7_days' => [
                'label' => 'آخر 7 أيام',
                'start' => Carbon::now()->subDays(7),
                'end' => Carbon::now(),
            ],
            'last_30_days' => [
                'label' => 'آخر 30 يوم',
                'start' => Carbon::now()->subDays(30),
                'end' => Carbon::now(),
            ],
            'this_month' => [
                'label' => 'هذا الشهر',
                'start' => Carbon::now()->startOfMonth(),
                'end' => Carbon::now(),
            ],
            'last_month' => [
                'label' => 'الشهر الماضي',
                'start' => Carbon::now()->subMonth()->startOfMonth(),
                'end' => Carbon::now()->subMonth()->endOfMonth(),
            ],
        ];
    }

    // Enhanced Data Management Methods
    public function refreshDashboardData()
    {
        try {
            $service = app(EnhancedTrackingService::class);
            $filters = $this->buildFilters();

            // Cache dashboard data for better performance
            $cacheKey = 'dashboard_data_' . md5(serialize($filters)) . '_' . auth()->id();
            
            $this->dashboardData = Cache::remember($cacheKey, 60, function () use ($service, $filters) {
                return $service->getDashboardOverview($filters);
            });
            
            if ($this->selectedCampaignId) {
                $this->campaignAnalytics = $service->getCampaignDetailedAnalytics(
                    $this->selectedCampaignId,
                    $filters
                );
                $this->dispatch('updateCharts', data: $this->campaignAnalytics);
            }

            if ($this->enableRealTime) {
                $this->realTimeData = $service->getRealTimeUpdates($this->selectedCampaignId);
            }

            if (!empty($this->comparisonCampaignIds)) {
                $this->comparisonData = $service->getCampaignComparison(
                    $this->comparisonCampaignIds,
                    $filters
                );
            }

            $this->lastUpdateTime = now()->format('H:i:s');
            $this->dispatch('dataRefreshed');
            
        } catch (\Exception $e) {
            Log::error('Error refreshing dashboard data: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تحديث البيانات. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }

    private function buildFilters(): array
    {
        $filters = [];

        // Enhanced Date filters with validation
        if ($this->useCustomDate && $this->customStartDate && $this->customEndDate) {
            try {
                $filters['start_date'] = Carbon::parse($this->customStartDate)->startOfDay();
                $filters['end_date'] = Carbon::parse($this->customEndDate)->endOfDay();
                
                // Validate date range
                if ($filters['start_date']->gt($filters['end_date'])) {
                    throw new \InvalidArgumentException('تاريخ البدء يجب أن يكون قبل تاريخ الانتهاء');
                }
                
            } catch (\Exception $e) {
                Log::warning('Invalid custom date range: ' . $e->getMessage());
                $this->useCustomDate = false;
                $this->datePreset = 'last_30_days';
            }
        }
        
        if (!$this->useCustomDate) {
            $preset = $this->filterOptions['date_presets'][$this->datePreset] ?? $this->getDefaultDatePresets()['last_30_days'];
            $filters['start_date'] = $preset['start'];
            $filters['end_date'] = $preset['end'];
        }

        // Enhanced Other filters with validation
        if (!empty($this->selectedProjects) && is_array($this->selectedProjects)) {
            $filters['project_ids'] = array_filter($this->selectedProjects, 'is_numeric');
        }

        if (!empty($this->selectedSources) && is_array($this->selectedSources)) {
            $validSources = array_intersect($this->selectedSources, array_keys($this->availableSources));
            if (!empty($validSources)) {
                $filters['sources'] = $validSources;
            }
        }

        if (!empty($this->selectedEventTypes) && is_array($this->selectedEventTypes)) {
            $validEventTypes = array_intersect($this->selectedEventTypes, array_keys($this->eventTypes));
            if (!empty($validEventTypes)) {
                $filters['event_types'] = $validEventTypes;
            }
        }

        if (!empty($this->selectedDeviceTypes) && is_array($this->selectedDeviceTypes)) {
            $validDeviceTypes = array_intersect($this->selectedDeviceTypes, array_keys($this->deviceTypes));
            if (!empty($validDeviceTypes)) {
                $filters['device_types'] = $validDeviceTypes;
            }
        }

        if ($this->selectedCampaignId && is_numeric($this->selectedCampaignId)) {
            $filters['campaign_id'] = $this->selectedCampaignId;
        }

        if (!empty($this->searchTerm)) {
            $filters['search'] = trim($this->searchTerm);
        }

        return $filters;
    }

    // Enhanced Filter Update Methods with debouncing
    public function updatedSearchTerm()
    {
        $this->resetPage();
        // Debounce search to avoid too many requests
        $this->dispatch('debounceSearch');
    }

    public function updatedSelectedProjects()
    {
        $this->validateSelectedProjects();
        $this->refreshDashboardData();
    }

    private function validateSelectedProjects()
    {
        if (!empty($this->selectedProjects)) {
            $validProjects = Project::whereIn('id', $this->selectedProjects)->pluck('id')->toArray();
            $this->selectedProjects = array_map('intval', $validProjects);
        }
    }

    public function updatedSelectedSources()
    {
        $this->validateSelectedSources();
        $this->refreshDashboardData();
    }

    private function validateSelectedSources()
    {
        if (!empty($this->selectedSources)) {
            $this->selectedSources = array_intersect($this->selectedSources, array_keys($this->availableSources));
        }
    }

    public function updatedSelectedEventTypes()
    {
        $this->validateSelectedEventTypes();
        $this->refreshDashboardData();
    }

    private function validateSelectedEventTypes()
    {
        if (!empty($this->selectedEventTypes)) {
            $this->selectedEventTypes = array_intersect($this->selectedEventTypes, array_keys($this->eventTypes));
        }
    }

    public function updatedDatePreset()
    {
        $this->useCustomDate = false;
        $this->refreshDashboardData();
    }

    public function updatedUseCustomDate()
    {
        if ($this->useCustomDate) {
            $this->datePreset = '';
        }
        $this->refreshDashboardData();
    }

    public function updatedCustomStartDate()
    {
        if ($this->useCustomDate && $this->customStartDate) {
            $this->validateCustomDates();
            $this->refreshDashboardData();
        }
    }

    public function updatedCustomEndDate()
    {
        if ($this->useCustomDate && $this->customEndDate) {
            $this->validateCustomDates();
            $this->refreshDashboardData();
        }
    }

    private function validateCustomDates()
    {
        try {
            if ($this->customStartDate && $this->customEndDate) {
                $startDate = Carbon::parse($this->customStartDate);
                $endDate = Carbon::parse($this->customEndDate);
                
                if ($startDate->gt($endDate)) {
                    $this->dispatch('showNotification', [
                        'type' => 'warning',
                        'message' => 'تاريخ البدء يجب أن يكون قبل تاريخ الانتهاء'
                    ]);
                    return false;
                }
                
                // Check if date range is too large (more than 1 year)
                if ($startDate->diffInDays($endDate) > 365) {
                    $this->dispatch('showNotification', [
                        'type' => 'warning',
                        'message' => 'الفترة الزمنية كبيرة جداً. يرجى اختيار فترة أقل من سنة واحدة'
                    ]);
                    return false;
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::warning('Invalid date format: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'تنسيق التاريخ غير صالح'
            ]);
            return false;
        }
    }

    public function updatedSelectedCampaignId($value)
    {
        if ($value && is_numeric($value)) {
            $this->viewMode = 'detailed';
        } else {
            $this->viewMode = 'overview';
            $this->campaignAnalytics = [];
        }
        $this->refreshDashboardData();
    }

    public function updatedEnableRealTime($value)
    {
        if ($value) {
            $this->refreshDashboardData();
        } else {
            $this->realTimeData = [];
        }
    }

    // Enhanced View Mode Methods
    public function switchView($mode)
    {
        $validModes = ['overview', 'detailed', 'management', 'comparison'];
        
        if (!in_array($mode, $validModes)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'وضع العرض غير صالح'
            ]);
            return;
        }
        
        $this->viewMode = $mode;
        
        if ($mode === 'overview') {
            $this->selectedCampaignId = null;
            $this->campaignAnalytics = [];
        }
        
        $this->refreshDashboardData();
    }

    public function selectCampaign($campaignId)
    {
        if (!is_numeric($campaignId)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'معرف الحملة غير صالح'
            ]);
            return;
        }
        
        // Verify campaign exists and user has access
        $campaign = Campaign::find($campaignId);
        if (!$campaign) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'الحملة غير موجودة'
            ]);
            return;
        }
        $this->selectedCampaignId = $campaignId;
        $this->viewMode = 'detailed';
        $this->refreshDashboardData();

    }

    public function clearSelection()
    {
        $this->selectedCampaignId = null;
        $this->viewMode = 'overview';
        $this->campaignAnalytics = [];
        $this->refreshDashboardData();
    }

    // Enhanced Campaign Management Methods
    public function openCreateModal()
    {
        $this->resetCampaignForm();
        $this->isEditMode = false;
        $this->showCampaignModal = true;
    }

    public function openEditModal($campaignId)
    {
        if (!is_numeric($campaignId)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'معرف الحملة غير صالح'
            ]);
            return;
        }
        
        try {
            $campaign = Campaign::findOrFail($campaignId);
            
            $this->resetCampaignForm();
            $this->isEditMode = true;
            $this->campaignIdToEdit = $campaignId;
            
            $this->name = $campaign->name;
            $this->description = $campaign->description ?? '';
            $this->project_id = $campaign->project_id;
            $this->source = $campaign->source;
            $this->start_date = $campaign->start_date ? $campaign->start_date->format('Y-m-d') : '';
            $this->end_date = $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '';
            $this->budget = $campaign->budget;
            $this->status = $campaign->status;
            $this->target_audience = $campaign->target_audience ?? '';
            $this->goals = is_array($campaign->goals) ? $campaign->goals : [];
            $this->metadata = is_array($campaign->metadata) ? $campaign->metadata : [];
            
            $this->showCampaignModal = true;
            
        } catch (\Exception $e) {
            Log::error('Error opening edit modal: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تحميل بيانات الحملة'
            ]);
        }
    }

    public function saveCampaign()
    {
        try {
            $this->validate();

            DB::beginTransaction();

            $data = [
                'name' => trim($this->name),
                'description' => trim($this->description) ?: null,
                'project_id' => $this->project_id,
                'source' => $this->source,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date ?: null,
                'budget' => $this->budget ?: null,
                'status' => $this->status,
                'target_audience' => trim($this->target_audience) ?: null,
                'goals' => !empty($this->goals) ? array_filter($this->goals) : null,
                'metadata' => !empty($this->metadata) ? $this->metadata : null,
            ];

            if ($this->isEditMode) {
                $campaign = Campaign::findOrFail($this->campaignIdToEdit);
                $campaign->update($data);
                $message = 'تم تحديث الحملة "' . $campaign->name . '" بنجاح';
                
                Log::info('Campaign updated', ['campaign_id' => $campaign->id, 'user_id' => auth()->id()]);
            } else {
                $campaign = Campaign::create($data);
                $message = 'تم إنشاء الحملة "' . $campaign->name . '" بنجاح';
                
                Log::info('Campaign created', ['campaign_id' => $campaign->id, 'user_id' => auth()->id()]);
            }

            DB::commit();

            $this->showCampaignModal = false;
            $this->refreshDashboardData();
            
            // Clear cache
            Cache::forget('campaigns_filter_options_' . auth()->id());
            
            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => $message
            ]);

            $this->dispatch('campaignUpdated', campaignId: $campaign->id);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = collect($e->errors())->flatten()->implode(' ');
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'يرجى تصحيح الأخطاء التالية: ' . $errors
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saving campaign: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء حفظ الحملة. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }

    public function confirmDelete($campaignId)
    {
        if (!is_numeric($campaignId)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'معرف الحملة غير صالح'
            ]);
            return;
        }
        
        $this->dispatch('showDeleteConfirmation', campaignId: $campaignId);
    }

    public function deleteCampaign($campaignId)
    {
        try {
            DB::beginTransaction();
            
            $campaign = Campaign::findOrFail($campaignId);
            $campaignName = $campaign->name;
            
            // Soft delete to preserve data integrity
            $campaign->delete();

            if ($this->selectedCampaignId == $campaignId) {
                $this->selectedCampaignId = null;
                $this->viewMode = 'overview';
            }

            // Remove from comparison if present
            $this->comparisonCampaignIds = array_filter($this->comparisonCampaignIds, function($id) use ($campaignId) {
                return $id != $campaignId;
            });

            DB::commit();
            
            $this->refreshDashboardData();
            
            // Clear cache
            Cache::forget('campaigns_filter_options_' . auth()->id());
            
            Log::info('Campaign deleted', ['campaign_id' => $campaignId, 'user_id' => auth()->id()]);
            
            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "تم حذف الحملة '{$campaignName}' بنجاح"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting campaign: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء حذف الحملة. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }

    public function duplicateCampaign($campaignId)
    {
        try {
            DB::beginTransaction();
            
            $originalCampaign = Campaign::findOrFail($campaignId);
            $newCampaign = $originalCampaign->replicate();
            $newCampaign->name = $originalCampaign->name . ' - نسخة ' . now()->format('Y-m-d H:i');
            $newCampaign->status = 'draft';
            $newCampaign->start_date = Carbon::now();
            $newCampaign->end_date = null;
            $newCampaign->save();

            DB::commit();
            
            $this->refreshDashboardData();
            
            // Clear cache
            Cache::forget('campaigns_filter_options_' . auth()->id());
            
            Log::info('Campaign duplicated', [
                'original_campaign_id' => $campaignId,
                'new_campaign_id' => $newCampaign->id,
                'user_id' => auth()->id()
            ]);
            
            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => 'تم نسخ الحملة بنجاح. الحملة الجديدة: "' . $newCampaign->name . '"'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error duplicating campaign: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء نسخ الحملة. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }

    public function toggleCampaignStatus($campaignId)
    {
        try {
            DB::beginTransaction();
            
            $campaign = Campaign::findOrFail($campaignId);
            $oldStatus = $campaign->status;
            $campaign->status = $campaign->status === 'active' ? 'paused' : 'active';
            $campaign->save();

            DB::commit();
            
            $this->refreshDashboardData();
            
            Log::info('Campaign status toggled', [
                'campaign_id' => $campaignId,
                'old_status' => $oldStatus,
                'new_status' => $campaign->status,
                'user_id' => auth()->id()
            ]);
            
            $statusText = $campaign->status === 'active' ? 'تم تفعيل' : 'تم إيقاف';
            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "{$statusText} الحملة '{$campaign->name}' بنجاح"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error toggling campaign status: ' . $e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تغيير حالة الحملة. يرجى المحاولة مرة أخرى.'
            ]);
        }
    }

    // Enhanced Comparison Methods
    public function openComparisonModal()
    {
        $this->showComparisonModal = true;
    }

    public function addToComparison($campaignId)
    {
        if (!is_numeric($campaignId)) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'معرف الحملة غير صالح'
            ]);
            return;
        }
        
        if (in_array($campaignId, $this->comparisonCampaignIds)) {
            $this->dispatch('showNotification', [
                'type' => 'warning',
                'message' => 'الحملة موجودة بالفعل في المقارنة'
            ]);
            return;
        }
        
        if (count($this->comparisonCampaignIds) >= 5) {
            $this->dispatch('showNotification', [
                'type' => 'warning',
                'message' => 'لا يمكن مقارنة أكثر من 5 حملات في نفس الوقت'
            ]);
            return;
        }
        
        // Verify campaign exists
        $campaign = Campaign::find($campaignId);
        if (!$campaign) {
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'الحملة غير موجودة'
            ]);
            return;
        }
        
        $this->comparisonCampaignIds[] = $campaignId;
        $this->refreshDashboardData();
        
        $this->dispatch('showNotification', [
            'type' => 'success',
            'message' => 'تم إضافة الحملة "' . $campaign->name . '" للمقارنة'
        ]);
    }

    public function removeFromComparison($campaignId)
    {
        $this->comparisonCampaignIds = array_filter($this->comparisonCampaignIds, function($id) use ($campaignId) {
            return $id != $campaignId;
        });
        
        // Reset array keys
        $this->comparisonCampaignIds = array_values($this->comparisonCampaignIds);
        
        $this->refreshDashboardData();
        
        $this->dispatch('showNotification', [
            'type' => 'success',
            'message' => 'تم إزالة الحملة من المقارنة'
        ]);
    }

    public function clearComparison()
    {
        $this->comparisonCampaignIds = [];
        $this->comparisonData = [];
        $this->viewMode = 'overview';
        
        $this->dispatch('showNotification', [
            'type' => 'success',
            'message' => 'تم مسح جميع الحملات من المقارنة'
        ]);
    }

    // Enhanced Utility Methods
    private function resetCampaignForm()
    {
        $this->name = '';
        $this->description = '';
        $this->project_id = '';
        $this->source = '';
        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->end_date = '';
        $this->budget = '';
        $this->status = 'active';
        $this->target_audience = '';
        $this->goals = [];
        $this->metadata = [];
        $this->campaignIdToEdit = null;
        
        $this->resetErrorBag();
    }

    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->selectedProjects = [];
        $this->selectedSources = [];
        $this->selectedEventTypes = [];
        $this->selectedDeviceTypes = [];
        $this->datePreset = 'last_30_days';
        $this->useCustomDate = false;
        $this->customStartDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->customEndDate = Carbon::now()->format('Y-m-d');
        
        $this->resetPage();
        $this->refreshDashboardData();
        
        $this->dispatch('showNotification', [
            'type' => 'success',
            'message' => 'تم مسح جميع الفلاتر'
        ]);
    }

    // Enhanced Computed Properties
    public function getProjectsProperty()
    {
        return Cache::remember('user_projects_' . auth()->id(), 300, function () {
            return Project::select('id', 'name')
                ->orderBy('name')
                ->get();
        });
    }

    public function getCampaignsProperty()
    {
        $query = Campaign::with('project:id,name')
            ->select('id', 'name', 'project_id', 'source', 'status', 'start_date', 'end_date', 'budget');

        // Apply search filter
        if (!empty($this->searchTerm)) {
            $searchTerm = '%' . trim($this->searchTerm) . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('target_audience', 'like', $searchTerm);
            });
        }

        // Apply project filter
        if (!empty($this->selectedProjects)) {
            $query->whereIn('project_id', $this->selectedProjects);
        }

        // Apply source filter
        if (!empty($this->selectedSources)) {
            $query->whereIn('source', $this->selectedSources);
        }

        return $query->orderBy('created_at', 'desc')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.mannager.campaigns')->layout('layouts.custom'); // تأكد من اسم الـ layout الصحيح
    }
}

