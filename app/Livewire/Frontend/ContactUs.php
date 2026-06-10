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

    protected $messages = [];

    public function getMessages(): array
    {
        return [
            'name.required' => __('public.contact.validation.name_required'),
            'name.min' => __('public.contact.validation.name_min'),
            'email.required' => __('public.contact.validation.email_required'),
            'email.email' => __('public.contact.validation.email_email'),
            'department.required' => __('public.contact.validation.department_required'),
            'message.required' => __('public.contact.validation.message_required'),
            'message.min' => __('public.contact.validation.message_min'),
        ];
    }

    public function submit()
    {
        $this->validate($this->rules, $this->getMessages());

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
