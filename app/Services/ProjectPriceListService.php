<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProjectPriceListService
{
    /**
     * Build a price-list PDF for the given project. The document lists all units
     * (available, reserved, sold — available first) with their status and prices,
     * the Riva and developer logos, and the extraction timestamp — because
     * availability and prices can change at any time.
     *
     * @return string Raw PDF bytes.
     */
    public function generate(Project $project): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(120);

        $project->loadMissing(['developer', 'city']);

        $query = $project->units()->select([
            'id', 'project_id', 'title', 'unit_type', 'unit_area',
            'floor', 'beadrooms', 'case', 'unit_price', 'show_price'
        ]);
        $driver = $query->getConnection()->getDriverName();
        $caseField = $driver === 'mysql' ? '`case`' : '"case"';

        $units = $query->orderByRaw("
            CASE {$caseField}
                WHEN 0 THEN 1
                WHEN 1 THEN 2
                WHEN 3 THEN 3
                WHEN 2 THEN 4
                ELSE 5
            END
        ")
        ->orderBy('unit_price')
        ->get();

        $html = view('pdf.project-price-list', [
            'project' => $project,
            'units' => $units,
            'rivaLogo' => $this->rivaLogo(),
            'developerLogo' => $this->developerLogo($project),
            'generatedAt' => now(),
        ])->render();

        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir)) {
            @File::makeDirectory($tempDir, 0755, true, true);
        }

        if (! is_writable($tempDir)) {
            $tempDir = sys_get_temp_dir();
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'default_font' => 'dejavusans',
            'default_font_size' => 11,
            'margin_left' => 12,
            'margin_right' => 12,
            'margin_top' => 14,
            'margin_bottom' => 14,
            'tempDir' => $tempDir,
            'useSubsets' => true,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        if (ob_get_length()) {
            @ob_end_clean();
        }

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }

    /**
     * A safe, downloadable file name for the project's price list.
     */
    public function fileName(Project $project): string
    {
        $slug = $project->slug ?: \Illuminate\Support\Str::slug($project->name) ?: 'project-'.$project->id;

        return 'price-list-'.$slug.'-'.now()->format('Ymd').'.pdf';
    }

    /**
     * The Riva logo as a base64 data URI (mPDF embeds it reliably regardless of
     * the environment/disk).
     */
    private function rivaLogo(): ?string
    {
        $path = public_path('frontend/img/logoyy.png');

        if (! is_file($path)) {
            return null;
        }

        if (filesize($path) > 2 * 1024 * 1024) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode((string) file_get_contents($path));
    }

    /**
     * The project's developer logo as a base64 data URI, or null when missing.
     */
    private function developerLogo(Project $project): ?string
    {
        $logo = $project->developer?->logo;

        if (! $logo || ! Storage::disk('public')->exists($logo)) {
            return null;
        }

        if (Storage::disk('public')->size($logo) > 2 * 1024 * 1024) {
            return null;
        }

        $mime = Storage::disk('public')->mimeType($logo) ?: 'image/png';

        if (str_contains($mime, 'svg') || str_ends_with(strtolower($logo), '.svg')) {
            return null;
        }

        $contents = Storage::disk('public')->get($logo);

        return 'data:'.$mime.';base64,'.base64_encode($contents);
    }
}
