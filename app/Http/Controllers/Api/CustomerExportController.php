<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitOrder;
use Illuminate\Http\Request;

class CustomerExportController extends Controller
{
    /**
     * Retrieve a list of customers with all their order details.
     * Can optionally filter by from_date and to_date query parameters.
     */
    public function index(Request $request)
    {
        $query = UnitOrder::query()
            ->whereNotNull('phone')
            ->orderBy('created_at', 'desc');

        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->input('from_date'));
        }

        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->input('to_date'));
        }

        // Use pagination to handle potentially large datasets
        $perPage = $request->input('per_page', 100);
        $customersPaginated = $query->paginate($perPage);

        // Get unique phones for the current page
        $phones = $customersPaginated->getCollection()->pluck('phone')->filter()->unique();

        // Load all orders with relations for these customers
        $allOrdersByPhone = UnitOrder::with(['unit', 'project', 'user', 'assignedSalesUser'])
            ->whereIn('phone', $phones)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('phone');

        // Format customer list with orders array
        $formattedCustomers = $phones->map(function ($phone) use ($allOrdersByPhone) {
            $orders = $allOrdersByPhone->get($phone, collect());
            $latestOrder = $orders->first();

            return [
                'name' => $orders->pluck('name')->filter()->first() ?? $latestOrder?->name,
                'phone' => $phone,
                'email' => $orders->pluck('email')->filter()->first() ?? $latestOrder?->email,
                'total_orders' => $orders->count(),
                'customer_status' => $latestOrder?->statusLabel(),
                'status_code' => $latestOrder?->status,
                'first_order_date' => $orders->last()?->created_at ? $orders->last()->created_at->format('Y-m-d H:i:s') : null,
                'last_update' => $latestOrder?->updated_at ? $latestOrder->updated_at->format('Y-m-d H:i:s') : null,
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'status' => $order->statusLabel(),
                        'status_code' => $order->status,
                        'status_color' => $order->statusColor(),
                        'unit_type' => $order->unit?->unit_type,
                        'unit_number' => $order->unit?->unit_number,
                        'unit_price' => $order->unit?->unit_price,
                        'project' => $order->project?->name,
                        'project_id' => $order->project_id,
                        'purchase_type' => $order->purchaseTypeLabel(),
                        'purchase_type_code' => $order->PurchaseType,
                        'purchase_purpose' => $order->purchasePurposeLabel(),
                        'message' => $order->message,
                        'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                        'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : null,
                        'user' => $order->user ? [
                            'id' => $order->user->id,
                            'name' => $order->user->name,
                            'email' => $order->user->email,
                        ] : null,
                        'assigned_sales' => $order->assignedSalesUser ? [
                            'id' => $order->assignedSalesUser->id,
                            'name' => $order->assignedSalesUser->name,
                            'email' => $order->assignedSalesUser->email,
                        ] : null,
                    ];
                })->values()->all(),
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $formattedCustomers,
            'meta' => [
                'current_page' => $customersPaginated->currentPage(),
                'last_page' => $customersPaginated->lastPage(),
                'per_page' => $customersPaginated->perPage(),
                'total' => $customersPaginated->total(),
            ]
        ]);
    }

    /**
     * Retrieve details and all orders for a single customer by phone number or order ID.
     */
    public function show(Request $request, string $identifier)
    {
        $orders = UnitOrder::query()
            ->with(['unit', 'project', 'user', 'assignedSalesUser'])
            ->where('phone', $identifier)
            ->orderBy('created_at', 'desc')
            ->get();

        if ($orders->isEmpty() && is_numeric($identifier)) {
            $orderById = UnitOrder::find($identifier);
            if ($orderById && $orderById->phone) {
                $orders = UnitOrder::query()
                    ->with(['unit', 'project', 'user', 'assignedSalesUser'])
                    ->where('phone', $orderById->phone)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } elseif ($orderById) {
                $orders = collect([$orderById]);
            }
        }

        if ($orders->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'العميل غير موجود أو لا توجد طلبات مرتبطة به.',
            ], 404);
        }

        $latestOrder = $orders->first();

        $customerData = [
            'name' => $orders->pluck('name')->filter()->first() ?? $latestOrder->name,
            'phone' => $latestOrder->phone,
            'email' => $orders->pluck('email')->filter()->first() ?? $latestOrder->email,
            'total_orders' => $orders->count(),
            'latest_status' => $latestOrder->statusLabel(),
            'latest_status_code' => $latestOrder->status,
            'latest_status_color' => $latestOrder->statusColor(),
            'first_order_date' => $orders->last()?->created_at ? $orders->last()->created_at->format('Y-m-d H:i:s') : null,
            'last_order_date' => $latestOrder->created_at ? $latestOrder->created_at->format('Y-m-d H:i:s') : null,
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'status' => $order->statusLabel(),
                    'status_code' => $order->status,
                    'status_color' => $order->statusColor(),
                    'purchase_type' => $order->purchaseTypeLabel(),
                    'purchase_type_code' => $order->PurchaseType,
                    'purchase_purpose' => $order->purchasePurposeLabel(),
                    'message' => $order->message,
                    'order_source' => $order->orderSourceLabel(),
                    'order_source_code' => $order->order_source,
                    'unit' => $order->unit ? [
                        'id' => $order->unit->id,
                        'title' => $order->unit->title,
                        'unit_type' => $order->unit->unit_type,
                        'building_number' => $order->unit->building_number,
                        'unit_number' => $order->unit->unit_number,
                        'unit_price' => $order->unit->unit_price,
                        'floor' => $order->unit->floor,
                    ] : null,
                    'project' => $order->project ? [
                        'id' => $order->project->id,
                        'name' => $order->project->name,
                    ] : null,
                    'assigned_sales' => $order->assignedSalesUser ? [
                        'id' => $order->assignedSalesUser->id,
                        'name' => $order->assignedSalesUser->name,
                        'email' => $order->assignedSalesUser->email,
                        'phone' => $order->assignedSalesUser->phone,
                    ] : null,
                    'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : null,
                    'updated_at' => $order->updated_at ? $order->updated_at->format('Y-m-d H:i:s') : null,
                ];
            })->values()->all(),
        ];

        return response()->json([
            'success' => true,
            'data' => $customerData,
        ]);
    }
}

