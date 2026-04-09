<?php

namespace App\Livewire\Mannager;

use App\Models\UnitOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AutoAssignmentReport extends Component
{
    use WithPagination;

    public $dateRange = 'today';
    public $startDate;
    public $endDate;

    public function mount()
    {
        $this->setDefaultDates();
    }

    public function updatedDateRange()
    {
        $this->setDefaultDates();
        $this->resetPage();
    }

    private function setDefaultDates()
    {
        switch ($this->dateRange) {
            case 'today':
                $this->startDate = Carbon::today()->startOfDay();
                $this->endDate = Carbon::today()->endOfDay();
                break;
            case 'yesterday':
                $this->startDate = Carbon::yesterday()->startOfDay();
                $this->endDate = Carbon::yesterday()->endOfDay();
                break;
            case 'this_week':
                $this->startDate = Carbon::now()->startOfWeek();
                $this->endDate = Carbon::now()->endOfWeek();
                break;
            case 'this_month':
                $this->startDate = Carbon::now()->startOfMonth();
                $this->endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                // Optional
                if (!$this->startDate) {
                    $this->startDate = Carbon::today()->startOfDay();
                    $this->endDate = Carbon::today()->endOfDay();
                }
                break;
        }
    }

    public function getSalesRepsQuery()
    {
        $salesUsers = User::role(config('lead_import.sales_role', 'sales'))->get();

        $counts = UnitOrder::whereNotNull('assigned_sales_user_id')
                ->where('created_at', '>=', $this->startDate)
                ->where('created_at', '<=', $this->endDate)
                // Optionally exclude bulk import, assuming we only want to track frontend auto-assignments
                ->where('order_source', '!=', UnitOrder::ORDER_SOURCE_BULK_IMPORT)
                ->select('assigned_sales_user_id', DB::raw('count(*) as total_assigned'))
                ->groupBy('assigned_sales_user_id')
                ->get()
                ->keyBy('assigned_sales_user_id');

        return $salesUsers->map(function ($user) use ($counts) {
            $user->total_assigned = $counts->has($user->id) ? $counts[$user->id]->total_assigned : 0;
            return $user;
        })->sortByDesc('total_assigned');
    }

    public function render()
    {
        $salesReps = $this->getSalesRepsQuery();
        $totalAssigned = $salesReps->sum('total_assigned');
        $activeRepsCount = $salesReps->where('is_active', true)->where('on_vacation', false)->count();

        $recentAssignments = UnitOrder::with(['assignedSalesUser', 'project'])
            ->whereNotNull('assigned_sales_user_id')
            ->where('created_at', '>=', $this->startDate)
            ->where('created_at', '<=', $this->endDate)
            ->where('order_source', '!=', UnitOrder::ORDER_SOURCE_BULK_IMPORT)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.mannager.auto-assignment-report', [
            'salesReps' => $salesReps,
            'totalAssigned' => $totalAssigned,
            'activeRepsCount' => $activeRepsCount,
            'recentAssignments' => $recentAssignments,
        ])->layout('layouts.custom');
    }
}
