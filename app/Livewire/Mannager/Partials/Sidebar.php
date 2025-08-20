<?php

namespace App\Livewire\Mannager\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Notifications\UnitOrderUpdated;
class Sidebar extends Component
{
    public $notifications;
    public $unreadCount = 0;
    protected $listeners = [
        'refreshNotifications' => 'loadNotifications',
        'notificationRead' => 'handleExternalNotificationRead',
        'allNotificationsRead' => 'handleAllNotificationsRead'
    ];
    public function mount()
    {
        $this->loadNotifications();
    }

    // دالة للتعامل مع حدث من مكون آخر
    public function handleExternalNotificationRead($notificationId)
    {
        $this->loadNotifications();
    }

    // دالة للتعامل مع حدث من مكون آخر
    public function handleAllNotificationsRead()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        if (Auth::check()) {
            // Get user notifications - latest first, limit to 10 for dropdown
            $this->notifications = Auth::user()
                ->notifications()
                ->whereType(UnitOrderUpdated::class) // فلترة حسب نوع الإشعار
                ->latest()
                ->take(10)
                ->get();
                
            $this->unreadCount = Auth::user()
                ->unreadNotifications()
                ->whereType(UnitOrderUpdated::class)
                ->count();
        }
    }

    public function markAsRead($notificationId)
    {
        if (Auth::check()) {
            $notification = Auth::user()->notifications()->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->loadNotifications();
                
                // استخدم dispatch بدلاً من emit للإصدارات الحديثة من Livewire
                $this->dispatch('notificationRead', notificationId: $notificationId);
            }
        }
    }

    public function markAllAsRead()
    {
        if (Auth::check()) {
            $count = Auth::user()
                ->unreadNotifications()
                ->whereType(UnitOrderUpdated::class)
                ->update(['read_at' => now()]);
                
            $this->loadNotifications();
            
            // استخدم dispatch بدلاً من emit
            $this->dispatch('allNotificationsRead');
        }
    }

    /**
     * التعامل مع النقر على الإشعار - الانتقال للطلب
     */
    public function handleNotificationClick($notificationId)
    {
        $this->markAsRead($notificationId);
        
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification && isset($notification->data['order_id'])) {
            // استخدم redirect()->to() بدلاً من redirect()->route() للتأكد من الصحة
            return redirect()->to(route('manager.order-details', $notification->data['order_id']));
        }
        
        // إرجاع رد فعل افتراضي إذا فشل التوجيه
        $this->dispatch('notification-handled');
        return null;
    }

    /**
     * الحصول على أيقونة حسب نوع الإشعار
     */
    public function getNotificationIcon($type)
    {
        return match($type) {
            'new_order' => 'plus-circle',
            'status_update' => 'refresh',
            'message_update' => 'message-circle',
            'permission_granted' => 'user-check',
            'permission_revoked' => 'user-x',
            default => 'bell'
        };
    }

    /**
     * الحصول على لون الإشعار حسب النوع
     */
    public function getNotificationColor($type)
    {
        return match($type) {
            'new_order' => 'bg-green-100 text-green-800 border border-green-200',
            'status_update' => 'bg-blue-100 text-blue-800 border border-blue-200',
            'message_update' => 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'permission_granted' => 'bg-green-100 text-green-800 border border-green-200',
            'permission_revoked' => 'bg-red-100 text-red-800 border border-red-200',
            default => 'bg-primary-100 text-primary-800 border border-primary-200'
        };
    }
                                                                                            
    public function render()
    {
        return view('livewire.mannager.partials.sidebar');
    }
}
