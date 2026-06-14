<?php

namespace App\Services;

use App\Models\Broker;
use App\Models\BrokerContractTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BrokerContractService
{
    /**
     * Disk holding all broker contract files. We use 'public' because on
     * Laravel Cloud it maps to the persistent S3 bucket, whereas the 'local'
     * disk is ephemeral (files vanish between requests/deploys). In local dev
     * it resolves to storage/app/public — consistent in both environments.
     */
    private const DISK = 'public';

    /**
     * Generate a personalised PDF contract for the given broker.
     * Uses the admin-uploaded template PDF with coordinate overlays, or falls
     * back to the fixed Blade template if no visual template is set.
     *
     * @return string  The storage path of the generated PDF.
     */
    public function generate(Broker $broker): string
    {
        $template = BrokerContractTemplate::getActiveTemplate();

        // Fallback to old DOMPDF view-based generation if no template is configured
        if (!$template || !Storage::disk(self::DISK)->exists($template->pdf_path)) {
            Log::warning('Active contract template PDF not available, using fallback.', [
                'template_id' => $template?->id,
            ]);
            return $this->generateFallback($broker);
        }

        // mPDF/FPDI needs a real local file path, so pull the template (which may
        // live on S3) down to a temp file.
        $templatePath = $this->localTemplateCopy($template->pdf_path);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 11,
            'default_font' => 'dejavusans',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);
        
        $mpdf->SetDirectionality('rtl');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $pageCount = $mpdf->setSourceFile($templatePath);
        $fieldsConfig = $template->fields_config ?? [];

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $mpdf->importPage($i);
            $size  = $mpdf->getTemplateSize($tplId);
            // Page size must be passed via `newformat`; AddPage()'s 2nd arg is
            // `$condition` (a string), so passing an array there throws.
            $mpdf->AddPageByArray([
                'orientation' => $size['width'] > $size['height'] ? 'L' : 'P',
                'newformat'   => [$size['width'], $size['height']],
            ]);
            $mpdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            foreach ($fieldsConfig as $fieldName => $coords) {
                if (!isset($coords['page']) || intval($coords['page']) !== $i || $fieldName === 'signature') {
                    continue;
                }

                $value = match ($fieldName) {
                    'name'             => $broker->name,
                    'reference_number' => $broker->reference_number,
                    'national_id'      => $broker->national_id ?? '—',
                    'phone'            => $broker->whatsapp ?? '—',
                    'email'            => $broker->email,
                    'iban'             => $broker->iban ?? '—',
                    'date'             => $broker->approved_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                    default            => '',
                };

                $this->writeTextOverlay($mpdf, $fieldName, $value, floatval($coords['x']), floatval($coords['y']), $size);
            }
        }

        $directory = "broker-documents/{$broker->id}/contract";
        $filename  = 'contract.pdf';
        $path      = "{$directory}/{$filename}";

        Storage::disk(self::DISK)->put($path, $mpdf->Output('', 'S'));
        @unlink($templatePath);

        // Update broker record
        $broker->update([
            'contract_path'    => $path,
            'contract_sent_at' => now(),
            // Invalidate any previous signature so the broker must re-sign
            'contract_signed_path' => null,
            'contract_signed_at'   => null,
        ]);

        return $path;
    }

    /**
     * Overlay a base64-encoded PNG signature onto the contract PDF.
     *
     * @param  string  $signatureDataUrl  The base64 data-URL from the canvas (image/png).
     * @return string  The storage path of the signed PDF.
     */
    public function sign(Broker $broker, string $signatureDataUrl): string
    {
        $template = BrokerContractTemplate::getActiveTemplate();

        // Fallback to old DOMPDF view-based signature if no template is configured
        if (!$template || !Storage::disk(self::DISK)->exists($template->pdf_path)) {
            return $this->signFallback($broker, $signatureDataUrl);
        }

        // mPDF/FPDI needs a real local file path (template may live on S3).
        $templatePath = $this->localTemplateCopy($template->pdf_path);

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font_size' => 11,
            'default_font' => 'dejavusans',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
        ]);
        
        $mpdf->SetDirectionality('rtl');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $pageCount = $mpdf->setSourceFile($templatePath);
        $fieldsConfig = $template->fields_config ?? [];

        for ($i = 1; $i <= $pageCount; $i++) {
            $tplId = $mpdf->importPage($i);
            $size  = $mpdf->getTemplateSize($tplId);
            // Page size must be passed via `newformat`; AddPage()'s 2nd arg is
            // `$condition` (a string), so passing an array there throws.
            $mpdf->AddPageByArray([
                'orientation' => $size['width'] > $size['height'] ? 'L' : 'P',
                'newformat'   => [$size['width'], $size['height']],
            ]);
            $mpdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);

            foreach ($fieldsConfig as $fieldName => $coords) {
                if (!isset($coords['page']) || (int) $coords['page'] !== $i) {
                    continue;
                }

                $xPct = (float) $coords['x'];
                $yPct = (float) $coords['y'];

                if ($fieldName === 'signature') {
                    $x_mm  = ($xPct / 100.0) * $size['width'];
                    $y_mm  = ($yPct / 100.0) * $size['height'];
                    $sigHtml = '<img src="' . $signatureDataUrl . '" style="max-width:50mm;max-height:25mm;" />';
                    $mpdf->WriteFixedPosHTML($sigHtml, $x_mm - 25, $y_mm - 12.5, 50, 25);
                } else {
                    $value = match ($fieldName) {
                        'name'             => $broker->name,
                        'reference_number' => $broker->reference_number,
                        'national_id'      => $broker->national_id ?? '—',
                        'phone'            => $broker->whatsapp ?? '—',
                        'email'            => $broker->email,
                        'iban'             => $broker->iban ?? '—',
                        'date'             => $broker->approved_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
                        default            => '',
                    };

                    $this->writeTextOverlay($mpdf, $fieldName, $value, $xPct, $yPct, $size);
                }
            }
        }

        $directory = "broker-documents/{$broker->id}/contract";
        $path      = "{$directory}/contract-signed.pdf";

        Storage::disk(self::DISK)->put($path, $mpdf->Output('', 'S'));
        @unlink($templatePath);

        $broker->update([
            'contract_signed_path' => $path,
            'contract_signed_at'   => now(),
        ]);

        return $path;
    }

    /**
     * Fallback HTML contract generation using dompdf.
     */
    protected function generateFallback(Broker $broker): string
    {
        $pdf = Pdf::loadView('pdf.broker-contract', [
            'broker' => $broker,
        ]);

        $pdf->setOption([
            'defaultFont'        => 'DejaVu Sans',
            'isRemoteEnabled'    => false,
            'isHtml5ParserEnabled' => true,
            'chroot'             => base_path(),
        ]);

        $directory = "broker-documents/{$broker->id}/contract";
        $filename  = 'contract.pdf';
        $path      = "{$directory}/{$filename}";

        Storage::disk(self::DISK)->put($path, $pdf->output());

        $broker->update([
            'contract_path'    => $path,
            'contract_sent_at' => now(),
            'contract_signed_path' => null,
            'contract_signed_at'   => null,
        ]);

        return $path;
    }

    /**
     * Fallback signature embedding using dompdf.
     */
    protected function signFallback(Broker $broker, string $signatureDataUrl): string
    {
        $base64 = preg_replace('/^data:image\/\w+;base64,/', '', $signatureDataUrl);
        $imageData = base64_decode($base64);

        $pdf = Pdf::loadView('pdf.broker-contract', [
            'broker'         => $broker,
            'signatureImage' => 'data:image/png;base64,' . base64_encode($imageData),
            'signedAt'       => now()->format('Y-m-d H:i'),
        ]);

        $pdf->setOption([
            'defaultFont'          => 'DejaVu Sans',
            'isRemoteEnabled'      => false,
            'isHtml5ParserEnabled' => true,
            'chroot'               => base_path(),
        ]);

        $directory = "broker-documents/{$broker->id}/contract";
        $path      = "{$directory}/contract-signed.pdf";

        Storage::disk(self::DISK)->put($path, $pdf->output());

        $broker->update([
            'contract_signed_path' => $path,
            'contract_signed_at'   => now(),
        ]);

        return $path;
    }

    /**
     * Copy a template PDF from the (possibly remote/S3) contracts disk to a
     * local temp file, because mPDF/FPDI's setSourceFile() requires a real path.
     *
     * @return string  Absolute path to the temp file (caller unlinks it).
     */
    private function localTemplateCopy(string $diskPath): string
    {
        $tmp = tempnam(sys_get_temp_dir(), 'broker_tpl_') . '.pdf';
        file_put_contents($tmp, Storage::disk(self::DISK)->get($diskPath));

        return $tmp;
    }

    /**
     * Write a single field value at the correct absolute position using mm coordinates.
     * Uses lateef font for Arabic fields and dejavusans for LTR fields (email, IBAN, etc.).
     *
     * @param array{width: float, height: float} $size Page dimensions in mm.
     */
    private function writeTextOverlay(\Mpdf\Mpdf $mpdf, string $fieldName, string $value, float $xPct, float $yPct, array $size): void
    {
        $x_mm = ($xPct / 100.0) * $size['width'];
        $y_mm = ($yPct / 100.0) * $size['height'];

        // LTR fields contain numbers/Latin characters — use dejavusans + ltr alignment
        $ltrFields = ['email', 'iban', 'phone', 'reference_number', 'national_id', 'date'];
        $isLtr = in_array($fieldName, $ltrFields, true);

        $font  = $isLtr ? 'dejavusans' : 'lateef';
        $dir   = $isLtr ? 'ltr' : 'rtl';
        $align = $isLtr ? 'left' : 'right';
        $boxW  = in_array($fieldName, ['name', 'email', 'iban'], true) ? 80.0 : 55.0;
        $boxH  = 8.0;

        // Center the box horizontally and vertically around the click point
        $boxLeft = $x_mm - ($boxW / 2.0);
        $boxTop  = $y_mm - ($boxH / 2.0);

        $html = '<p style="font-family:' . $font . ';font-size:11pt;direction:' . $dir . ';text-align:' . $align . ';color:#111827;font-weight:bold;margin:0;padding:0;">'
              . htmlspecialchars($value, ENT_QUOTES, 'UTF-8')
              . '</p>';

        $mpdf->WriteFixedPosHTML($html, $boxLeft, $boxTop, $boxW, $boxH);
    }
}
