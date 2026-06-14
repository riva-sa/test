<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerActivityLog extends Model
{
    protected $fillable = [
        'broker_id',
        'user_id',
        'action',
        'description',
        'ip_address',
    ];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record a broker-related audit entry.
     */
    public static function record(string $action, ?int $brokerId = null, ?string $description = null, ?int $userId = null): self
    {
        return static::create([
            'broker_id' => $brokerId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }

    public function actionLabel(): string
    {
        return match ($this->action) {
            'registered' => 'تسجيل جديد',
            'approved' => 'اعتماد الحساب',
            'rejected' => 'رفض الحساب',
            'login' => 'تسجيل دخول',
            'lead_submitted' => 'إرسال عميل',
            default => $this->action,
        };
    }
}
