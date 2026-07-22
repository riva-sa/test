<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectPriceListService;
use Illuminate\Http\Response;

class ProjectPdfController extends Controller
{
    public function download(Project $project, ProjectPriceListService $service)
    {
        $pdfContent = $service->generate($project);
        $fileName = $service->fileName($project);

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
