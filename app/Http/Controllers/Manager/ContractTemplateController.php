<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class ContractTemplateController extends Controller
{
    /**
     * Stream a contract-template PDF inline for the in-page preview.
     *
     * Templates live on the 'public' disk, but the `/storage` symlink is not
     * reliably served on some hosts (e.g. Laravel Cloud's ephemeral disk), so we
     * stream the file through the app instead — the same approach the broker
     * contract preview already uses successfully.
     */
    public function file(Request $request)
    {
        Gate::authorize('manage-brokers');

        $path = (string) $request->query('path', '');

        // Only template PDFs are accessible, and never via path traversal.
        abort_unless(
            str_starts_with($path, 'contract-templates/') && ! str_contains($path, '..'),
            403
        );
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path, basename($path), [
            'Content-Disposition' => 'inline',
            'X-Frame-Options'     => 'SAMEORIGIN',
        ]);
    }
}
