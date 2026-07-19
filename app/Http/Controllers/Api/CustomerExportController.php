<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnitOrder;
use Illuminate\Http\Request;

class CustomerExportController extends Controller
{
    /**
     * Retrieve a list of customer requests with name, phone, and registration date.
     * Can optionally filter by from_date and to_date query parameters.
     */
    public function index(Request $request)
    {
        $query = UnitOrder::query()
            ->select('id', 'name', 'phone', 'created_at')
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

        // Format the output specifically as requested: name, phone, registration date
        $formattedCustomers = $customers->getCollection()->map(function ($customer) {
            return [
                'name' => $customer->name,
                'phone' => $customer->phone,
                'registration_date' => $customer->created_at ? $customer->created_at->format('Y-m-d H:i:s') : null,
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
