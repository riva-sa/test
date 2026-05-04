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

        // جلب كل الطلبات ذات الصلة مرة واحدة لتحسين الأداء
        $relevantOrders = UnitOrder::accessibleBy($user)
            ->with(['project.salesManager', 'permissions.user'])
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
        $waitingListCount = $relevantOrders->where('status', 5)->count();

        // *** التعديل الرئيسي هنا: استخدام دالة isDelayed من الـ Trait ***
        $delayedOrders = $relevantOrders->filter(function ($order) {
            return $this->isOrderDelayed($order);
        })->count();

        $recentOrders = UnitOrder::accessibleBy($user)
            ->with(['unit', 'project.salesManager', 'permissions.user'])
            ->latest()
            ->take(10)
            ->get();

        $statusConfig = [];
        foreach (\App\Models\UnitOrder::STATUS_LABELS as $key => $label) {
            $statusConfig[$key] = [
                'label' => $label,
                'hex' => \App\Models\UnitOrder::STATUS_COLORS[$key] ?? '#6b7280',
            ];
        }

        // Fetch targets if the user is a sales rep
        $targetProgress = null;
        if ($user->hasRole('sales')) {
            $targetProgress = app(\App\Services\TargetTrackingService::class)->getAllProgress($user->id);
        }

        return view('livewire.mannager.manager-dashboard', [
            'customersCount' => $customersCount,
            'newOrders' => $newOrders,
            'openOrders' => $openOrders,
            'delayedOrders' => $delayedOrders,
            'SalesTransactions' => $SalesTransactions,
            'closedOrders' => $closedOrders,
            'waitingListCount' => $waitingListCount,
            'recentOrders' => $recentOrders,
            'allOrders' => $allOrders,
            'completedOrders' => $completedOrders,
            'statusConfig' => $statusConfig,
            'targetProgress' => $targetProgress,
        ])->layout('layouts.custom');
    }
}
