<?php
namespace App\Traits;

use Carbon\Carbon;

trait DelayedOrderLogic 
{
    /**
     * يحدد ما إذا كان الطلب متأخراً بناءً على المسؤول عنه.
     */
    public function isOrderDelayed($order)
    {
        // لا يوجد طلب أو تاريخ تحديث؟ إذن ليس متأخراً.
        if (!$order || !$order->updated_at) {
            return false;
        }

        // الطلبات المكتملة أو المغلقة لا تعتبر متأخرة أبداً.
        if (in_array($order->status, [3, 4])) { // 3: مغلق, 4: مكتمل
            return false;
        }

        // من هو المسؤول المباشر عن المشروع؟
        $responsibleManagerId = $order->project->sales_manager_id ?? null;

        // من هو آخر شخص قام بإجراء معتمد على الطلب؟
        $lastActionByUserId = $order->last_action_by_user_id;

        // إذا كان آخر إجراء تم بواسطة المسؤول المباشر، فالطلب ليس متأخراً.
        // لكننا سنظل نتحقق من الوقت.
        if ($lastActionByUserId === $responsibleManagerId) {
             // حتى لو كان المسؤول، إذا مر أكثر من 3 أيام، فهو متأخر.
             return $order->updated_at->lt(now()->subDays(3));
        }
        
        // تحقق مما إذا كان لدى المستخدم الذي قام بآخر إجراء صلاحية معتمدة من المسؤول
        $hasDelegatedPermission = $order->permissions()
            ->where('user_id', $lastActionByUserId)
            // افترض أن الصلاحية التي تسمح بتحديث الطلب هي 'manage'
            ->where('permission_type', 'manage') 
            ->exists();

        // إذا كان آخر إجراء تم بواسطة شخص لديه صلاحية، نتحقق من الوقت.
        if ($hasDelegatedPermission) {
            return $order->updated_at->lt(now()->subDays(3));
        }

        $lastValidActionDate = $order->last_action_at ?? $order->created_at;

        return Carbon::parse($lastValidActionDate)->lt(now()->subDays(3));
    }

    /**
     * يحدد ما إذا كان المستخدم الحالي مخولاً "بإعادة تعيين" حالة التأخير.
     */
    public function canClearDelayStatus($order, $userId = null)
    {
        $userId = $userId ?? auth()->id();
        $responsibleManagerId = $order->project->sales_manager_id ?? null;

        // إذا كان المستخدم هو المسؤول المباشر، فيمكنه ذلك.
        if ($userId == $responsibleManagerId) {
            return true;
        }

        // أو إذا كان لديه صلاحية إدارة مفوضة من المسؤول.
        return $order->permissions()
            ->where('user_id', $userId)
            ->where('permission_type', 'manage')
            ->exists();
    }

    /**
     * تحديث الطلب مع التحكم الذكي في حقل last_action_by_user_id.
     */
    public function updateOrderWithDelayControl($order, $dataToUpdate = [])
    {
        $userId = auth()->id();

        // إذا كان المستخدم الحالي مخولاً، نقوم بتحديث حقل المسؤولية.
        if ($this->canClearDelayStatus($order, $userId)) {
            $order->last_action_by_user_id = $userId;
        }
        
        // نقوم بتحديث البيانات المطلوبة وتاريخ التحديث دائماً.
        $order->fill($dataToUpdate);
        $order->touch(); // هذا يضمن تحديث updated_at
        $order->save();
    }
}