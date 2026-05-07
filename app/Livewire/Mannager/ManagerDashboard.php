<?php

namespace App\Livewire\Mannager;

use App\Models\CrmNotificationRecipient;
use App\Models\UnitOrder;
use App\Traits\DelayedOrderLogic;
use Carbon\Carbon;
use Livewire\Component;

class ManagerDashboard extends Component
{
    use DelayedOrderLogic;

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

        // Aggregate counts via DB-level queries (avoids loading all orders into memory)
        $base = UnitOrder::accessibleBy($user);

        $allOrders = $base->clone()->count();
        $customersCount = $base->clone()->whereNotNull('phone')->distinct('phone')->count('phone');
        $newOrders = $base->clone()->where('status', 0)->count();
        $openOrders = $base->clone()->where('status', 1)->count();
        $SalesTransactions = $base->clone()->where('status', 2)->count();
        $closedOrders = $base->clone()->where('status', 3)->count();
        $completedOrders = $base->clone()->where('status', 4)->count();
        $waitingListCount = $base->clone()->where('status', 5)->count();

        $delayedOrders = $base->clone()->delayed()->count();

        $recentOrders = UnitOrder::accessibleBy($user)
            ->with(['unit', 'project.salesManager', 'permissions.user', 'assignedSalesUser'])
            ->latest()
            ->take(10)
            ->get();

        $statusConfig = [];
        foreach (UnitOrder::STATUS_LABELS as $key => $label) {
            $statusConfig[$key] = [
                'label' => $label,
                'hex' => UnitOrder::STATUS_COLORS[$key] ?? '#6b7280',
            ];
        }

        // Target progress for sales reps viewing their own dashboard
        $targetProgress = null;
        if ($user->hasRole('sales')) {
            $targetProgress = app(\App\Services\TargetTrackingService::class)->getAllProgress($user->id);
        }

        // Recent unread system alerts for the notifications card
        $systemAlerts = CrmNotificationRecipient::where('user_id', $user->id)
            ->whereNull('read_at')
            ->with('notification')
            ->latest('created_at')
            ->take(6)
            ->get();

        $orderAlerts = $user->unreadNotifications()
            ->latest()
            ->take(4)
            ->get();

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
            'systemAlerts' => $systemAlerts,
            'orderAlerts' => $orderAlerts,
        ])->layout('layouts.custom');
    }
}
