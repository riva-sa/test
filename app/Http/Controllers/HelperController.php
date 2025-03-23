<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ProjectMedia;
use Illuminate\Support\Facades\Storage;
class HelperController extends Controller
{
    public function downloadPdf(Project $project, $file)
    {
        try {
            // Assume PDFs are stored in a subfolder like 'public/project-media'
            $filePath = $file;
            dd($filePath);
            // If the file exists in the storage, return the download response
            if (Storage::exists($filePath)) {
                return Storage::download($filePath);
            } else {
                abort(404, 'File not found');
            }
        } catch (\Exception $e) {
            abort(500, 'Error while processing the request');
        }
    }
}
