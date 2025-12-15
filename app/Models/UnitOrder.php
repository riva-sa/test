<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitOrder extends Model
{
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
    ];

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
                    'message' => "تم منح صلاحية {$perm->permission_type} للمستخدم {$perm->user->name} بواسطة {$perm->grantedBy->name}",
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
        ];

        return $statuses[$this->status] ?? 'gray';
    }
}
