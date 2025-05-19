<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',        // ID of the user receiving permission
        'unit_order_id',  // ID of the specific order they can access
        'permission_type', // Type of permission (e.g., 'view', 'edit', 'manage')
        'granted_by',     // ID of the user who granted the permission
        'expires_at',     // Optional expiration date for temporary access
    ];

    /**
     * Get the user that has this permission
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order this permission is for
     */
    public function order()
    {
        return $this->belongsTo(UnitOrder::class, 'unit_order_id');
    }

    /**
     * Get the user who granted this permission
     */
    public function grantedBy()
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
