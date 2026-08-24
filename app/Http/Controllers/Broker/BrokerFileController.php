<?php

namespace App\Http\Controllers\Broker;

use App\Http\Controllers\Controller;
use App\Models\BrokerDocument;
use App\Models\Project;
use App\Models\ProjectMedia;
use App\Models\Unit;
use App\Services\ProjectPriceListService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BrokerFileController extends Controller
{
    /**
     * Stream the broker's own contract (or signed copy) inline for in-page preview.
     */
    public function contract(string $type = 'contract')
    {
        $broker = Auth::guard('broker')->user();

        $path = match ($type) {
            'contract' => $broker->contract_path,
            'signed' => $broker->contract_signed_path,
            default => null,
        };

        // Contract files live on the 'public' disk (persistent S3 bucket on
        // Laravel Cloud); the 'local' disk is ephemeral there.
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, "contract-{$broker->reference_number}.pdf", [
            'Content-Disposition' => 'inline',
            'X-Frame-Options'     => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self'",
        ]);
    }

    /**
     * Stream one of the broker's own uploaded documents inline.
     */
    public function document(BrokerDocument $document)
    {
        $broker = Auth::guard('broker')->user();

        abort_unless($document->broker_id === $broker->id, 403);
        abort_unless(Storage::disk('public')->exists($document->path), 404);

        return Storage::disk('public')->response($document->path, $document->original_name, [
            'Content-Disposition' => 'inline',
        ]);
    }

    /**
     * Download a unit's floor-plan image. Streamed through the app so the
     * attachment disposition is honoured on any disk (local or S3).
     */
    public function unitFloorPlan(Unit $unit)
    {
        abort_unless($unit->floor_plan && Storage::disk('public')->exists($unit->floor_plan), 404);
        // Brokers may only reach floor plans of units in active projects.
        abort_unless($unit->project && $unit->project->status, 404);

        $ext  = pathinfo($unit->floor_plan, PATHINFO_EXTENSION) ?: 'jpg';
        $name = 'floor-plan-'.(Str::slug($unit->title) ?: $unit->id).'.'.$ext;

        return Storage::disk('public')->download($unit->floor_plan, $name);
    }

    /**
     * Download a single project media file (image or video).
     */
    public function downloadProjectMedia(Project $project, ProjectMedia $media)
    {
        abort_unless($media->project_id === $project->id, 404);
        abort_unless($media->media_url && Storage::disk('public')->exists($media->media_url), 404);
        abort_unless($project->status, 404);

        $ext = pathinfo($media->media_url, PATHINFO_EXTENSION) ?: ($media->media_type === 'video' ? 'mp4' : 'jpg');
        $title = $media->media_title ?: (Str::slug($project->name) . '-' . $media->id);
        $name = Str::slug($title) . '.' . $ext;

        return Storage::disk('public')->download($media->media_url, $name);
    }

    /**
     * Download all images of a project packaged into a ZIP archive.
     */
    public function downloadProjectImagesZip(Project $project)
    {
        abort_unless($project->status, 404);

        $images = $project->projectMedia()->where('media_type', 'image')->get();
        abort_if($images->isEmpty(), 404, 'No images available for this project');

        $zipFileName = 'project-' . (Str::slug($project->name) ?: $project->id) . '-images.zip';
        $tempZipPath = storage_path('app/temp/' . Str::uuid() . '.zip');

        if (! file_exists(dirname($tempZipPath))) {
            mkdir(dirname($tempZipPath), 0755, true);
        }

        $zip = new \ZipArchive();
        if ($zip->open($tempZipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP archive');
        }

        $count = 1;
        foreach ($images as $img) {
            if ($img->media_url && Storage::disk('public')->exists($img->media_url)) {
                $fileContents = Storage::disk('public')->get($img->media_url);
                $ext = pathinfo($img->media_url, PATHINFO_EXTENSION) ?: 'jpg';
                $entryName = sprintf('image-%02d.%s', $count++, $ext);
                $zip->addFromString($entryName, $fileContents);
            }
        }

        $zip->close();

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Download the price list PDF for a project for approved brokers.
     */
    public function downloadProjectPdf(Project $project, ProjectPriceListService $service)
    {
        abort_unless($project->status, 404);

        try {
            $pdfContent = $service->generate($project);
            $fileName = $service->fileName($project);

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                'Content-Length' => (string) strlen($pdfContent),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Broker Project PDF generation failed', [
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
