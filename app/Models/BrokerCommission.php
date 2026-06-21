<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrokerCommission extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_PAID = 'paid';

    public const STATUS_VOID = 'void';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'معلّقة',
        self::STATUS_APPROVED => 'معتمدة',
        self::STATUS_PAID => 'مدفوعة',
        self::STATUS_VOID => 'ملغاة',
    ];

    public const STATUS_COLORS = [
        self::STATUS_PENDING => '#EAB308',
        self::STATUS_APPROVED => '#3B82F6',
        self::STATUS_PAID => '#22C55E',
        self::STATUS_VOID => '#9CA3AF',
    ];

    protected $fillable = [
        'broker_id',
        'unit_order_id',
        'unit_id',
        'project_id',
        'unit_price',
        'commission_type',
        'commission_value',
        'commission_amount',
        'status',
        'approved_at',
        'approved_by',
        'paid_at',
        'paid_by',
        'payment_reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'commission_value' => 'decimal:2',
            'commission_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function order()
    {
        return $this->belongsTo(UnitOrder::class, 'unit_order_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function payments()
    {
        return $this->hasMany(BrokerCommissionPayment::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isVoid(): bool
    {
        return $this->status === self::STATUS_VOID;
    }

    /** Outstanding = earned but not yet paid (and not voided). */
    public function isOutstanding(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED], true);
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? '#6B7280';
    }

    /** Commissions that count as money the company still owes the broker. */
    public function scopeOutstanding($query)
    {
        return $query->whereIn('status', [self::STATUS_PENDING, self::STATUS_APPROVED]);
    }
}
