<?php

namespace App\Livewire\Mannager;

use App\Models\AuditLogEntry;
use App\Models\User;
use App\Models\UnitOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class SystemActivities extends Component
{
    use WithPagination;

    public $search = '';
    public $actor_id = '';
    public $activity_type = '';
    public $order_id = '';
    public $perPage = 50;

    // Search terms for filters
    public $orderSearch = '';
    public $actorSearch = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'actor_id' => ['except' => ''],
        'activity_type' => ['except' => ''],
        'order_id' => ['except' => ''],
    ];

    public function resetFilters()
    {
        $this->reset(['search', 'actor_id', 'activity_type', 'order_id', 'orderSearch', 'actorSearch']);
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingActorId()
    {
        $this->resetPage();
    }

    public function updatingActivityType()
    {
        $this->resetPage();
    }

    public function updatingOrderId()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        if (!$user->hasRole('Admin') && !$user->hasRole('sales_manager')) {
            abort(403);
        }

        $query = AuditLogEntry::query()->with(['actor', 'order.project']);

        if ($this->actor_id) {
            $query->where('actor_id', $this->actor_id);
        }

        if ($this->activity_type) {
            $query->where('activity_type', $this->activity_type);
        }

        if ($this->order_id) {
            $query->where('order_id', $this->order_id);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('actor', function($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('order', function($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                  })
                  ->orWhere('order_id', 'like', '%' . $this->search . '%');
            });
        }

        $activities = $query->latest('created_at')->paginate($this->perPage);

        // Advanced Grouping: Date -> Order
        $groupedActivities = [];
        foreach ($activities as $activity) {
            $date = Carbon::parse($activity->created_at);
            if ($date->isToday()) $dateKey = 'اليوم';
            elseif ($date->isYesterday()) $dateKey = 'أمس';
            else $dateKey = $date->translatedFormat('j F Y');

            $orderKey = $activity->order_id ?: 'system_' . $activity->id;
            
            if (!isset($groupedActivities[$dateKey])) {
                $groupedActivities[$dateKey] = [];
            }

            if (!isset($groupedActivities[$dateKey][$orderKey])) {
                $groupedActivities[$dateKey][$orderKey] = [
                    'order' => $activity->order,
                    'order_id' => $activity->order_id,
                    'items' => []
                ];
            }

            $groupedActivities[$dateKey][$orderKey]['items'][] = $activity;
        }

        // Fetch orders - Always load recent orders if search is empty
        $orders = UnitOrder::query()
            ->when($this->orderSearch, function($q) {
                $q->where('name', 'like', '%' . $this->orderSearch . '%')
                  ->orWhere('id', 'like', '%' . $this->orderSearch . '%');
            })
            ->latest()
            ->take(20)
            ->get();

        // Fetch actors - Always load actors if search is empty
        $actors = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'sales_manager', 'sales', 'follow_up']);
            })
            ->when($this->actorSearch, function($q) {
                $q->where('name', 'like', '%' . $this->actorSearch . '%');
            })
            ->orderBy('name')
            ->take(20)
            ->get();

        return view('livewire.mannager.system-activities', [
            'groupedActivities' => $groupedActivities,
            'activities' => $activities,
            'actors' => $actors,
            'orders' => $orders,
            'types' => [
                'status_change' => 'تغيير حالة',
                'data_change' => 'تعديل بيانات',
                'permission_grant' => 'منح صلاحية',
                'note_added' => 'إضافة ملاحظة',
                'leaderboard_adjustment' => 'تعديل لوحة الصدارة',
            ]
        ])->layout('layouts.custom');
    }
}
