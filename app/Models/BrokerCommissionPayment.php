<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Append-only audit record of a money event on a broker commission.
 * Never updated or deleted after creation.
 */
class BrokerCommissionPayment extends Model
{
    public const ACTION_PAID = 'paid';

    public const ACTION_REVERSED = 'reversed';

    public const ACTION_LABELS = [
        self::ACTION_PAID => 'دفع',
        self::ACTION_REVERSED => 'عكس دفعة',
    ];

    protected $fillable = [
        'broker_commission_id',
        'broker_id',
        'action',
        'amount',
        'payment_reference',
        'receipt_path',
        'reason',
        'performed_by',
        'performed_by_name',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function commission()
    {
        return $this->belongsTo(BrokerCommission::class, 'broker_commission_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function actionLabel(): string
    {
        return self::ACTION_LABELS[$this->action] ?? $this->action;
    }
}
