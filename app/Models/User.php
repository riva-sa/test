<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;
    use HasRoles;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        /* TODO: Please implement your own logic here. */
        return true; // str_ends_with($this->email, '@larament.test');
    }



    /**
     * Get all order permissions for this user
     */
    public function orderPermissions()
    {
        return $this->hasMany(OrderPermission::class);
    }

    /**
     * Check if user has permission for a specific order
     *
     * @param int $orderId
     * @param string $permissionType
     * @return bool
     */
    public function hasOrderPermission($orderId, $permissionType)
    {
        // First check if the user is the sales manager of the project associated with this order
        $order = UnitOrder::find($orderId);
        if (!$order) {
            return false;
        }
        // Check for explicit order permission
        return $this->orderPermissions()
            ->where('unit_order_id', $orderId)
            ->where('permission_type', $permissionType)
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    /**
     * Get all orders this user can access
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function accessibleOrders()
    {
        // Orders user created directly
        $createdOrderIds = UnitOrder::where('user_id', $this->id)->pluck('id');

        // Orders from projects user manages
        $managedProjectIds = $this->managedProjects()->pluck('id');
        $managedOrderIds = UnitOrder::whereIn('project_id', $managedProjectIds)->pluck('id');

        // Orders user has specific permissions for
        $permissionOrderIds = $this->orderPermissions()
            ->where(function ($query) {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->pluck('unit_order_id');

        // Combine all IDs
        $allOrderIds = $createdOrderIds->merge($managedOrderIds)->merge($permissionOrderIds)->unique();

        return UnitOrder::whereIn('id', $allOrderIds);
    }

    public function hasOrderPermissionViaModel(UnitOrder $order, string $permissionType)
    {
        // Check if the user is the sales manager of the project
        if ($order->project && $order->project->sales_manager_id == $this->id) {
            return true;
        }

        // Check if user created this order
        if ($order->user_id == $this->id) {
            return true;
        }

        // Check for explicit permission
        return $order->permissions
            ->where('user_id', $this->id)
            ->where('permission_type', $permissionType)
            ->filter(function ($perm) {
                return is_null($perm->expires_at) || $perm->expires_at->isFuture();
            })->isNotEmpty();
    }

    public function managedProjects()
    {
        return $this->hasMany(Project::class, 'sales_manager_id');
    }

}
