<?php

namespace App\Livewire\Mannager;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use App\Models\UnitOrder;
use App\Models\Project;

class ManagerDashboard extends Component
{

    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

    public function render()
    {
        $user = auth()->user();

        // Define base query filter based on role
        $filterByManager = function ($query) use ($user) {
            if ($user->hasRole('sales')) {
                $query->where('sales_manager_id', $user->id);
            }
        };

        // All orders count
        $allOrders = UnitOrder::whereHas('project', $filterByManager)
            ->count();

        // Completed orders count (status 4)
        $completedOrders = UnitOrder::whereHas('project', $filterByManager)
            ->where('status', 4)
            ->count();
        // Customer stats (unique customers by phone)
        $customersCount = UnitOrder::whereHas('project', $filterByManager)
            ->distinct('phone')
            ->count('phone');

        // Order status counts
        $newOrders = UnitOrder::whereHas('project', $filterByManager)
            ->where('status', 0)
            ->count();

        $openOrders = UnitOrder::whereHas('project', $filterByManager)
            ->where('status', 1)
            ->count();

        $SalesTransactions = UnitOrder::whereHas('project', $filterByManager)
            ->where('status', 2)
            ->count();

        $delayedOrders = UnitOrder::whereHas('project', $filterByManager)
            ->whereIn('status', [0, 1])
            ->where('created_at', '<', now()->subDays(3)) // مثال: الطلب له أكثر من 3 أيام
            ->count();

        $closedOrders = UnitOrder::whereHas('project', $filterByManager)
            ->where('status', 3)
            ->count();

        // 1. طلبات المستخدم مباشرة أو المشاريع التي يديرها
        $baseRecentOrdersQuery = UnitOrder::with(['unit', 'project.salesManager'])
            ->whereHas('project', $filterByManager)
            ->orWhere('user_id', $user->id);

        // 2. طلبات لديه صلاحية عليها
        $permissionOrdersQuery = UnitOrder::whereHas('permissions', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
                });
        })->with(['unit', 'project']);

        // 3. دمج الاثنين بدون تكرار باستخدام UNION
        $recentOrders = UnitOrder::whereIn('id', $baseRecentOrdersQuery->select('id'))
            ->orWhereIn('id', $permissionOrdersQuery->select('id'))
            ->with(['unit', 'project.salesManager'])
            ->latest()
            ->take(10)
            ->get();


        return view('livewire.mannager.manager-dashboard', [
            'customersCount' => $customersCount,
            'newOrders' => $newOrders,
            'openOrders' => $openOrders,
            'delayedOrders' => $delayedOrders,
            'SalesTransactions' => $SalesTransactions,
            'closedOrders' => $closedOrders,
            'recentOrders' => $recentOrders,
            'allOrders' => $allOrders,
            'completedOrders' => $completedOrders,
            'statusLabels' => [
                0 => 'جديد',
                1 => 'طلب مفتوح',
                2 => 'معاملات بيعية',
                3 => 'مغلق',
                4 => 'مكتمل'
            ],
            'statusColors' => [
                0 => 'blue',
                1 => 'yellow',
                2 => 'green',
                3 => 'gray',
                4 => 'green'
            ],
        ])->layout('layouts.custom');
    }

}
