<?php

namespace App\Jobs;

use App\Models\OrderExport;
use App\Models\UnitOrder;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ExportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600; // 10 minutes

    public int $tries = 2;

    public function __construct(
        public int $exportId,
        public int $userId,
        public array $filters = [],
    ) {
    }

    public function handle(): void
    {
        $export = OrderExport::find($this->exportId);

        if (! $export) {
            Log::error("ExportOrdersJob: Export record #{$this->exportId} not found.");
            return;
        }

        $user = User::find($this->userId);
        if (! $user) {
            $export->markAsFailed('المستخدم غير موجود.');
            return;
        }

        try {
            // Build the query with filters (same logic as ManageOrders)
            $query = $this->buildQuery($user);

            // Count total rows
            $totalRows = $query->count();
            $export->markAsProcessing($totalRows);

            if ($totalRows === 0) {
                $export->markAsFailed('لا توجد بيانات للتصدير بناءً على الفلاتر المحددة.');
                $this->notifyUser($user, 'فشل التصدير', 'لا توجد بيانات للتصدير.', 'danger');
                return;
            }

            // Prepare CSV file
            $directory = 'exports';
            Storage::disk('local')->makeDirectory($directory);
            $filePath = $directory . '/' . $export->file_name;
            $fullPath = Storage::disk('local')->path($filePath);

            @chmod(dirname($fullPath), 0775);

            $file = fopen($fullPath, 'w');

            // UTF-8 BOM for Excel compatibility
            fwrite($file, "\xEF\xBB\xBF");

            // Write CSV headers
            fputcsv($file, $this->getCsvHeaders());

            // Process in chunks to save memory
            $processedRows = 0;
            $chunkSize = 500;

            $query->with([
                'notes.user',
                'unit',
                'project',
                'user',
                'assignedSalesUser',
                'lastActionByUser',
            ])->chunk($chunkSize, function ($orders) use ($file, &$processedRows, $export, $totalRows) {
                foreach ($orders as $order) {
                    fputcsv($file, $this->mapOrderToRow($order));
                    $processedRows++;
                }

                // Update progress after each chunk
                $export->updateProgress($processedRows);
            });

            fclose($file);
            @chmod($fullPath, 0664);

            // Mark as completed
            $export->markAsCompleted($filePath);

            // Send success notification
            $downloadUrl = route('manager.export.download', ['export' => $export->id]);

            $this->notifyUser(
                $user,
                'اكتمل التصدير ✅',
                "تم تصدير {$totalRows} طلب بنجاح. الملف متاح للتحميل لمدة 24 ساعة.",
                'success',
                $downloadUrl
            );

        } catch (\Throwable $e) {
            Log::error("ExportOrdersJob failed: {$e->getMessage()}", [
                'export_id' => $this->exportId,
                'trace' => $e->getTraceAsString(),
            ]);

            $export->markAsFailed($e->getMessage());

            $this->notifyUser(
                $user,
                'فشل التصدير ❌',
                'حدث خطأ أثناء التصدير: ' . mb_substr($e->getMessage(), 0, 200),
                'danger'
            );
        }
    }

    public function failed(\Throwable $e): void
    {
        $export = OrderExport::find($this->exportId);
        $export?->markAsFailed('فشل الـ Job: ' . $e->getMessage());

        $user = User::find($this->userId);
        if ($user) {
            $this->notifyUser($user, 'فشل التصدير ❌', 'حدث خطأ غير متوقع أثناء التصدير.', 'danger');
        }
    }

    // ── Private helpers ──────────────────────────────────────────────

    private function buildQuery(User $user)
    {
        $query = UnitOrder::query()->accessibleBy($user);
        $filters = $this->filters;

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('bank_name', 'like', "%{$search}%")
                    ->orWhere('bank_employee_name', 'like', "%{$search}%")
                    ->orWhere('bank_employee_phone', 'like', "%{$search}%")
                    ->orWhereHas('unit', fn ($q) => $q->where('title', 'like', "%{$search}%"));
            });
        }

        if (isset($filters['statusFilter']) && $filters['statusFilter'] !== '') {
            $query->where('status', $filters['statusFilter']);
        }

        if (isset($filters['projectFilter']) && $filters['projectFilter'] !== '') {
            $query->where('project_id', $filters['projectFilter']);
        }

        if (! empty($filters['salesManagerFilter'])) {
            $query->where('assigned_sales_user_id', $filters['salesManagerFilter']);
        }

        if (! empty($filters['fromDate'])) {
            $query->whereDate('created_at', '>=', $filters['fromDate']);
        }

        if (! empty($filters['toDate'])) {
            $query->whereDate('created_at', '<=', $filters['toDate']);
        }

        if (isset($filters['sourceFilter']) && $filters['sourceFilter'] !== '') {
            $query->where(function ($q) use ($filters) {
                $q->where('order_source', $filters['sourceFilter'])
                    ->orWhere('marketing_source', $filters['sourceFilter']);
            });
        }

        if (isset($filters['sortField']) && isset($filters['sortDirection'])) {
            $query->orderBy($filters['sortField'], $filters['sortDirection']);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Selected IDs filter (for bulk export from Filament)
        if (! empty($filters['selectedIds'])) {
            $query->whereIn('id', $filters['selectedIds']);
        }

        return $query;
    }

    private function getCsvHeaders(): array
    {
        return [
            'Order ID',
            'Status',
            'Order Source',
            'Created At',
            'Updated At',
            'Customer Name',
            'Email',
            'Phone',
            'Purchase Type',
            'Purchase Purpose',
            'Support Type',
            'Initial Message',
            'Project Name',
            'Unit Title',
            'Assigned Employee',
            'Last Action By',
            'Created By',
            'Marketing Source',
            'Campaign Name',
            'Ad Set',
            'Ad Squad',
            'Ad Name',
            'External ID',
            'Bank Name',
            'Bank Employee Name',
            'Bank Employee Phone',
            'Is Waiting List',
            'Waiting List Unit Type',
            'Waiting List Budget',
            'Waiting List Location',
            'Waiting List Notes',
            'Notes Count',
            'Notes',
        ];
    }

    private function mapOrderToRow($order): array
    {
        $notes = $order->relationLoaded('notes') ? $order->notes : collect();
        $notesText = $notes->map(function ($note) {
            $author = $note->relationLoaded('user') && $note->user ? $note->user->name : '—';
            $date = $note->created_at ? $note->created_at->format('Y-m-d H:i') : '';
            return "[{$date}] {$author}: {$note->note}";
        })->implode(" | ");

        return [
            $order->id,
            $order->statusLabel(),
            $order->orderSourceLabel(),
            $order->created_at?->format('Y-m-d H:i:s') ?? '—',
            $order->updated_at?->format('Y-m-d H:i:s') ?? '—',
            $order->name,
            $order->email ?? '—',
            $order->phone ?? '—',
            $order->purchaseTypeLabel(),
            $order->purchasePurposeLabel(),
            $order->support_type ?? '—',
            $order->message ?? '—',
            $order->project?->name ?? '—',
            $order->unit?->title ?? '—',
            $order->assignedSalesUser?->name ?? '—',
            $order->lastActionByUser?->name ?? '—',
            $order->user?->name ?? '—',
            $order->marketing_source ?? '—',
            $order->campaign_name ?? '—',
            $order->ad_set ?? '—',
            $order->ad_squad ?? '—',
            $order->ad_name ?? '—',
            $order->external_id ?? '—',
            $order->bank_name ?? '—',
            $order->bank_employee_name ?? '—',
            $order->bank_employee_phone ?? '—',
            $order->is_waiting_list ? 'Yes' : 'No',
            $order->waiting_list_unit_type ?? '—',
            $order->waiting_list_budget ?? '—',
            $order->waiting_list_location ?? '—',
            $order->waiting_list_notes ?? '—',
            $notes->count(),
            $notesText ?: '—',
        ];
    }

    private function notifyUser(User $user, string $title, string $body, string $status, ?string $downloadUrl = null): void
    {
        try {
            $notification = FilamentNotification::make()
                ->title($title)
                ->body($body)
                ->{$status}();

            if ($downloadUrl) {
                $notification->actions([
                    \Filament\Notifications\Actions\Action::make('download')
                        ->label('تحميل الملف')
                        ->url($downloadUrl)
                        ->button()
                        ->openUrlInNewTab(),
                ]);
            }

            $notification->sendToDatabase([$user]);
        } catch (\Throwable $e) {
            Log::warning("Failed to send Filament notification: {$e->getMessage()}");
        }
    }
}
