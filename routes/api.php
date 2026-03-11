<?php

// ============================================================
//  File: routes/api.php  (add these lines)
// ============================================================

use App\Http\Controllers\Api\Ai\AiSearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('ai')
    ->middleware(['throttle:60,1', 'api.ai.key'])
    ->group(function () {
        Route::get('search',          [AiSearchController::class, 'search']);
        Route::get('search/projects', [AiSearchController::class, 'searchProjects']);
        Route::get('search/units',    [AiSearchController::class, 'searchUnits']);
    });