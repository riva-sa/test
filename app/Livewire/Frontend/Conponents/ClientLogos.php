<?php

namespace App\Livewire\Frontend\Conponents;

use App\Models\Developer;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ClientLogos extends Component
{
    public $clients;

    public function mount()
    {
        $this->clients = Cache::remember('client_logos', 3600, function () {
            return Developer::whereNotNull('logo')
                ->where('logo', '!=', '')
                ->orderBy('id')
                ->get();
        });
    }

    public function render()
    {
        return view('livewire.frontend.conponents.client-logos');
    }
}
