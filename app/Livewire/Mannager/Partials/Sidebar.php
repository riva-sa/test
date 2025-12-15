<?php

namespace App\Livewire\Mannager\Partials;

use App\Notifications\UnitOrderUpdated;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class Sidebar extends Component
{
    public $notifications;

    public $unreadCount = 0;

    public $isLoading = false;

    public $lastRefresh;

    protected $listeners = [
        'refreshNotifications' => 'loadNotifications',
        'notificationRead' => 'handleExternalNotificationRead',
        'allNotificationsRead' => 'handleAllNotificationsRead',
        'markNotificationAsRead' => 'markAsRead',
    ];

    public function mount()
    {
        $this->loadNotifications();
        $this->lastRefresh = now();
    }

    /**
     * Enhanced notification loading with caching and error handling
     */
    public function loadNotifications()
    {
        try {
            $this->isLoading = true;

            if (! Auth::check()) {
                $this->notifications = collect();
                $this->unreadCount = 0;

                return;
            }

            $userId = Auth::id();
            $cacheKey = "user_notifications_{$userId}";
            $cacheKeyUnread = "user_notifications_unread_count_{$userId}";

            // Cache notifications for 2 minutes to improve performance
            $this->notifications = Cache::remember($cacheKey, 120, function () {
                return Auth::user()
                    ->notifications()
                    ->whereType(UnitOrderUpdated::class)
                    ->latest()
                    ->take(15) // Increased limit for better UX
                    ->get();
            });

            // Cache unread count separately for better performance
            $this->unreadCount = Cache::remember($cacheKeyUnread, 60, function () {
                return Auth::user()
                    ->unreadNotifications()
                    ->whereType(UnitOrderUpdated::class)
                    ->count();
            });

            $this->lastRefresh = now();

        } catch (\Exception $e) {
            Log::error('Error loading notifications: '.$e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Fallback to empty collections
            $this->notifications = collect();
            $this->unreadCount = 0;

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تحميل الإشعارات',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Enhanced external notification read handler
     */
    public function handleExternalNotificationRead($notificationId)
    {
        try {
            $this->clearNotificationCache();
            $this->loadNotifications();

            Log::info('External notification read handled', [
                'notification_id' => $notificationId,
                'user_id' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling external notification read: '.$e->getMessage());
        }
    }

    /**
     * Enhanced all notifications read handler
     */
    public function handleAllNotificationsRead()
    {
        try {
            $this->clearNotificationCache();
            $this->loadNotifications();

            Log::info('All notifications read handled', [
                'user_id' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error handling all notifications read: '.$e->getMessage());
        }
    }

    /**
     * Enhanced mark as read with validation and error handling
     */
    public function markAsRead($notificationId)
    {
        try {
            if (! Auth::check()) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ]);

                return;
            }

            if (empty($notificationId)) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'معرف الإشعار غير صالح',
                ]);

                return;
            }

            $notification = Auth::user()->notifications()->find($notificationId);

            if (! $notification) {
                $this->dispatch('showNotification', [
                    'type' => 'warning',
                    'message' => 'الإشعار غير موجود أو تم حذفه',
                ]);

                return;
            }

            if ($notification->read_at) {
                // Already read, no need to update
                return;
            }

            $notification->markAsRead();

            // Clear cache and reload
            $this->clearNotificationCache();
            $this->loadNotifications();

            // Dispatch event for other components
            $this->dispatch('notificationRead', notificationId: $notificationId);

            Log::info('Notification marked as read', [
                'notification_id' => $notificationId,
                'user_id' => Auth::id(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking notification as read: '.$e->getMessage(), [
                'notification_id' => $notificationId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تحديث الإشعار',
            ]);
        }
    }

    /**
     * Enhanced mark all as read with batch processing
     */
    public function markAllAsRead()
    {
        try {
            if (! Auth::check()) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ]);

                return;
            }

            $this->isLoading = true;

            // Get unread notifications count before marking as read
            $unreadNotifications = Auth::user()
                ->unreadNotifications()
                ->whereType(UnitOrderUpdated::class);

            $unreadCount = $unreadNotifications->count();

            if ($unreadCount === 0) {
                $this->dispatch('showNotification', [
                    'type' => 'info',
                    'message' => 'لا توجد إشعارات غير مقروءة',
                ]);

                return;
            }

            // Mark all as read with current timestamp
            $updated = $unreadNotifications->update(['read_at' => now()]);

            // Clear cache and reload
            $this->clearNotificationCache();
            $this->loadNotifications();

            // Dispatch event for other components
            $this->dispatch('allNotificationsRead');

            Log::info('All notifications marked as read', [
                'count' => $updated,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => "تم تحديد {$unreadCount} إشعار كمقروء",
            ]);

        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: '.$e->getMessage(), [
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء تحديث الإشعارات',
            ]);
        } finally {
            $this->isLoading = false;
        }
    }

    /**
     * Enhanced notification click handler with validation and error handling
     */
    public function handleNotificationClick($notificationId)
    {
        try {
            if (! Auth::check()) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ]);

                return;
            }

            if (empty($notificationId)) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'معرف الإشعار غير صالح',
                ]);

                return;
            }

            // Mark as read first
            $this->markAsRead($notificationId);

            $notification = Auth::user()->notifications()->find($notificationId);

            if (! $notification) {
                $this->dispatch('showNotification', [
                    'type' => 'warning',
                    'message' => 'الإشعار غير موجود أو تم حذفه',
                ]);

                return;
            }

            $data = $notification->data;
            // Validate notification data
            if (! isset($data['order_id']) || empty($data['order_id'])) {
                $this->dispatch('showNotification', [
                    'type' => 'warning',
                    'message' => 'بيانات الإشعار غير مكتملة',
                ]);

                return;
            }

            // Validate order_id is numeric
            if (! is_numeric($data['order_id'])) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'معرف الطلب غير صالح',
                ]);

                return;
            }

            Log::info('Notification clicked', [
                'notification_id' => $notificationId,
                'order_id' => $data['order_id'],
                'user_id' => Auth::id(),
            ]);

            // Redirect to order details page
            // return redirect()->to(route('manager.order-details', $data['order_id']));
            return $this->redirect(route('manager.order-details', $data['order_id']), navigate: true);

        } catch (\Exception $e) {
            Log::error('Error handling notification click: '.$e->getMessage(), [
                'notification_id' => $notificationId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء فتح الطلب',
            ]);
        }

        // Fallback dispatch if redirect fails
        $this->dispatch('notification-handled');

        return null;
    }

    /**
     * Enhanced notification icon getter with validation
     */
    public function getNotificationIcon($type)
    {
        $icons = [
            'new_order' => 'plus-circle',
            'status_update' => 'refresh',
            'message_update' => 'message-circle',
            'permission_granted' => 'user-check',
            'permission_revoked' => 'user-x',
            'payment_received' => 'dollar-sign',
            'payment_failed' => 'x-circle',
            'reminder' => 'clock',
            'system_update' => 'settings',
        ];

        return $icons[$type] ?? 'bell';
    }

    /**
     * Enhanced notification color getter with validation and improved styling
     */
    public function getNotificationColor($type)
    {
        $colors = [
            'new_order' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
            'status_update' => 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200',
            'message_update' => 'bg-gradient-to-r from-yellow-100 to-orange-100 text-yellow-800 border border-yellow-200',
            'permission_granted' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
            'permission_revoked' => 'bg-gradient-to-r from-red-100 to-pink-100 text-red-800 border border-red-200',
            'payment_received' => 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
            'payment_failed' => 'bg-gradient-to-r from-red-100 to-pink-100 text-red-800 border border-red-200',
            'reminder' => 'bg-gradient-to-r from-purple-100 to-indigo-100 text-purple-800 border border-purple-200',
            'system_update' => 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200',
            'new_note' => 'bg-yellow-100 text-yellow-800',
            'client_update' => 'bg-purple-100 text-purple-800',
            'unit_info_update' => 'bg-purple-100 text-purple-800',
        ];

        return $colors[$type] ?? 'primary-100 text-primary-800 border border-primary-200';
    }

    /**
     * Enhanced notification type label getter with Arabic translations
     */
    public function getNotificationTypeLabel($type)
    {
        $labels = [
            'new_order' => 'طلب جديد',
            'status_update' => 'تحديث حالة',
            'message_update' => 'تحديث رسالة',
            'permission_granted' => 'صلاحية ممنوحة',
            'permission_revoked' => 'صلاحية ملغاة',
            'payment_received' => 'دفعة مستلمة',
            'payment_failed' => 'فشل في الدفع',
            'reminder' => 'تذكير',
            'system_update' => 'تحديث النظام',
            'new_note' => 'ملاحطة جديدة',
            'client_update' => 'تحديث بيانات عميل',
            'unit_info_update' => 'تحديث بيانات وحدة',
        ];

        return $labels[$type] ?? 'إشعار';
    }

    /**
     * Format notification time in Arabic
     */
    public function formatNotificationTime($createdAt)
    {
        try {
            $carbon = Carbon::parse($createdAt);

            // If less than 1 hour ago, show minutes
            if ($carbon->diffInHours(now()) < 1) {
                $minutes = $carbon->diffInMinutes(now());
                if ($minutes < 1) {
                    return 'الآن';
                }

                return "منذ {$minutes} دقيقة";
            }

            // If less than 24 hours ago, show hours
            if ($carbon->diffInDays(now()) < 1) {
                $hours = $carbon->diffInHours(now());

                return "منذ {$hours} ساعة";
            }

            // If less than 7 days ago, show days
            if ($carbon->diffInDays(now()) < 7) {
                $days = $carbon->diffInDays(now());

                return "منذ {$days} يوم";
            }

            // Otherwise show formatted date
            return $carbon->format('Y/m/d H:i');

        } catch (\Exception $e) {
            Log::warning('Error formatting notification time: '.$e->getMessage());

            return 'غير محدد';
        }
    }

    /**
     * Check if notification is recent (less than 24 hours)
     */
    public function isRecentNotification($createdAt)
    {
        try {
            return Carbon::parse($createdAt)->diffInHours(now()) < 24;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get notification priority based on type
     */
    public function getNotificationPriority($type)
    {
        $priorities = [
            'new_order' => 'high',
            'payment_failed' => 'high',
            'permission_revoked' => 'high',
            'status_update' => 'medium',
            'message_update' => 'medium',
            'payment_received' => 'medium',
            'permission_granted' => 'low',
            'reminder' => 'low',
            'system_update' => 'low',
        ];

        return $priorities[$type] ?? 'low';
    }

    /**
     * Clear notification cache
     */
    private function clearNotificationCache()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            Cache::forget("user_notifications_{$userId}");
            Cache::forget("user_notifications_unread_count_{$userId}");
        }
    }

    /**
     * Auto-refresh notifications periodically
     */
    public function autoRefreshNotifications()
    {
        // Only refresh if last refresh was more than 1 minute ago
        if ($this->lastRefresh && $this->lastRefresh->diffInMinutes(now()) >= 1) {
            $this->loadNotifications();
        }
    }

    /**
     * Delete notification (soft delete for audit trail)
     */
    public function deleteNotification($notificationId)
    {
        try {
            if (! Auth::check()) {
                $this->dispatch('showNotification', [
                    'type' => 'error',
                    'message' => 'يجب تسجيل الدخول أولاً',
                ]);

                return;
            }

            $notification = Auth::user()->notifications()->find($notificationId);

            if (! $notification) {
                $this->dispatch('showNotification', [
                    'type' => 'warning',
                    'message' => 'الإشعار غير موجود',
                ]);

                return;
            }

            $notification->delete();

            $this->clearNotificationCache();
            $this->loadNotifications();

            Log::info('Notification deleted', [
                'notification_id' => $notificationId,
                'user_id' => Auth::id(),
            ]);

            $this->dispatch('showNotification', [
                'type' => 'success',
                'message' => 'تم حذف الإشعار',
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting notification: '.$e->getMessage());
            $this->dispatch('showNotification', [
                'type' => 'error',
                'message' => 'حدث خطأ أثناء حذف الإشعار',
            ]);
        }
    }

    /**
     * Get notifications summary for dashboard
     */
    public function getNotificationsSummary()
    {
        try {
            if (! Auth::check()) {
                return [
                    'total' => 0,
                    'unread' => 0,
                    'recent' => 0,
                    'high_priority' => 0,
                ];
            }

            $notifications = $this->notifications ?? collect();

            return [
                'total' => $notifications->count(),
                'unread' => $this->unreadCount,
                'recent' => $notifications->filter(function ($notification) {
                    return $this->isRecentNotification($notification->created_at);
                })->count(),
                'high_priority' => $notifications->filter(function ($notification) {
                    $type = $notification->data['type'] ?? 'default';

                    return $this->getNotificationPriority($type) === 'high';
                })->count(),
            ];

        } catch (\Exception $e) {
            Log::error('Error getting notifications summary: '.$e->getMessage());

            return [
                'total' => 0,
                'unread' => 0,
                'recent' => 0,
                'high_priority' => 0,
            ];
        }
    }

    public function render()
    {
        return view('livewire.mannager.partials.sidebar')->layout('layouts.custom');
    }
}
