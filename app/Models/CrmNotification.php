<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrmNotification extends Model
{
    protected $fillable = ['type', 'sender_id', 'title', 'content'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(CrmNotificationRecipient::class, 'notification_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'crm_notification_recipients', 'notification_id', 'user_id')
            ->withPivot('read_at', 'created_at');
    }
}
