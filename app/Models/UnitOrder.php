<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOrder extends Model
{
    /**
     * Scope a query to only include orders accessible by the given user.
     */
    public function scopeAccessibleBy($query, $user)
    {
        if (! $user) {
            return $query->whereRaw('1=0');
        }

        // Admins, sales managers, and follow-up users see all orders
        if ($user->hasRole('sales_manager') || $user->hasRole('follow_up') || $user->hasRole('admin') || $user->hasRole('developer')) {
            return $query;
        }

        // Sales users are restricted to their projects or explicit permissions
        return $query->where(function ($q) use ($user) {
            $q->whereHas('project', function ($subQ) use ($user) {
                $subQ->where('sales_manager_id', $user->id);
            })->orWhereHas('permissions', function ($subQ) use ($user) {
                $subQ->where('user_id', $user->id)
                    ->where(function ($expQ) {
                        $expQ->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    });
            });
        });
    }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
        'message',
        'PurchaseType',
        'PurchasePurpose',
        'unit_id',
        'user_id',
        'project_id',
        'support_type',
        'last_action_by_user_id',
        'is_waiting_list',
        'waiting_list_unit_type',
        'waiting_list_budget',
        'waiting_list_location',
        'waiting_list_notes',
        'bank_employee_name',
        'bank_employee_phone',
        'bank_name',
        'order_source',
        'import_batch_id',
        'assigned_sales_user_id',
        'external_id',
        'marketing_source',
        'session_id',
        'campaign_name',
        'ad_squad',
        'ad_set',
        'ad_name',
    ];

    public const ORDER_SOURCE_LEGACY = 'legacy';

    public const ORDER_SOURCE_FRONTEND_POPUP = 'frontend_popup';

    public const ORDER_SOURCE_FRONTEND_UNIT = 'frontend_unit';

    public const ORDER_SOURCE_MANAGER = 'manager';

    public const ORDER_SOURCE_BULK_IMPORT = 'bulk_import';

    public const ORDER_SOURCE_SOCIAL_MEDIA = 'social_media';

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class, 'unit_order_id');
    }

    public function permissions()
    {
        return $this->hasMany(OrderPermission::class, 'unit_order_id');
    }

    // last_action_by_user_id
    public function lastActionByUser()
    {
        return $this->belongsTo(User::class, 'last_action_by_user_id');
    }

    public function assignedSalesUser()
    {
        return $this->belongsTo(User::class, 'assigned_sales_user_id');
    }

    public function forwardEvents()
    {
        return $this->hasMany(OrderForwardEvent::class);
    }

    public function activities()
    {
        $activities = collect();

        // 📝 الملاحظات
        foreach ($this->notes()->with('user')->get() as $note) {
            $activities->push([
                'type' => 'note',
                'message' => "أضاف {$note->user->name} ملاحظة: {$note->note}",
                'created_at' => $note->created_at,
            ]);
        }

        // 🔑 الصلاحيات
        foreach ($this->permissions()->with(['user', 'grantedBy'])->get() as $permission) {
            $currentUser = auth()->user();

            // التحقق إذا كان المستخدم الحالي ليس sales أو إذا كان sales ولديه الصلاحية
            if (! $currentUser->hasRole('sales') ||
                ($currentUser->hasRole('sales') && $permission->user_id == $currentUser->id)) {

                $activities->push([
                    'type' => 'permission',
                    'message' => "منح {$permission->grantedBy->name} صلاحية إلى {$permission->user->name}",
                    'created_at' => $permission->created_at,
                ]);
            }
        }

        // 📌 التحديثات على الحالة أو الرسالة
        if ($this->statusLabel() != 'جديد') {
            $activities->push([
                'type' => 'status',
                'message' => 'تم تحديث حالة الطلب إلى: '.$this->statusLabel(),
                'created_at' => $this->updated_at,
            ]);
        }

        return $activities->sortByDesc('created_at');
    }

    public function lastActivity()
    {
        $activities = collect();

        // آخر ملاحظة
        if ($note = $this->notes()->with('user')->latest()->first()) {
            $activities->push([
                'type' => 'note',
                'user_name' => $note->user->name ?? 'غير معروف',
                'message' => "أضاف {$note->user->name} ملاحظة جديدة: {$note->note}",
                'created_at' => $note->created_at,
                'priority' => 3, // أولوية عالية للملاحظات
            ]);
        }

        // آخر صلاحية
        if ($perm = $this->permissions()->with(['user', 'grantedBy'])->latest()->first()) {
            $currentUser = auth()->user();

            // التحقق إذا كان المستخدم الحالي ليس sales أو إذا كان sales ولديه الصلاحية
            if (! $currentUser->hasRole('sales') ||
                ($currentUser->hasRole('sales') && $perm->user_id == $currentUser->id)) {
                $activities->push([
                    'type' => 'permission',
                    'user_name' => $perm->grantedBy->name ?? 'غير معروف',
                    'message' => "تم منح صلاحية {$perm->permission_type} للمستخدم {$perm->user->name}",
                    'created_at' => $perm->created_at,
                    'priority' => 2,
                ]);
            }
        }

        // آخر تحديث حالة
        if ($this->updated_at && $this->statusLabel() != 'جديد') {
            // if not new
            $activities->push([
                'type' => 'status',
                'user_name' => $this->relationLoaded('lastActionByUser') ? ($this->lastActionByUser->name ?? 'النظام') : 'النظام',
                'message' => 'تم تحديث حالة الطلب إلى: '.$this->statusLabel(),
                'created_at' => $this->updated_at,
                'priority' => 1,
            ]);

        }

        // ترتيب حسب التاريخ والأولوية
        return $activities->sortByDesc(function ($item) {
            return $item['created_at']->timestamp.$item['priority'];
        })->first();
    }

    public function statusLabel()
    {
        $statuses = [
            0 => ['label' => 'جديد', 'color' => 'blue'],
            1 => ['label' => 'طلب مفتوح', 'color' => 'green'],
            2 => ['label' => 'معاملات بيعية', 'color' => 'yellow'],
            3 => ['label' => 'مغلق', 'color' => 'red'],
            4 => ['label' => 'مكتمل', 'color' => 'emerald'],
            5 => ['label' => 'قائمة انتظار', 'color' => 'amber'],
        ];

        return $statuses[$this->status]['label'] ?? 'غير معروف';
    }

    public function statusColor()
    {
        $statuses = [
            0 => 'blue',
            1 => 'green',
            2 => 'yellow',
            3 => 'red',
            4 => 'emerald',
            5 => 'amber',
        ];

        return $statuses[$this->status] ?? 'gray';
    }

    public function orderSourceLabel()
    {
        $sources = [
            self::ORDER_SOURCE_LEGACY => 'نظام قديم',
            self::ORDER_SOURCE_FRONTEND_POPUP => 'نافذة منبثقة',
            self::ORDER_SOURCE_FRONTEND_UNIT => 'صفحة الوحدة',
            self::ORDER_SOURCE_MANAGER => 'إضافة يدوية',
            self::ORDER_SOURCE_BULK_IMPORT => 'رفع ملف',
        ];

        return $sources[$this->order_source] ?? $this->order_source ?? 'غير معروف';
    }

    /**
     * Helper method to format marketing source with icons.
     */
    public function formattedMarketingSource()
    {
        $marketingSource = array_key_exists('marketing_source', $this->getAttributes()) ? $this->getAttribute('marketing_source') : null;
        $source = $marketingSource ?: ($this->order_source == self::ORDER_SOURCE_MANAGER ? 'إضافة يدوية' : 'مباشر');
        
        $icons = [
            'Facebook' => 'fab fa-facebook text-blue-600',
            'Instagram' => 'fab fa-instagram text-pink-600',
            'Snapchat' => 'fab fa-snapchat-ghost text-yellow-500',
            'Google' => 'fab fa-google text-red-500',
            'TikTok' => 'fab fa-tiktok text-gray-900',
            'Twitter' => 'fab fa-twitter text-blue-400',
            'LinkedIn' => 'fab fa-linkedin text-blue-700',
            'WhatsApp' => 'fab fa-whatsapp text-green-500',
            'إضافة يدوية' => 'fas fa-user-edit text-gray-600',
            'مباشر' => 'fas fa-link text-gray-500',
        ];
        
        $icon = $icons[$source] ?? 'fas fa-globe text-gray-400';
        
        return [
            'label' => $source,
            'icon' => $icon
        ];
    }
}
