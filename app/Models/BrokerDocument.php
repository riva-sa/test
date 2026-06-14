<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrokerDocument extends Model
{
    public const TYPE_NATIONAL_ID = 'national_id';

    public const TYPE_FAL_LICENSE = 'fal_license';

    public const TYPE_IBAN_FILE = 'iban_file';

    public const TYPE_LABELS = [
        self::TYPE_NATIONAL_ID => 'الهوية الوطنية / الإقامة',
        self::TYPE_FAL_LICENSE => 'رخصة فال',
        self::TYPE_IBAN_FILE => 'ملف الآيبان',
    ];

    protected $fillable = [
        'broker_id',
        'type',
        'path',
        'original_name',
    ];

    public function broker()
    {
        return $this->belongsTo(Broker::class);
    }

    public function typeLabel(): string
    {
        return self::TYPE_LABELS[$this->type] ?? $this->type;
    }
}
