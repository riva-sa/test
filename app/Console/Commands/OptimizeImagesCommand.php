<?php

namespace App\Console\Commands;

use App\Models\Developer;
use App\Models\ProjectMedia;
use App\Models\Unit;
use App\Services\ImageOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeImagesCommand extends Command
{
    protected $signature = 'images:optimize {--batch=50 : Number of images to process at once} {--model=all : Specific model to process (project-media, developer, unit)}';
    protected $description = 'Retroactively optimize existing images to WebP format';

    public function handle(ImageOptimizationService $optimizer)
    {
        $batchSize = $this->option('batch');
        $modelFilter = $this->option('model');
        $disk = Storage::disk('public');

        $pathsToProcess = [];

        $this->info("Scanning for images to optimize (Model: $modelFilter)...");

        // 1. Project Media
        if ($modelFilter === 'all' || $modelFilter === 'project-media') {
            $media = ProjectMedia::where('media_type', 'image')
                ->whereNotNull('media_url')
                ->pluck('media_url')->toArray();
            $pathsToProcess = array_merge($pathsToProcess, $media);
        }

        // 2. Developer Logos
        if ($modelFilter === 'all' || $modelFilter === 'developer') {
            $logos = Developer::whereNotNull('logo')->pluck('logo')->toArray();
            $pathsToProcess = array_merge($pathsToProcess, $logos);
        }

        // 3. Unit Floor Plans
        if ($modelFilter === 'all' || $modelFilter === 'unit') {
            $plans = Unit::whereNotNull('floor_plan')->pluck('floor_plan')->toArray();
            $pathsToProcess = array_merge($pathsToProcess, $plans);
        }

        $pathsToProcess = array_unique(array_filter($pathsToProcess));
        
        // Filter out already processed or non-existent files
        $validPaths = [];
        foreach ($pathsToProcess as $path) {
            if ($disk->exists($path) && !$optimizer->hasOptimizedVariants($path)) {
                $validPaths[] = $path;
            }
        }

        $total = count($validPaths);
        if ($total === 0) {
            $this->info('No unprocessed images found.');
            return 0;
        }

        $this->info("Found $total images to process.");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        // Process in batches
        $chunks = array_chunk($validPaths, $batchSize);
        $successCount = 0;
        $failCount = 0;

        foreach ($chunks as $chunk) {
            foreach ($chunk as $path) {
                try {
                    $results = $optimizer->optimize($path);
                    if (!empty($results)) {
                        $successCount++;
                    } else {
                        $failCount++; // Maybe skipped (upscaling/unsupported) or failed
                    }
                } catch (\Exception $e) {
                    $failCount++;
                }
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Optimization complete! Success: $successCount, Skipped/Failed: $failCount.");

        return 0;
    }
}
