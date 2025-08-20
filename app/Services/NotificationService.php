<?php

namespace App\Services;

use App\Models\User;
use App\Models\OrderPermission;
use App\Notifications\UnitOrderUpdated;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * إرسال إشعار بتحديث الحالة
     */
    public function notifyStatusUpdate($order, $oldStatus, $newStatus, $excludeUserId = null)
    {
        try {
            $notificationData = [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'status_name' => $this->getStatusName($newStatus),
                'project_name' => $order->project->name ?? 'غير محدد'
            ];

            // 1. الحصول على مدير المبيعات إذا موجود
            $salesManager = $order->project->sales_manager_id 
                ? User::with('roles')->find($order->project->sales_manager_id)
                : null;

            $usersToNotify = collect();
            
            // 2. إضافة مدير المبيعات (إذا لم يكن هو المستثنى)
            if ($salesManager && $salesManager->hasRole('sales_manager') && $salesManager->id != $excludeUserId) {
                $usersToNotify->push($salesManager);
            }

            // 3. إضافة المستخدمين ذوي الصلاحيات (استثناء المستخدم المطلوب)
            $permissionUsers = OrderPermission::where('unit_order_id', $order->id)
                ->with('user')
                ->get()
                ->pluck('user')
                ->filter()
                ->reject(function ($user) use ($excludeUserId) {
                    return $user->id == $excludeUserId;
                });

            $usersToNotify = $usersToNotify->merge($permissionUsers)->unique('id');

            // 4. إرسال الإشعارات
            foreach ($usersToNotify as $user) {
                $user->notify(new UnitOrderUpdated($order, 'status_update', $notificationData));
            }

        } catch (\Exception $e) {
            Log::error('Failed to send status update notifications: ' . $e->getMessage());
        }
    }

    /**
     * الحصول على اسم الحالة
     */
    private function getStatusName($status)
    {
        $labels = [
            0 => 'جديد',
            1 => 'طلب مفتوح',
            2 => 'معاملات بيعية',
            3 => 'مغلق',
            4 => 'مكتمل',
        ];
        return $labels[$status] ?? $status;
    }
}