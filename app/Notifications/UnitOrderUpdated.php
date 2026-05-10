<?php

namespace App\Notifications;

use App\Models\UnitOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UnitOrderUpdated extends Notification implements ShouldQueue
{
    use Queueable;
    
    /**
     * Get the middleware the notification should be sent through.
     */
    public function middleware(): array
    {
        return [new \Illuminate\Queue\Middleware\RateLimited('emails')];
    }

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
        // new_order_admin: managers/admins receive email only (no in-app clutter)
        if ($this->type === 'new_order_admin') {
            return ['mail'];
        }

        $channels = ['database'];

        if (in_array($this->type, ['new_order', 'order_assigned', 'new_note', 'status_update', 'message_update'])) {
            $channels[] = 'mail';
        }

        return $channels;
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
            'new_order', 'new_order_admin' => "تم إنشاء طلب جديد (#{$this->order->id}) للعميل {$this->order->name}",
            'status_update' => "قام {$userName} بتغيير حالة الطلب (#{$orderId}) إلى {$this->statusLabel()}",
            'new_note' => "أضاف {$userName} ملاحظة جديدة على الطلب (#{$orderId})",
            'client_update' => "قام {$userName} بتحديث بيانات العميل في الطلب (#{$orderId})",
            'unit_info_update' => "قام {$userName} بتحديث معلومات الوحدة في الطلب (#{$orderId})",
            'message_update' => "قام {$userName} بتعديل الملاحظة الرئيسية للطلب (#{$orderId})",
            'permission_revoked' => "تم إلغاء صلاحية المستخدم {$this->data['user_name']} من الطلب (#{$orderId})",
            'order_forwarded' => "تم توجيه الطلب (#{$orderId}) آلياً للمتابعة — العميل {$this->order->name}",
            'order_assigned' => "تم تعيينك لمتابعة الطلب الجديد (#{$orderId}) للعميل {$this->order->name}",
            default => "تم تحديث الطلب (#{$orderId}) بواسطة {$userName}",
        };

    }

    /**
     * Email message content — fetches fresh order data at dispatch time
     */
    public function toMail($notifiable)
    {
        // Re-fetch with current data so the email reflects the latest order state
        $order = $this->order->fresh(['unit', 'project', 'assignedSalesUser']) ?? $this->order;
        $message = $this->generateMessage();

        $subject = in_array($this->type, ['new_order', 'new_order_admin', 'order_assigned'])
            ? "طلب جديد #{$order->id} — {$order->name}"
            : "تحديث على الطلب #{$order->id}";

        // Use the rich template for new/assigned order emails
        if (in_array($this->type, ['order_assigned', 'new_order', 'new_order_admin'])) {
            $marketingInfo = $order->formattedMarketingSource();

            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject($subject)
                ->view('emails.order-assigned', [
                    'order' => $order,
                    'statusLabel' => $order->statusLabel(),
                    'orderSourceLabel' => $order->orderSourceLabel(),
                    'purchaseTypeLabel' => $order->purchaseTypeLabel(),
                    'purchasePurposeLabel' => $order->purchasePurposeLabel(),
                    'marketingSource' => $marketingInfo['label'] ?? null,
                    'orderUrl' => route('manager.order-details', $order->id),
                ]);
        }

        // Simple format for other notification types
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject($subject)
            ->line($message)
            ->action('عرض الطلب', route('manager.order-details', $order->id))
            ->line('شكراً لاستخدامك نظامنا');
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
            5 => 'قائمة انتظار',
        ];

        return $labels[$this->order->status] ?? $this->order->status;
    }
}
