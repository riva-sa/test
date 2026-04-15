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
        $ingestAction->execute($request->validated());

        return response()->json(['message' => 'Lead created successfully.'], 201);
    }
}
