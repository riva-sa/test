<?php

namespace App\Http\Middleware;

use App\Models\Broker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureBrokerApproved
{
    /**
     * Only approved brokers may access the broker portal.
     * Pending/rejected accounts are logged out and shown their application status.
     */
    public function handle(Request $request, Closure $next)
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker) {
            return redirect()->route('broker.login');
        }

        if (! $broker->isApproved()) {
            $status = $broker->status;
            $reason = $broker->rejection_reason;

            Auth::guard('broker')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('broker.login')->with(
                $status === Broker::STATUS_REJECTED ? 'broker_rejected' : 'broker_pending',
                $status === Broker::STATUS_REJECTED
                    ? ($reason ?: 'نأسف، تم رفض طلب التسجيل الخاص بك.')
                    : 'تم استلام طلب التسجيل وسيتم مراجعة البيانات من قبل الإدارة.'
            );
        }

        // Approved brokers must sign the contract, then wait for the admin's final
        // review/approval of the signed copy, before the portal is unlocked.
        if (! $broker->isActive() && ! $request->routeIs('broker.contract', 'broker.contract.*', 'broker.logout')) {
            return redirect()->route('broker.contract');
        }

        return $next($request);
    }
}
