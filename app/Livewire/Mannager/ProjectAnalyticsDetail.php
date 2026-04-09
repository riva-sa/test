<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use App\Services\EnhancedTrackingService;
use Carbon\Carbon;
use Livewire\Component;

class ProjectAnalyticsDetail extends Component
{
    public $project;
    public $dateRange = '30';
    public $analytics = [];

    public function mount($id)
    {
        $this->project = Project::findOrFail($id);
        $this->loadAnalytics();
    }

    public function updatedDateRange()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $trackingService = new EnhancedTrackingService();
        $startDate = Carbon::now()->subDays(intval($this->dateRange));
        
        $filters = [
            $startDate, Carbon::now()
        ];

        $this->analytics = $trackingService->getAnalytics($filters, $this->project->id);
        $this->analytics['conversion_rates'] = $trackingService->getConversionRates($filters, $this->project->id);
        $this->analytics['traffic_sources'] = $trackingService->getTrafficSources($filters, $this->project->id);
        
        $topContent = $trackingService->getTopPerformingContent($filters, 5, $this->project->id);
        $this->analytics['top_units'] = $topContent['units'];
    }

    public function render()
    {
        return view('livewire.mannager.project-analytics-detail')
            ->layout('layouts.custom');
    }
}
