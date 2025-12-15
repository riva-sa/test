<?php

namespace App\Exports;

use App\Models\UnitOrder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnitOrdersExport implements FromCollection, WithHeadings, WithMapping
{
    private $records;

    public function __construct(?Collection $records = null)
    {
        $this->records = $records ?? UnitOrder::with(['user', 'unit', 'project'])->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Status',
            'Message',
            'Purchase Type',
            'Purchase Purpose',
            'Unit Title',
            'Project Name',
            'Created At',
            'Updated At',
        ];
    }

    public function map($unitOrder): array
    {
        return [
            $unitOrder->id,
            $unitOrder->name,
            $unitOrder->email,
            $unitOrder->phone,
            $unitOrder->status,
            $unitOrder->message,
            $unitOrder->PurchaseType,
            $unitOrder->PurchasePurpose,
            $unitOrder->unit ? $unitOrder->unit->title : 'N/A',
            $unitOrder->project ? $unitOrder->project->name : 'N/A',
            $unitOrder->created_at ? $unitOrder->created_at->format('Y-m-d H:i:s') : 'N/A',
            $unitOrder->updated_at ? $unitOrder->updated_at->format('Y-m-d H:i:s') : 'N/A',
        ];
    }
}
