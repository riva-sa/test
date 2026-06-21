<?php

namespace App\Livewire\Broker;

use App\Models\BrokerCommission;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        $broker = Auth::guard('broker')->user()->load('documents');

        // Frozen commission ledger — each record was snapshotted when the deal
        // completed, so the figures stay stable even if a project's rate changes.
        $commissions = $broker->commissions()
            ->whereNot('status', BrokerCommission::STATUS_VOID)
            ->with(['unit:id,title', 'project:id,name'])
            ->latest()
            ->get();

        // The broker may only SEE money once an admin has approved the commission.
        // Pending commissions are shown as deals "under review" with no amount.
        $confirmedStatuses = [BrokerCommission::STATUS_APPROVED, BrokerCommission::STATUS_PAID];

        $sales = $commissions->map(function (BrokerCommission $c) use ($confirmedStatuses) {
            $confirmed = in_array($c->status, $confirmedStatuses, true);

            return [
                'unit'       => $c->unit?->title ?? '—',
                'project'    => $c->project?->name ?? '—',
                'price'      => (float) $c->unit_price,
                'confirmed'  => $confirmed,
                'commission' => $confirmed ? (float) $c->commission_amount : null,
                'status'     => $confirmed ? $c->statusLabel() : 'قيد المراجعة',
                'date'       => $c->created_at,
            ];
        });

        return view('livewire.broker.profile', [
            'broker' => $broker,
            'sales' => $sales,
            // Money figures count ONLY admin-approved (approved + paid) commissions.
            'totalCommission' => (float) $commissions->whereIn('status', $confirmedStatuses)->sum('commission_amount'),
            'paidCommission' => (float) $commissions->where('status', BrokerCommission::STATUS_PAID)->sum('commission_amount'),
            'outstandingCommission' => (float) $commissions->where('status', BrokerCommission::STATUS_APPROVED)->sum('commission_amount'),
            'soldUnitsCount' => $sales->count(),
        ])->layout('layouts.broker');
    }
}
