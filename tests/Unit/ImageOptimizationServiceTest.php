<?php

use App\Services\ImageOptimizationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;

uses(RefreshDatabase::class);

it('skips optimization if file does not exist', function () {
    Storage::fake('public');
    Log::shouldReceive('error')->once()->withArgs(function($message) {
        return str_contains($message, 'original not found');
    });

    $service = new ImageOptimizationService();
    $results = $service->optimize('missing.jpg');

    expect($results)->toBeEmpty();
});
