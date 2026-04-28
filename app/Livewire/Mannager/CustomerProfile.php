<?php

namespace App\Livewire\Mannager;

use App\Models\UnitOrder;
use Livewire\Component;
use Livewire\WithPagination;

class CustomerProfile extends Component
{
    use WithPagination;

    public $phone;
    public $perPage = 10;

    public function mount($phone)
    {
        $this->phone = $phone;
    }

    public function render()
    {
        $allOrders = UnitOrder::accessibleBy(auth()->user())
            ->where('phone', $this->phone)
            ->with(['unit', 'project', 'assignedSalesUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($allOrders->isEmpty()) {
            abort(404, 'Customer not found or no accessible orders.');
        }

        $latestOrder = $allOrders->first();
        
        $customerData = [
            'name' => $allOrders->pluck('name')->filter()->first() ?? 'Unknown',
            'email' => $allOrders->pluck('email')->filter()->first() ?? 'N/A',
            'phone' => $this->phone,
            'latest_status' => $latestOrder->statusLabel(),
            'latest_status_color' => $latestOrder->statusColor(),
            'total_orders' => $allOrders->count(),
            'first_order_date' => $allOrders->last()->created_at,
            'last_order_date' => $latestOrder->created_at,
        ];

        // Paginated orders for the table
        $paginatedOrders = UnitOrder::accessibleBy(auth()->user())
            ->where('phone', $this->phone)
            ->with(['unit', 'project', 'assignedSalesUser'])
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.mannager.customer-profile', [
            'customer' => (object)$customerData,
            'orders' => $paginatedOrders,
        ])->layout('layouts.custom', [
            'title' => 'Customer Profile: ' . $customerData['name'],
            'description' => 'Detailed profile and order history for ' . $customerData['name'],
        ]);
    }
}
