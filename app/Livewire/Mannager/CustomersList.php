<?php

namespace App\Livewire\Mannager;

use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersList extends Component
{
    use WithPagination;

    public $selectedCustomer = null;

    public $search = '';

    public $perPage = 10;

    public function selectCustomer($phone)
    {
        $this->selectedCustomer = $phone;
    }

    public function resetCustomer()
    {
        $this->selectedCustomer = null;
    }

    public function render()
    {
        if ($this->selectedCustomer) {
            return $this->renderCustomerOrders();
        }

        $customers = UnitOrder::accessibleBy(auth()->user())
            ->selectRaw('
                phone,
                MIN(name) as name,
                MIN(email) as email,
                COUNT(*) as orders_count
            ')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->groupBy('phone')
            ->orderBy('orders_count', 'desc')
            ->paginate($this->perPage);

        return view('livewire.mannager.customers-list', [
            'customers' => $customers,
        ])->layout('layouts.custom', [
            'title' => 'Customer Orders',
            'description' => 'List of orders for the selected customer.',
        ]);
    }

    protected function renderCustomerOrders()
    {
        $orders = UnitOrder::accessibleBy(auth()->user())
            ->with(['unit', 'project'])
            ->where('phone', $this->selectedCustomer)
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.mannager.customers-list', [
            'customerOrders' => $orders,
            'customerPhone' => $this->selectedCustomer,
        ])->layout('layouts.custom', [
            'title' => 'Customer Orders',
            'description' => 'List of orders for the selected customer.',
        ]);
    }
}
