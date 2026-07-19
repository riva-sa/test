<?php

namespace App\Livewire\Mannager;

use App\Mail\BrokerApplicationStatusMail;
use App\Mail\BrokerContractMail;
use App\Models\Broker;
use App\Models\BrokerActivityLog;
use App\Services\BrokerContractService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class BrokerApplications extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $selectedBrokerId = null;

    public $showRejectModal = false;

    public $rejectionReason = '';

    /** Base64 PNG data-URL of the manager's signature drawn at final approval. */
    public string $managerSignatureData = '';

    // Inline edit form for broker data + commission
    public bool $editing = false;

    public $editName = '';

    public $editEmail = '';

    public $editNationalId = '';

    public $editWhatsapp = '';

    public $editCity = '';

    public $editIban = '';

    public $editEmploymentStatus = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function mount()
    {
        Gate::authorize('manage-brokers');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewBroker($brokerId)
    {
        $this->selectedBrokerId = $brokerId;
    }

    public function closeDetails()
    {
        $this->selectedBrokerId = null;
        $this->showRejectModal = false;
        $this->rejectionReason = '';
        $this->editing = false;
        $this->managerSignatureData = '';
    }

    /**
     * Switch the details panel into edit mode, pre-filling the form with the
     * broker's current data and commission settings.
     */
    public function startEditing()
    {
        Gate::authorize('manage-brokers');

        $broker = Broker::findOrFail($this->selectedBrokerId);

        $this->editName = $broker->name;
        $this->editEmail = $broker->email;
        $this->editNationalId = $broker->national_id;
        $this->editWhatsapp = $broker->whatsapp;
        $this->editCity = $broker->city;
        $this->editIban = $broker->iban;
        $this->editEmploymentStatus = $broker->employment_status;

        $this->resetErrorBag();
        $this->editing = true;
    }

    public function cancelEditing()
    {
        $this->editing = false;
        $this->resetErrorBag();
    }

    /**
     * Persist edits to the broker's data and commission settings.
     */
    public function saveBroker()
    {
        Gate::authorize('manage-brokers');

        $broker = Broker::findOrFail($this->selectedBrokerId);

        // Force Latin digits on numeric fields.
        $this->editNationalId = $this->toLatinDigits($this->editNationalId);
        $this->editWhatsapp = $this->toLatinDigits($this->editWhatsapp);
        $this->editIban = strtoupper($this->toLatinDigits($this->editIban));

        $validated = $this->validate([
            'editName' => 'required|string|min:3|max:255',
            'editEmail' => 'required|email|max:255|unique:brokers,email,'.$broker->id,
            'editNationalId' => 'nullable|string|max:20',
            'editWhatsapp' => 'nullable|string|max:15',
            'editCity' => 'nullable|string|max:255',
            'editIban' => ['nullable', 'string', 'regex:/^SA\d{22}$/'],
            'editEmploymentStatus' => 'nullable|string|in:'.implode(',', array_keys(Broker::EMPLOYMENT_STATUSES)),
        ], [
            'editName.required' => 'الاسم مطلوب',
            'editEmail.required' => 'البريد الإلكتروني مطلوب',
            'editEmail.unique' => 'هذا البريد مستخدم لوسيط آخر',
            'editIban.regex' => 'الآيبان يجب أن يبدأ بـ SA يليها 22 رقماً',
        ]);

        $broker->update([
            'name' => $validated['editName'],
            'email' => $validated['editEmail'],
            'national_id' => $validated['editNationalId'] ?: null,
            'whatsapp' => $validated['editWhatsapp'] ?: null,
            'city' => $validated['editCity'] ?: null,
            'iban' => $validated['editIban'] ?: null,
            'employment_status' => $validated['editEmploymentStatus'] ?: null,
        ]);

        BrokerActivityLog::record('updated', $broker->id, "تعديل بيانات الوسيط ({$broker->reference_number})", Auth::id());

        session()->flash('message', "تم تحديث بيانات الوسيط {$broker->name} بنجاح.");
        $this->editing = false;
    }

    /**
     * Force a value to use Latin (English) digits.
     */
    private function toLatinDigits(?string $value): string
    {
        if ($value === null || $value === '') {
            return (string) $value;
        }

        $map = [
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
        ];

        return strtr($value, $map);
    }

    public function approve($brokerId)
    {
        Gate::authorize('manage-brokers');

        $broker = Broker::findOrFail($brokerId);

        $broker->update([
            'status'           => Broker::STATUS_APPROVED,
            'rejection_reason' => null,
            'approved_at'      => now(),
            'approved_by'      => Auth::id(),
        ]);

        BrokerActivityLog::record('approved', $broker->id, "اعتماد حساب الوسيط ({$broker->reference_number})", Auth::id());

        // Auto-generate personalised contract PDF from the fixed template
        $contractGenerated = true;
        try {
            app(BrokerContractService::class)->generate($broker);
            BrokerActivityLog::record('contract_sent', $broker->id, "توليد عقد الوساطة تلقائياً بعد الاعتماد ({$broker->reference_number})", Auth::id());
        } catch (\Throwable $e) {
            $contractGenerated = false;
            Log::error('Failed to auto-generate broker contract: ' . $e->getMessage(), ['broker_id' => $broker->id]);
        }

        $this->sendStatusMail($broker);

        // Notify the broker that their contract is ready to sign
        if ($contractGenerated) {
            try {
                Mail::to($broker->email)->send(new BrokerContractMail($broker));
            } catch (\Throwable $e) {
                Log::error('Failed to send broker contract email: ' . $e->getMessage(), ['broker_id' => $broker->id]);
            }
            session()->flash('message', "تم اعتماد الوسيط {$broker->name} بنجاح وتوليد العقد وإرساله له.");
        } else {
            session()->flash('error', "تم اعتماد الوسيط {$broker->name}، لكن فشل توليد العقد. يمكن للوسيط إعادة المحاولة من حسابه.");
        }
        
        $this->closeDetails();
    }

    /**
     * Final activation step: after the broker has signed, the admin draws their
     * own signature, which is stamped onto the signed contract; this counter-signs
     * the contract and unlocks the broker portal.
     */
    public function approveContract($brokerId)
    {
        Gate::authorize('manage-brokers');

        $broker = Broker::findOrFail($brokerId);

        if (! $broker->contractSigned()) {
            session()->flash('error', 'لا يمكن اعتماد العقد قبل أن يوقّعه الوسيط.');

            return;
        }

        if ($broker->contractApproved()) {
            session()->flash('error', 'تم اعتماد عقد هذا الوسيط مسبقاً.');

            return;
        }

        // The admin must draw their signature to counter-sign the contract.
        if (empty(trim($this->managerSignatureData)) || strlen($this->managerSignatureData) < 100) {
            $this->addError('managerSignatureData', 'يرجى رسم توقيع المدير أولاً قبل اعتماد العقد.');

            return;
        }

        // Stamp the manager signature onto the broker-signed PDF before activating.
        try {
            app(BrokerContractService::class)->applyManagerSignature($broker, $this->managerSignatureData);
        } catch (\Throwable $e) {
            Log::error('Failed to apply manager signature to contract: '.$e->getMessage(), ['broker_id' => $broker->id]);
            session()->flash('error', 'حدث خطأ أثناء ختم توقيع المدير على العقد. يرجى المحاولة مرة أخرى.');

            return;
        }

        $broker->update([
            'contract_approved_at' => now(),
            'contract_approved_by' => Auth::id(),
        ]);

        $this->managerSignatureData = '';

        BrokerActivityLog::record('contract_approved', $broker->id, "توقيع المدير واعتماد العقد النهائي وتفعيل حساب الوسيط ({$broker->reference_number})", Auth::id());

        try {
            $broker->notify(new \App\Notifications\CRMAlertNotification(
                'تم تفعيل حسابك في بوابة الوسطاء',
                'تمت مراجعة عقدك الموقّع واعتماده. حسابك الآن مُفعّل ويمكنك استخدام البوابة.'
            ));
        } catch (\Throwable $e) {
            Log::error('Failed to notify broker about contract approval: ' . $e->getMessage(), ['broker_id' => $broker->id]);
        }

        session()->flash('message', "تم اعتماد عقد الوسيط {$broker->name} وتفعيل حسابه بنجاح.");
        $this->closeDetails();
    }

    public function openRejectModal($brokerId)
    {
        $this->selectedBrokerId = $brokerId;
        $this->showRejectModal = true;
        $this->rejectionReason = '';
    }

    public function reject()
    {
        Gate::authorize('manage-brokers');

        $this->validate([
            'rejectionReason' => 'required|string|min:5|max:1000',
        ], [], ['rejectionReason' => 'سبب الرفض']);

        $broker = Broker::findOrFail($this->selectedBrokerId);

        $broker->update([
            'status' => Broker::STATUS_REJECTED,
            'rejection_reason' => $this->rejectionReason ?: null,
        ]);

        BrokerActivityLog::record('rejected', $broker->id, "رفض حساب الوسيط ({$broker->reference_number})".($this->rejectionReason ? ' - السبب: '.$this->rejectionReason : ''), Auth::id());

        $this->sendStatusMail($broker);

        session()->flash('message', "تم رفض طلب الوسيط {$broker->name} وإرسال إشعار له.");
        $this->closeDetails();
    }

    /**
     * Re-generate the contract PDF from the fixed template and resend it to
     * the broker (e.g. after a template update or if the broker lost access).
     * This resets any previous signature.
     */
    public function regenerateContract($brokerId)
    {
        Gate::authorize('manage-brokers');

        $broker = Broker::findOrFail($brokerId);

        if (! $broker->isApproved()) {
            session()->flash('error', 'يجب اعتماد الوسيط أولاً قبل إعادة توليد العقد.');

            return;
        }

        try {
            app(BrokerContractService::class)->generate($broker);
        } catch (\Throwable $e) {
            Log::error('Failed to regenerate broker contract: ' . $e->getMessage(), ['broker_id' => $broker->id]);
            session()->flash('error', 'حدث خطأ أثناء توليد العقد. يرجى المحاولة مرة أخرى.');

            return;
        }

        BrokerActivityLog::record('contract_sent', $broker->id, "إعادة توليد عقد الوساطة ({$broker->reference_number})", Auth::id());

        try {
            Mail::to($broker->email)->send(new BrokerContractMail($broker));
        } catch (\Throwable $e) {
            Log::error('Failed to send broker contract email: ' . $e->getMessage(), ['broker_id' => $broker->id]);
        }

        session()->flash('message', "تم إعادة توليد العقد وإرساله إلى الوسيط {$broker->name} بنجاح.");
        $this->closeDetails();
    }

    private function sendStatusMail(Broker $broker): void
    {
        try {
            Mail::to($broker->email)->send(new BrokerApplicationStatusMail($broker));
        } catch (\Throwable $e) {
            Log::error('Failed to send broker status email: '.$e->getMessage(), ['broker_id' => $broker->id]);
        }
    }

    public function render()
    {
        $brokers = Broker::with(['documents', 'approvedBy', 'contractApprovedBy'])
            ->withCount('orders')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('whatsapp', 'like', "%{$this->search}%")
                        ->orWhere('reference_number', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->latest()
            ->paginate(15);

        $selectedBroker = $this->selectedBrokerId
            ? Broker::with(['documents', 'approvedBy', 'contractApprovedBy'])->find($this->selectedBrokerId)
            : null;

        return view('livewire.mannager.broker-applications', [
            'brokers' => $brokers,
            'selectedBroker' => $selectedBroker,
            'pendingCount' => Broker::where('status', Broker::STATUS_PENDING)->count(),
            'employmentStatuses' => Broker::EMPLOYMENT_STATUSES,
        ])->layout('layouts.custom');
    }
}
