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

        if (! Auth::user()->hasRole('sales')) {
            $customers = UnitOrder::selectRaw('
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
        } else {
            $customers = UnitOrder::selectRaw('
                phone,
                MIN(name) as name,
                MIN(email) as email,
                COUNT(*) as orders_count
            ')
                ->whereHas('project', function ($query) {
                    $query->where('sales_manager_id', auth()->id());
                })
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
        }

        return view('livewire.mannager.customers-list', [
            'customers' => $customers,
        ])->layout('layouts.custom', [
            'title' => 'Customer Orders',
            'description' => 'List of orders for the selected customer.',
        ]);
    }

    protected function renderCustomerOrders()
    {
        if (Auth::user()->hasRole('sales_manager') || Auth::user()->hasRole('follow_up')) {
            $orders = UnitOrder::with(['unit', 'project'])
                ->where('phone', $this->selectedCustomer)
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        } else {
            $orders = UnitOrder::with(['unit', 'project'])
                ->where('phone', $this->selectedCustomer)
                ->whereHas('project', function ($query) {
                    $query->where('sales_manager_id', auth()->id());
                })
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage);
        }

        return view('livewire.mannager.customers-list', [
            'customerOrders' => $orders,
            'customerPhone' => $this->selectedCustomer,
        ])->layout('layouts.custom', [
            'title' => 'Customer Orders',
            'description' => 'List of orders for the selected customer.',
        ]);
    }
}
