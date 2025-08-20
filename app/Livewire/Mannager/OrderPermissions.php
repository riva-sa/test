<?php

namespace App\Livewire\Mannager;

use Livewire\Component;
use App\Models\OrderPermission;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Notifications\UnitOrderUpdated;

class OrderPermissions extends Component
{
    public UnitOrder $order;
    public $users;
    public $permissions;

    public $user_id;
    public $permission_type;
    public $expires_at;

    public function mount(UnitOrder $order)
    {
        $this->order = $order;
        // if order status is not 4, redirect to order details
        if ($this->order->status == 4) {
            session()->flash('error', 'لا يمكن إدارة الصلاحيات لطلب مكتمل');
            return redirect()->route('manager.order-details', $this->order->id);
        }
        $this->users = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['sales']);
        })->get();

        $this->loadPermissions();
    }

    public function loadPermissions()
    {
        $this->permissions = OrderPermission::with(['user', 'grantedBy'])
            ->where('unit_order_id', $this->order->id)
            ->get();
    }

    public function grantPermission()
    {
        $this->validate([
            'user_id' => ['required', Rule::exists('users', 'id')],
            'permission_type' => ['required', Rule::in(['view', 'edit', 'manage'])],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $order = UnitOrder::findOrFail($this->order->id);
        $user = User::findOrFail($this->user_id);

        OrderPermission::updateOrCreate(
            [
                'user_id' => $this->user_id,
                'unit_order_id' => $this->order->id,
                'permission_type' => $this->permission_type,
            ],
            [
                'granted_by' => Auth::id(),
                'expires_at' => $this->expires_at,
            ]
        );

        $user->notify(new UnitOrderUpdated($order, 'permission_granted', [
            'user_name' => $user->name,
            'granted_by' => auth()->user()->name
        ]));

        // إشعار المدراء أيضاً
        $managers = User::whereHas('roles', function($query) {
            $query->whereIn('name', ['sales_manager', 'super_admin']);
        })->where('id', '!=', auth()->id())->get();

        foreach ($managers as $manager) {
            $manager->notify(new UnitOrderUpdated($order, 'permission_granted', [
                'user_name' => $user->name,
                'granted_by' => auth()->user()->name
            ]));
        }

        $this->reset(['user_id', 'permission_type', 'expires_at']);

        $this->loadPermissions();

        session()->flash('success', 'تم منح الصلاحية بنجاح');
    }

    public function revokePermission($id)
    {
        $permission = OrderPermission::findOrFail($id);


        $permission->delete();

        $this->loadPermissions();

        session()->flash('success', 'تم إلغاء الصلاحية بنجاح');
    }

    public function render()
    {
        return view('livewire.mannager.order-permissions')->layout('layouts.custom', [
            'title' => 'صلاحيات الطلبات',
            'description' => 'إدارة صلاحيات الطلبات للمستخدمين',
        ]);
    }
}
