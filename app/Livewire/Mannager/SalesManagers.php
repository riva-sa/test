<?php

namespace App\Livewire\Mannager;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class SalesManagers extends Component
{
    public $salesUsers = [];

    public $editingUser = null;

    public $editFields = [];
    public $isEditing = false;
    public $isAdding = false;
    public $newFields = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'password' => '',
    ];

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

    public function startAdding()
    {
        $this->isAdding = true;
        $this->editingUser = null;
        $this->newFields = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'password' => '',
        ];
    }

    public function cancelAdding()
    {
        $this->isAdding = false;
        $this->resetErrorBag();
    }

    public function saveNewUser()
    {
        $this->validate([
            'newFields.name' => 'required|string|max:255',
            'newFields.email' => 'required|email|unique:users,email',
            'newFields.password' => 'required|min:8',
            'newFields.phone' => 'nullable|string',
        ], [
            'newFields.name.required' => 'الاسم مطلوب',
            'newFields.email.required' => 'البريد الإلكتروني مطلوب',
            'newFields.email.email' => 'البريد الإلكتروني غير صالح',
            'newFields.email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
            'newFields.password.required' => 'كلمة المرور مطلوبة',
            'newFields.password.min' => 'كلمة المرور يجب أن لا تقل عن 8 أحرف',
        ]);

        $user = User::create([
            'name' => $this->newFields['name'],
            'email' => $this->newFields['email'],
            'password' => bcrypt($this->newFields['password']),
            'phone' => $this->newFields['phone'],
            'is_active' => true,
        ]);

        $user->assignRole('sales');

        $this->isAdding = false;
        session()->flash('status', 'تم إضافة مندوب مبيعات جديد بنجاح!');
        $this->mount();
    }

    public function startEditing($userId)
    {
        $user = $this->salesUsers->firstWhere('id', $userId);

        if ($user) {
            $this->editingUser = $user->id;
            $this->isEditing = true;
            $this->isAdding = false;
            $this->editFields = [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'is_active' => (bool)$user->is_active,
                'on_vacation' => (bool)$user->on_vacation,
            ];
        }
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->editingUser = null;
        $this->resetErrorBag();
    }

    public function saveEdit($userId)
    {
        $this->validate([
            'editFields.name' => 'required|string|max:255',
            'editFields.email' => 'required|email|unique:users,email,' . $userId,
            'editFields.phone' => 'nullable|string',
        ], [
            'editFields.name.required' => 'الاسم مطلوب',
            'editFields.email.required' => 'البريد الإلكتروني مطلوب',
            'editFields.email.unique' => 'هذا البريد الإلكتروني مستخدم بالفعل',
        ]);

        $user = User::findOrFail($userId);
        $user->update([
            'name' => $this->editFields['name'],
            'email' => $this->editFields['email'],
            'phone' => $this->editFields['phone'],
            'is_active' => $this->editFields['is_active'],
            'on_vacation' => $this->editFields['on_vacation'],
        ]);

        $this->isEditing = false;
        $this->editingUser = null;
        session()->flash('status', 'تم تحديث بيانات الموزع بنجاح!');
        $this->mount();
    }

    public function render()
    {
        return view('livewire.mannager.sales-managers', [
            'isSalesManager' => true,
        ])->layout('layouts.custom');
    }
}
