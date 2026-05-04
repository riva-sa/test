<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmNotificationRecipient extends Model
{
    const UPDATED_AT = null;

    protected $fillable = ['notification_id', 'user_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function notification()
    {
        return $this->belongsTo(CrmNotification::class, 'notification_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
