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
        $this->order->touch();

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

        $this->isEditingClient = false;
        session()->flash('message', 'تم تحديث بيانات العميل بنجاح');
    }

    public function isDelayed()
    {
        // إذا لم يكن هناك طلب أو لم يتم تحديثه مطلقًا
        if (!$this->order || !$this->order->updated_at) {
            return false;
        }

        // إذا كان الطلب مغلقًا فلا نعرض التأخير
        if ($this->order->status == 3) {
            return false;
        }

        // التحقق مما إذا كان آخر تعديل يزيد عن 3 أيام
        return $this->order->updated_at->lt(Carbon::now()->subDays(3));
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

        $this->note = '';
        session()->flash('message', 'تمت إضافة الملاحظة بنجاح');
        $this->loadOrder();
    }

    public function updateStatus($status)
    {
        $this->order->status = $status;
        $this->order->save();
        $this->loadOrder();
        session()->flash('messageStatus', 'تم التعديل بنجاح');

    }

    public function render()
    {
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
                'technical' => 'فنى',
                'financial' => 'مالى',
                'general' => 'عام'
            ]
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
