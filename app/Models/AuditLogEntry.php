<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLogEntry extends Model
{
    protected $table = 'audit_logs_view';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(UnitOrder::class, 'order_id');
    }

    public function getActivityLabel(): string
    {
        return match ($this->activity_type) {
            'status_change' => 'تغيير حالة',
            'permission_grant' => 'منح صلاحية',
            'note_added' => 'إضافة ملاحظة',
            'leaderboard_adjustment' => 'تعديل لوحة الصدارة',
            default => $this->activity_type,
        };
    }
}
