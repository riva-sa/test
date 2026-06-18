<?php

namespace App\Filament\Pages;

use App\Models\Project;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageProjectCommissions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'الإعدادات';

    protected static ?string $navigationLabel = 'عمولات المشاريع';

    protected static ?string $title = 'عمولات المشاريع';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.manage-project-commissions';

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        // Commission settings belong to the admin (CRM) panel only; the broker
        // "app" panel discovers the same pages directory but must not expose them.
        return Filament::getCurrentPanel()?->getId() === 'admin';
    }

    public function mount(): void
    {
        $projects = Project::query()
            ->orderBy('name')
            ->get(['id', 'name', 'commission_type', 'commission_value'])
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'commission_type' => $project->commission_type ?? Project::COMMISSION_PERCENTAGE,
                'commission_value' => $project->commission_value,
            ])
            ->all();

        $this->form->fill(['projects' => $projects]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Repeater::make('projects')
                    ->label('المشاريع')
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false)
                    ->columns(3)
                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                    ->schema([
                        TextInput::make('name')
                            ->label('المشروع')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpan(1),
                        Select::make('commission_type')
                            ->label('نوع العمولة')
                            ->options(Project::COMMISSION_TYPES)
                            ->default(Project::COMMISSION_PERCENTAGE)
                            ->required()
                            ->live()
                            ->native(false)
                            ->columnSpan(1),
                        TextInput::make('commission_value')
                            ->label(fn (Get $get) => $get('commission_type') === Project::COMMISSION_FIXED ? 'قيمة العمولة (ريال لكل وحدة)' : 'قيمة العمولة (% من سعر الوحدة)')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->minValue(0)
                            ->maxValue(fn (Get $get) => $get('commission_type') === Project::COMMISSION_FIXED ? null : 100)
                            ->suffix(fn (Get $get) => $get('commission_type') === Project::COMMISSION_FIXED ? 'ريال' : '%')
                            ->columnSpan(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data['projects'] ?? [] as $row) {
            if (empty($row['id'])) {
                continue;
            }

            Project::whereKey($row['id'])->update([
                'commission_type' => $row['commission_type'],
                'commission_value' => $row['commission_value'],
            ]);
        }

        Notification::make()
            ->success()
            ->title('تم حفظ العمولات بنجاح')
            ->send();
    }
}
