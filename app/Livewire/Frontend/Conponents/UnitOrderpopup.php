<?php

namespace App\Livewire\Frontend\Conponents;

use Livewire\Component;
use App\Models\Unit;
use App\Models\UnitOrder;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UnitOrderpopup extends Component
{
    use LivewireAlert;
    public $firstName, $lastName, $email, $phone, $project_id, $unit_id;
    public $showOrderSheet = false;
    public $currentStep = 1;
    public $purchaseType = 'cash'; // Default value
    public $purchasePurpose = 'living'; // Default value
    public $units = [];
    public $support_type = null;

    // Purchase Type Options
    public $purchaseTypes = [
        'cash' => 'كاش',
        'bank' => 'تمويل بنكي'
    ];

    // Purchase Purpose Options
    public $purchasePurposes = [
        'living' => 'سكن',
        'invest' => 'استثمار'
    ];
    // Validation Rules
    protected $rules = [
        'firstName' => 'required|min:3|max:50',
        'lastName' => 'required|min:3|max:50',
        'email' => 'required|email',
        'phone' => 'required|regex:/^5[0-9]{8}$/|size:9',
        'unit_id' => 'required|exists:units,id',
        'purchaseType' => 'required|in:cash,bank',
        'purchasePurpose' => 'required|in:living,invest',
        'support_type' => 'nullable',
    ];

    // Custom Error Messages
    protected $messages = [
        'unit_id.required' => 'الرجاء اختيار الوحدة',
        'unit_id.exists' => 'الوحدة المختارة غير موجودة',
        'firstName.required' => 'الرجاء إدخال الاسم',
        'firstName.min' => 'يجب أن يكون الاسم 3 أحرف على الأقل',
        'firstName.max' => 'يجب أن لا يتجاوز الاسم 50 حرفاً',

        'lastName.required' => 'الرجاء إدخال الاسم',
        'lastName.min' => 'يجب أن يكون الاسم 3 أحرف على الأقل',
        'lastName.max' => 'يجب أن لا يتجاوز الاسم 50 حرفاً',
        'email.required' => 'الرجاء إدخال البريد الإلكتروني',
        'email.email' => 'الرجاء إدخال بريد إلكتروني صحيح',
        'phone.required' => 'الرجاء إدخال رقم الهاتف',
        'phone.regex' => 'رقم الجوال يجب أن يبدأ بالرقم 5 ويكون 9 أرقام',
        'phone.size' => 'رقم الجوال يجب أن يكون 9 أرقام بالضبط',
        'phone.min' => 'يجب أن يكون رقم الهاتف 10 أرقام على الأقل',
        'purchaseType.required' => 'الرجاء اختيار طريقة الشراء',
        'purchaseType.in' => 'طريقة الشراء غير صحيحة',
        'purchasePurpose.required' => 'الرجاء اختيار الغرض من الشراء',
        'purchasePurpose.in' => 'الغرض من الشراء غير صحيح'
    ];

    public function resetForm()
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->purchaseType = 'cash';
        $this->purchasePurpose = 'living';

        $this->resetErrorBag();
    }

    protected $listeners = [
        'UnitOrderOpen' => 'loadUnitFromDispatch'
    ];

    public function loadUnitFromDispatch($data)
    {
        $this->UnitOrderOpen($data['projectId']);
    }

    public function UnitOrderOpen($projectId)
    {
        $this->units = Unit::where('project_id', $projectId)->where('case',0)->get();
        $this->showOrderSheet = true;
    }

    public function submitOrderUnit()
    {
        $this->validate();

        try {
            $unit = Unit::where('id',$this->unit_id)->first();
            if ($unit) {
                $project = $unit->project;
                $fullPhone = '+966' . $this->phone;
                // Save the interest to database
                UnitOrder::create([
                    'unit_id' => $this->unit_id,
                    'project_id' => $project->id,
                    'name' => $this->firstName . ' ' . $this->lastName,
                    'email' => $this->email,
                    'phone' => $fullPhone,
                    'PurchaseType' => $this->purchaseType,
                    'PurchasePurpose' => $this->purchasePurpose,
                    'support_type' => $this->support_type,
                    'status' => 0
                ]);

                $this->alert('success', 'تم تسجيل اهتمامك بنجاح', [
                    'position' => 'bottom',
                    'timer' => 5000,
                ]);

                $this->currentStep = 1;
                $this->closeSideSheet();
                $this->resetForm();
            }

        } catch (\Exception $e) {
            $this->alert('error', 'حدث خطأ أثناء تسجيل اهتمامك. الرجاء المحاولة مرة أخرى.', [
                'position' => 'bottom',
                'timer' => 5000,
            ]);
        }
    }

    public function closeSideSheet()
    {
        $this->showOrderSheet = false;
        $this->currentStep = 1;
    }

    public function goToFormStep()
    {
        $this->currentStep = 2;
    }

    public function render()
    {
        return view('livewire.frontend.conponents.unit-orderpopup');
    }
}
