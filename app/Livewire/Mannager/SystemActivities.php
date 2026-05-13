<?php

namespace App\Livewire\Mannager;

use App\Models\AuditLogEntry;
use App\Models\User;
use App\Models\UnitOrder;
use Livewire\Component;
use Livewire\WithPagination;

class SystemActivities extends Component
{
    use WithPagination;

    public $search = '';
    public $actor_id = '';
    public $activity_type = '';
    public $order_id = '';
    public $perPage = 25;

    protected $queryString = [
        'search' => ['except' => ''],
        'actor_id' => ['except' => ''],
        'activity_type' => ['except' => ''],
        'order_id' => ['except' => ''],
        'page' => ['except' => 1],
    ];

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

        $query = AuditLogEntry::query()->with(['actor', 'order']);

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

        return view('livewire.mannager.system-activities', [
            'activities' => $activities,
            'actors' => User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Admin', 'sales_manager', 'sales', 'follow_up']);
            })->get(),
            'orders' => UnitOrder::latest()->take(50)->get(), // Limited for performance, search should handle others
            'types' => [
                'status_change' => 'تغيير حالة',
                'permission_grant' => 'منح صلاحية',
                'note_added' => 'إضافة ملاحظة',
                'leaderboard_adjustment' => 'تعديل لوحة الصدارة',
            ]
        ])->layout('layouts.custom');
    }
}
