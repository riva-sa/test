<?php

namespace App\Livewire\Mannager;

use Livewire\Component;
use App\Models\Project;
use App\Models\Unit;
use App\Models\TrackingEvent;
use App\Services\TrackingService;
use Carbon\Carbon;

class TrackingAnalytics extends Component
{
    public $dateRange = '30';
    public $startDate;
    public $endDate;
    
    protected $trackingService;
    
    public function boot(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }
    
    public function mount()
    {
        $this->updateDateRange();
    }
    
    public function updatedDateRange()
    {
        $this->updateDateRange();
    }
    
    private function updateDateRange()
    {
        switch ($this->dateRange) {
            case '1':
                $this->startDate = Carbon::now()->subDay();
                break;
            case '7':
                $this->startDate = Carbon::now()->subDays(7);
                break;
            case '30':
                $this->startDate = Carbon::now()->subDays(30);
                break;
            case '90':
                $this->startDate = Carbon::now()->subDays(90);
                break;
            case '365':
                $this->startDate = Carbon::now()->subDays(365);
                break;
        }
        $this->endDate = Carbon::now();
    }
    
    public function getAnalyticsProperty()
    {
        return $this->trackingService->getAnalytics([$this->startDate, $this->endDate]);
    }
    
    public function getConversionRatesProperty()
    {
        return $this->trackingService->getConversionRates([$this->startDate, $this->endDate]);
    }
    
    public function getPopularUnitsProperty()
    {
        return $this->trackingService->getPopularUnits(10, (int)$this->dateRange);
    }
    
    public function getPopularProjectsProperty()
    {
        return $this->trackingService->getPopularProjects(10, (int)$this->dateRange);
    }
    
    public function getTopPerformingContentProperty()
    {
        return [
            'projects' => $this->trackingService->getTopPerformingContent([$this->startDate, $this->endDate], 5)['projects'],
            'units' => $this->trackingService->getTopPerformingContent([$this->startDate, $this->endDate], 5)['units']
        ];
    }
    
    public function getTrafficSourcesProperty()
    {
        return $this->trackingService->getTrafficSources([$this->startDate, $this->endDate]);
    }

    public function render()
    {
        return view('livewire.mannager.tracking-analytics')->layout('layouts.custom');
    }
}