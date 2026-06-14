<?php

namespace App\Livewire\Broker;

use App\Models\Broker;
use App\Models\BrokerActivityLog;
use App\Models\BrokerDocument;
use App\Models\City;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class Register extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1 — Account
    public $broker_type = Broker::TYPE_INDIVIDUAL;

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    // Step 2 — Profile
    public $name = '';

    public $national_id = '';

    public $whatsapp = '';

    public $city = '';

    public $iban = '';

    public $employment_status = '';

    public $heard_about_us = '';

    // Step 3 — Documents
    public $national_id_file;

    public $fal_license_file;

    public $iban_file;

    public $cities = [];

    public function mount()
    {
        if (Auth::guard('broker')->check()) {
            return redirect()->route('broker.dashboard');
        }

        $this->cities = City::orderBy('name')->pluck('name')->toArray();
    }

    protected function stepRules(int $step): array
    {
        return match ($step) {
            1 => [
                'broker_type' => 'required|in:'.Broker::TYPE_INDIVIDUAL, // company is coming soon
                'email' => 'required|email|max:255|unique:brokers,email',
                'password' => 'required|string|min:8|confirmed',
            ],
            2 => [
                'name' => 'required|string|min:3|max:255',
                'national_id' => 'required|string|min:10|max:20',
                'whatsapp' => 'required|string|min:9|max:15',
                'city' => 'required|string|max:255',
                'iban' => 'required|string|min:15|max:34',
                'employment_status' => 'required|string|in:'.implode(',', array_keys(Broker::EMPLOYMENT_STATUSES)),
                'heard_about_us' => 'required|string|in:'.implode(',', array_keys(Broker::HEARD_ABOUT_US_OPTIONS)),
            ],
            3 => [
                'national_id_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'fal_license_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
                'iban_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ],
            default => [],
        };
    }

    protected $messages = [
        'email.required' => 'البريد الإلكتروني مطلوب',
        'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
        'email.unique' => 'هذا البريد الإلكتروني مسجل مسبقاً',
        'password.required' => 'كلمة المرور مطلوبة',
        'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
        'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        'name.required' => 'الاسم الكامل مطلوب',
        'national_id.required' => 'رقم الهوية / الإقامة مطلوب',
        'national_id.min' => 'رقم الهوية يجب أن يكون 10 أرقام على الأقل',
        'whatsapp.required' => 'رقم الواتساب مطلوب',
        'city.required' => 'المدينة مطلوبة',
        'iban.required' => 'رقم الآيبان مطلوب',
        'iban.min' => 'رقم الآيبان غير صحيح',
        'employment_status.required' => 'الحالة الوظيفية مطلوبة',
        'heard_about_us.required' => 'يرجى اختيار كيف سمعت عنا',
        'national_id_file.required' => 'ملف الهوية الوطنية / الإقامة مطلوب',
        'national_id_file.mimes' => 'الملف يجب أن يكون PDF أو صورة',
        'national_id_file.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجا',
        'fal_license_file.required' => 'ملف رخصة فال مطلوب',
        'fal_license_file.mimes' => 'الملف يجب أن يكون PDF أو صورة',
        'fal_license_file.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجا',
        'iban_file.mimes' => 'الملف يجب أن يكون PDF أو صورة',
        'iban_file.max' => 'حجم الملف يجب ألا يتجاوز 5 ميجا',
    ];

    public function nextStep()
    {
        $this->validate($this->stepRules($this->step));

        if ($this->step < 3) {
            $this->step++;
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function submit()
    {
        // Re-validate everything in case data changed between steps
        $this->validate(array_merge($this->stepRules(1), $this->stepRules(2), $this->stepRules(3)));

        $broker = DB::transaction(function () {
            $broker = Broker::create([
                'broker_type' => $this->broker_type,
                'email' => $this->email,
                'password' => $this->password,
                'name' => $this->name,
                'national_id' => $this->national_id,
                'whatsapp' => $this->whatsapp,
                'city' => $this->city,
                'iban' => $this->iban,
                'employment_status' => $this->employment_status,
                'heard_about_us' => $this->heard_about_us,
                'status' => Broker::STATUS_PENDING,
                'reference_number' => Broker::generateReferenceNumber(),
            ]);

            // Documents are stored on the private local disk and served only to admins
            $files = [
                BrokerDocument::TYPE_NATIONAL_ID => $this->national_id_file,
                BrokerDocument::TYPE_FAL_LICENSE => $this->fal_license_file,
                BrokerDocument::TYPE_IBAN_FILE => $this->iban_file,
            ];

            foreach ($files as $type => $file) {
                if (! $file) {
                    continue;
                }

                $path = $file->store("broker-documents/{$broker->id}", 'local');

                BrokerDocument::create([
                    'broker_id' => $broker->id,
                    'type' => $type,
                    'path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                ]);
            }

            return $broker;
        });

        BrokerActivityLog::record('registered', $broker->id, "تسجيل وسيط جديد ({$broker->reference_number})");

        $this->step = 4;
    }

    public function render()
    {
        return view('livewire.broker.register', [
            'employmentStatuses' => Broker::EMPLOYMENT_STATUSES,
            'heardAboutUsOptions' => Broker::HEARD_ABOUT_US_OPTIONS,
        ])->layout('layouts.broker-guest');
    }
}
