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
                    $query->accessibleBy($selectedUser);
                }
            })
            ->when($this->fromDate, function ($query) {
                $query->whereDate('created_at', '>=', $this->fromDate);
            })
            ->when($this->toDate, function ($query) {
                $query->whereDate('created_at', '<=', $this->toDate);
            });

        $records = $query->orderBy($this->sortField, $this->sortDirection)->get();

        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UnitOrdersExport($records), 'orders_export_'.now()->format('Y-m-d').'.xlsx');
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
                    $query->accessibleBy($selectedUser);
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
            'salesManagers' => \App\Models\User::whereHas('roles', function ($q) {
                $q->where('name', 'sales');
            })->get(),
        ])->layout('layouts.custom');
    }

    private function getDelayedOrdersCount($user)
    {
        return UnitOrder::accessibleBy($user)
            ->delayed()
            ->count();
    }
}
