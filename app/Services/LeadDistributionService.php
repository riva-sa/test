<?php

namespace App\Services;

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
                $project = $this->resolveProject($row['project_name']);
                if (! $project) {
                    $failed[] = ['row' => $rowNum, 'reason' => 'المشروع غير موجود: '.$row['project_name']];

                    continue;
                }

                $phone = $this->normalizePhone($row['client_phone']);
                if ($phone === '') {
                    $failed[] = ['row' => $rowNum, 'reason' => 'رقم الهاتف غير صالح'];

                    continue;
                }

                $key = $phone.'|'.$project->id;
                if (isset($seenKeys[$key])) {
                    $skipped[] = ['row' => $rowNum, 'reason' => 'تكرار في الملف لنفس الهاتف والمشروع'];

                    continue;
                }
                $seenKeys[$key] = true;

                if (UnitOrder::where('project_id', $project->id)->where('phone', $phone)->exists()) {
                    $skipped[] = ['row' => $rowNum, 'reason' => 'يوجد طلب بنفس الهاتف لهذا المشروع'];

                    continue;
                }

                $assignee = $pool[$rr % $poolSize];
                $rr++;

                $email = 'import.'.Str::lower(Str::limit($batchId, 36, '')).'.'.$rowNum.'.'.Str::random(6).'@invalid.local';

                DB::transaction(function () use ($row, $project, $phone, $email, $batchId, $assignee, $grantedByUserId, &$imported): void {
                    $order = UnitOrder::create([
                        'name' => $row['client_name'],
                        'email' => $email,
                        'phone' => $phone,
                        'message' => null,
                        'PurchaseType' => null,
                        'PurchasePurpose' => null,
                        'unit_id' => null,
                        'project_id' => $project->id,
                        'support_type' => null,
                        'status' => 0,
                        'order_source' => UnitOrder::ORDER_SOURCE_BULK_IMPORT,
                        'import_batch_id' => $batchId,
                        'assigned_sales_user_id' => $assignee->id,
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
        if (! isset($colMap['client_name'], $colMap['client_phone'], $colMap['project_name'])) {
            return ['valid' => [], 'errors' => [['row' => 1, 'reason' => 'الأعمدة المطلوبة: Client Name, Client Phone Number, Project Name']]];
        }

        $valid = [];
        $errors = [];

        foreach ($sheet->slice(1)->values() as $i => $row) {
            $excelRowNum = $i + 2;
            $cells = Collection::make($row)->toArray();
            $name = trim((string) ($cells[$colMap['client_name']] ?? ''));
            $phoneRaw = trim((string) ($cells[$colMap['client_phone']] ?? ''));
            $projectName = trim((string) ($cells[$colMap['project_name']] ?? ''));

            if ($name === '' && $phoneRaw === '' && $projectName === '') {
                continue;
            }

            if ($name === '' || $phoneRaw === '' || $projectName === '') {
                $errors[] = ['row' => $excelRowNum, 'reason' => 'صف غير مكتمل'];

                continue;
            }

            $valid[] = [
                'client_name' => $name,
                'client_phone' => $phoneRaw,
                'project_name' => $projectName,
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
            'client_phone' => ['client phone number', 'phone', 'رقم الهاتف', 'الجوال', 'mobile'],
            'project_name' => ['project name', 'المشروع', 'project'],
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
        return $raw;
    }
}
