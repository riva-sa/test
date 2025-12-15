<?php

namespace App\Livewire\Frontend\Conponents;

use Livewire\Component;

class PdfViewer extends Component
{
    public $pdfUrl;

    public $showModal = false;

    protected $listeners = ['showPdf'];

    public function showPdf($url)
    {
        $this->pdfUrl = $url;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function render()
    {
        return view('livewire.frontend.conponents.pdf-viewer');
    }
}
