<?php

namespace App\Livewire\Broker;

use App\Models\BrokerActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class Contract extends Component
{
    use WithFileUploads;

    public $agreed = false;

    public $signed_contract;

    protected $messages = [
        'agreed.accepted' => 'يجب الموافقة على بنود العقد أولاً',
        'signed_contract.required' => 'يرجى رفع نسخة العقد الموقعة',
        'signed_contract.mimes' => 'العقد الموقع يجب أن يكون بصيغة PDF',
        'signed_contract.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجا',
    ];

    public function mount()
    {
        $broker = Auth::guard('broker')->user();

        // العقد موقع بالفعل → لا حاجة لهذه الصفحة
        if ($broker->contractSigned()) {
            return redirect()->route('broker.dashboard');
        }
    }

    public function downloadContract()
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker->contractSent() || ! Storage::disk('local')->exists($broker->contract_path)) {
            session()->flash('error', 'العقد غير متوفر حالياً.');

            return;
        }

        return Storage::disk('local')->download($broker->contract_path, 'broker-contract-'.$broker->reference_number.'.pdf');
    }

    public function submit()
    {
        $broker = Auth::guard('broker')->user();

        if (! $broker->contractSent()) {
            session()->flash('error', 'لم يتم إرسال العقد بعد من قبل الإدارة.');

            return;
        }

        $this->validate([
            'agreed' => 'accepted',
            'signed_contract' => 'required|file|mimes:pdf|max:10240',
        ]);

        $path = $this->signed_contract->store("broker-documents/{$broker->id}/contract", 'local');

        $broker->update([
            'contract_signed_path' => $path,
            'contract_signed_at' => now(),
        ]);

        BrokerActivityLog::record('contract_signed', $broker->id, "وافق الوسيط على العقد ورفع النسخة الموقعة ({$broker->reference_number})");

        session()->flash('message', 'تم اعتماد العقد بنجاح. أهلاً بك في بوابة الوسطاء!');

        return redirect()->route('broker.dashboard');
    }

    public function render()
    {
        return view('livewire.broker.contract', [
            'broker' => Auth::guard('broker')->user(),
        ])->layout('layouts.broker-guest');
    }
}
