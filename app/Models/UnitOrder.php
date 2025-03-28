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
    ];

    protected $casts =
    [
        'status' => 'boolean',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    function project() {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
