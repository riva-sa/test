<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectPriceListService;
use Illuminate\Support\Facades\Log;

class ProjectPdfController extends Controller
{
    public function download(Project $project, ProjectPriceListService $service)
    {
        try {
            $pdfContent = $service->generate($project);
            $fileName = $service->fileName($project);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => (string) strlen($pdfContent),
            ]);
        } catch (\Throwable $e) {
            Log::error('Project PDF generation failed', [
                'project_id' => $project->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Failed to generate PDF document: ' . $e->getMessage(),
            ], 500);
        }
    }
}
