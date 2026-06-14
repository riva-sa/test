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
        try {
            app(BrokerContractService::class)->generate($broker);
            BrokerActivityLog::record('contract_sent', $broker->id, "توليد عقد الوساطة تلقائياً بعد الاعتماد ({$broker->reference_number})", Auth::id());
        } catch (\Throwable $e) {
            Log::error('Failed to auto-generate broker contract: ' . $e->getMessage(), ['broker_id' => $broker->id]);
        }

        $this->sendStatusMail($broker);

        // Notify the broker that their contract is ready to sign
        try {
            Mail::to($broker->email)->send(new BrokerContractMail($broker));
        } catch (\Throwable $e) {
            Log::error('Failed to send broker contract email: ' . $e->getMessage(), ['broker_id' => $broker->id]);
        }

        session()->flash('message', "تم اعتماد الوسيط {$broker->name} بنجاح وتوليد العقد وإرساله له.");
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
            'rejectionReason' => 'nullable|string|max:1000',
        ]);

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
        $brokers = Broker::with(['documents', 'approvedBy'])
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
            ? Broker::with(['documents', 'approvedBy'])->find($this->selectedBrokerId)
            : null;

        return view('livewire.mannager.broker-applications', [
            'brokers' => $brokers,
            'selectedBroker' => $selectedBroker,
            'pendingCount' => Broker::where('status', Broker::STATUS_PENDING)->count(),
        ])->layout('layouts.custom');
    }
}
