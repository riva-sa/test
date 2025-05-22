<?php

namespace App\Livewire\Frontend\Conponents;

use App\Helpers\MediaHelper;
use App\Models\Unit;
use App\Models\UnitOrder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;

class UnitPopup extends Component
{
    use LivewireAlert;
    public $firstName, $lastName, $email, $phone;
            // $message;
    public $selectedUnit;
    public $showSideSheet = false;
    public $currentStep = 1;
    public $purchaseType = 'cash'; // Default value
    public $purchasePurpose = 'living'; // Default value
    public $support_type = null;
    public $unitImages = [];
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
        'purchaseType' => 'required|in:cash,bank',
        'purchasePurpose' => 'required|in:living,invest'
    ];

    // Custom Error Messages
    protected $messages = [
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

    // Add this method to load images
    protected function loadUnitImages()
    {
        $this->unitImages = [];

        if ($this->selectedUnit->image) {
            $this->unitImages[] = [
                'url' => MediaHelper::getUrl($this->selectedUnit->image),
                'is_main' => true,
            ];
        }

        if ($this->selectedUnit->floor_plan) {
            $this->unitImages[] = [
                'url' => MediaHelper::getUrl($this->selectedUnit->floor_plan),
                'is_main' => false,
            ];
        }
    }

    public function resetForm()
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->purchaseType = 'cash';
        $this->purchasePurpose = 'living';
        $this->support_type = null;
        $this->resetErrorBag();
    }

    public function submitInterest()
    {
        $this->validate();

        try {
            // Save the interest to database
            $fullPhone = '+966' . $this->phone;
            UnitOrder::create([
                'unit_id' => $this->selectedUnit->id,
                'project_id' => $this->selectedUnit->project->id,
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
        } catch (\Exception $e) {
            $this->alert('error', 'حدث خطأ أثناء تسجيل اهتمامك. الرجاء المحاولة مرة أخرى.', [
                'position' => 'bottom',
                'timer' => 5000,
            ]);
        }
    }

    protected $listeners = [
        'loadUnit' => 'loadUnitFromDispatch'
    ];

    public function loadUnitFromDispatch($data)
    {
        $this->loadUnit($data['unitId']);
    }

    public function loadUnit($unitId)
    {
        $this->selectedUnit = Unit::with(['features', 'project.projectMedia'])->findOrFail($unitId);
        $this->showSideSheet = true;
        $this->currentStep = 1;

        // Load unit images
        $this->loadUnitImages();

        // Dispatch event for JavaScript
        $this->dispatch('sideSheetOpened');
    }

    public function closeSideSheet()
    {
        $this->showSideSheet = false;
        $this->selectedUnit = null;
        $this->currentStep = 1;
    }

    public function goToFormStep()
    {
        $this->currentStep = 2;
    }

    public function render()
    {
        return view('livewire.frontend.conponents.unit-popup');
    }
}
