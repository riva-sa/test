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

        // فلتر أساسي بناءً على دور المستخدم
        $filterByManager = function ($query) use ($user) {
            if ($user->hasRole('sales')) {
                $query->where('sales_manager_id', $user->id);
            }
        };

        // جلب كل الطلبات ذات الصلة مرة واحدة لتحسين الأداء
        $relevantOrders = UnitOrder::with(['project', 'permissions'])
            ->whereHas('project', $filterByManager)
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

        // *** التعديل الرئيسي هنا: استخدام دالة isDelayed من الـ Trait ***
        $delayedOrders = $relevantOrders->filter(function ($order) {
            return $this->isOrderDelayed($order);
        })->count();

        // جلب الطلبات الحديثة (الكود الحالي سليم)
        $baseRecentOrdersQuery = UnitOrder::with(['unit', 'project.salesManager'])
            ->whereHas('project', $filterByManager)
            ->orWhere('user_id', $user->id);

        $permissionOrdersQuery = UnitOrder::whereHas('permissions', function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                        ->orWhere('expires_at', '>', now());
                });
        })->with(['unit', 'project']);

        $recentOrders = UnitOrder::whereIn('id', $baseRecentOrdersQuery->select('id'))
            ->orWhereIn('id', $permissionOrdersQuery->select('id'))
            ->with(['unit', 'project.salesManager'])
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
