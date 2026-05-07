<?php

namespace App\Console\Commands;

use App\Models\OrderPermission;
use App\Models\Project;
use App\Models\UnitOrder;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BackfillOldOrderAssignments extends Command
{
    protected $signature = 'orders:backfill-assignments
                            {--dry-run : Show what would be updated without making changes}
                            {--project= : Only process a specific project ID}
                            {--force-user= : Force assign all unassigned orders to this user ID (for projects where sales_manager_id was already removed)}';

    protected $description = 'Assign old unassigned orders to the project sales manager so they remain visible after switching to auto-distribution';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $specificProject = $this->option('project');
        $forceUserId = $this->option('force-user');

        if ($dryRun) {
            $this->warn('🔍 DRY RUN — لن يتم تعديل أي بيانات');
        }

        $totalUpdated = 0;
        $totalPermissions = 0;

        // ──────────────────────────────────────────────
        // PHASE 1: Projects that still have sales_manager_id
        // ──────────────────────────────────────────────
        $this->info('═══════════════════════════════════════');
        $this->info('المرحلة 1: المشاريع التي لا تزال تملك مسؤول مبيعات');
        $this->info('═══════════════════════════════════════');

        $projectsWithManager = Project::whereNotNull('sales_manager_id');
        if ($specificProject) {
            $projectsWithManager->where('id', $specificProject);
        }
        $projectsWithManager = $projectsWithManager->with('salesManager')->get();

        foreach ($projectsWithManager as $project) {
            [$updated, $perms] = $this->assignOrdersToUser(
                $project,
                $project->sales_manager_id,
                $project->salesManager->name ?? 'غير معروف',
                $dryRun
            );
            $totalUpdated += $updated;
            $totalPermissions += $perms;
        }

        if ($projectsWithManager->isEmpty()) {
            $this->line('  ⏭️  لا توجد مشاريع لديها sales_manager_id');
        }

        // ──────────────────────────────────────────────
        // PHASE 2: Projects where sales_manager_id was already removed
        // ──────────────────────────────────────────────
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('المرحلة 2: المشاريع التي أُزيل منها مسؤول المبيعات');
        $this->info('═══════════════════════════════════════');

        $projectsWithoutManager = Project::whereNull('sales_manager_id');
        if ($specificProject) {
            $projectsWithoutManager->where('id', $specificProject);
        }
        $projectsWithoutManager = $projectsWithoutManager->get();

        foreach ($projectsWithoutManager as $project) {
            $unassignedOrders = UnitOrder::where('project_id', $project->id)
                ->whereNull('assigned_sales_user_id')
                ->get();

            if ($unassignedOrders->isEmpty()) {
                $this->line("  ⏭️  المشروع [{$project->name}] — لا توجد طلبات بدون تعيين");

                continue;
            }

            $this->warn("📁 المشروع: {$project->name} (ID: {$project->id}) — sales_manager_id = NULL");
            $this->warn("   📦 طلبات بدون تعيين: {$unassignedOrders->count()}");

            // Try to detect the previous manager from order data
            $detectedUserId = $this->detectPreviousManager($unassignedOrders);

            $targetUserId = $forceUserId ?? $detectedUserId;

            if (! $targetUserId) {
                $this->error("   ❌ لم نتمكن من تحديد المسؤول السابق — استخدم --force-user=USER_ID");

                continue;
            }

            $targetUser = User::find($targetUserId);
            if (! $targetUser) {
                $this->error("   ❌ المستخدم ID: {$targetUserId} غير موجود");

                continue;
            }

            $source = $forceUserId ? 'محدد يدويًا' : 'تم اكتشافه تلقائيًا';
            $this->info("   👤 المسؤول المستهدف: {$targetUser->name} (ID: {$targetUserId}) — {$source}");

            [$updated, $perms] = $this->assignOrdersToUser(
                $project,
                $targetUserId,
                $targetUser->name,
                $dryRun
            );
            $totalUpdated += $updated;
            $totalPermissions += $perms;
        }

        if ($projectsWithoutManager->isEmpty()) {
            $this->line('  ⏭️  لا توجد مشاريع بدون مسؤول مبيعات');
        }

        // ──────────────────────────────────────────────
        // Summary
        // ──────────────────────────────────────────────
        $this->newLine();
        $this->info('═══════════════════════════════════════');

        if ($dryRun) {
            $this->warn("🔍 DRY RUN — سيتم تعيين {$totalUpdated} طلب عند التشغيل الفعلي");
            $this->info('💡 لتنفيذ التعديلات، شغّل الأمر بدون --dry-run');
        } else {
            $this->info('🎉 تم بنجاح!');
            $this->info("   📦 طلبات مُعينة: {$totalUpdated}");
            $this->info("   🔑 صلاحيات جديدة: {$totalPermissions}");
            $this->newLine();
            $this->info('✅ يمكنك الآن إزالة sales_manager_id من المشاريع بأمان — الطلبات القديمة ستظل مرئية للمندوبين.');

            Log::info("BackfillOldOrderAssignments: Updated {$totalUpdated} orders, created {$totalPermissions} permissions");
        }

        return self::SUCCESS;
    }

    /**
     * Assign unassigned orders in a project to a specific user.
     *
     * @return array [updatedCount, permissionsCreatedCount]
     */
    private function assignOrdersToUser(Project $project, int $userId, string $userName, bool $dryRun): array
    {
        $unassignedOrders = UnitOrder::where('project_id', $project->id)
            ->whereNull('assigned_sales_user_id')
            ->get();

        if ($unassignedOrders->isEmpty()) {
            $this->line("  ⏭️  المشروع [{$project->name}] — لا توجد طلبات بدون تعيين");

            return [0, 0];
        }

        $this->info("📁 المشروع: {$project->name} (ID: {$project->id})");
        $this->info("   👤 المسؤول: {$userName} (ID: {$userId})");
        $this->info("   📦 طلبات بدون تعيين: {$unassignedOrders->count()}");

        if ($dryRun) {
            return [$unassignedOrders->count(), 0];
        }

        $updated = 0;
        $permsCreated = 0;

        DB::transaction(function () use ($unassignedOrders, $userId, &$updated, &$permsCreated) {
            foreach ($unassignedOrders as $order) {
                $order->assigned_sales_user_id = $userId;
                $order->saveQuietly(); // Avoid triggering observer

                $perm = OrderPermission::firstOrCreate([
                    'user_id' => $userId,
                    'unit_order_id' => $order->id,
                    'permission_type' => 'manage',
                ], [
                    'granted_by' => null,
                ]);

                $updated++;
                if ($perm->wasRecentlyCreated) {
                    $permsCreated++;
                }
            }
        });

        $this->info("   ✅ تم تعيين {$updated} طلب — صلاحيات جديدة: {$permsCreated}");
        $this->newLine();

        return [$updated, $permsCreated];
    }

    /**
     * Try to detect who was the previous sales manager from order data.
     */
    private function detectPreviousManager($orders): ?int
    {
        // Strategy 1: Check last_action_by_user_id — the most active user is likely the manager
        $lastActionUsers = $orders->pluck('last_action_by_user_id')
            ->filter()
            ->countBy()
            ->sortDesc();

        if ($lastActionUsers->isNotEmpty()) {
            $candidateId = $lastActionUsers->keys()->first();
            $user = User::find($candidateId);
            if ($user && $user->hasRole('sales')) {
                return $candidateId;
            }
        }

        // Strategy 2: Check existing OrderPermission records
        $permUsers = OrderPermission::whereIn('unit_order_id', $orders->pluck('id'))
            ->select('user_id', DB::raw('count(*) as cnt'))
            ->groupBy('user_id')
            ->orderByDesc('cnt')
            ->first();

        if ($permUsers) {
            $user = User::find($permUsers->user_id);
            if ($user && $user->hasRole('sales')) {
                return $permUsers->user_id;
            }
        }

        return null;
    }
}
