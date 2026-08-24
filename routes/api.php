<?php

// ============================================================
//  File: routes/api.php  (add these lines)
// ============================================================

use App\Http\Controllers\Api\Ai\AiSearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('ai')
    ->middleware(['throttle:60,1', 'api.ai.key'])
    ->group(function () {
        Route::post('search', [AiSearchController::class, 'search']);
        Route::get('search/projects', [AiSearchController::class, 'searchProjects']);
        Route::get('search/units', [AiSearchController::class, 'searchUnits']);
    });

Route::post('zapier/social-media-lead', [App\Http\Controllers\Api\ZapierLeadController::class, 'store']);
Route::get('customers/export', [\App\Http\Controllers\Api\CustomerExportController::class, 'index'])
    ->middleware(['throttle:60,1', 'agent.api.key']);
Route::get('customers/{identifier}', [\App\Http\Controllers\Api\CustomerExportController::class, 'show'])
    ->middleware(['throttle:60,1', 'agent.api.key']);

Route::middleware(['throttle:60,1', 'agent.api.key'])->group(function () {
    Route::get('projects', [\App\Http\Controllers\Api\ProjectCatalogController::class, 'index']);
    Route::get('projects/{id}', [\App\Http\Controllers\Api\ProjectCatalogController::class, 'show']);
    Route::get('units', [\App\Http\Controllers\Api\ProjectCatalogController::class, 'units']);
    Route::get('units/{id}', [\App\Http\Controllers\Api\ProjectCatalogController::class, 'showUnit']);
});


