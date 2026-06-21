<?php

namespace App\Traits;

use App\Models\BrokerActivityLog;
use App\Models\BrokerCommission;
use App\Services\BrokerCommissionService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Shared commission lifecycle actions used by both the global payouts ledger and
 * a single broker's statement screen. Payment and reversal are governed financial
 * actions (see BrokerCommissionService). The host component MUST `use WithFileUploads`
 * (for the receipt upload) and may define a `loadData()` refresh hook.
 */
trait ManagesBrokerCommissions
{
    /** Commission currently being settled in the payment modal. */
    public ?int $payingCommissionId = null;

    public bool $showPayModal = false;

    public string $paymentReference = '';

    /** Uploaded transfer receipt (image or PDF) — mandatory proof of payment. */
    public $receipt = null;

    /** Commission currently being reversed. */
    public ?int $reversingCommissionId = null;

    public bool $showReverseModal = false;

    public string $reversalReason = '';

    /** Commission currently being voided. */
    public ?int $voidingCommissionId = null;

    public bool $showVoidModal = false;

    public string $voidReason = '';

    public function approveCommission(int $id): void
    {
        Gate::authorize('manage-brokers');

        $commission = BrokerCommission::findOrFail($id);

        if (! $commission->isPending()) {
            session()->flash('error', 'لا يمكن اعتماد إلا العمولات المعلّقة.');

            return;
        }

        if ((float) $commission->commission_amount <= 0) {
            session()->flash('error', 'لا يمكن اعتماد عمولة بقيمة صفر. راجع نسبة عمولة المشروع أولاً.');

            return;
        }

        $commission->update([
            'status' => BrokerCommission::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        BrokerActivityLog::record('commission_approved', $commission->broker_id,
            'اعتماد عمولة بقيمة '.number_format((float) $commission->commission_amount, 2).' ريال (طلب #'.$commission->unit_order_id.')', Auth::id());

        session()->flash('message', 'تم اعتماد العمولة بنجاح.');
        $this->afterCommissionChange();
    }

    public function openPayModal(int $id): void
    {
        Gate::authorize('pay-broker-commissions');

        $commission = BrokerCommission::findOrFail($id);

        if (! $commission->isApproved()) {
            session()->flash('error', 'يجب اعتماد العمولة قبل دفعها.');

            return;
        }

        $this->payingCommissionId = $id;
        $this->paymentReference = '';
        $this->receipt = null;
        $this->resetErrorBag();
        $this->showPayModal = true;
    }

    public function closePayModal(): void
    {
        $this->showPayModal = false;
        $this->payingCommissionId = null;
        $this->paymentReference = '';
        $this->receipt = null;
    }

    public function confirmPayment(): void
    {
        Gate::authorize('pay-broker-commissions');

        $this->validate([
            'paymentReference' => 'required|string|max:255',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], [], [
            'paymentReference' => 'مرجع الحوالة',
            'receipt' => 'إيصال التحويل',
        ]);

        // Persist the receipt before settling so the audit row references a real file.
        // Stored on the persistent disk but marked PRIVATE — it is never publicly
        // readable and is only served through the gated CommissionReceiptController.
        $receiptPath = $this->receipt->store('broker-commission-receipts/'.$this->payingCommissionId, 'public');
        \Illuminate\Support\Facades\Storage::disk('public')->setVisibility($receiptPath, 'private');

        try {
            app(BrokerCommissionService::class)->recordPayment(
                $this->payingCommissionId,
                Auth::user(),
                trim($this->paymentReference),
                $receiptPath,
            );
        } catch (\DomainException $e) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($receiptPath);
            session()->flash('error', $e->getMessage());
            $this->closePayModal();
            $this->afterCommissionChange();

            return;
        }

        session()->flash('message', 'تم تسجيل دفع العمولة بنجاح وحفظ الإيصال.');
        $this->closePayModal();
        $this->afterCommissionChange();
    }

    public function openReverseModal(int $id): void
    {
        Gate::authorize('reverse-broker-commissions');

        $this->reversingCommissionId = $id;
        $this->reversalReason = '';
        $this->resetErrorBag();
        $this->showReverseModal = true;
    }

    public function closeReverseModal(): void
    {
        $this->showReverseModal = false;
        $this->reversingCommissionId = null;
        $this->reversalReason = '';
    }

    public function confirmReversal(): void
    {
        Gate::authorize('reverse-broker-commissions');

        $this->validate([
            'reversalReason' => 'required|string|min:5|max:1000',
        ], [], ['reversalReason' => 'سبب العكس']);

        try {
            app(BrokerCommissionService::class)->reversePayment(
                $this->reversingCommissionId,
                Auth::user(),
                trim($this->reversalReason),
            );
        } catch (\DomainException $e) {
            session()->flash('error', $e->getMessage());
            $this->closeReverseModal();
            $this->afterCommissionChange();

            return;
        }

        session()->flash('message', 'تم عكس الدفعة وإعادة العمولة إلى حالة "معتمدة".');
        $this->closeReverseModal();
        $this->afterCommissionChange();
    }

    public function openVoidModal(int $id): void
    {
        Gate::authorize('manage-brokers');

        $commission = BrokerCommission::findOrFail($id);

        if ($commission->isPaid()) {
            session()->flash('error', 'لا يمكن إلغاء عمولة مدفوعة. اعكس الدفعة أولاً.');

            return;
        }

        $this->voidingCommissionId = $id;
        $this->voidReason = '';
        $this->resetErrorBag();
        $this->showVoidModal = true;
    }

    public function closeVoidModal(): void
    {
        $this->showVoidModal = false;
        $this->voidingCommissionId = null;
        $this->voidReason = '';
    }

    public function confirmVoid(): void
    {
        Gate::authorize('manage-brokers');

        $this->validate([
            'voidReason' => 'required|string|min:5|max:1000',
        ], [], ['voidReason' => 'سبب الإلغاء']);

        $commission = BrokerCommission::findOrFail($this->voidingCommissionId);

        // Re-check: never void money that has already been paid out.
        if ($commission->isPaid()) {
            session()->flash('error', 'لا يمكن إلغاء عمولة مدفوعة. اعكس الدفعة أولاً.');
            $this->closeVoidModal();

            return;
        }

        $commission->update([
            'status' => BrokerCommission::STATUS_VOID,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        BrokerActivityLog::record('commission_voided', $commission->broker_id,
            'إلغاء عمولة (طلب #'.$commission->unit_order_id.') - السبب: '.trim($this->voidReason), Auth::id());

        session()->flash('message', 'تم إلغاء العمولة وتسجيل السبب.');
        $this->closeVoidModal();
        $this->afterCommissionChange();
    }

    /** Optional refresh hook; host components may override. */
    protected function afterCommissionChange(): void
    {
        if (method_exists($this, 'loadData')) {
            $this->loadData();
        }
    }
}
