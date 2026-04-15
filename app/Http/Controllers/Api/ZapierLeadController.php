<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Actions\IngestSocialMediaLead;
use App\Http\Requests\Api\SocialMediaLeadRequest;

class ZapierLeadController extends Controller
{
    /**
     * Store a new social media lead.
     */
    public function store(SocialMediaLeadRequest $request, IngestSocialMediaLead $ingestAction)
    {
        try {
            $ingestAction->execute($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Lead created successfully.'
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Zapier Lead Ingestion Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'payload' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process lead. Please try again later or contact support.',
                'debug_message' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}
