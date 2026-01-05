<?php

use App\Models\Project;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require __DIR__.'/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

// Enable query logging
DB::enableQueryLog();

// Simulate ProjectSingle logic
// The component receives a slug and finds the project
// public function mount($slug) { $this->project = Project::where('slug', $slug)->firstOrFail(); ... }

// Let's pick a project slug
$projectSlug = Project::where('status', 1)->value('slug');

if (! $projectSlug) {
    echo "No active project found.\n";
    exit;
}

echo 'Testing ProjectSingle for slug: '.$projectSlug."\n";

// 1. Current implementation simulation (Lazy loading)
echo "\n--- Current Implementation (Lazy Loading) ---\n";
DB::flushQueryLog();

$project = Project::where('slug', $projectSlug)->firstOrFail();

// Simulate accessing relationships in the view
// @foreach ($project->features as $features)
$features = $project->features;
foreach ($features as $feature) {
    // Access attributes
    $name = $feature->name;
}

// @foreach ($project->guarantees as $guarantee)
$guarantees = $project->guarantees;
foreach ($guarantees as $guarantee) {
    // Access attributes
    $name = $guarantee->name;
}

// Sales manager phone logic often accesses relationship if not careful, but let's check basic view usage first.

$log = DB::getQueryLog();
echo 'Total Queries: '.count($log)."\n";
foreach ($log as $query) {
    echo ' - '.$query['query'].' ('.$query['time']."ms)\n";
}

// 2. Optimized implementation simulation (Eager Loading)
echo "\n--- Optimized Implementation (Eager Loading) ---\n";
DB::flushQueryLog();

$projectOptimized = Project::where('slug', $projectSlug)
    ->with(['developer', 'projectMedia', 'features', 'guarantees', 'landmarks', 'projectType', 'salesManager']) // Eager load relationships
    ->firstOrFail();

// Simulate accessing relationships in the view
$features = $projectOptimized->features;
foreach ($features as $feature) {
    $name = $feature->name;
}

$guarantees = $projectOptimized->guarantees;
foreach ($guarantees as $guarantee) {
    $name = $guarantee->name;
}

$developer = $projectOptimized->developer;
$logo = $developer->logo ?? 'default';

$media = $projectOptimized->projectMedia;
$count = $media->count();

$landmarks = $projectOptimized->landmarks;
$count = $landmarks->count();

$virtualTour = $projectOptimized->virtualTour;

$projectType = $projectOptimized->projectType;
$typeName = $projectType->name ?? 'default';

$salesManager = $projectOptimized->salesManager;
$phone = $salesManager->phone ?? 'default';

$priceRange = $projectOptimized->price_range;
$spaceRange = $projectOptimized->space_range;

$logOptimized = DB::getQueryLog();
echo 'Total Queries: '.count($logOptimized)."\n";
foreach ($logOptimized as $query) {
    echo ' - '.$query['query'].' ('.$query['time']."ms)\n";
}
