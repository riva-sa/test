<?php

namespace App\Exports;

use App\Models\UnitOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Collection;

class UnitOrdersExport implements FromCollection
{

    public function __construct(Collection $records = null)
    {
        $this->records = $records ?? UnitOrder::with(['user', 'unit', 'project'])->get();
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return UnitOrder::all();
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
            'User Name',
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
            $unitOrder->unit->title,
            $unitOrder->user->name,
            $unitOrder->project->name,
            $unitOrder->created_at->format('Y-m-d H:i:s'),
            $unitOrder->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
