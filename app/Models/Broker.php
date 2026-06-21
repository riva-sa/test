<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Broker extends Authenticatable
{
    use HasFactory, Notifiable;

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_LABELS = [
        self::STATUS_PENDING => 'قيد المراجعة',
        self::STATUS_APPROVED => 'معتمد',
        self::STATUS_REJECTED => 'مرفوض',
    ];

    public const STATUS_COLORS = [
        self::STATUS_PENDING => '#EAB308',
        self::STATUS_APPROVED => '#22C55E',
        self::STATUS_REJECTED => '#DC2626',
    ];

    public const TYPE_INDIVIDUAL = 'individual';

    public const TYPE_COMPANY = 'company';

    public const EMPLOYMENT_STATUSES = [
        'employee' => 'موظف',
        'freelancer' => 'عمل حر',
        'business_owner' => 'صاحب عمل',
        'unemployed' => 'غير موظف',
    ];

    public const HEARD_ABOUT_US_OPTIONS = [
        'social_media' => 'وسائل التواصل الاجتماعي',
        'friend' => 'صديق / معارف',
        'google' => 'بحث جوجل',
        'ads' => 'إعلانات',
        'other' => 'أخرى',
    ];

    protected $fillable = [
        'broker_type',
        'name',
        'email',
        'password',
        'national_id',
        'whatsapp',
        'city',
        'iban',
        'employment_status',
        'heard_about_us',
        'reference_number',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'contract_path',
        'contract_sent_at',
        'contract_signed_path',
        'contract_signed_at',
        'contract_approved_at',
        'contract_approved_by',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'approved_at' => 'datetime',
            'contract_sent_at' => 'datetime',
            'contract_signed_at' => 'datetime',
            'contract_approved_at' => 'datetime',
        ];
    }

    /**
     * Admin has uploaded and sent the contract PDF to this broker.
     */
    public function contractSent(): bool
    {
        return ! is_null($this->contract_sent_at) && ! is_null($this->contract_path);
    }

    /**
     * Broker approved the contract and uploaded the signed copy.
     */
    public function contractSigned(): bool
    {
        return ! is_null($this->contract_signed_at) && ! is_null($this->contract_signed_path);
    }

    /**
     * Admin reviewed the final signed contract and approved (activated) the account.
     */
    public function contractApproved(): bool
    {
        return ! is_null($this->contract_approved_at);
    }

    /**
     * Approved brokers cannot use the portal before signing the contract.
     */
    public function needsContractSignature(): bool
    {
        return $this->isApproved() && ! $this->contractSigned();
    }

    /**
     * Broker has signed but is still waiting for the admin's final review/approval
     * of the signed contract before the portal is unlocked.
     */
    public function awaitingContractApproval(): bool
    {
        return $this->isApproved() && $this->contractSigned() && ! $this->contractApproved();
    }

    /**
     * Fully active: approved, signed, and the signed contract approved by an admin.
     */
    public function isActive(): bool
    {
        return $this->isApproved() && $this->contractSigned() && $this->contractApproved();
    }

    /**
     * Generate a unique sequential broker reference number (e.g. BRK-00012).
     */
    public static function generateReferenceNumber(): string
    {
        $next = (int) (static::max('id') ?? 0) + 1;

        do {
            $reference = 'BRK-'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
            $next++;
        } while (static::where('reference_number', $reference)->exists());

        return $reference;
    }

    public function documents()
    {
        return $this->hasMany(BrokerDocument::class);
    }

    public function orders()
    {
        return $this->hasMany(UnitOrder::class);
    }

    public function commissions()
    {
        return $this->hasMany(BrokerCommission::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(BrokerActivityLog::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function contractApprovedBy()
    {
        return $this->belongsTo(User::class, 'contract_approved_by');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function statusLabel(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function statusColor(): string
    {
        return self::STATUS_COLORS[$this->status] ?? '#6B7280';
    }

    public function brokerTypeLabel(): string
    {
        return $this->broker_type === self::TYPE_COMPANY ? 'شركة' : 'فرد';
    }

    public function employmentStatusLabel(): string
    {
        return self::EMPLOYMENT_STATUSES[$this->employment_status] ?? ($this->employment_status ?? '—');
    }

    public function heardAboutUsLabel(): string
    {
        return self::HEARD_ABOUT_US_OPTIONS[$this->heard_about_us] ?? ($this->heard_about_us ?? '—');
    }
}
