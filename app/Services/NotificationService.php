<?php

namespace App\Services;

use App\Models\OrderPermission;
use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\UnitOrderUpdated;
use App\Notifications\CRMAlertNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Notify on new order creation:
     * - Responsible agent: in-app (database) + email
     * - All sales managers and admins: email only
     */
    /**
     * Notify on new order creation:
     * - Assigned agent (auto-assigned or pre-set): in-app + email via 'order_assigned' type
     * - All sales managers and admins: email only via 'new_order_admin' type
     *
     * The observer calls $order->refresh() before this so assigned_sales_user_id is current.
     */
    public function notifyNewOrder(UnitOrder $order): void
    {
        try {
            $order->loadMissing(['unit', 'project', 'assignedSalesUser']);

            $agent = $order->assignedSalesUser;
            if ($agent && $agent->is_active) {
                // Use order_assigned type so the message reads "تم تعيينك لمتابعة الطلب"
                $agent->notify(new UnitOrderUpdated($order, 'order_assigned', []));

                // Bust the sidebar cache so the badge updates immediately
                Cache::forget("user_notifications_{$agent->id}");
                Cache::forget("user_notifications_unread_count_{$agent->id}");
            }

            $managersAndAdmins = User::role(['sales_manager', 'Admin'])
                ->where('is_active', true)
                ->get();

            foreach ($managersAndAdmins as $manager) {
                if ($manager->id !== $agent?->id) {
                    $manager->notify(new UnitOrderUpdated($order, 'new_order_admin', []));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send new_order notification for order #'.$order->id.': '.$e->getMessage());
        }
    }

    /**
     * Send a general system alert to specified roles or users.
     */
    public function sendAlert(string $title, string $message, array $roles = ['Admin'], array $userIds = []): void
    {
        try {
            $recipients = User::query()
                ->when(!empty($roles), fn($q) => $q->orWhereHas('roles', fn($rq) => $rq->whereIn('name', $roles)))
                ->when(!empty($userIds), fn($q) => $q->orWhereIn('id', $userIds))
                ->where('is_active', true)
                ->get();

            foreach ($recipients as $recipient) {
                $recipient->notify(new CRMAlertNotification($title, $message));
            }

            Log::info('System alert sent', ['title' => $title, 'recipient_count' => $recipients->count()]);
        } catch (\Exception $e) {
            Log::error('Failed to send system alert: '.$e->getMessage());
        }
    }

    /**
     * إرسال إشعار بتحديث الحالة
     *
     * @param  mixed  $oldStatus
     * @param  mixed  $newStatus
     */
    public function notifyStatusUpdate(UnitOrder $order, $oldStatus, $newStatus): void
    {
        $notificationData = [
            'old_status' => $this->getStatusName($oldStatus),
            'new_status' => $this->getStatusName($newStatus),
            'project_name' => $order->project->name ?? 'غير محدد',
        ];
        $this->sendNotification($order, 'status_update', $notificationData);
    }

    /**
     * إرسال إشعار بإضافة ملاحظة جديدة
     * Only send email notification when added by sales_manager
     */
    public function notifyNewNote(UnitOrder $order, string $noteContent): void
    {
        $addedBy = auth()->user();

        $notificationData = [
            'note_preview' => mb_substr($noteContent, 0, 50).'...',
            'added_by' => $addedBy->name,
            'is_sales_manager' => $addedBy->hasRole('sales_manager'),
        ];
        $this->sendNotification($order, 'new_note', $notificationData);
    }

    /**
     * إرسال إشعار بتعديل بيانات العميل
     */
    public function notifyClientUpdate(UnitOrder $order, array $changedData): void
    {
        $notificationData = [
            'changed_fields' => array_keys($changedData),
        ];
        $this->sendNotification($order, 'client_update', $notificationData);
    }

    /**
     * إرسال إشعار بتعديل معلومات الوحدة
     */
    public function notifyUnitInfoUpdate(UnitOrder $order, array $changedData): void
    {
        $notificationData = [
            'changed_fields' => array_keys($changedData),
        ];
        $this->sendNotification($order, 'unit_info_update', $notificationData);
    }

    /**
     * إرسال إشعار بتعديل رسالة الطلب الرئيسية
     */
    public function notifyMessageUpdate(UnitOrder $order): void
    {
        $this->sendNotification($order, 'message_update');
    }

    /**
     * الدالة المركزية لإرسال الإشعارات
     */
    private function sendNotification(UnitOrder $order, string $type, array $data = []): void
    {
        try {
            $usersToNotify = $this->getInterestedUsers($order);

            // إرسال الإشعارات
            foreach ($usersToNotify as $user) {
                $user->notify(new UnitOrderUpdated($order, $type, $data));
            }
        } catch (\Exception $e) {
            Log::error("Failed to send '{$type}' notification for order #{$order->id}: ".$e->getMessage());
        }
    }

    /**
     * الحصول على قائمة المستخدمين المهتمين بالطلب
     */
    private function getInterestedUsers(UnitOrder $order): Collection
    {
        $usersToNotify = collect();
        $excludeUserId = auth()->id(); // استثناء المستخدم الذي قام بالإجراء

        // 1. إضافة مدير المبيعات للمشروع
        if ($order->project && $order->project->sales_manager_id) {
            $salesManager = User::find($order->project->sales_manager_id);
            if ($salesManager) {
                $usersToNotify->push($salesManager);
            }
        }

        // 2. إضافة المستخدمين الذين لديهم صلاحيات على الطلب
        $permissionUsers = OrderPermission::where('unit_order_id', $order->id)
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter(); // إزالة أي قيم null

        $usersToNotify = $usersToNotify->merge($permissionUsers);

        // 3. إزالة المستخدم الحالي من القائمة وتكرار المستخدمين
        return $usersToNotify->where('id', '!=', $excludeUserId)->unique('id');
    }

    /**
     * الحصول على اسم الحالة
     */
    private function getStatusName($status): string
    {
        $labels = [
            0 => 'جديد',
            1 => 'طلب مفتوح',
            2 => 'معاملات بيعية',
            3 => 'مغلق',
            4 => 'مكتمل',
            5 => 'قائمة انتظار',
        ];

        return $labels[$status] ?? (string) $status;
    }
}
