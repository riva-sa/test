<?php
namespace App\Livewire\Frontend\Conponents;
use App\Models\Developer;
use Livewire\Component;
use App\Models\Partner;

class ClientLogos extends Component
{
    public $clients;
    
    public function mount()
    {
        $this->clients = Developer::whereNotNull('logo')
                                  ->where('logo', '!=', '')
                                  ->orderBy('id')
                                  ->get();
    }
    
    public function render()
    {
        return view('livewire.frontend.conponents.client-logos');
    }
}