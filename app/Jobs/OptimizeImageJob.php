<?php

namespace App\Jobs;

use App\Services\ImageOptimizationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class OptimizeImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [30, 60, 120]; // Exponential backoff

    protected string $originalPath;

    public function __construct(string $originalPath)
    {
        $this->originalPath = $originalPath;
    }

    public function handle(ImageOptimizationService $optimizer): void
    {
        Log::info('Starting background image optimization', ['path' => $this->originalPath]);
        $optimizer->optimize($this->originalPath);
    }
}
