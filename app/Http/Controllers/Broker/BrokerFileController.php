<?php

namespace App\Http\Controllers\Broker;

use App\Http\Controllers\Controller;
use App\Models\BrokerDocument;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
}
