<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use App\Models\UnitOrder;
use App\Traits\DelayedOrderLogic;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ManageOrders extends Component
{
    use DelayedOrderLogic;
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $projectFilter = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 50;

    public $salesManagerFilter = '';

    public $delayedFilter = '';

    public $fromDate = '';

    public $toDate = '';

    // Bulk actions
    public $selectedOrders = [];
    public $bulkAssigneeId = '';
    public bool $clearOldPermissions = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
        'salesManagerFilter' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 100],
        'delayedFilter' => ['except' => ''],
        'fromDate' => ['except' => ''],
        'toDate' => ['except' => ''],
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
        return $this->isOrderDelayed($order);
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

    public function updated($property)
    {
        // Only reset pagination when a filter/search field changes.
        // Selecting orders (checkboxes) or changing other UI state must
        // NOT send the user back to page 1.
        if (in_array($property, [
            'search',
            'statusFilter',
            'projectFilter',
            'salesManagerFilter',
            'delayedFilter',
            'fromDate',
            'toDate',
            'perPage',
        ], true)) {
            $this->resetPage();
        }
    }

    public function bulkAssign()
    {
        if (empty($this->selectedOrders)) {
            session()->flash('error', 'الرجاء تحديد طلبات للتعيين.');
            return;
        }

        if (empty($this->bulkAssigneeId)) {
            session()->flash('error', 'الرجاء اختيار موظف مبيعات.');
            return;
        }

        $orders = UnitOrder::accessibleBy(auth()->user())
            ->whereIn('id', $this->selectedOrders)
            ->get();

        foreach ($orders as $order) {
            // Remove existing permissions only if requested
            if ($this->clearOldPermissions) {
                $order->permissions()->delete();
            }

            // Update assigned_sales_user_id
            $order->update([
                'assigned_sales_user_id' => $this->bulkAssigneeId,
                'last_action_by_user_id' => auth()->id()
            ]);

            // Create/update permission for this user
            \App\Models\OrderPermission::create([
                'unit_order_id' => $order->id, 
                'user_id' => $this->bulkAssigneeId,
                'permission_type' => 'manage', 
                'granted_by' => auth()->id()
            ]);
        }

        $this->selectedOrders = [];
        $this->bulkAssigneeId = '';
        session()->flash('message', 'تم تعيين الطلبات بنجاح.');
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

    public function deleteOrder($orderId)
    {
        $order = UnitOrder::accessibleBy(auth()->user())->find($orderId);

        if ($order) {
            $order->delete();
            session()->flash('message', 'تم حذف الطلب بنجاح.');
        } else {
            session()->flash('error', 'لا تملك صلاحية حذف هذا الطلب.');
        }
    }

    public function export()
    {
        $query = UnitOrder::with([
            'notes.user',
            'unit',
            'project.salesManager',
            'user',
            'permissions.user',
            'lastActionByUser',
            'assignedSalesUser',
        ])->accessibleBy(auth()->user());

        $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_employee_name', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_employee_phone', 'like', '%'.$this->search.'%')
                    ->orWhereHas('unit', function ($q) {
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
                $selectedUser = \App\Models\User::find($this->salesManagerFilter);
                if ($selectedUser) {
                    $query->where("assigned_sales_user_id", $this->salesManagerFilter);
                }
            })
            ->when($this->fromDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->toDate);
            });

        $query->orderBy($this->sortField, $this->sortDirection);

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UnitOrdersExport($query), 'orders_export_'.now()->format('Y-m-d').'.xlsx');
    }

    public function render()
    {
        $query = UnitOrder::with([
            'notes',
            'unit',
            'project.salesManager',
            'user',
            'permissions.user',
            'lastActionByUser',
            'assignedSalesUser',
        ])->accessibleBy(auth()->user());

        // الخطوة 3: تطبيق فلاتر الواجهة (البحث، الحالة، المشروع، إلخ)
        $query->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_name', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_employee_name', 'like', '%'.$this->search.'%')
                    ->orWhere('bank_employee_phone', 'like', '%'.$this->search.'%')
                    ->orWhereHas('unit', function ($q) {
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
                $selectedUser = \App\Models\User::find($this->salesManagerFilter);
                if ($selectedUser) {
                    $query->where("assigned_sales_user_id", $this->salesManagerFilter);
                }
            })
            ->when($this->fromDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->toDate);
            });

        if ($this->delayedFilter == '1') {
            $query->delayed();
        }

        $orders = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        $delayedOrdersCount = $this->getDelayedOrdersCount(auth()->user());

        // الخطوة 9: إرجاع العرض مع كل البيانات المطلوبة
        return view('livewire.mannager.manage-orders', [
            'orders' => $orders,
            'delayedOrdersCount' => $delayedOrdersCount,
            'statusLabels' => UnitOrder::STATUS_LABELS,
            'purchaseTypes' => [
                'cash' => 'كاش',
                'installment' => 'تقسيط',
            ],
            'purchasePurposes' => [
                'investment' => 'استثمار',
                'personal' => 'سكنى',
            ],
            'supportTypes' => [
                'technical' => 'فنى',
                'financial' => 'مالى',
                'general' => 'عام',
            ],
            'projects' => Project::all(),
            'salesManagers' => \App\Models\User::role('sales')->get(),
        ])->layout('layouts.custom');
    }

    private function getDelayedOrdersCount($user)
    {
        return UnitOrder::accessibleBy($user)
            ->delayed()
            ->count();
    }
}
