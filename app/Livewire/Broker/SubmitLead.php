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

    // بيانات الاهتمام (مشروع/مشاريع + وحدة/وحدات)
    public $selectedProjects = [];

    public $selectedUnits = [];

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

    // Client lookup
    public $clientSearchResults = [];
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
            'selectedProjects' => 'required|array|min:1',
            'selectedProjects.*' => 'exists:projects,id',
            'selectedUnits' => 'nullable|array',
            'selectedUnits.*' => 'exists:units,id',
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
        'selectedProjects.required' => 'يرجى اختيار مشروع واحد على الأقل',
        'selectedProjects.min' => 'يرجى اختيار مشروع واحد على الأقل',
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
                $this->selectedProjects = [(string) $projectId];
            }
        }

        $this->loadUnits();

        if ($unitId = request()->query('unit')) {
            if (collect($this->availableUnits)->pluck('id')->contains((int) $unitId)) {
                $this->selectedUnits = [(string) $unitId];
            }
        }
    }

    public function updatedSelectedProjects()
    {
        $this->loadUnits();

        // إزالة الوحدات التي لم تعد ضمن المشاريع المختارة
        $validIds = collect($this->availableUnits)->pluck('id')->map(fn ($id) => (string) $id);
        $this->selectedUnits = array_values(array_filter($this->selectedUnits, fn ($id) => $validIds->contains((string) $id)));
    }

    /**
     * When phone changes, check if the client already exists.
     */
    public function updatedPhone()
    {
        $this->checkExistingClient();
    }

    /**
     * When country code changes, re-check.
     */
    public function updatedCountryCode()
    {
        $this->checkExistingClient();
    }

    /**
     * Search for existing clients by phone number.
     */
    public function checkExistingClient()
    {
        $this->existingClientWarning = null;
        $this->existingClientFound = false;
        $this->clientSearchResults = [];

        $rawPhone = trim($this->phone);
        if (strlen($rawPhone) < 7) {
            return;
        }

        $fullPhone = $this->countryCode . $rawPhone;

        // Search by exact full phone or raw phone
        $existing = UnitOrder::where(function ($q) use ($rawPhone, $fullPhone) {
            $q->where('phone', $rawPhone)
              ->orWhere('phone', $fullPhone)
              ->orWhere('phone', 'like', '%' . $rawPhone);
        })
        ->select('id', 'name', 'phone', 'status', 'created_at')
        ->orderByDesc('created_at')
        ->limit(3)
        ->get();

        if ($existing->isNotEmpty()) {
            $this->existingClientFound = true;

            // Auto-fill name from the latest record
            if (empty($this->name)) {
                $this->name = $existing->first()->name;
            }

            $this->clientSearchResults = $existing->map(fn($o) => [
                'id'         => $o->id,
                'name'       => $o->name,
                'phone'      => $o->phone,
                'status'     => UnitOrder::STATUS_LABELS[$o->status] ?? '—',
                'created_at' => $o->created_at->format('Y-m-d'),
            ])->toArray();

            $this->existingClientWarning = 'تنبيه: هذا العميل موجود مسبقاً في قاعدة البيانات (' . $existing->count() . ' طلب سابق). سيتم إشعار الإدارة عند الإرسال.';
        }
    }

    /**
     * Fill name from a selected existing client record.
     */
    public function fillFromExisting(string $name)
    {
        $this->name = $name;
    }

    private function loadUnits()
    {
        $this->availableUnits = empty($this->selectedProjects)
            ? []
            : Unit::whereIn('project_id', $this->selectedProjects)
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
        $raw = trim($this->phone);

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

        // Recheck database for existing client to ensure fresh status
        $this->checkExistingClient();

        if (BlockedNumber::where('phone', $fullPhone)->orWhere('phone', $this->phone)->exists()) {
            session()->flash('error', 'عذراً، هذا الرقم محظور من تقديم طلبات.');

            return;
        }

        // الوحدات المختارة (متاحة فقط ومن ضمن المشاريع المختارة)
        $units = Unit::whereIn('id', $this->selectedUnits ?: [])
            ->whereIn('project_id', $this->selectedProjects)
            ->where('case', '0')
            ->get();

        $details = collect([
            'نوع العقار: '.$this->property_type,
            $this->budget ? 'الميزانية: '.$this->budget : null,
            $this->city ? 'المدينة المفضلة: '.$this->city : null,
        ])->filter()->implode(' | ');

        $orderMessage = trim(($this->message ? $this->message."\n" : '')."[بيانات الوسيط] ".$details);

        $isExistingClient = $this->existingClientFound;

        $createdCount = DB::transaction(function () use ($broker, $units, $orderMessage, $fullPhone) {
            $count = 0;

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

            // طلب لكل وحدة مختارة
            foreach ($units as $unit) {
                UnitOrder::create($baseData + [
                    'project_id' => $unit->project_id,
                    'unit_id' => $unit->id,
                ]);
                $count++;
            }

            // طلب لكل مشروع مختار ليس له وحدات مختارة
            $projectsWithUnits = $units->pluck('project_id')->unique();
            foreach ($this->selectedProjects as $projectId) {
                if (! $projectsWithUnits->contains((int) $projectId)) {
                    UnitOrder::create($baseData + [
                        'project_id' => $projectId,
                        'unit_id' => null,
                    ]);
                    $count++;
                }
            }

            return $count;
        });

        // إشعار الجهات المعنية إذا كان العميل موجوداً مسبقاً
        if ($isExistingClient) {
            try {
                $rawPhone = trim($this->phone);
                // البحث عن الطلبات السابقة لهذا العميل للحصول على الوسيط السابق أو موظف المبيعات المعين
                $existingOrders = UnitOrder::where(function ($q) use ($fullPhone, $rawPhone) {
                    $q->where('phone', $fullPhone)
                      ->orWhere('phone', $this->phone)
                      ->orWhere('phone', 'like', '%' . $rawPhone);
                })->get();

                // 1. إشعار المشرفين والمسؤولين (الأدمن)
                $admins = User::whereHas('roles', fn($q) => $q->whereIn('name', ['Admin', 'sales_manager']))->where('is_active', true)->get();
                $alertTitle = 'عميل موجود مسبقاً — طلب وسيط';
                $alertMessage = "الوسيط {$broker->name} أرسل عميلاً موجوداً مسبقاً في قاعدة البيانات: {$this->name} ({$fullPhone}). عدد الطلبات المنشأة: {$createdCount}.";
                foreach ($admins as $admin) {
                    $admin->notify(new CRMAlertNotification($alertTitle, $alertMessage));
                }

                // 2. إشعار الوسيط الأصلي (إن وجد وكان مختلفاً عن الوسيط الحالي)
                $notifiedBrokers = [];
                foreach ($existingOrders as $oldOrder) {
                    if ($oldOrder->broker_id && $oldOrder->broker_id != $broker->id && !in_array($oldOrder->broker_id, $notifiedBrokers)) {
                        $oldBroker = \App\Models\Broker::find($oldOrder->broker_id);
                        if ($oldBroker) {
                            $brokerTitle = 'تنبيه: محاولة تسجيل عميل خاص بك';
                            $brokerMessage = "تم تقديم طلب جديد لعميلك المسجل مسبقاً: {$this->name} ({$fullPhone}) بواسطة وسيط آخر.";
                            $oldBroker->notify(new CRMAlertNotification($brokerTitle, $brokerMessage));
                            $notifiedBrokers[] = $oldOrder->broker_id;
                        }
                    }
                }

                // 3. إشعار موظف المبيعات المعين على الطلب السابق (إن وجد)
                $notifiedSales = [];
                foreach ($existingOrders as $oldOrder) {
                    if ($oldOrder->assigned_sales_user_id && !in_array($oldOrder->assigned_sales_user_id, $notifiedSales)) {
                        $salesUser = User::find($oldOrder->assigned_sales_user_id);
                        if ($salesUser && $salesUser->is_active) {
                            $salesTitle = 'تحديث: طلب جديد لعميل معين لك';
                            $salesMessage = "العميل المعين لك {$this->name} ({$fullPhone}) تم رفع طلب جديد له بواسطة الوسيط {$broker->name}.";
                            $salesUser->notify(new CRMAlertNotification($salesTitle, $salesMessage));
                            $notifiedSales[] = $oldOrder->assigned_sales_user_id;
                        }
                    }
                }

                // 4. إشعار الوسيط الحالي مقدم الطلب
                $brokerTitle = 'طلب عميل مكرر';
                $brokerMessage = "تم استلام طلبك للعميل {$this->name} ({$fullPhone}). يرجى العلم بأن العميل مسجل مسبقاً في النظام وسيتم مراجعة الطلب.";
                $broker->notify(new CRMAlertNotification($brokerTitle, $brokerMessage));

            } catch (\Exception $e) {
                Log::error('Failed to notify admin and concerned parties about existing client: ' . $e->getMessage());
            }
        }

        BrokerActivityLog::record('lead_submitted', $broker->id, "إرسال عميل ({$this->name} - {$fullPhone}) — عدد الطلبات: {$createdCount}" . ($isExistingClient ? ' [عميل موجود مسبقاً]' : ''));

        $successMsg = "تم إرسال العميل بنجاح وإنشاء {$createdCount} طلب. يمكنك متابعة الحالة من صفحة طلباتي.";
        if ($isExistingClient) {
            $successMsg .= ' (تم إشعار الإدارة بأن هذا العميل موجود مسبقاً)';
        }

        session()->flash('message', $successMsg);

        return redirect()->route('broker.leads');
    }

    public function render()
    {
        return view('livewire.broker.submit-lead', [
            'projects' => Project::where('status', true)->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.broker');
    }
}
