<?php

namespace App\Livewire\Mannager;

use App\Models\BlockedNumber;
use Livewire\Component;
use Livewire\WithPagination;

class BlockedNumbers extends Component
{
    use WithPagination;

    public $phone = '';
    public $reason = '';
    public $search = '';

    protected $rules = [
        'phone' => 'required|unique:blocked_numbers,phone',
        'reason' => 'nullable|string|max:255',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function addNumber()
    {
        $this->validate();

        BlockedNumber::create([
            'phone' => $this->phone,
            'reason' => $this->reason,
        ]);

        $this->reset(['phone', 'reason']);
        session()->flash('message', 'تم حظر الرقم بنجاح.');
    }

    public function deleteNumber($id)
    {
        BlockedNumber::findOrFail($id)->delete();
        session()->flash('message', 'تم إلغاء حظر الرقم.');
    }

    public function render()
    {
        $blockedNumbers = BlockedNumber::where('phone', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.mannager.blocked-numbers', [
            'blockedNumbers' => $blockedNumbers
        ])->layout('layouts.custom');
    }
}
