<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\BrokerDocument;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class BrokerDocumentController extends Controller
{
    /**
     * Broker documents live on the private local disk and are only served to admins.
     */
    public function show(BrokerDocument $document)
    {
        Gate::authorize('manage-brokers');

        abort_unless(Storage::disk('local')->exists($document->path), 404);

        return Storage::disk('local')->response($document->path, $document->original_name);
    }

    /**
     * Serve the sent contract or the broker-signed copy to admins.
     */
    public function contract(Broker $broker, string $type)
    {
        Gate::authorize('manage-brokers');

        $path = match ($type) {
            'contract' => $broker->contract_path,
            'signed' => $broker->contract_signed_path,
            default => null,
        };

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, "broker-{$broker->reference_number}-{$type}.pdf");
    }
}
