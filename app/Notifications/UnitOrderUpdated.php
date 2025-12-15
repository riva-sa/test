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
     * @param  UnitOrder  $order  الطلب
     * @param  string  $type  نوع الحدث: new_order, status_update, message_update, permission_granted, permission_revoked
     * @param  array  $data  بيانات إضافية حسب الحاجة
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
        $orderId = $this->order->id;
        $userName = auth()->user()?->name ?? 'النظام';

        return match ($this->type) {
            'new_order' => "تم إنشاء طلب جديد (#{$this->order->id}) للعميل {$this->order->name}",
            'status_update' => "قام {$userName} بتغيير حالة الطلب (#{$orderId}) إلى {$this->statusLabel()}",
            'new_note' => "أضاف {$userName} ملاحظة جديدة على الطلب (#{$orderId})",
            'client_update' => "قام {$userName} بتحديث بيانات العميل في الطلب (#{$orderId})",
            'unit_info_update' => "قام {$userName} بتحديث معلومات الوحدة في الطلب (#{$orderId})",
            'message_update' => "قام {$userName} بتعديل الملاحظة الرئيسية للطلب (#{$orderId})",
            'permission_granted' => "تم منح صلاحية للمستخدم {$this->data['user_name']} على الطلب (#{$orderId})",
            'permission_revoked' => "تم إلغاء صلاحية المستخدم {$this->data['user_name']} من الطلب (#{$orderId})",
            default => "تم تحديث الطلب (#{$orderId}) بواسطة {$userName}",
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
