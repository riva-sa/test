<?php

namespace App\Livewire\Broker;

use App\Models\Broker;
use App\Models\BrokerActivityLog;
use App\Models\User;
use App\Services\BrokerContractService;
use App\Services\CrmNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Contract extends Component
{
    use WithFileUploads;

    public bool $agreed = false;

    /** Base64 PNG data-URL captured from the signature canvas. */
    public string $signatureData = '';

    /** Which tab is active: 'view' | 'sign_online' | 'upload' */
    public string $activeTab = 'view';

    /** The offline-signed PDF file uploaded by the broker. */
    public $signedPdfFile = null;

    /** Whether the upload agreement checkbox is checked. */
    public bool $uploadAgreed = false;

    protected $messages = [
        'agreed.accepted'          => 'يجب الموافقة على بنود العقد أولاً',
        'uploadAgreed.accepted'    => 'يجب الموافقة على صحة المستند المرفوع',
        'signatureData.required'   => 'يرجى رسم توقيعك في الحقل أعلاه',
        'signedPdfFile.required'   => 'يرجى اختيار ملف العقد الموقّع',
        'signedPdfFile.mimes'      => 'يجب أن يكون الملف المرفوع بصيغة PDF فقط',
        'signedPdfFile.max'        => 'حجم الملف يجب ألا يتجاوز 20 ميجابايت',
    ];

    public function mount(): void
    {
        $broker = Auth::guard('broker')->user();

        // Account fully active (signed + admin-approved) → no need to be here
        if ($broker->isActive()) {
            redirect()->route('broker.dashboard');
        }

        // Auto-retry generation once if it's missing but approved
        if ($broker->isApproved() && !$broker->contractSent()) {
            try {
                app(\App\Services\BrokerContractService::class)->generate($broker);
                $broker->refresh();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Auto-retry contract generation failed in mount: ' . $e->getMessage());
            }
        }
    }

    public function retryContractGeneration(): void
    {
        $broker = Auth::guard('broker')->user();

        if ($broker->contractSent()) {
            return;
        }

        try {
            app(\App\Services\BrokerContractService::class)->generate($broker);
            $broker->refresh();
            session()->flash('message', 'تم إعداد العقد بنجاح.');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Failed to manually retry contract generation: ' . $e->getMessage());
            session()->flash('error', 'تعذر إعداد العقد. يرجى المحاولة مرة أخرى أو التواصل مع الدعم الفني.');
        }
    }

    public function downloadContract(): mixed
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker->contractSent() || ! Storage::disk('public')->exists($broker->contract_path)) {
            session()->flash('error', 'العقد غير متوفر حالياً.');

            return null;
        }

        return Storage::disk('public')->download(
            $broker->contract_path,
            'broker-contract-' . $broker->reference_number . '.pdf'
        );
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['view', 'sign_online', 'upload']) ? $tab : 'view';
        // Reset upload state when leaving upload tab
        if ($tab !== 'upload') {
            $this->signedPdfFile = null;
            $this->uploadAgreed = false;
        }
    }

    public function clearSignature(): void
    {
        $this->signatureData = '';
    }

    /**
     * Submit the contract with a canvas-drawn e-signature.
     */
    public function submit(): mixed
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker->contractSent()) {
            session()->flash('error', 'العقد غير جاهز بعد. يرجى المحاولة لاحقاً.');

            return null;
        }

        $this->validate([
            'agreed'        => 'accepted',
            'signatureData' => 'required|string',
        ]);

        // Reject empty / whitespace-only signatures (blank canvas submit)
        if (empty(trim($this->signatureData)) || strlen($this->signatureData) < 100) {
            $this->addError('signatureData', 'يرجى رسم توقيعك أولاً قبل الإرسال.');

            return null;
        }

        try {
            app(BrokerContractService::class)->sign($broker, $this->signatureData);
        } catch (\Throwable $e) {
            session()->flash('error', 'حدث خطأ أثناء حفظ التوقيع. يرجى المحاولة مرة أخرى.');

            return null;
        }

        BrokerActivityLog::record(
            'contract_signed',
            $broker->id,
            "وقّع الوسيط على العقد إلكترونياً ({$broker->reference_number})"
        );

        $this->notifyManagersOfSignature($broker);

        session()->flash('message', 'تم توقيع العقد بنجاح. عقدك الآن قيد المراجعة النهائية من الإدارة، وسيتم تفعيل حسابك بعد اعتماده.');

        return redirect()->route('broker.contract');
    }

    /**
     * Accept an uploaded, manually-signed PDF from the broker.
     */
    public function submitUpload(): mixed
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker->contractSent()) {
            session()->flash('error', 'العقد غير جاهز بعد. يرجى المحاولة لاحقاً.');

            return null;
        }

        $this->validate([
            'uploadAgreed'  => 'accepted',
            'signedPdfFile' => 'required|mimes:pdf|max:20480',
        ]);

        $directory = "broker-documents/{$broker->id}/contract";
        $filename  = 'contract-signed.pdf';
        $path      = "{$directory}/{$filename}";

        try {
            // Store the uploaded file into local disk at the signed contract path
            $fileContent = file_get_contents($this->signedPdfFile->getRealPath());
            Storage::disk('public')->put($path, $fileContent);
        } catch (\Throwable $e) {
            session()->flash('error', 'حدث خطأ أثناء رفع الملف. يرجى المحاولة مرة أخرى.');

            return null;
        }

        $broker->update([
            'contract_signed_path' => $path,
            'contract_signed_at'   => now(),
        ]);

        BrokerActivityLog::record(
            'contract_signed',
            $broker->id,
            "رفع الوسيط نسخة العقد الموقّعة يدوياً ({$broker->reference_number})"
        );

        $this->notifyManagersOfSignature($broker);

        session()->flash('message', 'تم استلام العقد الموقّع بنجاح. عقدك الآن قيد المراجعة النهائية من الإدارة، وسيتم تفعيل حسابك بعد اعتماده.');

        return redirect()->route('broker.contract');
    }

    /**
     * Alert the CRM managers (via the in-app notification bell) that a broker has
     * signed their contract and it is awaiting final approval.
     */
    private function notifyManagersOfSignature(Broker $broker): void
    {
        try {
            // The notification needs a User as sender; prefer the admin who
            // approved this broker, otherwise fall back to any admin.
            $sender = $broker->approvedBy ?: User::role('Admin')->first();

            $recipientIds = User::role('Admin')->where('is_active', true)->pluck('id')->toArray();

            if (! $sender || empty($recipientIds)) {
                return;
            }

            app(CrmNotificationService::class)->send(
                'broker_contract_signed',
                $sender,
                'توقيع عقد وسيط',
                "قام الوسيط {$broker->name} ({$broker->reference_number}) بتوقيع عقد الوساطة، وهو الآن بانتظار الاعتماد النهائي.",
                $recipientIds
            );
        } catch (\Throwable $e) {
            Log::error('Failed to notify managers of broker signature: '.$e->getMessage(), ['broker_id' => $broker->id]);
        }
    }

    public function render(): mixed
    {
        return view('livewire.broker.contract', [
            'broker' => Auth::guard('broker')->user(),
        ])->layout('layouts.broker-guest');
    }
}
