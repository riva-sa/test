<?php

namespace App\Console\Commands;

use App\Models\OptimizedImage;
use Illuminate\Console\Command;

class ImageOptimizationStatusCommand extends Command
{
    protected $signature = 'images:status';
    protected $description = 'Show the status of the image optimization queue';

    public function handle()
    {
        $total = OptimizedImage::count();
        $completed = OptimizedImage::completed()->count();
        $failed = OptimizedImage::failed()->count();
        $pending = OptimizedImage::pending()->count();

        $this->table(
            ['Status', 'Count'],
            [
                ['Total Variants Tracked', $total],
                ['Completed', $completed],
                ['Failed', $failed],
                ['Pending', $pending],
            ]
        );

        if ($failed > 0) {
            $this->error("$failed images failed to optimize. Check the optimized_images table for error messages.");
        }

        return 0;
    }
}
