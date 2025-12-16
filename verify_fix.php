<?php

use App\Models\Project;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Testing query...\n";

    // Prepare subquery for unit counts
    $unitCounts = Unit::select('project_id')
        ->selectRaw('count(case when "case" = 0 then 1 end)::int as available_units_count')
        ->selectRaw('count(case when "case" = 1 then 1 end)::int as reserved_units_count')
        ->selectRaw('count(case when "case" = 2 then 1 end)::int as sold_units_count')
        ->groupBy('project_id');

    $query = Project::with(['projectType', 'developer', 'units'])
        ->leftJoinSub($unitCounts, 'unit_counts', 'projects.id', '=', 'unit_counts.project_id')
        ->select('projects.*', 'unit_counts.available_units_count', 'unit_counts.reserved_units_count', 'unit_counts.sold_units_count')
        ->where('status', 1)
        ->orderByRaw('
        CASE
            WHEN unit_counts.available_units_count > 0 THEN 1
            WHEN unit_counts.reserved_units_count > 0 THEN 2
            ELSE 3
        END
    ');

    $sql = $query->toSql();
    echo "Generated SQL: " . $sql . "\n";
    
    // Attempt to run the query (limit 1)
    $results = $query->limit(1)->get();
    echo "Query executed successfully.\n";
    
    if ($results->isNotEmpty()) {
        $project = $results->first();
        echo "First project: " . $project->id . "\n";
        echo "Available: " . $project->available_units_count . "\n";
        echo "Reserved: " . $project->reserved_units_count . "\n";
    } else {
        echo "No projects found.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
