<?php

namespace App\Livewire\Mannager;

use App\Services\TrackingService;
use Livewire\Component;

class SessionJourneys extends Component
{
    // خصائص لتخزين كل البيانات الجديدة
    public $journeys;

    public $stats = [];

    public $topFunnels = [];

    public $frictionPoints = [];

    // خصائص الـ Modal
    public $selectedJourneyEvents = [];

    public $showJourneyModal = false;

    public function mount(TrackingService $trackingService)
    {
        $analysisData = $trackingService->getSessionJourneys();

        $this->journeys = $analysisData['journeys'];
        $this->stats = $analysisData['stats'];
        $this->topFunnels = $analysisData['top_funnels'];
        $this->frictionPoints = $analysisData['friction_points'];
    }

    public function showJourneyDetails($sessionId)
    {
        $journey = $this->journeys->firstWhere('session_id', $sessionId);
        if ($journey) {
            $eventsCollection = $journey['events'];
            $eventsCollection->load(['trackable' => function ($query) {
                if ($query->getQuery()->from === 'projects') {
                    $query->select('id', 'name');
                } elseif ($query->getQuery()->from === 'units') {
                    $query->select('id', 'title', 'unit_number');
                }
            }]);

            $this->selectedJourneyEvents = $eventsCollection;
            $this->showJourneyModal = true;
        }
    }

    public function render()
    {
        return view('livewire.mannager.session-journeys')->layout('layouts.custom');
    }
}
