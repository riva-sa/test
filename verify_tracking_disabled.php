<?php

use App\Models\Project;
use App\Services\EnhancedTrackingService;
use App\Services\TrackingService;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Verifying tracking disablement...\n";

// 1. Test Trackable trait
echo "Testing Trackable trait...\n";
$project = new Project;
$project->id = 99999; // Dummy ID
$project->name = 'Test Project';
// Mock saving if needed, but track() usually works on instance if it doesn't require DB existence for relation (morphMany needs ID)

// We can mock the relation or just check the return value of track()
// Since we modified track() to return null immediately, it shouldn't even try to create a relation.
$result = $project->track('visit');
if ($result === null) {
    echo "PASS: Project::track() returned null.\n";
} else {
    echo 'FAIL: Project::track() returned '.print_r($result, true)."\n";
}

$shouldTrack = $project->shouldTrack('visit');
if ($shouldTrack === false) {
    echo "PASS: Project::shouldTrack() returned false.\n";
} else {
    echo 'FAIL: Project::shouldTrack() returned '.var_export($shouldTrack, true)."\n";
}

// 2. Test TrackingService
echo "Testing TrackingService...\n";
$service = new TrackingService;
$analytics = $service->getAnalytics();
if ($analytics['overview']['total_events'] === 0 && empty($analytics['daily_stats'])) {
    echo "PASS: TrackingService::getAnalytics() returned empty stats.\n";
} else {
    echo "FAIL: TrackingService::getAnalytics() returned data.\n";
}

// 3. Test EnhancedTrackingService
echo "Testing EnhancedTrackingService...\n";
$enhancedService = new EnhancedTrackingService;
$dashboard = $enhancedService->getDashboardOverview();
if (empty($dashboard['current']) && empty($dashboard['previous'])) {
    echo "PASS: EnhancedTrackingService::getDashboardOverview() returned empty stats.\n";
} else {
    echo "FAIL: EnhancedTrackingService::getDashboardOverview() returned data.\n";
}

// 4. Verify no new TrackingEvents are created (requires DB connection)
// We can't easily verify this without actually running the app, but the unit tests above confirm the code path is blocked.

echo "Verification complete.\n";
