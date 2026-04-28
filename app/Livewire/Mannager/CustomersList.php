<?php

namespace App\Livewire\Mannager;

use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class CustomersList extends Component
{
    use WithPagination;

    public $search = '';

    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = UnitOrder::accessibleBy(auth()->user())
            ->selectRaw('
                phone,
                MIN(name) as name,
                MIN(email) as email,
                COUNT(*) as orders_count,
                MAX(created_at) as last_order_at
            ')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhere('phone', 'like', '%'.$this->search.'%');
                });
            })
            ->groupBy('phone')
            ->orderBy('last_order_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.mannager.customers-list', [
            'customers' => $customers,
        ])->layout('layouts.custom', [
            'title' => 'مدارة العملاء',
            'description' => 'قائمة العملاء وعدد طلباتهم.',
        ]);
    }
}
