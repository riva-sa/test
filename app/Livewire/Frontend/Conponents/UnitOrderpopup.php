<?php

namespace App\Livewire\Frontend\Conponents;

use App\Mail\UnitOrderNotification as MailUnitOrderNotification;
use App\Models\BlockedNumber;
use App\Models\OrderPermission;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\UnitOrderUpdated;
use App\Services\TrackingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class UnitOrderpopup extends Component
{
    use LivewireAlert;

    public $firstName;

    public $lastName;

    public $email;

    public $phone;

    public $project_id;

    public $unit_id;

    public $showOrderSheet = false;

    public $currentStep = 1;

    public $purchaseType = ''; // Default value

    public $purchasePurpose = ''; // Default value

    public $units = [];

    public $support_type = null;

    // Purchase Type Options
    public $purchaseTypes = [];

    // Purchase Purpose Options
    public $purchasePurposes = [];

    // Validation Rules
    protected $rules = [
        'firstName' => 'required|min:3|max:50',
        'lastName' => 'required|min:3|max:50',
        'email' => 'nullable|email',
        'phone' => 'required|regex:/^5[0-9]{8}$/|size:9',
        'unit_id' => 'required|exists:units,id',
        'purchaseType' => 'required|in:cash,bank',
        'purchasePurpose' => 'required|in:personal,investment',
        'support_type' => 'nullable',
    ];

    // Custom Error Messages
    protected $messages = [];

    public function getOrderMessages(): array
    {
        return [
            'unit_id.required' => __('public.unit.validation.unit_required'),
            'unit_id.exists' => __('public.unit.validation.unit_exists'),
            'firstName.required' => __('public.unit.validation.first_name_required'),
            'firstName.min' => __('public.unit.validation.first_name_min'),
            'firstName.max' => __('public.unit.validation.first_name_max'),
            'lastName.required' => __('public.unit.validation.last_name_required'),
            'lastName.min' => __('public.unit.validation.last_name_min'),
            'lastName.max' => __('public.unit.validation.last_name_max'),
            'email.required' => __('public.unit.validation.email_required'),
            'email.email' => __('public.unit.validation.email_email'),
            'phone.required' => __('public.unit.validation.phone_required'),
            'phone.regex' => __('public.unit.validation.phone_regex'),
            'phone.size' => __('public.unit.validation.phone_size'),
            'phone.min' => __('public.unit.validation.phone_min'),
            'purchaseType.required' => __('public.unit.validation.purchase_type_required'),
            'purchaseType.in' => __('public.unit.validation.purchase_type_in'),
            'purchasePurpose.required' => __('public.unit.validation.purchase_purpose_required'),
            'purchasePurpose.in' => __('public.unit.validation.purchase_purpose_in'),
        ];
    }

    protected $trackingService;

    public function boot(TrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
        $this->purchaseTypes = [
            'cash' => __('public.unit.cash'),
            'bank' => __('public.unit.bank'),
        ];
        $this->purchasePurposes = [
            'personal' => __('public.unit.personal'),
            'investment' => __('public.unit.investment'),
        ];
    }

    public function resetForm()
    {
        $this->firstName = '';
        $this->lastName = '';
        $this->email = '';
        $this->phone = '';
        $this->purchaseType = '';
        $this->purchasePurpose = '';

        $this->resetErrorBag();
    }

    protected $listeners = [
        'UnitOrderOpen' => 'loadUnitFromDispatch',
    ];

    public function loadUnitFromDispatch($data)
    {
        $this->UnitOrderOpen($data['projectId']);
    }

    public function UnitOrderOpen($projectId)
    {
        $this->units = Unit::where('project_id', $projectId)
            ->where('case', 0)
            ->select(['id', 'title', 'building_number', 'unit_number'])
            ->orderBy('id', 'desc')
            ->get();
        $this->showOrderSheet = true;
    }

    public function submitOrderUnit()
    {
        $this->validate($this->rules, $this->getOrderMessages());

        try {
            $unit = Unit::where('id', $this->unit_id)->first();
            if ($unit) {
                $project = $unit->project;
                $fullPhone = '+966'.$this->phone;

                // Check for blocked numbers
                $isBlocked = BlockedNumber::where('phone', $fullPhone)->exists();
                if ($isBlocked) {
                    $this->alert('error', 'عذراً، لا يمكن تقدم طلب الان.', [
                        'position' => 'bottom',
                        'timer' => 5000,
                    ]);

                    return;
                }

                $recentDuplicate = UnitOrder::where('unit_id', $this->unit_id)
                    ->where('phone', $fullPhone)
                    ->where('created_at', '>', now()->subMinutes(2))
                    ->exists();
                if ($recentDuplicate) {
                    $this->alert('warning', 'تم إرسال طلب مشابه مؤخرًا. الرجاء المحاولة لاحقًا.', [
                        'position' => 'bottom',
                        'timer' => 5000,
                    ]);

                    return;
                }
                // Save the interest to database
                $unitOrder = UnitOrder::create([
                    'unit_id' => $this->unit_id,
                    'project_id' => $project->id,
                    'name' => $this->firstName.' '.$this->lastName,
                    'email' => $this->email,
                    'phone' => $fullPhone,
                    'PurchaseType' => $this->purchaseType,
                    'PurchasePurpose' => $this->purchasePurpose,
                    'support_type' => $this->support_type,
                    'status' => 0,
                    'order_source' => UnitOrder::ORDER_SOURCE_FRONTEND_POPUP,
                ]);

                // Track unit order
                $this->trackingService->trackUnitOrder($unit, [
                    'purchase_type' => $this->purchaseType,
                    'purchase_purpose' => $this->purchasePurpose,
                    'customer_name' => $this->firstName.' '.$this->lastName,
                    'order_id' => $unitOrder->id,
                    'source' => 'project_order_popup',
                ]);

                $this->sendEmailNotifications($unitOrder, $project, $unit);

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

    /**
     * إرسال الإشعارات والبريد الإلكتروني للمستخدمين المعنيين
     */
    private function sendEmailNotifications($unitOrder, $project, $unit)
    {
        try {
            $emailData = [
                'unit_order' => $unitOrder,
                'project' => $project,
                'unit' => $unit,
                'customer_name' => $unitOrder->name,
                'customer_email' => $unitOrder->email,
                'customer_phone' => $unitOrder->phone,
                'purchase_type' => $this->purchaseTypes[$unitOrder->PurchaseType] ?? $unitOrder->PurchaseType,
                'purchase_purpose' => $this->purchasePurposes[$unitOrder->PurchasePurpose] ?? $unitOrder->PurchasePurpose,
            ];

            // 1. معالجة مدير المبيعات المسؤول عن المشروع
            $salesManager = $project->sales_manager_id ? User::with('roles')->find($project->sales_manager_id) : null;
            // 2. الحصول على جميع المدراء المعنيين (بدون تكرار)
            $managers = User::whereHas('roles', function ($query) {
                $query->whereIn('name', ['sales_manager', 'follow_up']);
            })->get();

            // 3. إنشاء مجموعة مستخدمين فريدة لتجنب التكرار
            $usersToNotify = collect();

            if ($salesManager && ($salesManager->hasRole('sales') || $salesManager->hasRole('sales_manager'))) {
                $usersToNotify->push($salesManager);
            }

            $usersToNotify = $usersToNotify->merge($managers)->unique('id');
            // 4. إرسال الإشعارات للمستخدمين المحددين
            foreach ($usersToNotify as $user) {
                $notificationData = [
                    'customer_name' => $unitOrder->name,
                    'unit_type' => $unit->type ?? 'غير محدد',
                    'project_name' => $project->name,
                ];

                // إرسال إشعار النظام
                $user->notify(new UnitOrderUpdated($unitOrder, 'new_order', $notificationData));
                Mail::to($user->email)->queue(new MailUnitOrderNotification($emailData, 'sales_manager'));

                // إرسال البريد الإلكتروني فقط لمدير المبيعات المسؤول
                // if ($user->id === optional($salesManager)->id && $user->email) {
                //     Mail::to($user->email)
                //         ->send(new MailUnitOrderNotification($emailData, 'sales_manager'));
                // }
            }

            // 5. معالجة المستخدمين ذوي الصلاحيات المخصصة
            OrderPermission::where('unit_order_id', $unitOrder->id)
                ->with('user')
                ->get()
                ->each(function ($permission) use ($unitOrder, $emailData) {
                    if ($permission->user && $permission->user->email) {
                        $permission->user->notify(new UnitOrderUpdated($unitOrder, 'new_order', [
                            'customer_name' => $unitOrder->name,
                            'unit_type' => $emailData['unit']->type ?? 'غير محدد',
                        ]));

                        Mail::to($permission->user->email)
                            ->queue(new MailUnitOrderNotification($emailData, 'permission_user'));
                    }
                });

        } catch (\Exception $e) {
            Log::error('فشل إرسال الإشعارات: '.$e->getMessage());
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
