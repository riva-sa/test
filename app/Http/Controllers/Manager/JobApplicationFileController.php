<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class JobApplicationFileController extends Controller
{
    /**
     * Application files live on the private local disk and are only served to admins.
     */
    public function show(JobApplication $application, string $type)
    {
        Gate::authorize('manage-careers');

        $path = match ($type) {
            'cv' => $application->cv_path,
            'cover_letter' => $application->cover_letter_path,
            'portfolio' => $application->portfolio_path,
            default => null,
        };

        // Files live on the 'public' disk (persistent S3 bucket on Laravel
        // Cloud); the 'local' disk is ephemeral there.
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $name = str_replace(' ', '-', $application->name);

        return Storage::disk('public')->response($path, "application-{$application->id}-{$name}-{$type}.{$extension}");
    }
}
