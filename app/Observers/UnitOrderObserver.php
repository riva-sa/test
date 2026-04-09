<?php

namespace App\Observers;

use App\Models\UnitOrder;
use App\Services\ApplicationForwardingService;

class UnitOrderObserver
{
    public function created(UnitOrder $order): void
    {
        app(ApplicationForwardingService::class)->forward($order);
    }
}
