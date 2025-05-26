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

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
        'salesManagerFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
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

    public function isDelayed($order)
    {
        // إذا لم يكن هناك طلب أو لم يتم تحديثه مطلقًا
        if (!$order || !$order->updated_at) {
            return false;
        }

        // إذا كان الطلب مغلقًا فلا نعرض التأخير
        if ($order->status == 3) {
            return false;
        }

        // التحقق مما إذا كان آخر تعديل يزيد عن 3 أيام
        return $order->updated_at->lt(Carbon::now()->subDays(3));
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