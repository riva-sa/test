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
        'last_action_by_user_id'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function project() {
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
}
