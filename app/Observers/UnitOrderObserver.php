<?php

namespace App\Observers;

use App\Models\UnitOrder;
use App\Services\ApplicationForwardingService;

class UnitOrderObserver
{
    public function created(UnitOrder $order): void
    {
        // 1. Auto-assign the order to an available sales representative
        app(\App\Services\AutoAssignmentService::class)->assign($order);
        
        // 2. Forward notifications securely
        app(ApplicationForwardingService::class)->forward($order);
    }
}
