<?php

namespace App\Console\Commands;

use App\Models\OrderExport;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanExpiredExports extends Command
{
    protected $signature = 'exports:clean';

    protected $description = 'Delete expired export files and their database records';

    public function handle(): int
    {
        $expired = OrderExport::expired()->get();

        $deletedFiles = 0;
        $deletedRecords = 0;

        foreach ($expired as $export) {
            // Delete the file if it exists
            if ($export->file_path && Storage::disk('local')->exists($export->file_path)) {
                Storage::disk('local')->delete($export->file_path);
                $deletedFiles++;
            }

            $export->delete();
            $deletedRecords++;
        }

        // Also clean failed exports older than 7 days
        $oldFailed = OrderExport::where('status', 'failed')
            ->where('created_at', '<', now()->subDays(7))
            ->get();

        foreach ($oldFailed as $export) {
            $export->delete();
            $deletedRecords++;
        }

        $this->info("Cleaned {$deletedFiles} files and {$deletedRecords} records.");

        return self::SUCCESS;
    }
}
