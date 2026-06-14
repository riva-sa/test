<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitOrder extends Model
{
    use HasFactory;
    /**
     * Scope a query to only include orders accessible by the given user.
     */
    public function scopeAccessibleBy($query, $user)
    {
        if (! $user) {
            return $query->whereRaw('1=0');
        }

        // Admins, sales managers, follow-up, developers, and project managers see all orders
        if ($user->hasRole('sales_manager') || $user->hasRole('follow_up') || $user->hasRole('Admin') || $user->hasRole('developer') || $user->hasRole('project_manager')) {
            return $query;
        }

        // Sales users see orders they are directly assigned to, orders in projects they manage,
        // orders where they have an explicit (non-expired) permission,
        // or orders they previously interacted with (notes, status changes, last action).
        return $query->where(function ($q) use ($user) {
            $q->where('assigned_sales_user_id', $user->id)
                ->orWhereHas('project', function ($subQ) use ($user) {
                    $subQ->where('sales_manager_id', $user->id);
                })
                ->orWhereHas('permissions', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id)
                        ->where(function ($expQ) {
                            $expQ->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        });
                })
                // Orders the user was the last to act on
                ->orWhere('last_action_by_user_id', $user->id)
                // Orders where the user added notes
                ->orWhereHas('notes', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                })
                // Orders where the user changed the status
                ->orWhereHas('statusTransitions', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                });
        });
    }

    /**
     * Scope for delayed orders (3+ days since last activity).
     */
    public function scopeDelayed($query)
    {
        return $query->whereNotIn('status', [3, 4])
            ->where('updated_at', '<', now()->subDays(3));
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
        'broker_id',
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

    public const STATUS_COLORS = [
        0 => '#3B82F6', // جديد - blue
        1 => '#F97316', // طلب مفتوح - orange
        2 => '#5457E3', // معاملات بيعية - purple
        3 => '#9CA3AF', // مغلق - gray
        4 => '#22C55E', // مكتمل - green
        5 => '#EAB308', // قائمة انتظار - yellow
    ];

    public const STATUS_LABELS = [
        0 => 'جديد',
        1 => 'طلب مفتوح',
        2 => 'معاملات بيعية',
        3 => 'مغلق',
        4 => 'مكتمل',
        5 => 'قائمة انتظار',
    ];

    public const PURCHASE_TYPES = [
        'cash' => 'كاش',
        'installment' => 'تقسيط',
        'bank' => 'تمويل بنكي',
        'Cash' => 'كاش', // Legacy
        'Installment' => 'تمويل بنكي', // Legacy/Manager
    ];

    public const PURCHASE_PURPOSES = [
        'investment' => 'استثمار',
        'personal' => 'سكنى',
        'invest' => 'استثمار', // Frontend
        'living' => 'سكنى', // Frontend
        'Residential' => 'سكنى', // Manager
        'Commercial' => 'استثمار', // Manager
    ];

    public const ORDER_SOURCE_LEGACY = 'legacy';

    public const ORDER_SOURCE_FRONTEND_POPUP = 'frontend_popup';

    public const ORDER_SOURCE_FRONTEND_UNIT = 'frontend_unit';

    public const ORDER_SOURCE_MANAGER = 'manager';

    public const ORDER_SOURCE_BULK_IMPORT = 'bulk_import';

    public const ORDER_SOURCE_SOCIAL_MEDIA = 'social_media';

    public const ORDER_SOURCE_BROKER = 'broker';

    /**
     * Scope orders submitted by a specific broker (broker portal only sees its own leads).
     */
    public function scopeForBroker($query, $broker)
    {
        $brokerId = is_object($broker) ? $broker->id : $broker;

        return $query->where('broker_id', $brokerId);
    }

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

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function forwardEvents()
    {
        return $this->hasMany(OrderForwardEvent::class);
    }

    public function statusTransitions()
    {
        return $this->hasMany(OrderStatusTransition::class, 'unit_order_id');
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
        return self::STATUS_LABELS[$this->status] ?? 'غير معروف';
    }

    public function statusColor()
    {
        return self::STATUS_COLORS[$this->status] ?? '#6B7280';
    }

    public function purchaseTypeLabel()
    {
        return self::PURCHASE_TYPES[$this->PurchaseType] ?? $this->PurchaseType ?? '—';
    }

    public function purchasePurposeLabel()
    {
        return self::PURCHASE_PURPOSES[$this->PurchasePurpose] ?? $this->PurchasePurpose ?? '—';
    }

    public function orderSourceLabel()
    {
        $sources = [
            self::ORDER_SOURCE_LEGACY => 'نظام قديم',
            self::ORDER_SOURCE_FRONTEND_POPUP => 'نافذة منبثقة',
            self::ORDER_SOURCE_FRONTEND_UNIT => 'صفحة الوحدة',
            self::ORDER_SOURCE_MANAGER => 'إضافة يدوية',
            self::ORDER_SOURCE_BULK_IMPORT => 'رفع ملف',
            self::ORDER_SOURCE_SOCIAL_MEDIA => 'سوشيال ميديا',
            self::ORDER_SOURCE_BROKER => 'وسيط عقاري',
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

        if ($this->order_source == self::ORDER_SOURCE_BROKER) {
            $source = 'وسيط عقاري';
        }

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
            'وسيط عقاري' => 'fas fa-handshake text-purple-600',
        ];

        $icon = $icons[$source] ?? 'fas fa-globe text-gray-400';

        return [
            'label' => $source,
            'icon' => $icon,
        ];
    }
}
