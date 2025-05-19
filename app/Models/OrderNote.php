<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderNote extends Model
{
    protected $fillable = ['unit_order_id', 'note', 'user_id'];

    public function order()
    {
        return $this->belongsTo(UnitOrder::class, 'unit_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
