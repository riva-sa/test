<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitOrder;
use Illuminate\Http\Request;

class CustomerExportController extends Controller
{
    /**
     * Retrieve a list of customer requests with name, phone, registration date,
     * plus status, unit type, project, purchase type, and last update.
     * Can optionally filter by from_date and to_date query parameters.
     */
    public function index(Request $request)
    {
        $query = UnitOrder::query()
            ->with(['unit', 'project', 'user'])
            ->select('id', 'name', 'email', 'phone', 'status', 'PurchaseType', 'unit_id', 'project_id', 'user_id', 'created_at', 'updated_at')
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
        $customers = $query->paginate($perPage);

        // Format the output: customer, status, unit, project, purchase type, dates, and user data
        $formattedCustomers = $customers->getCollection()->map(function ($customer) {
            return [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'email' => $customer->email,
                'customer_status' => $customer->statusLabel(),
                'status_code' => $customer->status,
                'unit_type' => $customer->unit?->unit_type,
                'project' => $customer->project?->name,
                'purchase_type' => $customer->purchaseTypeLabel(),
                'purchase_type_code' => $customer->PurchaseType,
                'registration_date' => $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : null,
                'last_update' => $customer->updated_at ? $customer->updated_at->format('Y-m-d H:i:s') : null,
                'user' => $customer->user ? [
                    'id' => $customer->user->id,
                    'name' => $customer->user->name,
                    'email' => $customer->user->email,
                    'phone' => $customer->user->phone,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedCustomers,
            'meta' => [
                'current_page' => $customers->currentPage(),
                'last_page' => $customers->lastPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
            ]
        ]);
    }
}
