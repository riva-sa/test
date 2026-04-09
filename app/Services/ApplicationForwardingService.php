<?php

namespace App\Services;

use App\Models\OrderForwardEvent;
use App\Models\OrderPermission;
use App\Models\UnitOrder;
use App\Models\User;
use App\Notifications\UnitOrderUpdated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApplicationForwardingService
{
    public function forward(UnitOrder $order): void
    {
        if (! config('order_forwarding.enabled')) {
            return;
        }

        $allowedSources = config('order_forwarding.sources', []);
        if (! in_array($order->order_source, $allowedSources, true)) {
            return;
        }

        $this->notifyConfiguredUsers($order);
        $this->grantPermissionsByRoles($order);
        $this->dispatchWebhook($order);
    }

    private function notifyConfiguredUsers(UnitOrder $order): void
    {
        $ids = config('order_forwarding.notify_user_ids', []);
        if ($ids === []) {
            return;
        }

        try {
            $users = User::whereIn('id', $ids)->get();
            foreach ($users as $user) {
                $user->notify(new UnitOrderUpdated($order, 'order_forwarded', [
                    'customer_name' => $order->name,
                ]));
            }
            $this->logEvent($order, 'notify_users', 'success', ['user_ids' => $ids]);
        } catch (\Throwable $e) {
            Log::warning('Order forwarding notify failed: '.$e->getMessage());
            $this->logEvent($order, 'notify_users', 'fail', ['error' => $e->getMessage()]);
        }
    }

    private function grantPermissionsByRoles(UnitOrder $order): void
    {
        $roleNames = array_filter(config('order_forwarding.grant_permission_roles', []));
        if ($roleNames === []) {
            return;
        }

        $permissionType = (string) config('order_forwarding.permission_type', 'manage');

        try {
            $grantedIds = [];
            foreach ($roleNames as $roleName) {
                $roleName = trim((string) $roleName);
                if ($roleName === '') {
                    continue;
                }
                $users = User::role($roleName)->get();
                foreach ($users as $user) {
                    OrderPermission::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'unit_order_id' => $order->id,
                            'permission_type' => $permissionType,
                        ],
                        [
                            'granted_by' => null,
                        ]
                    );
                    $grantedIds[] = $user->id;
                }
            }

            $this->logEvent($order, 'grant_permissions_by_role', 'success', [
                'roles' => $roleNames,
                'user_ids' => array_values(array_unique($grantedIds)),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Order forwarding permissions failed: '.$e->getMessage());
            $this->logEvent($order, 'grant_permissions_by_role', 'fail', ['error' => $e->getMessage()]);
        }
    }

    private function dispatchWebhook(UnitOrder $order): void
    {
        $url = config('order_forwarding.webhook_url');
        if (empty($url)) {
            return;
        }

        try {
            $response = Http::timeout((int) config('order_forwarding.webhook_timeout', 5))
                ->acceptJson()
                ->post($url, [
                    'unit_order_id' => $order->id,
                    'order_source' => $order->order_source,
                    'project_id' => $order->project_id,
                    'unit_id' => $order->unit_id,
                    'created_at' => $order->created_at?->toIso8601String(),
                ]);

            $this->logEvent($order, 'webhook', $response->successful() ? 'success' : 'fail', [
                'status' => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Order forwarding webhook failed: '.$e->getMessage());
            $this->logEvent($order, 'webhook', 'fail', ['error' => $e->getMessage()]);
        }
    }

    private function logEvent(UnitOrder $order, string $strategy, string $status, array $payload): void
    {
        OrderForwardEvent::create([
            'unit_order_id' => $order->id,
            'strategy' => $strategy,
            'status' => $status,
            'payload' => $payload,
        ]);
    }
}
