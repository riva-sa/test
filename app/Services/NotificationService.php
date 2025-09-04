<?php

namespace App\Services;

use App\Models\User;
use App\Models\UnitOrder;
use App\Models\OrderPermission;
use App\Notifications\UnitOrderUpdated;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;

class NotificationService
{
    /**
     * إرسال إشعار بتحديث الحالة
     * @param UnitOrder $order
     * @param mixed $oldStatus
     * @param mixed $newStatus
     */
    public function notifyStatusUpdate(UnitOrder $order, $oldStatus, $newStatus): void
    {
        $notificationData = [
            'old_status' => $this->getStatusName($oldStatus),
            'new_status' => $this->getStatusName($newStatus),
            'project_name' => $order->project->name ?? 'غير محدد'
        ];
        $this->sendNotification($order, 'status_update', $notificationData);
    }

    /**
     * إرسال إشعار بإضافة ملاحظة جديدة
     * @param UnitOrder $order
     * @param string $noteContent
     */
    public function notifyNewNote(UnitOrder $order, string $noteContent): void
    {
        $notificationData = [
            'note_preview' => mb_substr($noteContent, 0, 50) . '...',
            'added_by' => auth()->user()->name,
        ];
        $this->sendNotification($order, 'new_note', $notificationData);
    }

    /**
     * إرسال إشعار بتعديل بيانات العميل
     * @param UnitOrder $order
     * @param array $changedData
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
     * @param UnitOrder $order
     * @param array $changedData
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
     * @param UnitOrder $order
     */
    public function notifyMessageUpdate(UnitOrder $order): void
    {
        $this->sendNotification($order, 'message_update');
    }

    /**
     * الدالة المركزية لإرسال الإشعارات
     * @param UnitOrder $order
     * @param string $type
     * @param array $data
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
            Log::error("Failed to send '{$type}' notification for order #{$order->id}: " . $e->getMessage());
        }
    }

    /**
     * الحصول على قائمة المستخدمين المهتمين بالطلب
     * @param UnitOrder $order
     * @return Collection
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
        ];
        return $labels[$status] ?? (string) $status;
    }
}
