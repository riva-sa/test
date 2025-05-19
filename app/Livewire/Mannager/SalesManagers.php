<?php

namespace App\Livewire\Mannager;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SalesManagers extends Component
{
    public $salesUsers = [];
    public $editingUser = null;
    public $editFields = [];

    public function mount()
    {
        // التأكد من أن المستخدم لديه دور sales_manager
        if (Auth::user()->hasRole('sales')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // جلب جميع المستخدمين بدور sales
        $this->salesUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'sales');
        })->get();

    }

    public function startEditing($userId)
    {
        $user = $this->salesUsers->firstWhere('id', $userId);

        if ($user) {
            $this->editingUser = $user->id;
            $this->editFields = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
            ];
        }
    }

    public function saveEdit($userId)
    {
        $user = User::findOrFail($userId);
        $user->update([
            'name' => $this->editFields['name'],
            'email' => $this->editFields['email'],
            'phone' => $this->editFields['phone'],
        ]);

        $this->editingUser = null;
        session()->flash('status', 'تم تحديث بيانات الموزع بنجاح!');
        $this->mount(); // إعادة تحميل البيانات
    }

    public function render()
    {
        return view('livewire.mannager.sales-managers', [
            'isSalesManager' => true
        ])->layout('layouts.custom');
    }
}