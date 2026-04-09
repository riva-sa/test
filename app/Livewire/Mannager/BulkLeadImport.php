<?php

namespace App\Livewire\Mannager;

use App\Imports\BulkLeadSheetImport;
use App\Services\LeadDistributionService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class BulkLeadImport extends Component
{
    use WithFileUploads;

    public $file;

    /** @var array<string, mixed>|null */
    public $lastResult;

    protected function rules()
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ];
    }

    public function import(LeadDistributionService $distributionService)
    {
        $this->validate();

        $import = new BulkLeadSheetImport;
        Excel::import($import, $this->file);

        $parsed = $distributionService->parseSheetRows($import->rows);

        if ($parsed['valid'] === [] && $parsed['errors'] === []) {
            $this->lastResult = [
                'imported' => 0,
                'skipped' => [],
                'failed' => [['row' => 0, 'reason' => 'لا توجد بيانات في الملف']],
                'parse_only' => true,
            ];
            $this->reset('file');

            return;
        }

        if ($parsed['valid'] === [] && $parsed['errors'] !== []) {
            $this->lastResult = [
                'imported' => 0,
                'skipped' => [],
                'failed' => $parsed['errors'],
                'parse_only' => true,
            ];
            $this->reset('file');

            return;
        }

        $batchId = (string) \Illuminate\Support\Str::uuid();
        $outcome = $distributionService->assignAndCreate($parsed['valid'], $batchId, Auth::id());

        $this->lastResult = [
            'imported' => $outcome['imported'],
            'skipped' => array_merge($parsed['errors'] ?? [], $outcome['skipped']),
            'failed' => $outcome['failed'],
            'batch_id' => $batchId,
        ];

        $this->reset('file');
        session()->flash('bulk_import_message', 'اكتمل استيراد الملف.');
    }

    public function render()
    {
        return view('livewire.mannager.bulk-lead-import')->layout('layouts.custom');
    }
}
