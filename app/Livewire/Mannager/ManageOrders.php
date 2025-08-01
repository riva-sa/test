<?php

namespace App\Livewire\Mannager;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UnitOrder;
use App\Models\OrderNote;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ManageOrders extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $projectFilter = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    public $salesManagerFilter = '';
    public $delayedFilter = '';
    
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
        'salesManagerFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10],
        'delayedFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingProjectFilter()
    {
        $this->resetPage();
    }

    public function updatingDelayedFilter()
    {
        $this->resetPage();
    }

    public function isDelayed($order)
    {
        if (!$order || !$order->updated_at) {
            return false;
        }

        // الطلب مكتمل أو مغلق؟ مش متأخر
        if (in_array($order->status, [3, 4])) {
            return false;
        }

        $lastActorId = $order->last_action_by_user_id;
        $salesManagerId = $order->project->sales_manager_id ?? null;

        // لو آخر من تعامل هو المسؤول المباشر → مش متأخر
        if ($lastActorId == $salesManagerId) {
            return false;
        }

        // هل الشخص عنده صلاحية إدارة للطلب من نفس المسؤول؟
        $hasDelegatedPermission = $order->permissions()
            ->where('user_id', $lastActorId)
            ->where('permission_type', 'manage') // أو 'edit' حسب منطقك
            ->where('granted_by', $salesManagerId) // فقط من المسؤول المباشر
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasDelegatedPermission) {
            return false;
        }

        // ما عدا ذلك، إذا مر أكثر من 3 أيام → متأخر
        return $order->updated_at->lt(now()->subDays(3));
    }



    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
            $this->sortField = $field;
        }
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect()->route('frontend.home');
    }

    public function render()
    {
        $query = UnitOrder::with(['notes', 'unit', 'project.salesManager', 'user']);
        if (auth()->user()->hasRole('sales')) {
            $query->where(function ($q) {
                // الطلبات التي المشروع الخاص بها تحت إدارة المستخدم
                $q->whereHas('project', function($subQ) {
                    $subQ->where('sales_manager_id', auth()->id());
                })
                // أو الطلبات التي تم منح المستخدم صلاحية عليها
                ->orWhereHas('permissions', function($subQ) {
                    $subQ->where('user_id', auth()->id());
                });
            });
        }

        // Apply other filters...
        $orders = $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('email', 'like', '%'.$this->search.'%')
                  ->orWhere('phone', 'like', '%'.$this->search.'%')
                  ->orWhereHas('unit', function($q) {
                      $q->where('title', 'like', '%'.$this->search.'%');
                  });
            });
        })
        ->when($this->statusFilter !== '', function ($query) {
            $query->where('status', $this->statusFilter);
        })
        ->when($this->projectFilter !== '', function ($query) {
            $query->where('project_id', $this->projectFilter);
        })
        ->when($this->delayedFilter == '1', function ($query) {
            $query->where('status', '!=', 3)
                ->where('updated_at', '<', now()->subDays(3));
        })
        ->when($this->salesManagerFilter, function ($query) {
            $query->whereHas('project', function ($q) {
                $q->where('sales_manager_id', $this->salesManagerFilter);
            });
        })
        ->orderBy($this->sortField, $this->sortDirection)
        ->paginate($this->perPage);


        return view('livewire.mannager.manage-orders', [
            'orders' => $orders,
            'statusLabels' => [
                0 => 'جديد',
                1 => 'طلب مفتوح',
                2 => 'معاملات بيعية',
                3 => 'مغلق',
                4 => 'مكتمل'
            ],
            'purchaseTypes' => [
                'cash' => 'كاش',
                'installment' => 'تقسيط'
            ],
            'purchasePurposes' => [
                'investment' => 'استثمار',
                'personal' => 'سكنى'
            ],
            'supportTypes' => [
                'technical' => 'فنى',
                'financial' => 'مالى',
                'general' => 'عام'
            ],
            'projects' => Project::all(),
            'salesManagers' => \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'sales');
            })->get()
        ])->layout('layouts.custom');
    }
}