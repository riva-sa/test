<?php

namespace App\Livewire\Mannager;

use App\Mail\BrokerApplicationStatusMail;
use App\Mail\BrokerContractMail;
use App\Models\Broker;
use App\Models\BrokerActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class BrokerApplications extends Component
{
    use WithFileUploads, WithPagination;

    public $contractFile;

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
            'status' => Broker::STATUS_APPROVED,
            'rejection_reason' => null,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
        ]);

        BrokerActivityLog::record('approved', $broker->id, "اعتماد حساب الوسيط ({$broker->reference_number})", Auth::id());

        $this->sendStatusMail($broker);

        session()->flash('message', "تم اعتماد الوسيط {$broker->name} بنجاح وإرسال إشعار له.");
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
     * بعد الاعتماد: رفع عقد PDF وإرساله للوسيط ليوافق عليه ويرفع النسخة الموقعة.
     */
    public function sendContract($brokerId)
    {
        Gate::authorize('manage-brokers');

        $broker = Broker::findOrFail($brokerId);

        if (! $broker->isApproved()) {
            session()->flash('error', 'يجب اعتماد الوسيط أولاً قبل إرسال العقد.');

            return;
        }

        $this->validate([
            'contractFile' => 'required|file|mimes:pdf|max:10240',
        ], [
            'contractFile.required' => 'يرجى اختيار ملف العقد',
            'contractFile.mimes' => 'العقد يجب أن يكون بصيغة PDF',
            'contractFile.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجا',
        ]);

        $path = $this->contractFile->store("broker-documents/{$broker->id}/contract", 'local');

        $broker->update([
            'contract_path' => $path,
            'contract_sent_at' => now(),
            // إعادة إرسال عقد جديد تُلغي أي توقيع سابق
            'contract_signed_path' => null,
            'contract_signed_at' => null,
        ]);

        BrokerActivityLog::record('contract_sent', $broker->id, "إرسال عقد الوساطة للوسيط ({$broker->reference_number})", Auth::id());

        try {
            Mail::to($broker->email)->send(new BrokerContractMail($broker));
        } catch (\Throwable $e) {
            Log::error('Failed to send broker contract email: '.$e->getMessage(), ['broker_id' => $broker->id]);
        }

        $this->contractFile = null;

        session()->flash('message', "تم إرسال العقد إلى الوسيط {$broker->name} بنجاح.");
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
