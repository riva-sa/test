<?php

namespace App\Livewire\Mannager;

use App\Models\UnitOrder;
use App\Traits\DelayedOrderLogic;
use Livewire\Component; // تأكد من أن هذا السطر موجود

class ManagerDashboard extends Component
{
    use DelayedOrderLogic; // استخدام الـ Trait

    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

    // لا حاجة لدالة getDelayedOrdersCount المنفصلة هنا، لأننا سنستخدم الـ Trait

    public function render()
    {
        $user = auth()->user();

        $filterByAccess = function ($query) use ($user) {
            if ($user->hasRole('sales')) {
                $query->where(function ($q) use ($user) {
                    // الطلبات التي يكون المستخدم هو مدير المبيعات المسؤول عن مشروعها
                    $q->whereHas('project', function ($subQ) use ($user) {
                        $subQ->where('sales_manager_id', $user->id);
                    })
                    // أو الطلبات التي مُنح المستخدم صلاحية للوصول إليها
                    ->orWhereHas('permissions', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    })
                    // أو الطلبات المعينة له مباشرة (توزيع)
                    ->orWhere('assigned_sales_user_id', $user->id);
                });
            }
        };

        // جلب كل الطلبات ذات الصلة (بما في ذلك الطلبات الجديدة status 0)
        $relevantOrders = UnitOrder::with(['project', 'permissions.user', 'lastActionByUser', 'assignedSalesUser'])
            ->where($filterByAccess)
            ->latest()
            ->get();

        // حساب الإحصائيات من البيانات التي تم جلبها
        $allOrders = $relevantOrders->count();
        $completedOrders = $relevantOrders->where('status', 4)->count();
        $customersCount = $relevantOrders->unique('phone')->count();
        $newOrders = $relevantOrders->where('status', 0)->count();
        $openOrders = $relevantOrders->where('status', 1)->count();
        $SalesTransactions = $relevantOrders->where('status', 2)->count();
        $closedOrders = $relevantOrders->where('status', 3)->count();

        // حساب الطلبات المتأخرة
        $delayedOrders = $relevantOrders->filter(function ($order) {
            return $this->isOrderDelayed($order);
        })->count();

        // جلب الطلبات الحديثة
        $recentOrders = UnitOrder::where($filterByAccess)
            ->with(['unit', 'project.salesManager', 'lastActionByUser', 'permissions.user', 'assignedSalesUser'])
            ->latest()
            ->take(10)
            ->get();

        $statusConfig = [
            0 => ['label' => 'جديد', 'color' => 'blue', 'hex' => '#3b82f6'],
            1 => ['label' => 'طلب مفتوح', 'color' => 'yellow', 'hex' => '#f59e0b'],
            2 => ['label' => 'معاملات بيعية', 'color' => 'green', 'hex' => '#22c55e'],
            3 => ['label' => 'مغلق', 'color' => 'gray', 'hex' => '#6b7280'],
            4 => ['label' => 'مكتمل', 'color' => 'teal', 'hex' => '#14b8a6'],
        ];

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
            'statusConfig' => $statusConfig,
        ])->layout('layouts.custom');
    }
}
