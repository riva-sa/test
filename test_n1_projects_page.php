<?php

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

DB::enableQueryLog();

// Simulate ProjectsPage query with FIX
$unitCounts = Unit::toBase()
    ->select('project_id')
    ->selectRaw('count(case when "case" = 0 then 1 end) as available_units_count')
    ->groupBy('project_id');

$projects = Project::with(['projectType', 'developer', 'units', 'projectMedia']) // Added projectMedia
    ->leftJoinSub($unitCounts, 'unit_counts', 'projects.id', '=', 'unit_counts.project_id')
    ->select('projects.*', 'unit_counts.available_units_count')
    ->where('status', 1)
    ->where('unit_counts.available_units_count', '>', 0)
    ->limit(5)
    ->get();

echo 'Projects count: '.$projects->count()."\n";

foreach ($projects as $project) {
    // Access properties used in blade
    $mainImage = $project->getMainImages();

    // Simulate the fix in blade: access collection instead of query
    // But check if it works even if I call ->first() on the collection
    $firstMedia = $project->projectMedia->first();

    $priceRange = $project->price_range;
}

$log = DB::getQueryLog();
echo 'Total queries: '.count($log)."\n";
