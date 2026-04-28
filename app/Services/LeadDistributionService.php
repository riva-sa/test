<?php

namespace App\Services;

use App\Models\BlockedNumber;
use App\Models\OrderPermission;
use App\Models\Project;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadDistributionService
{
    /**
     * @param  array<int, array{client_name: string, client_phone: string, project_name: string}>  $validRows
     * @return array{imported: int, skipped: array, failed: array}
     */
    public function assignAndCreate(array $validRows, string $batchId, int $grantedByUserId): array
    {
        $salesUsers = User::role(config('lead_import.sales_role', 'sales'))
            ->where('is_active', true)
            ->where('on_vacation', false)
            ->orderBy('id')
            ->get();
        if ($salesUsers->isEmpty()) {
            return [
                'imported' => 0,
                'skipped' => [],
                'failed' => [['row' => 0, 'reason' => 'لا يوجد مندوبي مبيعات (دور sales)']],
            ];
        }

        $pool = $salesUsers->shuffle()->values();
        $poolSize = $pool->count();
        $imported = 0;
        $skipped = [];
        $failed = [];
        $seenKeys = [];
        $rr = 0;

        foreach ($validRows as $index => $row) {
            $rowNum = $index + 2;
            try {
                $project = null;
                if (!empty($row['project_name'])) {
                    $project = $this->resolveProject($row['project_name']);
                    if (! $project) {
                        $failed[] = ['row' => $rowNum, 'reason' => 'المشروع غير موجود: '.$row['project_name']];

                        continue;
                    }
                }

                $phone = $this->normalizePhone($row['client_phone']);
                if ($phone === '') {
                    $failed[] = ['row' => $rowNum, 'reason' => 'رقم الهاتف غير صالح'];

                    continue;
                }

                // Check for blocked numbers
                if (BlockedNumber::where('phone', $phone)->exists()) {
                    $failed[] = ['row' => $rowNum, 'reason' => 'هذا الرقم محظور من النظام'];
                    continue;
                }

                $project_id = $project ? $project->id : null;
                $key = $phone.'|'.($project_id ?? 'null');
                if (isset($seenKeys[$key])) {
                    $skipped[] = ['row' => $rowNum, 'reason' => 'تكرار في الملف لنفس الهاتف والمشروع'];

                    continue;
                }
                $seenKeys[$key] = true;

                if (UnitOrder::where('project_id', $project_id)->where('phone', $phone)->exists()) {
                    $skipped[] = ['row' => $rowNum, 'reason' => 'يوجد طلب بنفس الهاتف لهذا المشروع'];

                    continue;
                }

                $assignee = null;
                if (!empty($row['assigned_employee'])) {
                    $assignee = User::role(config('lead_import.sales_role', 'sales'))
                        ->where('name', 'like', '%' . trim($row['assigned_employee']) . '%')
                        ->first();
                }

                if (!$assignee) {
                    $assignee = $pool[$rr % $poolSize];
                    $rr++;
                }

                $purchaseType = $row['purchase_type'] ?? null;
                if ($purchaseType === 'كاش') $purchaseType = 'cash';
                elseif ($purchaseType === 'تقسيط') $purchaseType = 'installment';

                $purchasePurpose = $row['purchase_purpose'] ?? null;
                if ($purchasePurpose === 'استثمار') $purchasePurpose = 'investment';
                elseif ($purchasePurpose === 'سكنى' || $purchasePurpose === 'سكني') $purchasePurpose = 'personal';

                $email = 'import.'.Str::lower(Str::limit($batchId, 36, '')).'.'.$rowNum.'.'.Str::random(6).'@invalid.local';

                DB::transaction(function () use ($row, $project_id, $phone, $email, $batchId, $assignee, $grantedByUserId, $purchaseType, $purchasePurpose, &$imported): void {
                    $order = UnitOrder::create([
                        'name' => $row['client_name'],
                        'email' => $email,
                        'phone' => $phone,
                        'message' => null,
                        'PurchaseType' => $purchaseType,
                        'PurchasePurpose' => $purchasePurpose,
                        'unit_id' => null,
                        'project_id' => $project_id,
                        'support_type' => null,
                        'status' => 0,
                        'order_source' => UnitOrder::ORDER_SOURCE_BULK_IMPORT,
                        'import_batch_id' => $batchId,
                        'assigned_sales_user_id' => $assignee->id,
                        'marketing_source' => $row['channel'] ?? null,
                        'waiting_list_unit_type' => $row['unit_type'] ?? null,
                    ]);

                    OrderPermission::firstOrCreate(
                        [
                            'user_id' => $assignee->id,
                            'unit_order_id' => $order->id,
                            'permission_type' => 'manage',
                        ],
                        [
                            'granted_by' => $grantedByUserId,
                        ]
                    );

                    $imported++;
                });
            } catch (\Throwable $e) {
                $failed[] = ['row' => $rowNum, 'reason' => $e->getMessage()];
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
            'failed' => $failed,
        ];
    }

    /**
     * @return array{valid: array, errors: array}
     */
    public function parseSheetRows(Collection $sheet): array
    {
        if ($sheet->isEmpty()) {
            return ['valid' => [], 'errors' => [['row' => 1, 'reason' => 'الملف فارغ']]];
        }

        $headerRow = $sheet->first();
        if (! $headerRow) {
            return ['valid' => [], 'errors' => [['row' => 1, 'reason' => 'لا يوجد رأس أعمدة']]];
        }

        $colMap = $this->mapHeaders($headerRow->toArray());
        if (! isset($colMap['client_name'], $colMap['client_phone'])) {
            return ['valid' => [], 'errors' => [['row' => 1, 'reason' => 'الأعمدة المطلوبة: Client Name, Client Phone Number']]];
        }

        $valid = [];
        $errors = [];

        foreach ($sheet->slice(1)->values() as $i => $row) {
            $excelRowNum = $i + 2;
            $cells = Collection::make($row)->toArray();
            $name = trim((string) ($cells[$colMap['client_name']] ?? ''));
            $phoneRaw = trim((string) ($cells[$colMap['client_phone']] ?? ''));
            $projectName = isset($colMap['project_name']) ? trim((string) ($cells[$colMap['project_name']] ?? '')) : '';

            if ($name === '' && $phoneRaw === '' && $projectName === '') {
                continue;
            }

            if ($name === '' || $phoneRaw === '') {
                $errors[] = ['row' => $excelRowNum, 'reason' => 'اسم العميل ورقم الجوال مطلوبان'];

                continue;
            }

            $valid[] = [
                'client_name' => $name,
                'client_phone' => $phoneRaw,
                'project_name' => $projectName,
                'channel' => trim((string) ($cells[$colMap['channel']] ?? '')),
                'assigned_employee' => trim((string) ($cells[$colMap['assigned_employee']] ?? '')),
                'unit_type' => trim((string) ($cells[$colMap['unit_type']] ?? '')),
                'purchase_type' => trim((string) ($cells[$colMap['purchase_type']] ?? '')),
                'purchase_purpose' => trim((string) ($cells[$colMap['purchase_purpose']] ?? '')),
            ];
        }

        return ['valid' => $valid, 'errors' => $errors];
    }

    /**
     * @param  array<int, string|null>  $headerCells
     * @return array<string, int>|array{}
     */
    private function mapHeaders(array $headerCells): array
    {
        $aliases = [
            'client_name' => ['client name', 'اسم العميل', 'الاسم', 'name'],
            'client_phone' => ['client phone number', 'phone', 'رقم الهاتف', 'الجوال', 'mobile', 'رقم الجوال'],
            'project_name' => ['project name', 'المشروع', 'project', 'اسم المشروع'],
            'channel' => ['channel', 'source', 'القناة', 'مصدر العميل', 'القناة (مصدر العميل)'],
            'assigned_employee' => ['assigned employee', 'employee', 'الموظف', 'اسم الموظف المسند له', 'اسم الموظف'],
            'unit_type' => ['unit type', 'نوع الوحدة', 'unit'],
            'purchase_type' => ['purchase type', 'نوع الشراء', 'purchase'],
            'purchase_purpose' => ['purchase purpose', 'الغرض من الشراء', 'purpose'],
        ];

        $map = [];
        foreach ($headerCells as $idx => $cell) {
            $n = mb_strtolower(trim((string) $cell));
            foreach ($aliases as $key => $names) {
                if (in_array($n, $names, true)) {
                    $map[$key] = (int) $idx;
                    break;
                }
            }
        }

        return $map;
    }

    private function resolveProject(string $name): ?Project
    {
        $trimmed = trim($name);
        $exact = Project::query()->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($trimmed)])->get();
        if ($exact->count() === 1) {
            return $exact->first();
        }
        if ($exact->count() > 1) {
            return null;
        }

        $like = Project::query()
            ->whereRaw('LOWER(name) LIKE ?', ['%'.mb_strtolower($trimmed).'%'])
            ->get();
        if ($like->count() === 1) {
            return $like->first();
        }

        return null;
    }

    public function normalizePhone(string $raw): string
    {
        // $digits = preg_replace('/\D+/', '', $raw) ?? '';
        // if (str_starts_with($digits, '966')) {
        //     return '+'.$digits;
        // }
        // if (str_starts_with($digits, '0')) {
        //     $digits = substr($digits, 1);
        // }
        // if (strlen($digits) === 9 && str_starts_with($digits, '5')) {
        //     return '+966'.$digits;
        // }
        // if (strlen($digits) >= 9) {
        //     return '+'.$digits;
        // }

        return $raw;
    }
}
