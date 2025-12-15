<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateOrder extends Component
{
    // بيانات الطلب
    public $name;

    public $email;

    public $phone;

    public $message;

    public $PurchaseType;

    public $PurchasePurpose;

    public $unit_id;

    public $project_id;

    public $support_type;

    // القوائم المنسدلة
    public $projects = [];

    public $units = [];

    // قوائم الخيارات
    public $purchaseTypes = [
        'Cash' => 'كاش',
        'Installment' => 'بنك',
    ];

    public $purchasePurposes = [
        'Residential' => 'سكني',
        'Commercial' => 'استثماري',
    ];

    public $supportTypes = [
        'مدعوم' => 'مدعوم',
        'غير مدعوم' => 'غير مدعوم',
    ];

    // قواعد التحقق
    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'required|string|min:9|max:15',
        'message' => 'nullable|string',
        'PurchaseType' => 'required|string',
        'PurchasePurpose' => 'required|string',
        'project_id' => 'required|exists:projects,id',
        'unit_id' => 'required|exists:units,id',
        'support_type' => 'required|string',
    ];

    protected $messages = [
        'name.required' => 'اسم العميل مطلوب',
        'email.required' => 'البريد الإلكتروني مطلوب',
        'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
        'phone.required' => 'رقم الهاتف مطلوب',
        'PurchaseType.required' => 'نوع الشراء مطلوب',
        'PurchasePurpose.required' => 'الغرض من الشراء مطلوب',
        'project_id.required' => 'يرجى اختيار المشروع',
        'unit_id.required' => 'يرجى اختيار الوحدة',
        'support_type.required' => 'نوع الدعم مطلوب',
    ];

    public function mount()
    {
        // تحميل المشاريع
        $this->projects = Project::select('id', 'name')->get();
    }

    public function updatedProjectId($value)
    {
        if (! empty($value)) {
            if (User::where('id', Auth::id())->first()->hasRole('sales_manager') || User::where('id', Auth::id())->first()->hasRole('follow_up')) {
                // تحديث قائمة الوحدات المتاحة حسب المشروع المختار
                $this->units = Unit::where('project_id', $value)
                    ->select('id', 'title')
                    ->get();
            } else {
                // تحديث قائمة الوحدات المتاحة حسب المشروع المختار
                $this->units = Unit::where('project_id', $value)
                    ->where('case', '0')
                    ->select('id', 'title')
                    ->get();
            }
        } else {
            $this->units = [];
        }

        // إعادة تعيين الوحدة المختارة
        $this->unit_id = null;
    }

    public function createOrder()
    {
        $this->validate();

        UnitOrder::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
            'PurchaseType' => $this->PurchaseType,
            'PurchasePurpose' => $this->PurchasePurpose,
            'unit_id' => $this->unit_id,
            'project_id' => $this->project_id,
            'support_type' => $this->support_type,
            'user_id' => Auth::id(), // مستخدم النظام الذي أنشأ الطلب
            'status' => '1',
        ]);

        session()->flash('message', 'تم إنشاء الطلب بنجاح!');

        // إعادة تعيين النموذج
        $this->resetForm();

        // إعادة توجيه لصفحة الطلبات
        return redirect()->route('manager.orders');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->message = '';
        $this->PurchaseType = '';
        $this->PurchasePurpose = '';
        $this->unit_id = null;
        $this->project_id = null;
        $this->support_type = '';
    }

    public function render()
    {
        return view('livewire.mannager.create-order')->layout('layouts.custom');
    }
}
