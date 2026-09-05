<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProjectPriceListService
{
    /**
     * Build a price-list PDF for the given project. The document lists all units
     * (sold first, then reserved, then available) — each group ordered by the
     * unit number numerically — with their status and prices,
     * the Riva and developer logos, and the extraction timestamp — because
     * availability and prices can change at any time.
     *
     * @return string Raw PDF bytes.
     */
    public function generate(Project $project): string
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(180);

        $project->loadMissing(['developer', 'city']);

        $query = $project->units()->select([
            'id', 'project_id', 'title', 'unit_number', 'unit_type', 'unit_area',
            'floor', 'beadrooms', 'case', 'unit_price', 'show_price'
        ]);
        $driver = $query->getConnection()->getDriverName();
        $caseField = $driver === 'mysql' ? '`case`' : '"case"';
        // MySQL needs UNSIGNED to cast a string column to a number; SQLite uses INTEGER.
        $numericUnitNumber = $driver === 'mysql'
            ? 'CAST(unit_number AS UNSIGNED)'
            : 'CAST(unit_number AS INTEGER)';

        // Sold units first, then reserved, then available (under construction last).
        $units = $query->orderByRaw("
            CASE {$caseField}
                WHEN 2 THEN 1
                WHEN 1 THEN 2
                WHEN 0 THEN 3
                WHEN 3 THEN 4
                ELSE 5
            END
        ")
        // Within each status group, order by the unit number as a number (not by id),
        // falling back to a string comparison for non-numeric unit numbers.
        ->orderByRaw("{$numericUnitNumber} ASC")
        ->orderBy('unit_number')
        ->get();

        $rivaLogo = $this->rivaLogo();
        $developerLogo = $this->developerLogo($project);

        $html = view('pdf.project-price-list', [
            'project' => $project,
            'units' => $units,
            'rivaLogo' => $rivaLogo,
            'developerLogo' => $developerLogo,
            'generatedAt' => now(),
        ])->render();

        $tempDir = $this->resolveTempDir();

        try {
            return $this->renderMpdf($html, $tempDir);
        } catch (\Throwable $e) {
            Log::warning('mPDF Price list generation failed on primary pass, retrying safely without images', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);

            // Fallback retry: render without any logos in case image parsing caused the failure
            $safeHtml = view('pdf.project-price-list', [
                'project' => $project,
                'units' => $units,
                'rivaLogo' => null,
                'developerLogo' => null,
                'generatedAt' => now(),
            ])->render();

            return $this->renderMpdf($safeHtml, sys_get_temp_dir());
        }
    }

    /**
     * Render the given HTML into a PDF string using mPDF.
     */
    private function renderMpdf(string $html, string $tempDir): string
    {
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
            'simpleTables' => true,
            'packTableData' => true,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->WriteHTML($html);

        if (ob_get_length()) {
            @ob_end_clean();
        }

        return $mpdf->Output('', \Mpdf\Output\Destination::STRING_RETURN);
    }

    /**
     * Resolve a safe and writable temporary directory for mPDF.
     */
    private function resolveTempDir(): string
    {
        $tempDir = storage_path('app/mpdf');
        if (! is_dir($tempDir) || ! is_writable($tempDir)) {
            @File::makeDirectory($tempDir, 0775, true, true);
        }

        if (! is_dir($tempDir) || ! is_writable($tempDir)) {
            $tempDir = sys_get_temp_dir() . '/mpdf';
            if (! is_dir($tempDir)) {
                @mkdir($tempDir, 0775, true);
            }
            if (! is_writable($tempDir)) {
                $tempDir = sys_get_temp_dir();
            }
        }

        return $tempDir;
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
        try {
            $path = public_path('frontend/img/logoyy.png');

            if (! is_file($path) || filesize($path) > 2 * 1024 * 1024) {
                return null;
            }

            $contents = file_get_contents($path);
            if (! $contents) {
                return null;
            }

            return $this->sanitizeImageForPdf($contents);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * The project's developer logo as a base64 data URI, or null when missing.
     */
    private function developerLogo(Project $project): ?string
    {
        try {
            $logo = $project->developer?->logo;

            if (! $logo || ! is_string($logo)) {
                return null;
            }

            $contents = null;

            // Handle full URL vs relative storage path
            if (str_starts_with($logo, 'http://') || str_starts_with($logo, 'https://')) {
                $parsed = parse_url($logo, PHP_URL_PATH);
                if ($parsed && str_contains($parsed, '/storage/')) {
                    $logoPath = ltrim(explode('/storage/', $parsed)[1] ?? '', '/');
                    if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                        $contents = Storage::disk('public')->get($logoPath);
                    }
                }
            } else {
                $logoPath = ltrim(str_replace('/storage/', '', $logo), '/');
                if (Storage::disk('public')->exists($logoPath)) {
                    $contents = Storage::disk('public')->get($logoPath);
                }
            }

            if (! $contents || strlen($contents) > 2 * 1024 * 1024) {
                return null;
            }

            return $this->sanitizeImageForPdf($contents);
        } catch (\Throwable $e) {
            Log::warning('Failed to load developer logo for project price list', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Validate, downscale, and convert an image to a safe PNG data-URI so mPDF
     * will never segfault or run out of memory parsing unsupported or oversized formats.
     */
    private function sanitizeImageForPdf(string $rawContents): ?string
    {
        if (! function_exists('getimagesizefromstring')) {
            return null;
        }

        $imageInfo = @getimagesizefromstring($rawContents);
        if (! $imageInfo || empty($imageInfo['mime'])) {
            return null;
        }

        $mime = strtolower($imageInfo['mime']);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
        if (! in_array($mime, $allowedMimes, true)) {
            return null;
        }

        if (! function_exists('imagecreatefromstring')) {
            if ($mime === 'image/webp') {
                return null; // mPDF cannot render raw WebP without conversion
            }
            return 'data:' . $mime . ';base64,' . base64_encode($rawContents);
        }

        $gdImg = @imagecreatefromstring($rawContents);
        if (! $gdImg) {
            return null;
        }

        $width = imagesx($gdImg);
        $height = imagesy($gdImg);

        if ($width <= 0 || $height <= 0) {
            imagedestroy($gdImg);
            return null;
        }

        // Cap dimensions at 360x180 max to keep PDF memory usage and file size minimal
        $maxWidth = 360;
        $maxHeight = 180;

        if ($width > $maxWidth || $height > $maxHeight) {
            $ratio = min($maxWidth / $width, $maxHeight / $height);
            $newWidth = max(1, (int) round($width * $ratio));
            $newHeight = max(1, (int) round($height * $ratio));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, false);
            imagesavealpha($resized, true);
            $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
            imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);
            imagecopyresampled($resized, $gdImg, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($gdImg);
            $gdImg = $resized;
        }

        ob_start();
        imagepng($gdImg, null, 6);
        $cleanPng = ob_get_clean();
        imagedestroy($gdImg);

        if (! $cleanPng || strlen($cleanPng) === 0) {
            return null;
        }

        return 'data:image/png;base64,' . base64_encode($cleanPng);
    }
}
