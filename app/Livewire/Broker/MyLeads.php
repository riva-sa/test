<?php

namespace App\Livewire\Broker;

use App\Models\UnitOrder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyLeads extends Component
{
    use WithPagination;

    public $search = '';

    public $statusFilter = '';

    public $projectFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'projectFilter' => ['except' => ''],
    ];

    public function updating($name)
    {
        $this->resetPage();
    }

    public function render()
    {
        $broker = Auth::guard('broker')->user();

        // الوسيط يرى طلباته فقط — أي بيانات داخلية (ملاحظات الفريق وغيرها) لا تُحمَّل إطلاقاً
        $leads = UnitOrder::forBroker($broker)
            ->with(['project:id,name', 'unit:id,title'])
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%")
                        ->orWhere('id', $this->search);
                });
            })
            ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->projectFilter, fn ($q) => $q->where('project_id', $this->projectFilter))
            ->latest()
            ->paginate(15);

        $projects = UnitOrder::forBroker($broker)
            ->with('project:id,name')
            ->get()
            ->pluck('project')
            ->filter()
            ->unique('id')
            ->values();

        return view('livewire.broker.my-leads', [
            'leads' => $leads,
            'projects' => $projects,
            'statusLabels' => UnitOrder::STATUS_LABELS,
        ])->layout('layouts.broker');
    }
}
