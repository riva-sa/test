<?php

namespace App\Livewire\Broker;

use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class LeadDetails extends Component
{
    public UnitOrder $order;

    public function mount($id)
    {
        $broker = Auth::guard('broker')->user();

        // الوسيط يرى طلباته فقط — بدون ملاحظات الفريق أو أي بيانات داخلية
        $this->order = UnitOrder::forBroker($broker)
            ->with(['project:id,name', 'unit:id,title,unit_type,unit_area,unit_price,show_price'])
            ->findOrFail($id);

        Gate::forUser($broker)->authorize('broker-view-order', $this->order);
    }

    /**
     * تايم لاين تحديثات الطلب: الإنشاء + كل تغييرات الحالة (بدون أسماء الموظفين).
     */
    public function getTimelineProperty()
    {
        $timeline = collect([[
            'title' => 'تم استلام الطلب',
            'description' => 'وصل طلبك لفريق المبيعات وسيتم التواصل مع العميل',
            'status' => null,
            'color' => '#3B82F6',
            'date' => $this->order->created_at,
        ]]);

        $transitions = $this->order->statusTransitions()
            ->orderBy('created_at')
            ->get();

        foreach ($transitions as $transition) {
            $label = UnitOrder::STATUS_LABELS[$transition->to_status] ?? 'غير معروف';

            $timeline->push([
                'title' => 'تحديث الحالة: '.$label,
                'description' => match ((int) $transition->to_status) {
                    0 => 'الطلب بانتظار المعالجة',
                    1 => 'جاري التواصل مع العميل ومعالجة الطلب',
                    2 => 'العميل مهتم وجاري إتمام المعاملات البيعية',
                    3 => 'تم إغلاق الطلب',
                    4 => 'تم إتمام الطلب بنجاح 🎉',
                    5 => 'تمت إضافة العميل لقائمة الانتظار',
                    default => null,
                },
                'status' => (int) $transition->to_status,
                'color' => UnitOrder::STATUS_COLORS[$transition->to_status] ?? '#6B7280',
                'date' => $transition->created_at,
            ]);
        }

        return $timeline->sortByDesc('date')->values();
    }

    public function render()
    {
        return view('livewire.broker.lead-details')->layout('layouts.broker');
    }
}
