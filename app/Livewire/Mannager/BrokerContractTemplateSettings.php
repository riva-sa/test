<?php

namespace App\Livewire\Mannager;

use App\Models\BrokerContractTemplate;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BrokerContractTemplateSettings extends Component
{
    use WithFileUploads;

    public $pdfFile;
    public $tempPdfUrl  = null;
    public $tempPdfPath = null;
    public $savedTemplate = null;
    public $fieldsConfig  = [];

    /** Server-rendered PNG URLs, one per page. Populated when Ghostscript is available. */
    public array $pageImages = [];

    public array $mappableFields = [
        'name'             => 'اسم الوسيط',
        'reference_number' => 'رقم العضوية',
        'national_id'      => 'رقم الهوية / السجل',
        'phone'            => 'رقم الواتساب',
        'email'            => 'البريد الإلكتروني',
        'iban'             => 'رقم الآيبان (IBAN)',
        'date'             => 'تاريخ الاعتماد',
        'signature'        => 'توقيع الوسيط',
        'manager_signature' => 'توقيع المدير',
    ];

    public function mount(): void
    {
        Gate::authorize('manage-brokers');

        $this->savedTemplate = BrokerContractTemplate::getActiveTemplate();
        if ($this->savedTemplate) {
            $this->fieldsConfig = $this->savedTemplate->fields_config ?? [];
            $this->tempPdfPath  = $this->savedTemplate->pdf_path;
            $this->tempPdfUrl   = $this->templateFileUrl($this->savedTemplate->pdf_path);
            $this->pageImages   = $this->loadPageImages($this->savedTemplate->pdf_path);

            // Older templates were saved while Ghostscript wasn't detected, so no
            // high-quality page images exist. Render them now if GS is available
            // (gives correct Arabic instead of the garbled PDF.js fallback).
            if (empty($this->pageImages) && $this->ghostscriptBin()) {
                $this->pageImages = $this->renderPdfToImages($this->savedTemplate->pdf_path);
            }
        }
    }

    public function updatedPdfFile(): void
    {
        $this->validate([
            'pdfFile' => 'required|mimes:pdf|max:10240',
        ], [
            'pdfFile.mimes' => 'يجب اختيار ملف بصيغة PDF فقط.',
            'pdfFile.max'   => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',
        ]);

        $this->tempPdfPath = $this->pdfFile->store('contract-templates/temp', 'public');
        $this->tempPdfUrl  = $this->templateFileUrl($this->tempPdfPath);
        $this->pageImages  = $this->renderPdfToImages($this->tempPdfPath);
        $this->fieldsConfig = [];
    }

    public function setFieldCoordinates(string $fieldName, int $page, float $x, float $y): void
    {
        if (array_key_exists($fieldName, $this->mappableFields)) {
            $this->fieldsConfig[$fieldName] = [
                'page' => $page,
                'x'    => $x,
                'y'    => $y,
            ];
        }
    }

    public function clearField(string $fieldName): void
    {
        unset($this->fieldsConfig[$fieldName]);
    }

    public function saveSettings(): mixed
    {
        Gate::authorize('manage-brokers');

        if (!$this->tempPdfPath && !$this->savedTemplate) {
            $this->addError('pdfFile', 'يرجى رفع ملف العقد بصيغة PDF أولاً.');
            return null;
        }

        $pdfPath = $this->savedTemplate ? $this->savedTemplate->pdf_path : '';

        if ($this->pdfFile && $this->tempPdfPath) {
            $fileName  = 'template_' . time() . '.pdf';
            $finalPath = 'contract-templates/' . $fileName;

            if (Storage::disk('public')->exists($this->tempPdfPath)) {
                Storage::disk('public')->copy($this->tempPdfPath, $finalPath);
                Storage::disk('public')->delete($this->tempPdfPath);

                // Move rendered page images to the final location
                $tempDir  = $this->pageImagesDir($this->tempPdfPath);
                $finalDir = $this->pageImagesDir($finalPath);
                if (Storage::disk('public')->exists($tempDir)) {
                    Storage::disk('public')->makeDirectory($finalDir);
                    foreach (Storage::disk('public')->files($tempDir) as $file) {
                        Storage::disk('public')->copy($file, $finalDir . '/' . basename($file));
                    }
                    Storage::disk('public')->deleteDirectory($tempDir);
                }

                $pdfPath = $finalPath;
            }
        }

        BrokerContractTemplate::where('is_active', true)->update(['is_active' => false]);

        $this->savedTemplate = BrokerContractTemplate::create([
            'pdf_path'      => $pdfPath,
            'fields_config' => $this->fieldsConfig,
            'is_active'     => true,
        ]);

        session()->flash('message', 'تم حفظ قالب العقد وإحداثيات الحقول بنجاح.');

        return redirect()->route('manager.broker-applications');
    }

    public function render(): mixed
    {
        return view('livewire.mannager.broker-contract-template-settings')
            ->layout('layouts.custom');
    }

    // ─── private helpers ──────────────────────────────────────────────────────

    /**
     * Build a URL that streams a template PDF through the app rather than the
     * `/storage` symlink, which isn't reliably served on Laravel Cloud.
     */
    private function templateFileUrl(string $path): string
    {
        return route('manager.broker-contract-template.file', ['path' => $path]);
    }

    /** Convert every page of a PDF to a PNG using Ghostscript. Returns image URLs. */
    private function renderPdfToImages(string $pdfPath): array
    {
        $fullPath = Storage::disk('public')->path($pdfPath);
        $dir      = $this->pageImagesDir($pdfPath);

        Storage::disk('public')->deleteDirectory($dir);
        Storage::disk('public')->makeDirectory($dir);

        $gs = $this->ghostscriptBin();
        if (!$gs) {
            // No Ghostscript — caller falls back to PDF.js
            Storage::disk('public')->deleteDirectory($dir);
            return [];
        }

        $outputDir     = Storage::disk('public')->path($dir);
        $outputPattern = $outputDir . '/page-%d.png';

        $cmd = escapeshellarg($gs)
             . ' -dBATCH -dNOPAUSE -dSAFER -sDEVICE=png16m -r150'
             . ' -sOutputFile=' . escapeshellarg($outputPattern)
             . ' ' . escapeshellarg($fullPath)
             . ' 2>&1';

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0) {
            Log::warning('Ghostscript PDF render failed', ['output' => implode("\n", $output)]);
            Storage::disk('public')->deleteDirectory($dir);
            return [];
        }

        return $this->loadPageImages($pdfPath);
    }

    /** Load already-rendered PNG image URLs for a given PDF path. */
    private function loadPageImages(string $pdfPath): array
    {
        $dir    = $this->pageImagesDir($pdfPath);
        $images = [];
        for ($i = 1; Storage::disk('public')->exists($dir . '/page-' . $i . '.png'); $i++) {
            $images[] = '/storage/' . $dir . '/page-' . $i . '.png';
        }
        return $images;
    }

    /** Storage directory for the rendered pages of a given PDF path. */
    private function pageImagesDir(string $pdfPath): string
    {
        return dirname($pdfPath) . '/' . pathinfo($pdfPath, PATHINFO_FILENAME) . '-pages';
    }

    /** Return the Ghostscript binary path, or null if not installed. */
    private function ghostscriptBin(): ?string
    {
        // 1) Look it up on PATH (works when the web server inherits a full PATH).
        foreach (['gs', 'ghostscript'] as $bin) {
            $path = trim((string) shell_exec("command -v {$bin} 2>/dev/null"));
            if ($path !== '' && is_executable($path)) {
                return $path;
            }
        }

        // 2) Fall back to common absolute install locations. The web server
        //    (e.g. `php artisan serve`) often runs with a trimmed PATH that
        //    omits /opt/homebrew/bin, so `command -v` finds nothing there.
        foreach ([
            '/opt/homebrew/bin/gs',   // Apple-silicon Homebrew
            '/usr/local/bin/gs',      // Intel Homebrew / manual install
            '/usr/bin/gs',            // Linux distro packages
        ] as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
