<?php

namespace App\Livewire\Frontend;

use App\Models\Contact;
use Livewire\Component;

class ContactUs extends Component
{
    public $name = '';

    public $email = '';

    public $department = '';

    public $message = '';

    public $success = false;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email',
        'department' => 'required',
        'message' => 'required|min:10',
    ];

    protected $messages = [
        'name.required' => 'الاسم مطلوب',
        'name.min' => 'الاسم يجب أن يكون على الأقل 3 أحرف',
        'email.required' => 'البريد الإلكتروني مطلوب',
        'email.email' => 'يرجى إدخال بريد إلكتروني صحيح',
        'department.required' => 'يرجى اختيار القسم',
        'message.required' => 'الرسالة مطلوبة',
        'message.min' => 'الرسالة يجب أن تكون على الأقل 10 أحرف',
    ];

    public function submit()
    {
        $this->validate();

        Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'department' => $this->department,
            'message' => $this->message,
            'status' => 'new',
        ]);

        $this->reset(['name', 'email', 'department', 'message']);
        $this->success = true;
    }

    public function render()
    {
        return view('livewire.frontend.contact-us');
    }
}
