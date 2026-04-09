<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
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

    public $isWaitingList = false;

    public $waiting_list_unit_type;

    public $waiting_list_budget;

    public $waiting_list_location;

    public $waiting_list_notes;

    public $bank_employee_name;

    public $bank_employee_phone;

    public $bank_name;

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

    public $waitingListUnitTypes = [
        'apartment' => 'شقة',
        'villa' => 'فيلا',
        'townhouse' => 'تاون هاوس',
        'office' => 'مكتب',
        'shop' => 'محل',
        'other' => 'أخرى',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|min:9|max:15',
            'message' => 'nullable|string',
            'PurchaseType' => [Rule::requiredIf(! $this->isWaitingList), 'nullable', 'string'],
            'PurchasePurpose' => [Rule::requiredIf(! $this->isWaitingList), 'nullable', 'string'],
            'project_id' => [Rule::requiredIf(! $this->isWaitingList), 'nullable', 'exists:projects,id'],
            'unit_id' => [Rule::requiredIf(! $this->isWaitingList), 'nullable', 'exists:units,id'],
            'support_type' => [Rule::requiredIf(! $this->isWaitingList), 'nullable', 'string'],
            'waiting_list_unit_type' => [Rule::requiredIf($this->isWaitingList), 'nullable', 'string', 'max:255'],
            'waiting_list_budget' => 'nullable|string|max:255',
            'waiting_list_location' => 'nullable|string|max:255',
            'waiting_list_notes' => 'nullable|string|max:2000',
            'bank_employee_name' => 'nullable|string|max:255',
            'bank_employee_phone' => 'nullable|string|max:30',
            'bank_name' => 'nullable|string|max:255',
        ];
    }

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
        'waiting_list_unit_type.required' => 'يرجى تحديد نوع الوحدة لقائمة الانتظار',
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

    public function updatedIsWaitingList($value)
    {
        if (! $value) {
            return;
        }

        $this->project_id = null;
        $this->unit_id = null;
        $this->units = [];
        $this->PurchaseType = '';
        $this->PurchasePurpose = '';
        $this->support_type = '';
    }

    public function createOrder()
    {
        $this->validate();

        $isWaitingList = (bool) $this->isWaitingList;

        UnitOrder::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'message' => $this->message,
            'PurchaseType' => $isWaitingList ? null : $this->PurchaseType,
            'PurchasePurpose' => $isWaitingList ? null : $this->PurchasePurpose,
            'unit_id' => $isWaitingList ? null : $this->unit_id,
            'project_id' => $isWaitingList ? null : $this->project_id,
            'support_type' => $isWaitingList ? null : $this->support_type,
            'is_waiting_list' => $isWaitingList,
            'waiting_list_unit_type' => $isWaitingList ? $this->waiting_list_unit_type : null,
            'waiting_list_budget' => $isWaitingList ? $this->waiting_list_budget : null,
            'waiting_list_location' => $isWaitingList ? $this->waiting_list_location : null,
            'waiting_list_notes' => $isWaitingList ? $this->waiting_list_notes : null,
            'bank_employee_name' => (! $isWaitingList && $this->PurchaseType === 'Installment') ? $this->bank_employee_name : null,
            'bank_employee_phone' => (! $isWaitingList && $this->PurchaseType === 'Installment') ? $this->bank_employee_phone : null,
            'bank_name' => (! $isWaitingList && $this->PurchaseType === 'Installment') ? $this->bank_name : null,
            'user_id' => Auth::id(), // مستخدم النظام الذي أنشأ الطلب
            'status' => $isWaitingList ? 5 : 1,
            'order_source' => UnitOrder::ORDER_SOURCE_MANAGER,
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
        $this->isWaitingList = false;
        $this->waiting_list_unit_type = '';
        $this->waiting_list_budget = '';
        $this->waiting_list_location = '';
        $this->waiting_list_notes = '';
        $this->bank_employee_name = '';
        $this->bank_employee_phone = '';
        $this->bank_name = '';
        $this->units = [];
    }

    public function render()
    {
        return view('livewire.mannager.create-order')->layout('layouts.custom');
    }
}
