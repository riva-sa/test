<?php

namespace App\Livewire\Mannager;

use App\Jobs\ExportOrdersJob;
use App\Models\OrderExport;
use App\Models\Project;
use App\Models\UnitOrder;
use App\Traits\DelayedOrderLogic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public $sourceFilter = '';

    // Bulk actions
    public $selectedOrders = [];
    public $bulkAssigneeId = '';
    public bool $clearOldPermissions = true;

    // Export status tracking
    public ?int $activeExportId = null;
    public string $exportStatus = '';
    public int $exportProgress = 0;
    public int $exportTotalRows = 0;
    public int $exportProcessedRows = 0;
    public bool $showExportStatus = false;
    public ?string $exportErrorMessage = null;

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
        'sourceFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadActiveExport();
    }

    public function loadActiveExport()
    {
        $export = OrderExport::where('user_id', auth()->id())
            ->where(function ($q) {
                $q->whereIn('status', ['pending', 'processing'])
                    ->orWhere(function ($completedQ) {
                        $completedQ->where('status', 'completed')
                            ->where('completed_at', '>=', now()->subMinutes(30));
                    });
            })
            ->latest()
            ->first();

        if ($export) {
            $this->activeExportId = $export->id;
            $this->showExportStatus = true;
            $this->checkExportStatus();
        }
    }

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
            'sourceFilter',
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
        // Prevent multiple concurrent exports
        $existingExport = OrderExport::where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        if ($existingExport) {
            $this->activeExportId = $existingExport->id;
            $this->showExportStatus = true;
            $this->checkExportStatus();
            return;
        }

        $fileName = 'orders_export_' . now()->format('Y-m-d_His') . '.csv';

        // Capture current filters
        $filters = [
            'search' => $this->search,
            'statusFilter' => $this->statusFilter,
            'projectFilter' => $this->projectFilter,
            'salesManagerFilter' => $this->salesManagerFilter,
            'fromDate' => $this->fromDate,
            'toDate' => $this->toDate,
            'sourceFilter' => $this->sourceFilter,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ];

        // Create export record
        $export = OrderExport::create([
            'user_id' => auth()->id(),
            'file_name' => $fileName,
            'filters' => $filters,
            'status' => 'pending',
        ]);

        // Dispatch background job
        ExportOrdersJob::dispatch($export->id, auth()->id(), $filters);

        // Show status UI
        $this->activeExportId = $export->id;
        $this->exportStatus = 'pending';
        $this->exportProgress = 0;
        $this->showExportStatus = true;
        $this->exportErrorMessage = null;
    }

    public function checkExportStatus()
    {
        if (! $this->activeExportId) {
            return;
        }

        $export = OrderExport::find($this->activeExportId);

        if (! $export) {
            $this->showExportStatus = false;
            return;
        }

        $this->exportStatus = $export->status;
        $this->exportProgress = $export->progress;
        $this->exportTotalRows = $export->total_rows;
        $this->exportProcessedRows = $export->processed_rows;
        $this->exportErrorMessage = $export->error_message;
    }

    public function downloadExport()
    {
        if (! $this->activeExportId) {
            return;
        }

        $export = OrderExport::find($this->activeExportId);

        if (! $export || ! $export->isDownloadable()) {
            session()->flash('error', 'الملف غير متاح للتحميل.');
            return;
        }

        return Storage::disk('local')->download(
            $export->file_path,
            $export->file_name,
            ['Content-Type' => 'text/csv; charset=UTF-8']
        );
    }

    public function dismissExportStatus()
    {
        $this->showExportStatus = false;
        $this->activeExportId = null;
        $this->exportStatus = '';
        $this->exportProgress = 0;
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
            'broker',
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
            })
            ->when($this->sourceFilter !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('order_source', $this->sourceFilter)
                        ->orWhere('marketing_source', $this->sourceFilter);
                });
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
            'orderSources' => $this->getOrderSources(),
        ])->layout('layouts.custom');
    }

    private function getOrderSources(): array
    {
        $orderSources = [
            'legacy' => 'نظام قديم',
            'frontend_popup' => 'نافذة منبثقة',
            'frontend_unit' => 'صفحة الوحدة',
            'manager' => 'إضافة يدوية',
            'bulk_import' => 'رفع ملف',
            'social_media' => 'سوشيال ميديا',
            'broker' => 'وسيط عقاري',
        ];

        // Add the actual marketing/lead sources found in the data
        UnitOrder::query()
            ->whereNotNull('marketing_source')
            ->where('marketing_source', '!=', '')
            ->distinct()
            ->orderBy('marketing_source')
            ->pluck('marketing_source')
            ->each(function ($source) use (&$orderSources) {
                if (! array_key_exists($source, $orderSources)) {
                    $orderSources[$source] = $source;
                }
            });

        return $orderSources;
    }

    private function getDelayedOrdersCount($user)
    {
        return UnitOrder::accessibleBy($user)
            ->delayed()
            ->count();
    }
}
