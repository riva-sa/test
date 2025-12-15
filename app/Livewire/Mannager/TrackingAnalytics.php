<?php

namespace App\Livewire\Mannager;

use App\Models\Campaign;
use App\Models\Project;
use App\Models\TrackingEvent;
use App\Models\Unit;
use App\Services\TrackingService;
use Carbon\Carbon;
use Livewire\Component;

class TrackingAnalytics extends Component
{
    public $dateRange = '30';

    public $customStartDate = '';

    public $customEndDate = '';

    public $useCustomDate = false;

    public $selectedCampaign = '';

    public $selectedProject = '';

    public $filterMode = 'general'; // 'general', 'campaign', 'project'

    // Campaign creation properties
    public $showCampaignModal = false;

    public $campaignId = null; // لتحديد الحملة التي يتم تعديلها

    public $isEditMode = false;

    public $campaignName = '';

    public $campaignDescription = '';

    public $campaignProject = '';

    public $campaignSource = '';

    public $campaignStartDate = '';

    public $campaignEndDate = '';

    public $campaignBudget = '';

    public $campaignGoals = [];

    public $startDate;

    public $endDate;

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    protected $trackingService;

    public function boot(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function mount()
    {
        $this->updateDateRange();
        $this->campaignStartDate = Carbon::now()->format('Y-m-d');
        $this->campaignEndDate = Carbon::now()->addDays(30)->format('Y-m-d');
    }

    public function updatedDateRange()
    {
        $this->useCustomDate = false;
        $this->updateDateRange();
    }

    public function updatedUseCustomDate()
    {
        if ($this->useCustomDate) {
            $this->updateDateRange();
        }
    }

    public function updatedCustomStartDate()
    {
        $this->updateDateRange();
    }

    public function updatedCustomEndDate()
    {
        $this->updateDateRange();
    }

    private function updateDateRange()
    {
        if ($this->useCustomDate) {
            $this->startDate = Carbon::parse($this->customStartDate)->startOfDay();
            $this->endDate = Carbon::parse($this->customEndDate)->endOfDay();
        } else {
            $this->endDate = Carbon::now()->endOfDay();
            $this->startDate = Carbon::now()->subDays((int) $this->dateRange)->startOfDay();
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }
    // private function updateDateRange()
    // {
    //     switch ($this->dateRange) {
    //         case '1':
    //             $this->startDate = Carbon::now()->subDay();
    //             break;
    //         case '7':
    //             $this->startDate = Carbon::now()->subDays(7);
    //             break;
    //         case '30':
    //             $this->startDate = Carbon::now()->subDays(30);
    //             break;
    //         case '90':
    //             $this->startDate = Carbon::now()->subDays(90);
    //             break;
    //         case '365':
    //             $this->startDate = Carbon::now()->subDays(365);
    //             break;
    //     }
    //     $this->endDate = Carbon::now();
    // }

    private function updateCustomDateRange()
    {
        if ($this->customStartDate && $this->customEndDate) {
            $this->startDate = Carbon::parse($this->customStartDate);
            $this->endDate = Carbon::parse($this->customEndDate)->endOfDay();
        }
    }

    public function openCreateModal()
    {
        $this->isEditMode = false;
        $this->resetCampaignForm();
        $this->showCampaignModal = true;
    }

    /**
     * يفتح نافذة التعديل لحملة موجودة
     */
    public function openEditModal($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $this->isEditMode = true;
        $this->campaignId = $campaign->id;
        $this->campaignName = $campaign->name;
        $this->campaignDescription = $campaign->description;
        $this->campaignProject = $campaign->project_id;
        $this->campaignSource = $campaign->source;
        $this->campaignStartDate = Carbon::parse($campaign->start_date)->format('Y-m-d');
        $this->campaignEndDate = $campaign->end_date ? Carbon::parse($campaign->end_date)->format('Y-m-d') : '';
        $this->campaignBudget = $campaign->budget;
        $this->campaignGoals = $campaign->goals ?? [];

        $this->showCampaignModal = true;
    }

    public function closeCampaignModal()
    {
        $this->showCampaignModal = false;
        $this->resetCampaignForm();
    }

    public function resetCampaignForm()
    {
        $this->campaignId = null;
        $this->isEditMode = false;
        $this->campaignName = '';
        $this->campaignDescription = '';
        $this->campaignProject = '';
        $this->campaignSource = '';
        $this->campaignStartDate = Carbon::now()->format('Y-m-d');
        $this->campaignEndDate = Carbon::now()->addDays(30)->format('Y-m-d');
        $this->campaignBudget = '';
        $this->campaignGoals = [];
    }

    public function openCampaignModal()
    {
        $this->showCampaignModal = true;
        $this->resetCampaignForm();
    }

    public function saveCampaign()
    {
        $rules = [
            'campaignName' => 'required|string|max:255',
            'campaignProject' => 'required|exists:projects,id',
            'campaignSource' => 'required|string',
            'campaignStartDate' => 'required|date',
            'campaignEndDate' => 'nullable|date|after_or_equal:campaignStartDate',
            'campaignBudget' => 'nullable|numeric|min:0',
        ];

        $messages = [
            'campaignName.required' => 'اسم الحملة مطلوب',
            'campaignProject.required' => 'المشروع مطلوب',
            'campaignSource.required' => 'مصدر الحملة مطلوب',
        ];

        $this->validate($rules, $messages);

        $data = [
            'name' => $this->campaignName,
            'description' => $this->campaignDescription,
            'project_id' => $this->campaignProject,
            'source' => $this->campaignSource,
            'start_date' => $this->campaignStartDate,
            'end_date' => $this->campaignEndDate ?: null,
            'budget' => $this->campaignBudget ?: null,
            'goals' => $this->campaignGoals,
            'status' => Campaign::STATUS_ACTIVE,
        ];

        if ($this->isEditMode) {
            // وضع التعديل
            $campaign = Campaign::findOrFail($this->campaignId);
            $campaign->update($data);
            session()->flash('message', 'تم تحديث الحملة بنجاح.');
        } else {
            // وضع الإنشاء
            Campaign::create($data);
            session()->flash('message', 'تم إنشاء الحملة بنجاح.');
        }

        $this->closeCampaignModal();
        $this->dispatch('campaign-updated'); // حدث لتحديث أي قوائم
    }

    /**
     * يحذف حملة
     */
    public function deleteCampaign($campaignId)
    {
        $campaign = Campaign::findOrFail($campaignId);
        $campaign->delete();
        session()->flash('message', 'تم حذف الحملة بنجاح.');
        $this->dispatch('campaign-updated');
    }

    public function createCampaign()
    {
        $this->validate([
            'campaignName' => 'required|string|max:255',
            'campaignProject' => 'required|exists:projects,id',
            'campaignSource' => 'required|string',
            'campaignStartDate' => 'required|date',
            'campaignEndDate' => 'nullable|date|after:campaignStartDate',
            'campaignBudget' => 'nullable|numeric|min:0',
        ], [
            'campaignName.required' => 'اسم الحملة مطلوب',
            'campaignProject.required' => 'المشروع مطلوب',
            'campaignSource.required' => 'مصدر الحملة مطلوب',
            'campaignStartDate.required' => 'تاريخ البداية مطلوب',
            'campaignEndDate.after' => 'تاريخ النهاية يجب أن يكون بعد تاريخ البداية',
        ]);

        Campaign::create([
            'name' => $this->campaignName,
            'description' => $this->campaignDescription,
            'project_id' => $this->campaignProject,
            'source' => $this->campaignSource,
            'start_date' => $this->campaignStartDate,
            'end_date' => $this->campaignEndDate ?: null,
            'budget' => $this->campaignBudget ?: null,
            'status' => Campaign::STATUS_ACTIVE,
            'goals' => $this->campaignGoals,
        ]);

        $this->closeCampaignModal();
        session()->flash('message', 'تم إنشاء الحملة بنجاح');
        $this->dispatch('campaign-created');
    }

    public function getAnalyticsProperty()
    {
        $projectId = null;
        $campaignId = null;

        if ($this->filterMode === 'project') {
            $projectId = $this->selectedProject;
        } elseif ($this->filterMode === 'campaign') {
            $campaignId = $this->selectedCampaign;
        }

        $dateRange = [$this->startDate, $this->endDate];

        return $this->trackingService->getAnalytics($dateRange, $projectId, $campaignId);
    }

    public function getConversionRatesProperty()
    {
        $campaignId = $this->filterMode === 'campaign' ? $this->selectedCampaign : null;

        return $this->trackingService->getConversionRates([$this->startDate, $this->endDate], $campaignId);
    }

    public function getPopularUnitsProperty()
    {
        $days = $this->useCustomDate ? $this->startDate->diffInDays($this->endDate) : (int) $this->dateRange;
        $campaignId = $this->filterMode === 'campaign' ? $this->selectedCampaign : null;

        return $this->trackingService->getPopularUnits(5, $days, $campaignId);
    }

    public function getPopularProjectsProperty()
    {
        $days = $this->useCustomDate ? $this->startDate->diffInDays($this->endDate) : (int) $this->dateRange;
        $campaignId = $this->filterMode === 'campaign' ? $this->selectedCampaign : null;

        return $this->trackingService->getPopularProjects(5, $days, $campaignId);
    }

    public function getTopPerformingContentProperty()
    {
        $campaignId = $this->filterMode === 'campaign' ? $this->selectedCampaign : null;

        return [
            'projects' => $this->trackingService->getTopPerformingContent([$this->startDate, $this->endDate], 5, $campaignId)['projects'],
            'units' => $this->trackingService->getTopPerformingContent([$this->startDate, $this->endDate], 5, $campaignId)['units'],
        ];
    }

    public function getTrafficSourcesProperty()
    {
        $campaignId = $this->filterMode === 'campaign' ? $this->selectedCampaign : null;

        return $this->trackingService->getTrafficSources([$this->startDate, $this->endDate], $campaignId);
    }

    public function getProjectsProperty()
    {
        return Project::where('status', 1)->orderBy('name')->get();
    }

    public function getCampaignsProperty()
    {
        $query = Campaign::with('project')
            ->orderBy($this->sortField, $this->sortDirection);

        if ($this->selectedProject) {
            $query->where('project_id', $this->selectedProject);
        }

        return $query->get();
    }

    public function getCampaignAnalyticsProperty()
    {
        if (! $this->selectedCampaign) {
            return null;
        }

        $campaign = Campaign::find($this->selectedCampaign);
        if (! $campaign) {
            return null;
        }

        return $this->trackingService->getCampaignAnalytics($campaign);
    }

    public function getProjectAnalyticsProperty()
    {
        if (! $this->selectedProject || $this->filterMode !== 'project') {
            return null;
        }

        // Get project-specific analytics for the selected time period
        $project = Project::find($this->selectedProject);
        if (! $project) {
            return null;
        }

        $query = TrackingEvent::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where(function ($q) use ($project) {
                $q->where('trackable_type', 'project')
                    ->where('trackable_id', $project->id)
                    ->orWhere(function ($subQ) use ($project) {
                        $subQ->where('trackable_type', 'unit')
                            ->whereIn('trackable_id',
                                Unit::where('project_id', $project->id)->pluck('id')
                            );
                    });
            });

        $analytics = [
            'project' => $project,
            'overview' => [
                'total_events' => $query->count(),
                'total_visits' => $query->clone()->eventType('visit')->count(),
                'total_views' => $query->clone()->eventType('view')->count(),
                'total_shows' => $query->clone()->eventType('show')->count(),
                'total_orders' => $query->clone()->eventType('order')->count(),
                'total_whatsapp' => $query->clone()->eventType('whatsapp')->count(),
                'total_calls' => $query->clone()->eventType('call')->count(),
            ],
            'period_days' => round($this->startDate->diffInDays($this->endDate)),
            'units_count' => Unit::where('project_id', $project->id)->count(),
        ];

        return $analytics;
    }

    public function render()
    {
        return view('livewire.mannager.tracking-analytics')->layout('layouts.custom');
    }
}
