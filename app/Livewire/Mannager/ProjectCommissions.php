<?php

namespace App\Livewire\Mannager;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectCommissions extends Component
{
    use WithPagination;

    public string $search = '';

    /**
     * Per-project commission edits, keyed by project id:
     * [id => ['commission_type' => ..., 'commission_value' => ...]].
     *
     * @var array<int, array<string, mixed>>
     */
    public array $commissions = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->hasRole('Admin'), 403);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /** Seed the editable state for a project the first time its row renders. */
    protected function hydrateRow(Project $project): void
    {
        if (! array_key_exists($project->id, $this->commissions)) {
            $this->commissions[$project->id] = [
                'commission_type' => $project->commission_type ?? Project::COMMISSION_PERCENTAGE,
                'commission_value' => $project->commission_value,
            ];
        }
    }

    public function saveAll(): void
    {
        abort_unless(auth()->user()?->hasRole('Admin'), 403);

        $this->resetErrorBag();

        $hasError = false;

        foreach ($this->commissions as $projectId => $row) {
            $validator = validator($row, [
                'commission_type' => ['required', 'in:'.implode(',', array_keys(Project::COMMISSION_TYPES))],
                'commission_value' => ['required', 'numeric', 'min:0'],
            ], [], [
                'commission_type' => 'نوع العمولة',
                'commission_value' => 'قيمة العمولة',
            ]);

            if ($validator->fails()) {
                foreach ($validator->errors()->keys() as $field) {
                    $this->addError("commissions.{$projectId}.{$field}", $validator->errors()->first($field));
                }
                $hasError = true;

                continue;
            }

            $validated = $validator->validated();

            // Percentage commissions can't exceed 100%.
            if ($validated['commission_type'] === Project::COMMISSION_PERCENTAGE && (float) $validated['commission_value'] > 100) {
                $this->addError("commissions.{$projectId}.commission_value", 'النسبة المئوية لا يمكن أن تتجاوز 100%.');
                $hasError = true;

                continue;
            }

            Project::whereKey($projectId)->update([
                'commission_type' => $validated['commission_type'],
                'commission_value' => $validated['commission_value'],
            ]);
        }

        if ($hasError) {
            return;
        }

        session()->flash('message', 'تم تحديث عمولات المشاريع بنجاح.');
    }

    public function render(): mixed
    {
        $projects = Project::query()
            // Only show available projects: those with at least one available unit (case 0),
            // excluding fully sold or reserved projects.
            ->whereHas('units', fn ($query) => $query->where('case', 0))
            ->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->paginate(15);

        foreach ($projects as $project) {
            $this->hydrateRow($project);
        }

        return view('livewire.mannager.project-commissions', [
            'projects' => $projects,
            'commissionTypes' => Project::COMMISSION_TYPES,
        ])->layout('layouts.custom');
    }
}
