<?php

namespace App\Notifications;

use App\Models\UnitOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UnitOrderUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $type;
    public $data;

    /**
     * @param UnitOrder $order الطلب
     * @param string $type نوع الحدث: new_order, status_update, message_update, permission_granted, permission_revoked
     * @param array $data بيانات إضافية حسب الحاجة
     */
    public function __construct(UnitOrder $order, string $type, array $data = [])
    {
        $this->order = $order;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * القنوات المستخدمة
     */
    public function via($notifiable)
    {
        return ['database']; // ممكن تضيف mail أو broadcast لو حابب
    }

    /**
     * البيانات المخزنة في قاعدة البيانات
     */
    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->order->id,
            'type' => $this->type,
            'message' => $this->generateMessage(),
            'data' => $this->data,
            'updated_by' => auth()->user()?->name,
        ];
    }

    /**
     * توليد رسالة بناءً على نوع الحدث
     */
    protected function generateMessage()
    {
        return match ($this->type) {
            'new_order' => "تم إنشاء طلب جديد (#{$this->order->id}) للعميل {$this->order->name}",
            'status_update' => "تم تغيير حالة الطلب (#{$this->order->id}) إلى {$this->statusLabel()}",
            'message_update' => "تم تعديل رسالة/ملاحظة الطلب (#{$this->order->id})",
            'permission_granted' => "تم منح صلاحية لمستخدم {$this->data['user_name']} على الطلب (#{$this->order->id})",
            'permission_revoked' => "تم إلغاء صلاحية المستخدم {$this->data['user_name']} من الطلب (#{$this->order->id})",
            default => "تم تحديث الطلب (#{$this->order->id})",
        };
    }

    /**
     * إرجاع النص العربي للحالة
     */
    protected function statusLabel()
    {
        $labels = [
            0 => 'جديد',
            1 => 'طلب مفتوح',
            2 => 'معاملات بيعية',
            3 => 'مغلق',
            4 => 'مكتمل',
        ];
        return $labels[$this->order->status] ?? $this->order->status;
    }
}
