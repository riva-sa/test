<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusTransition extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'unit_order_id',
        'user_id',
        'attributed_user_id',
        'from_status',
        'to_status',
        'created_at',
    ];

    public function order()
    {
        return $this->belongsTo(UnitOrder::class, 'unit_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
