<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\BrokerCommissionPayment;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CommissionReceiptController extends Controller
{
    /**
     * Serve a commission payment receipt. Sensitive financial proof — only users
     * who may settle commissions can view it.
     */
    public function show(BrokerCommissionPayment $payment)
    {
        Gate::authorize('pay-broker-commissions');

        abort_unless($payment->receipt_path && Storage::disk('public')->exists($payment->receipt_path), 404);

        $extension = pathinfo($payment->receipt_path, PATHINFO_EXTENSION);
        $filename = 'receipt-'.$payment->id.($extension ? '.'.$extension : '');

        return Storage::disk('public')->response($payment->receipt_path, $filename);
    }
}
