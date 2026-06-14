<?php

namespace App\Livewire\Broker;

use App\Models\BrokerActivityLog;
use App\Services\BrokerContractService;
use Illuminate\Support\Facades\Auth;
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

        // Already signed → no need to be here
        if ($broker->contractSigned()) {
            redirect()->route('broker.dashboard');
        }
    }

    public function downloadContract(): mixed
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker->contractSent() || ! Storage::disk('local')->exists($broker->contract_path)) {
            session()->flash('error', 'العقد غير متوفر حالياً.');

            return null;
        }

        return Storage::disk('local')->download(
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

        session()->flash('message', 'تم اعتماد العقد وتوقيعه بنجاح. أهلاً بك في بوابة الوسطاء!');

        return redirect()->route('broker.dashboard');
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
            Storage::disk('local')->put($path, $fileContent);
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

        session()->flash('message', 'تم استلام العقد الموقّع بنجاح. أهلاً بك في بوابة الوسطاء!');

        return redirect()->route('broker.dashboard');
    }

    public function render(): mixed
    {
        return view('livewire.broker.contract', [
            'broker' => Auth::guard('broker')->user(),
        ])->layout('layouts.broker-guest');
    }
}
