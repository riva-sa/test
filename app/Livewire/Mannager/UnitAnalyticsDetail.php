<?php

namespace App\Livewire\Mannager;

use App\Models\Unit;
use App\Models\TrackingEvent;
use Carbon\Carbon;
use Livewire\Component;

class UnitAnalyticsDetail extends Component
{
    public $unit;
    public $dateRange = '30';
    public $analytics = [];

    public function mount($id)
    {
        $this->unit = Unit::with('project')->findOrFail($id);
        $this->loadAnalytics();
    }

    public function updatedDateRange()
    {
        $this->loadAnalytics();
    }

    public function loadAnalytics()
    {
        $startDate = Carbon::now()->subDays(intval($this->dateRange));
        
        $query = TrackingEvent::whereBetween('created_at', [$startDate, Carbon::now()])
            ->where('trackable_type', 'App\Models\Unit')
            ->where('trackable_id', $this->unit->id);
            
        $this->analytics['overview'] = [
            'total_events' => $query->count(),
            'total_shows' => $query->clone()->where('event_type', 'show')->count(),
            'total_views' => $query->clone()->where('event_type', 'view')->count(),
            'total_orders' => $query->clone()->where('event_type', 'order')->count(),
            'total_whatsapp' => $query->clone()->whereIn('event_type', ['whatsapp', 'WhatsAppClick'])->count(),
            'total_calls' => $query->clone()->whereIn('event_type', ['call', 'PhoneCall'])->count(),
        ];
        
        $visits = $this->analytics['overview']['total_views'];
        $orders = $this->analytics['overview']['total_orders'];
        $whatsapp = $this->analytics['overview']['total_whatsapp'];
        $calls = $this->analytics['overview']['total_calls'];
        
        $this->analytics['conversion_rates'] = [
            'view_to_order' => $visits > 0 ? round(($orders / $visits) * 100, 2) : 0,
            'engagement_rate' => $visits > 0 ? round((($whatsapp + $calls + $orders) / $visits) * 100, 2) : 0,
        ];
    }

    public function render()
    {
        return view('livewire.mannager.unit-analytics-detail')
            ->layout('layouts.custom');
    }
}
