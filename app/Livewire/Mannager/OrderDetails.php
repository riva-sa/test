<?php

namespace App\Livewire\Mannager;

use Livewire\Component;
use App\Models\UnitOrder;
use App\Models\OrderNote;
use App\Models\OrderPermission;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class OrderDetails extends Component
{
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
    // إضافة هذه الوظائف في الكلاس OrderDetails

    /**
     * بدء تعديل ملاحظة الطلب
     */
    public function startEditMessage()
    {
        $this->isEditingMessage = true;
        $this->orderMessage = $this->order->message ?? '';
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
        $this->order->last_action_by_user_id = auth()->id();
        $this->order->touch();
        $this->order->save();

        $this->loadOrder(); // Reload order data
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
            'message' => $this->orderMessage
        ]);

        $this->isEditingMessage = false;
        $this->orderMessage = '';

        // إعادة تحميل البيانات
        $this->loadOrder();
        // 👇 Update the order's updated_at timestamp
        $this->order->touch();
        $this->order->last_action_by_user_id = auth()->id();
        $this->order->save();
        session()->flash('message', 'تم تحديث ملاحظة الطلب بنجاح');
    }

    /**
     * حذف ملاحظة الطلب
     */
    public function deleteOrderMessage()
    {
        $this->order->update([
            'message' => null
        ]);

        $this->isEditingMessage = false;
        $this->orderMessage = '';
        // 👇 Update the order's updated_at timestamp
        $this->order->last_action_by_user_id = auth()->id();
        $this->order->touch();
        $this->order->save();
        // إعادة تحميل البيانات
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
        $this->validate([
            'clientData.name' => 'required|string|max:255',
            'clientData.email' => 'required|email|max:255',
            'clientData.phone' => 'required|string|max:20',
        ]);

        $this->order->update($this->clientData);
        // 👇 Update the order's updated_at timestamp
        $this->order->last_action_by_user_id = auth()->id();
        $this->order->touch();
        $this->order->save();
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
        $this->validate([
            'unitData.project_id' => 'required|exists:projects,id',
            'unitData.unit_id' => 'required|exists:units,id',
            'unitData.purchase_type' => 'required|in:cash,installment',
            'unitData.purchase_purpose' => 'required|in:investment,personal',
            'unitData.support_type' => 'required',
        ]);

        $this->order->update([
            'project_id' => $this->unitData['project_id'],
            'unit_id' => $this->unitData['unit_id'],
            'PurchaseType' => $this->unitData['purchase_type'],
            'PurchasePurpose' => $this->unitData['purchase_purpose'],
            'support_type' => $this->unitData['support_type'],
        ]);

        // 👇 Update the order's updated_at timestamp
        $this->order->last_action_by_user_id = auth()->id();
        $this->order->touch();
        $this->order->save();

        $this->isEditingUnitInfo = false;
        session()->flash('message', 'تم تحديث معلومات الوحدة بنجاح');
        $this->loadOrder();
    }

    public function isDelayed()
    {
        
        if (!$this->order || !$this->order->updated_at) {
            return false;
        }

        // الطلب مكتمل أو مغلق؟ مش متأخر
        if (in_array($this->order->status, [3, 4])) {
            return false;
        }

        $lastActorId = $this->order->last_action_by_user_id;
        $salesManagerId = $this->order->project->sales_manager_id ?? null;

        // لو آخر من تعامل هو المسؤول المباشر → مش متأخر
        if ($lastActorId == $salesManagerId) {
            return false;
        }

        // هل الشخص عنده صلاحية إدارة للطلب من نفس المسؤول؟
        $hasDelegatedPermission = $this->order->permissions()
            ->where('user_id', $lastActorId)
            ->where('permission_type', 'manage') // أو 'edit' حسب منطقك
            ->where('granted_by', $salesManagerId) // فقط من المسؤول المباشر
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();

        if ($hasDelegatedPermission) {
            return false;
        }

        // ما عدا ذلك، إذا مر أكثر من 3 أيام → متأخر
        return $this->order->updated_at->lt(now()->subDays(3));
    }

    public function loadOrder()
    {
        $this->order = UnitOrder::with(['notes.user.roles', 'unit', 'project.salesManager'])->findOrFail($this->orderId);
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
        $this->order->touch();
        $this->order->last_action_by_user_id = auth()->id();
        $this->note = '';
        $this->order->save();

        session()->flash('message', 'تمت إضافة الملاحظة بنجاح');
        $this->loadOrder();
    }

    public function updateStatus($status)
    {
        $this->order->status = $status;
        // 👇 Update the order's updated_at timestamp
        $this->order->last_action_by_user_id = auth()->id();
        $this->order->touch();
        $this->order->save();
        $this->order->save();
        $this->loadOrder();

        session()->flash('messageStatus', 'تم التعديل بنجاح');

    }

    public function render()
    {

        $previousOrder = \App\Models\UnitOrder::where('id', '<', $this->order->id)->orderBy('id', 'desc')->first();
        $nextOrder = \App\Models\UnitOrder::where('id', '>', $this->order->id)->orderBy('id')->first();
        return view('livewire.mannager.order-details', [
            'statusLabels' => [
                0 => 'جديد',
                1 => 'طلب مفتوح',
                2 => 'معاملات بيعية',
                3 => 'مغلق',
                4 => 'مكتمل'
            ],
            'purchaseTypes' => [
                'cash' => 'كاش',
                'installment' => 'تقسيط'
            ],
            'purchasePurposes' => [
                'investment' => 'استثمار',
                'personal' => 'سكنى'
            ],
            'supportTypes' => [
                'مدعوم' => 'مدعوم',
                'غير مدعوم' => 'غير مدعوم'
            ],
            'projects' => Project::all(),
            'units' => $this->isEditingUnitInfo && isset($this->unitData['project_id'])
                ? Unit::where('project_id', $this->unitData['project_id'])->get()
                : collect(),
            'previousOrder' => $previousOrder,
            'nextOrder' => $nextOrder,
        ])->layout('layouts.custom');
    }
    public function logout()
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('frontend.home');
    }

}
