<?php

namespace App\Livewire\Broker;

use App\Models\BlockedNumber;
use App\Models\BrokerActivityLog;
use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubmitLead extends Component
{
    // بيانات العميل
    public $name = '';

    public $phone = '';

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
            'phone' => 'required|string|min:9|max:15',
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

    public function submit()
    {
        $this->validate();

        $broker = Auth::guard('broker')->user();

        if (BlockedNumber::where('phone', $this->phone)->exists()) {
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

        $createdCount = DB::transaction(function () use ($broker, $units, $orderMessage) {
            $count = 0;

            $baseData = [
                'name' => $this->name,
                'phone' => $this->phone,
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

        BrokerActivityLog::record('lead_submitted', $broker->id, "إرسال عميل ({$this->name} - {$this->phone}) — عدد الطلبات: {$createdCount}");

        session()->flash('message', "تم إرسال العميل بنجاح وإنشاء {$createdCount} طلب. يمكنك متابعة الحالة من صفحة طلباتي.");

        return redirect()->route('broker.leads');
    }

    public function render()
    {
        return view('livewire.broker.submit-lead', [
            'projects' => Project::where('status', true)->orderBy('name')->get(['id', 'name']),
        ])->layout('layouts.broker');
    }
}
