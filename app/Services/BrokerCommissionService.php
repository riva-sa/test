<?php

namespace App\Services;

use App\Models\BrokerActivityLog;
use App\Models\BrokerCommission;
use App\Models\BrokerCommissionPayment;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BrokerCommissionService
{
    /** Order status that represents a completed (sold) deal. */
    public const STATUS_COMPLETED = 4;

    /**
     * Keep the broker's commission ledger in sync with an order's state.
     *
     * Called whenever an order's status changes. When the order becomes
     * "completed" it freezes a commission record (price + rate + amount as they
     * are right now). When it leaves "completed" an unpaid record is voided; a
     * record that was already paid is left untouched (we never un-pay money).
     */
    public function syncForOrder(UnitOrder $order, ?int $actorUserId = null): ?BrokerCommission
    {
        // Only broker-sourced orders attached to a unit can earn commission.
        if (! $order->broker_id || ! $order->unit_id) {
            return null;
        }

        $existing = BrokerCommission::where('unit_order_id', $order->id)->first();

        if ((int) $order->status !== self::STATUS_COMPLETED) {
            return $this->voidIfUnpaid($existing, $actorUserId);
        }

        // Reuse an existing frozen record — never overwrite a snapshot that's
        // already pending/approved/paid, otherwise a later rate change would
        // retroactively alter earned commission.
        if ($existing && ! $existing->isVoid()) {
            return $existing;
        }

        $order->loadMissing(['unit.project', 'broker']);
        $unit = $order->unit;
        $project = $unit?->project;

        if (! $project) {
            return $existing;
        }

        $price = (float) ($unit->unit_price ?? 0);
        $amount = $project->commissionForPrice($price);

        // No commission is owed when the project's rate yields zero — don't create
        // a noisy zero-value record that could never be paid.
        if ($amount <= 0) {
            return $existing;
        }

        $attributes = [
            'broker_id' => $order->broker_id,
            'unit_id' => $unit->id,
            'project_id' => $project->id,
            'unit_price' => $price,
            'commission_type' => $project->commission_type,
            'commission_value' => $project->commission_value,
            'commission_amount' => $amount,
            'status' => BrokerCommission::STATUS_PENDING,
            'approved_at' => null,
            'approved_by' => null,
            'paid_at' => null,
            'paid_by' => null,
        ];

        if ($existing) {
            // Was voided, now sold again — refresh the snapshot.
            $existing->update($attributes);
            $commission = $existing;
        } else {
            try {
                $commission = BrokerCommission::create(['unit_order_id' => $order->id] + $attributes);
            } catch (\Illuminate\Database\QueryException $e) {
                // A concurrent completion already created the row (unique unit_order_id) —
                // reuse it instead of failing the request.
                return BrokerCommission::where('unit_order_id', $order->id)->first();
            }
        }

        BrokerActivityLog::record(
            'commission_earned',
            $order->broker_id,
            "تسجيل عمولة مستحقة بقيمة ".number_format($amount, 2)." ريال عن الوحدة (طلب #{$order->id})",
            $actorUserId
        );

        return $commission;
    }

    private function voidIfUnpaid(?BrokerCommission $commission, ?int $actorUserId): ?BrokerCommission
    {
        if (! $commission || $commission->isVoid() || $commission->isPaid()) {
            return $commission;
        }

        $commission->update([
            'status' => BrokerCommission::STATUS_VOID,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        BrokerActivityLog::record(
            'commission_voided',
            $commission->broker_id,
            "إلغاء عمولة (طلب #{$commission->unit_order_id}) بعد تغيّر حالة الطلب من مكتمل",
            $actorUserId
        );

        return $commission;
    }

    /**
     * Record a payment against a commission under full financial governance:
     *  - only an APPROVED commission can be paid (lifecycle enforced),
     *  - the broker must have an IBAN on file,
     *  - the settlement is atomic and concurrency-safe (a conditional UPDATE
     *    inside a locked transaction) so two managers can never double-pay,
     *  - an immutable payment-audit row is appended with who/when/proof.
     *
     * @throws \DomainException on any governance violation (Arabic message).
     */
    public function recordPayment(int $commissionId, User $actor, string $paymentReference, ?string $receiptPath = null): BrokerCommissionPayment
    {
        return DB::transaction(function () use ($commissionId, $actor, $paymentReference, $receiptPath) {
            /** @var BrokerCommission $commission */
            $commission = BrokerCommission::with('broker')->lockForUpdate()->findOrFail($commissionId);

            if ($commission->status !== BrokerCommission::STATUS_APPROVED) {
                throw new \DomainException('لا يمكن الدفع إلا لعمولة معتمدة. اعتمد العمولة أولاً.');
            }

            if (empty($commission->broker?->iban)) {
                throw new \DomainException('لا يمكن الدفع: الوسيط ليس لديه آيبان مسجّل.');
            }

            if ((float) $commission->commission_amount <= 0) {
                throw new \DomainException('قيمة العمولة غير صالحة للدفع.');
            }

            // Atomic, idempotent settlement: only succeeds if the row is still
            // 'approved'. A concurrent payment flips it first and this returns 0.
            $affected = BrokerCommission::whereKey($commission->id)
                ->where('status', BrokerCommission::STATUS_APPROVED)
                ->update([
                    'status' => BrokerCommission::STATUS_PAID,
                    'paid_at' => now(),
                    'paid_by' => $actor->id,
                    'payment_reference' => $paymentReference,
                ]);

            if ($affected !== 1) {
                throw new \DomainException('تمت تسوية هذه العمولة بالفعل من جلسة أخرى.');
            }

            $payment = BrokerCommissionPayment::create([
                'broker_commission_id' => $commission->id,
                'broker_id' => $commission->broker_id,
                'action' => BrokerCommissionPayment::ACTION_PAID,
                'amount' => $commission->commission_amount,
                'payment_reference' => $paymentReference,
                'receipt_path' => $receiptPath,
                'performed_by' => $actor->id,
                'performed_by_name' => $actor->name,
                'ip_address' => request()->ip(),
            ]);

            BrokerActivityLog::record(
                'commission_paid',
                $commission->broker_id,
                'دفع عمولة بقيمة '.number_format((float) $commission->commission_amount, 2)." ريال (طلب #{$commission->unit_order_id}) - مرجع: {$paymentReference}",
                $actor->id
            );

            return $payment;
        });
    }

    /**
     * Reverse a recorded payment. Most sensitive action: requires a reason, is
     * never a silent delete, returns the commission to 'approved', and appends a
     * 'reversed' audit row. The original payment row is left intact.
     *
     * @throws \DomainException when the commission is not in a paid state.
     */
    public function reversePayment(int $commissionId, User $actor, string $reason): BrokerCommissionPayment
    {
        return DB::transaction(function () use ($commissionId, $actor, $reason) {
            /** @var BrokerCommission $commission */
            $commission = BrokerCommission::lockForUpdate()->findOrFail($commissionId);

            if ($commission->status !== BrokerCommission::STATUS_PAID) {
                throw new \DomainException('لا يمكن عكس دفعة لعمولة غير مدفوعة.');
            }

            $amount = $commission->commission_amount;

            $affected = BrokerCommission::whereKey($commission->id)
                ->where('status', BrokerCommission::STATUS_PAID)
                ->update([
                    'status' => BrokerCommission::STATUS_APPROVED,
                    'paid_at' => null,
                    'paid_by' => null,
                    'payment_reference' => null,
                ]);

            if ($affected !== 1) {
                throw new \DomainException('تعذّر عكس الدفعة، ربما عُكست بالفعل.');
            }

            $reversal = BrokerCommissionPayment::create([
                'broker_commission_id' => $commission->id,
                'broker_id' => $commission->broker_id,
                'action' => BrokerCommissionPayment::ACTION_REVERSED,
                'amount' => $amount,
                'reason' => $reason,
                'performed_by' => $actor->id,
                'performed_by_name' => $actor->name,
                'ip_address' => request()->ip(),
            ]);

            BrokerActivityLog::record(
                'commission_payment_reversed',
                $commission->broker_id,
                'عكس دفعة عمولة بقيمة '.number_format((float) $amount, 2)." ريال (طلب #{$commission->unit_order_id}) - السبب: {$reason}",
                $actor->id
            );

            return $reversal;
        });
    }
}
