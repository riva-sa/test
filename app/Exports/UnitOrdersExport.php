<?php

namespace App\Exports;

use App\Models\UnitOrder;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UnitOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    private Builder $query;

    // Column letter => section background color (hex without #)
    private const SECTION_COLORS = [
        // Order Info (A–E)
        'A' => 'E8EAF6', 'B' => 'E8EAF6', 'C' => 'E8EAF6', 'D' => 'E8EAF6', 'E' => 'E8EAF6',
        // Customer Info (F–H)
        'F' => 'E3F2FD', 'G' => 'E3F2FD', 'H' => 'E3F2FD',
        // Purchase Intent (I–L)
        'I' => 'E8F5E9', 'J' => 'E8F5E9', 'K' => 'E8F5E9', 'L' => 'E8F5E9',
        // Unit & Project (M–N)
        'M' => 'FFF8E1', 'N' => 'FFF8E1',
        // Assignment (O–Q)
        'O' => 'FCE4EC', 'P' => 'FCE4EC', 'Q' => 'FCE4EC',
        // Marketing & Campaign (R–W)
        'R' => 'F3E5F5', 'S' => 'F3E5F5', 'T' => 'F3E5F5', 'U' => 'F3E5F5', 'V' => 'F3E5F5', 'W' => 'F3E5F5',
        // Banking Info (X–Z)
        'X' => 'E0F7FA', 'Y' => 'E0F7FA', 'Z' => 'E0F7FA',
        // Waiting List (AA–AE)
        'AA' => 'FFF3E0', 'AB' => 'FFF3E0', 'AC' => 'FFF3E0', 'AD' => 'FFF3E0', 'AE' => 'FFF3E0',
        // Notes (AF–AG)
        'AF' => 'EFEBE9', 'AG' => 'EFEBE9',
    ];

    public function __construct(?Builder $query = null)
    {
        $this->query = $query ?? UnitOrder::with([
            'user', 'unit', 'project', 'assignedSalesUser', 'lastActionByUser', 'notes.user',
        ]);
    }

    public function title(): string
    {
        return 'Orders';
    }

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return [
            // ── Order Info ──────────────────────────────────────────────
            'Order ID',
            'Status',
            'Order Source',
            'Created At',
            'Updated At',
            // ── Customer Info ────────────────────────────────────────────
            'Customer Name',
            'Email',
            'Phone',
            // ── Purchase Intent ──────────────────────────────────────────
            'Purchase Type',
            'Purchase Purpose',
            'Support Type',
            'Initial Message',
            // ── Unit & Project ───────────────────────────────────────────
            'Project Name',
            'Unit Title',
            // ── Assignment ───────────────────────────────────────────────
            'Assigned Employee',
            'Last Action By',
            'Created By',
            // ── Marketing & Campaign ─────────────────────────────────────
            'Marketing Source',
            'Campaign Name',
            'Ad Set',
            'Ad Squad',
            'Ad Name',
            'External ID',
            // ── Banking Info ─────────────────────────────────────────────
            'Bank Name',
            'Bank Employee Name',
            'Bank Employee Phone',
            // ── Waiting List ─────────────────────────────────────────────
            'Is Waiting List',
            'Waiting List Unit Type',
            'Waiting List Budget',
            'Waiting List Location',
            'Waiting List Notes',
            // ── Notes ────────────────────────────────────────────────────
            'Notes Count',
            'Notes',
        ];
    }

    public function map($order): array
    {
        $notes = $order->relationLoaded('notes') ? $order->notes : collect();
        $notesText = $notes->map(function ($note) {
            $author = $note->relationLoaded('user') && $note->user ? $note->user->name : '—';
            $date   = $note->created_at ? $note->created_at->format('Y-m-d H:i') : '';
            return "[{$date}] {$author}: {$note->note}";
        })->implode("\n");

        return [
            // Order Info
            $order->id,
            $order->statusLabel(),
            $order->orderSourceLabel(),
            $order->created_at?->format('Y-m-d H:i:s') ?? '—',
            $order->updated_at?->format('Y-m-d H:i:s') ?? '—',
            // Customer Info
            $order->name,
            $order->email ?? '—',
            $order->phone ?? '—',
            // Purchase Intent
            $order->purchaseTypeLabel(),
            $order->purchasePurposeLabel(),
            $order->support_type ?? '—',
            $order->message ?? '—',
            // Unit & Project
            $order->project?->name ?? '—',
            $order->unit?->title ?? '—',
            // Assignment
            $order->assignedSalesUser?->name ?? '—',
            $order->lastActionByUser?->name ?? '—',
            $order->user?->name ?? '—',
            // Marketing & Campaign
            $order->marketing_source ?? '—',
            $order->campaign_name ?? '—',
            $order->ad_set ?? '—',
            $order->ad_squad ?? '—',
            $order->ad_name ?? '—',
            $order->external_id ?? '—',
            // Banking Info
            $order->bank_name ?? '—',
            $order->bank_employee_name ?? '—',
            $order->bank_employee_phone ?? '—',
            // Waiting List
            $order->is_waiting_list ? 'Yes' : 'No',
            $order->waiting_list_unit_type ?? '—',
            $order->waiting_list_budget ?? '—',
            $order->waiting_list_location ?? '—',
            $order->waiting_list_notes ?? '—',
            // Notes
            $notes->count(),
            $notesText ?: '—',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Header row: bold white text on dark blue
        $sheet->getStyle('A1:AG1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1A237E'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        // Section tint on header cells
        foreach (self::SECTION_COLORS as $col => $hex) {
            $sheet->getStyle("{$col}1")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($hex);
            $sheet->getStyle("{$col}1")->getFont()
                ->getColor()->setRGB('1A237E');
        }

        // Data rows: alternating light gray / white, thin borders
        if ($lastRow > 1) {
            for ($row = 2; $row <= $lastRow; $row++) {
                $bg = ($row % 2 === 0) ? 'F9FAFB' : 'FFFFFF';
                $sheet->getStyle("A{$row}:AG{$row}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $bg],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'E5E7EB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_TOP,
                        'wrapText' => true,
                    ],
                ]);
            }
        }

        // Freeze the header row
        $sheet->freezePane('A2');

        // Row height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}
