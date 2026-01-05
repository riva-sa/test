<?php

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

DB::enableQueryLog();

// Simulate sort by unit_price
$sortBy = 'unit_price';
$sortDirection = 'asc';

$unitCounts = Unit::toBase()
    ->select('project_id')
    ->selectRaw('count(case when "case" = 0 then 1 end) as available_units_count')
    ->groupBy('project_id');

$query = Project::with(['projectType', 'developer', 'units', 'projectMedia'])
    ->leftJoinSub($unitCounts, 'unit_counts', 'projects.id', '=', 'unit_counts.project_id')
    ->select('projects.*', 'unit_counts.available_units_count')
    ->where('status', 1)
    ->where('unit_counts.available_units_count', '>', 0);

if ($sortBy === 'unit_price') {
    $query->addSelect([
        'min_unit_price' => Unit::selectRaw('MIN(unit_price)')
            ->whereColumn('units.project_id', 'projects.id')
            ->limit(1),
    ])->orderBy('min_unit_price', $sortDirection);
}

$projects = $query->limit(5)->get();

echo 'Projects count: '.$projects->count()."\n";
foreach ($projects as $project) {
    echo "Project: {$project->id}, Min Price: {$project->min_unit_price}\n";
}

$log = DB::getQueryLog();
// echo "SQL: " . $log[count($log)-1]['query'] . "\n";
