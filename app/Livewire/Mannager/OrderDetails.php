<?php

namespace App\Livewire\Mannager;

use App\Models\OrderNote;
use App\Models\OrderPermission;
use App\Models\Project;
use App\Models\Unit;
use App\Models\UnitOrder;
use App\Services\NotificationService;
use App\Traits\DelayedOrderLogic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetails extends Component
{
    use DelayedOrderLogic;

    public $order;

    public $note = '';

    public $orderId;

    public $isEditingClient = false;

    public $clientData = [];

    public $permissions = [];

    public $isEditingUnitCase = false;

    public $unitCase = '';

    // إضافة هذه الخصائص في بداية الكلاس
    public $isEditingMessage = false;

    public $orderMessage = '';

    public $isEditingUnitInfo = false;

    public $unitData = [];

    public $isEditingBank = false;

    public $bankData = [];

    // إضافة هذه الوظائف في الكلاس OrderDetails

    /**
     * بدء تعديل ملاحظة الطلب
     */
    public function startEditMessage()
    {
        $this->isEditingMessage = true;
        $this->orderMessage = $this->order->message ?? '';
    }

    // تحديث هذه الدالة لتستخدم الـ trait
    public function isDelayed()
    {
        return $this->isOrderDelayed($this->order);
    }

    /**
     * إلغاء تعديل ملاحظة الطلب
     */
    public function cancelEditMessage()
    {
        $this->isEditingMessage = false;
        $this->orderMessage = '';
        $this->resetErrorBag('orderMessage');
    }

    public function startEditBank()
    {
        $this->isEditingBank = true;
        $this->bankData = [
            'bank_employee_name' => $this->order->bank_employee_name ?? '',
            'bank_employee_phone' => $this->order->bank_employee_phone ?? '',
            'bank_name' => $this->order->bank_name ?? '',
        ];
    }

    public function cancelEditBank()
    {
        $this->isEditingBank = false;
        $this->bankData = [];
        $this->resetErrorBag('bankData');
    }

    public function saveBankData()
    {
        $this->validate([
            'bankData.bank_employee_name' => 'nullable|string|max:255',
            'bankData.bank_employee_phone' => 'nullable|string|max:30',
            'bankData.bank_name' => 'nullable|string|max:255',
        ]);

        $this->order->update([
            'bank_employee_name' => $this->bankData['bank_employee_name'] !== '' ? $this->bankData['bank_employee_name'] : null,
            'bank_employee_phone' => $this->bankData['bank_employee_phone'] !== '' ? $this->bankData['bank_employee_phone'] : null,
            'bank_name' => $this->bankData['bank_name'] !== '' ? $this->bankData['bank_name'] : null,
        ]);

        $this->updateOrderWithDelayControl($this->order, $this->bankData);
        $this->loadOrder();

        $this->isEditingBank = false;
        session()->flash('message', 'تم تحديث بيانات موظف البنك بنجاح');
    }

    public function startEditUnitCase()
    {
        $this->unitCase = $this->order->unit?->case ?? '';
        $this->isEditingUnitCase = true;
    }

    public function saveUnitCase()
    {
        $this->validate([
            'unitCase' => 'required|integer|between:0,3',
        ]);

        if ($this->order->unit) {
            $this->order->unit->update([
                'case' => $this->unitCase,
            ]);
        }

        $this->isEditingUnitCase = false;
        session()->flash('message', 'تم تحديث حالة الوحدة بنجاح');

        // 👇 Update the order's updated_at timestamp
        $this->updateOrderWithDelayControl($this->order, $this->clientData);
        $this->loadOrder();
    }

    /**
     * حفظ ملاحظة الطلب
     */
    public function saveOrderMessage()
    {
        $this->validate([
            'orderMessage' => 'nullable|string|max:1000',
        ], [
            'orderMessage.max' => 'الملاحظة يجب أن تكون أقل من 1000 حرف',
        ]);

        $this->order->update([
            'message' => $this->orderMessage,
        ]);

        $this->updateOrderWithDelayControl($this->order, ['message' => $this->orderMessage]);
        $this->loadOrder();

        app(NotificationService::class)->notifyMessageUpdate($this->order);

        $this->isEditingMessage = false;
        $this->orderMessage = '';
        session()->flash('message', 'تم تحديث ملاحظة الطلب بنجاح');
    }

    /**
     * حذف ملاحظة الطلب
     */
    public function deleteOrderMessage()
    {
        $this->order->update([
            'message' => null,
        ]);

        $this->isEditingMessage = false;
        $this->orderMessage = '';

        // استخدام الدالة المحدثة
        $this->updateOrderWithDelayControl($this->order, $this->clientData);
        $this->loadOrder();

        session()->flash('message', 'تم حذف ملاحظة الطلب بنجاح');
    }

    public function mount($id)
    {
        $this->orderId = $id;
        $this->loadOrder();
        $this->permissions = OrderPermission::with(['user', 'grantedBy'])
            ->where('unit_order_id', $this->order->id)
            ->get();

        // إعداد المتغيرات الجديدة
        $this->isEditingMessage = false;
        $this->orderMessage = '';
    }

    public function startEditClient()
    {
        $this->isEditingClient = true;
        $this->clientData = [
            'name' => $this->order->name,
            'email' => $this->order->email,
            'phone' => $this->order->phone,
        ];
    }

    public function saveClientData()
    {
        $validatedData = $this->validate([
            'clientData.name' => 'required|string|max:255',
            'clientData.email' => 'required|email|max:255',
            'clientData.phone' => 'required|string|max:20',
        ]);

        $this->order->update($this->clientData);
        $this->updateOrderWithDelayControl($this->order, $this->clientData);
        // ** إرسال الإشعار **
        app(NotificationService::class)->notifyClientUpdate($this->order, $validatedData['clientData']);

        $this->isEditingClient = false;
        session()->flash('message', 'تم تحديث بيانات العميل بنجاح');
    }

    public function startEditUnitInfo()
    {
        $this->isEditingUnitInfo = true;
        $this->unitData = [
            'project_id' => $this->order->project_id,
            'unit_id' => $this->order->unit_id,
            'purchase_type' => $this->order->PurchaseType,
            'purchase_purpose' => $this->order->PurchasePurpose,
            'support_type' => $this->order->support_type,
        ];
    }

    public function saveUnitInfo()
    {
        $validatedData = $this->validate([
            'unitData.project_id' => 'required|exists:projects,id',
            'unitData.unit_id' => 'required|exists:units,id',
            'unitData.purchase_type' => 'required|in:cash,installment',
            'unitData.purchase_purpose' => 'required|in:investment,personal',
            'unitData.support_type' => 'required',
        ]);

        $updateData = [
            'project_id' => $this->unitData['project_id'],
            'unit_id' => $this->unitData['unit_id'],
            'PurchaseType' => $this->unitData['purchase_type'],
            'PurchasePurpose' => $this->unitData['purchase_purpose'],
            'support_type' => $this->unitData['support_type'],
        ];

        // استخدام الدالة الجديدة من الـ Trait
        $this->updateOrderWithDelayControl($this->order, $updateData);
        $this->isEditingUnitInfo = false;
        // ** إرسال الإشعار **
        app(NotificationService::class)->notifyUnitInfoUpdate($this->order, $updateData);
        session()->flash('message', 'تم تحديث معلومات الوحدة بنجاح');
        $this->loadOrder();
    }

    public function loadOrder()
    {
        $this->order = $this->getAccessibleOrdersQuery()
            ->with(['notes.user.roles', 'unit', 'project.salesManager', 'assignedSalesUser'])
            ->findOrFail($this->orderId);
    }

    public function addNote()
    {
        $this->validate([
            'note' => 'required|string',
        ]);

        OrderNote::create([
            'unit_order_id' => $this->orderId,
            'note' => $this->note,
            'user_id' => Auth::id(),
        ]);

        // 👇 Update the order's updated_at timestamp
        $this->updateOrderWithDelayControl($this->order);
        $this->note = '';

        // ** إرسال الإشعار **
        app(NotificationService::class)->notifyNewNote($this->order, $this->note);

        session()->flash('message', 'تمت إضافة الملاحظة بنجاح');
        $this->loadOrder();
    }

    public function updateStatus($status)
    {
        $oldStatus = $this->order->status;
        $this->order->status = $status;

        // إشعار المهتمين بتحديث الحالة
        app(NotificationService::class)
            ->notifyStatusUpdate($this->order, $oldStatus, $this->order->status, Auth::id());

        $this->updateOrderWithDelayControl($this->order, ['status' => $status]);
        $this->loadOrder();

        return response()->json([
            'message' => 'تم تحديث حالة الطلب بنجاح',
            'new_status' => $status,
        ]);

        session()->flash('messageStatus', 'تم التعديل بنجاح');

    }

    public function render()
    {
        $accessibleOrdersQuery = $this->getAccessibleOrdersQuery();
        $previousOrder = (clone $accessibleOrdersQuery)->where('id', '<', $this->order->id)->orderBy('id', 'desc')->first();
        $nextOrder = (clone $accessibleOrdersQuery)->where('id', '>', $this->order->id)->orderBy('id')->first();

        return view('livewire.mannager.order-details', [
            'statusLabels' => [
                0 => 'جديد',
                1 => 'طلب مفتوح',
                2 => 'معاملات بيعية',
                3 => 'مغلق',
                4 => 'مكتمل',
                5 => 'قائمة انتظار',
            ],
            'purchaseTypes' => [
                'cash' => 'كاش',
                'installment' => 'تقسيط',
                'bank' => 'تمويل بنكي',
            ],
            'purchasePurposes' => [
                'investment' => 'استثمار',
                'personal' => 'سكنى',
            ],
            'supportTypes' => [
                'مدعوم' => 'مدعوم',
                'غير مدعوم' => 'غير مدعوم',
            ],
            'projects' => Project::all(),
            'units' => $this->isEditingUnitInfo && isset($this->unitData['project_id'])
                ? Unit::where('project_id', $this->unitData['project_id'])->get()
                : collect(),
            'previousOrder' => $previousOrder,
            'nextOrder' => $nextOrder,
        ])->layout('layouts.custom');
    }

    private function getAccessibleOrdersQuery(): Builder
    {
        return UnitOrder::accessibleBy(Auth::user());
    }

    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('frontend.home');
    }
}
