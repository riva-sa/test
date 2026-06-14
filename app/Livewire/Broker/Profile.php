<?php

namespace App\Livewire\Broker;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Profile extends Component
{
    public function render()
    {
        $broker = Auth::guard('broker')->user()->load('documents');

        return view('livewire.broker.profile', [
            'broker' => $broker,
        ])->layout('layouts.broker');
    }
}
