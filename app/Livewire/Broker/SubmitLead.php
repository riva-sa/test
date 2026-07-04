<?php

namespace App\Livewire\Broker;

use App\Models\BlockedNumber;
use App\Models\BrokerActivityLog;
use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\CRMAlertNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class SubmitLead extends Component
{
    // بيانات العميل
    public $name = '';

    public $phone = '';

    public $countryCode = '+966'; // Default Saudi Arabia

    // بيانات الاهتمام (مشروع واحد + وحدة واحدة)
    public $selectedProject = null;

    public $selectedUnit = null;

    // بيانات الطلب (نفس حقول نموذج الطلب الحالي في CRM)
    public $property_type = '';

    public $PurchaseType = '';

    public $PurchasePurpose = '';

    public $support_type = '';

    public $budget = '';

    public $city = '';

    public $bank_name = '';

    public $message = '';

    public $availableUnits = [];

    // Client lookup — only whether the client already exists is exposed to the broker,
    // never any of the client's own data (privacy requirement).
    public $existingClientWarning = null;
    public $existingClientFound = false;

    public $countryCodes = [
        '+966' => '🇸🇦 +966',
        '+971' => '🇦🇪 +971',
        '+965' => '🇰🇼 +965',
        '+973' => '🇧🇭 +973',
        '+974' => '🇶🇦 +974',
        '+968' => '🇴🇲 +968',
        '+967' => '🇾🇪 +967',
        '+20'  => '🇪🇬 +20',
        '+962' => '🇯🇴 +962',
        '+961' => '🇱🇧 +961',
        '+963' => '🇸🇾 +963',
        '+964' => '🇮🇶 +964',
        '+970' => '🇵🇸 +970',
        '+249' => '🇸🇩 +249',
        '+218' => '🇱🇾 +218',
        '+213' => '🇩🇿 +213',
        '+212' => '🇲🇦 +212',
        '+216' => '🇹🇳 +216',
        '+699' => '🌍 +699',
        '+1'   => '🇺🇸 +1',
        '+44'  => '🇬🇧 +44',
        '+33'  => '🇫🇷 +33',
        '+49'  => '🇩🇪 +49',
        '+90'  => '🇹🇷 +90',
        '+92'  => '🇵🇰 +92',
        '+91'  => '🇮🇳 +91',
        '+880' => '🇧🇩 +880',
        '+63'  => '🇵🇭 +63',
    ];

    public $propertyTypes = [
        'شقة' => 'شقة',
        'فيلا' => 'فيلا',
        'دور' => 'دور',
        'تاون هاوس' => 'تاون هاوس',
        'دوبلكس' => 'دوبلكس',
        'أرض' => 'أرض',
        'تجاري' => 'تجاري',
    ];

    public $purchaseTypes = [
        'cash' => 'كاش',
        'bank' => 'بنك / تمويل',
    ];

    public $purchasePurposes = [
        'personal' => 'سكنى',
        'investment' => 'استثمار',
    ];

    public $supportTypes = [
        'مدعوم' => 'مدعوم',
        'غير مدعوم' => 'غير مدعوم',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'phone' => 'required|string|min:7|max:15',
            'countryCode' => 'required|string',
            'selectedProject' => 'required|exists:projects,id',
            'selectedUnit' => 'nullable|exists:units,id',
            'property_type' => 'required|string|max:50',
            'PurchaseType' => 'required|string|in:cash,bank',
            'PurchasePurpose' => 'required|string|in:personal,investment',
            'support_type' => 'required|string|max:50',
            'budget' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:2000',
        ];
    }

    protected $messages = [
        'name.required' => 'اسم العميل مطلوب',
        'phone.required' => 'رقم الهاتف مطلوب',
        'selectedProject.required' => 'يرجى اختيار المشروع',
        'selectedProject.exists' => 'المشروع المختار غير صالح',
        'selectedUnit.exists' => 'الوحدة المختارة غير صالحة',
        'property_type.required' => 'نوع العقار مطلوب',
        'PurchaseType.required' => 'طريقة الشراء مطلوبة',
        'PurchasePurpose.required' => 'الغرض من الشراء مطلوب',
        'support_type.required' => 'نوع الدعم مطلوب',
    ];

    public function mount()
    {
        // دعم التحديد المسبق من صفحة المشروع / الوحدة
        if ($projectId = request()->query('project')) {
            if (Project::where('status', true)->whereKey($projectId)->exists()) {
                $this->selectedProject = (string) $projectId;
            }
        }

        $this->loadUnits();

        if ($unitId = request()->query('unit')) {
            if (collect($this->availableUnits)->pluck('id')->contains((int) $unitId)) {
                $this->selectedUnit = (string) $unitId;
            }
        }
    }

    public function updatedSelectedProject()
    {
        $this->loadUnits();

        // إزالة الوحدة المختارة إذا لم تعد تابعة للمشروع المختار
        if ($this->selectedUnit) {
            $unit = Unit::find($this->selectedUnit);
            if (!$unit || (string) $unit->project_id !== (string) $this->selectedProject) {
                $this->selectedUnit = null;
            }
        }
    }

    /**
     * When phone changes, check if the client already exists.
     */
    public function updatedPhone()
    {
        $this->phone = $this->toLatinDigits($this->phone);
        $this->checkExistingClient();
    }

    /**
     * Force a value to use Latin (English) digits, converting Arabic-Indic / Persian digits.
     */
    private function toLatinDigits(?string $value): string
    {
        if ($value === null || $value === '') {
            return (string) $value;
        }

        $map = [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ];

        return strtr($value, $map);
    }

    /**
     * When country code changes, re-check.
     */
    public function updatedCountryCode()
    {
        $this->checkExistingClient();
    }

    /**
     * Check whether this phone number already exists in our CRM.
     *
     * For privacy, we only ever expose the fact that the client is already
     * registered with Riva — never the client's name, status, history or any
     * other detail, and we never auto-fill anything from existing records.
     */
    public function checkExistingClient()
    {
        $this->existingClientWarning = null;
        $this->existingClientFound = false;

        if ($this->clientExistsInCrm()) {
            $this->existingClientFound = true;
            $this->existingClientWarning = 'هذا العميل مسجّل بالفعل لدى ريفا، ولا يمكن إرساله.';
        }
    }

    /**
     * Does the entered phone number already belong to a client in our CRM?
     */
    private function clientExistsInCrm(): bool
    {
        $rawPhone = trim($this->toLatinDigits($this->phone));
        if (strlen($rawPhone) < 7) {
            return false;
        }

        $fullPhone = $this->buildFullPhone();

        return UnitOrder::where(function ($q) use ($rawPhone, $fullPhone) {
            $q->where('phone', $rawPhone)
              ->orWhere('phone', $fullPhone)
              ->orWhere('phone', 'like', '%' . $rawPhone);
        })->exists();
    }

    private function loadUnits()
    {
        $this->availableUnits = !$this->selectedProject
            ? []
            : Unit::where('project_id', $this->selectedProject)
                ->where('case', '0')
                ->select('id', 'title', 'unit_type', 'unit_price', 'project_id')
                ->with('project:id,name')
                ->get()
                ->toArray();
    }

    /**
     * Build the full phone number with country code.
     */
    private function buildFullPhone(): string
    {
        $raw = trim($this->toLatinDigits($this->phone));

        // If phone already starts with +, don't prepend code
        if (str_starts_with($raw, '+')) {
            return $raw;
        }

        return $this->countryCode . $raw;
    }

    public function submit()
    {
        $this->validate();

        $broker = Auth::guard('broker')->user();

        $fullPhone = $this->buildFullPhone();

        if (BlockedNumber::where('phone', $fullPhone)->orWhere('phone', $this->phone)->exists()) {
            session()->flash('error', 'عذراً، هذا الرقم محظور من تقديم طلبات.');

            return;
        }

        // Reject the lead entirely if the client is already registered in our CRM.
        // The broker is told only that the client already exists with Riva — no other detail.
        if ($this->clientExistsInCrm()) {
            $this->existingClientFound = true;
            $this->existingClientWarning = 'هذا العميل مسجّل بالفعل لدى ريفا، ولا يمكن إرساله.';

            $this->notifyExistingClientAttempt($broker, $fullPhone);

            BrokerActivityLog::record('lead_rejected_existing', $broker->id, "محاولة إرسال عميل مسجّل مسبقاً ({$fullPhone}) — تم الرفض");

            session()->flash('error', 'هذا العميل مسجّل بالفعل لدى ريفا، ولا يمكن إرسال الطلب.');

            return;
        }

        // الوحدة المختارة (متاحة فقط ومن ضمن المشروع المختار)
        $unit = null;
        if ($this->selectedUnit) {
            $unit = Unit::where('id', $this->selectedUnit)
                ->where('project_id', $this->selectedProject)
                ->where('case', '0')
                ->first();
        }

        $details = collect([
            'نوع العقار: '.$this->property_type,
            $this->budget ? 'الميزانية: '.$this->budget : null,
            $this->city ? 'المدينة المفضلة: '.$this->city : null,
        ])->filter()->implode(' | ');

        $orderMessage = trim(($this->message ? $this->message."\n" : '')."[بيانات الوسيط] ".$details);

        $createdCount = DB::transaction(function () use ($broker, $unit, $orderMessage, $fullPhone) {
            $baseData = [
                'name' => $this->name,
                'phone' => $fullPhone,
                'message' => $orderMessage,
                'PurchaseType' => $this->PurchaseType,
                'PurchasePurpose' => $this->PurchasePurpose,
                'support_type' => $this->support_type,
                'bank_name' => $this->PurchaseType === 'bank' ? ($this->bank_name ?: null) : null,
                'status' => 0, // جديد — يدخل نفس Workflow الفريق الداخلي
                'order_source' => UnitOrder::ORDER_SOURCE_BROKER,
                'broker_id' => $broker->id,
            ];

            if ($unit) {
                UnitOrder::create($baseData + [
                    'project_id' => $unit->project_id,
                    'unit_id' => $unit->id,
                ]);
            } else {
                UnitOrder::create($baseData + [
                    'project_id' => $this->selectedProject,
                    'unit_id' => null,
                ]);
            }

            return 1;
        });

        BrokerActivityLog::record('lead_submitted', $broker->id, "إرسال عميل ({$this->name} - {$fullPhone}) — عدد الطلبات: {$createdCount}");

        session()->flash('message', "تم إرسال العميل بنجاح وإنشاء طلب. يمكنك متابعة الحالة من صفحة طلباتي.");

        return redirect()->route('broker.leads');
    }

    /**
     * Notify the internal team (and the original broker / assigned salesperson)
     * that a broker attempted to submit a client that is already in our CRM.
     * The lead itself is rejected; this is purely an internal alert.
     */
    private function notifyExistingClientAttempt($broker, string $fullPhone): void
    {
        try {
            $rawPhone = trim($this->phone);

            $existingOrders = UnitOrder::where(function ($q) use ($fullPhone, $rawPhone) {
                $q->where('phone', $fullPhone)
                  ->orWhere('phone', $this->phone)
                  ->orWhere('phone', 'like', '%' . $rawPhone);
            })->get();

            // 1. Admins / sales managers
            $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'sales_manager']))->where('is_active', true)->get();
            $alertTitle = 'محاولة إرسال عميل مسجّل مسبقاً — وسيط';
            $alertMessage = "حاول الوسيط {$broker->name} إرسال عميل مسجّل بالفعل لدى ريفا ({$fullPhone}). تم رفض الطلب تلقائياً.";
            foreach ($admins as $admin) {
                $admin->notify(new CRMAlertNotification($alertTitle, $alertMessage));
            }

            // 2. The original broker who owns this client (if different)
            $notifiedBrokers = [];
            foreach ($existingOrders as $oldOrder) {
                if ($oldOrder->broker_id && $oldOrder->broker_id != $broker->id && !in_array($oldOrder->broker_id, $notifiedBrokers)) {
                    if ($oldBroker = \App\Models\Broker::find($oldOrder->broker_id)) {
                        $oldBroker->notify(new CRMAlertNotification(
                            'تنبيه: محاولة تسجيل عميل خاص بك',
                            "حاول وسيط آخر إرسال عميلك المسجّل مسبقاً ({$fullPhone}). تم رفض الطلب."
                        ));
                        $notifiedBrokers[] = $oldOrder->broker_id;
                    }
                }
            }

            // 3. The salesperson assigned to the existing client (if any)
            $notifiedSales = [];
            foreach ($existingOrders as $oldOrder) {
                if ($oldOrder->assigned_sales_user_id && !in_array($oldOrder->assigned_sales_user_id, $notifiedSales)) {
                    $salesUser = User::find($oldOrder->assigned_sales_user_id);
                    if ($salesUser && $salesUser->is_active) {
                        $salesUser->notify(new CRMAlertNotification(
                            'محاولة إرسال عميل معيّن لك',
                            "حاول الوسيط {$broker->name} إرسال العميل المعيّن لك ({$fullPhone}). تم رفض الطلب."
                        ));
                        $notifiedSales[] = $oldOrder->assigned_sales_user_id;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to notify about existing-client lead attempt: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.broker.submit-lead', [
            'projects' => Project::where('status', true)->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.broker');
    }
}
