<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Livewire\Frontend\Conponents\ProjectsTab;
use Illuminate\Support\Facades\DB;

// Enable query logging
DB::enableQueryLog();

// Instantiate the component
$component = new ProjectsTab;
$component->mount();

// Simulate rendering (which triggers the property access in the view)
// We'll just manually iterate and access the properties to simulate the view
$count = 0;
foreach ($component->projects as $project) {
    $count++;
    $price = $project->price_range;
    $space = $project->space_range;
    $beds = $project->bedroom_range;
    $baths = $project->bathroom_range;
    $images = $project->getMainImages();
}

$queries = DB::getQueryLog();
echo 'Total projects: '.$count."\n";
echo 'Total queries: '.count($queries)."\n";

// Print counts of query types
$types = [];
foreach ($queries as $q) {
    $sql = $q['query'];
    // simplified key
    $key = substr($sql, 0, 50);
    if (! isset($types[$key])) {
        $types[$key] = 0;
    }
    $types[$key]++;
}

foreach ($types as $key => $val) {
    echo "$val x $key...\n";
}
