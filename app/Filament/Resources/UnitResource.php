<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Group;
use Filament\Forms\Set;
use Illuminate\Validation\Rule;
use App\Filament\Resources\UnitResource\RelationManagers\ProjectsRelationManager;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\MultiSelectFilter;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Projects';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'الوحدات';

    public static function getGloballySearchableAttributes(): array
    {
        return [

        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Section
                Forms\Components\Section::make('Basic Information')
                ->schema([
                    // title

                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (string $state, Forms\Set $set) {
                            // Generate a random string (e.g., 6 characters long)
                            $randomString = Str::random(6); // You can adjust the length as needed

                            // Combine the title and random string to create the slug
                            $slug = Str::slug($state) . '-' . $randomString;

                            // Set the slug field
                            $set('slug', $slug);
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->nullable()
                        ->maxLength(255)
                        ->unique(Unit::class, 'slug', ignoreRecord: true)
                        ->columnSpan(1)
                        ->dehydrated(fn ($state) => filled($state)),

                    Forms\Components\Select::make('project_id')
                        ->relationship('project', 'name')
                        ->required()
                        ->columnSpan(1)
                        ->preload()
                        ->native(false)
                        ->noSearchResultsMessage('No projects found.')
                        ->suffixAction(
                            Action::make('createProject')
                                ->icon('heroicon-m-plus')
                                ->url(ProjectResource::getUrl('create'))
                                ->openUrlInNewTab()
                        ),
                    Forms\Components\TextInput::make('unit_type')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('building_number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('unit_number')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(1),
                    // sale_type
                    // Forms\Components\Select::make('sale_type')
                    //     ->options([
                    //         'direct' => 'direct',
                    //         'installment' => 'Installment',
                    //     ])
                    //     ->required()
                    //     ->columnSpan(1),
                    Forms\Components\TextInput::make('floor')
                        ->required()
                        ->columnSpan(1),
                    Forms\Components\RichEditor::make('description')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(3),

            // Unit Specifications Section
            Forms\Components\Section::make('Unit Specifications')
                ->schema([
                    Forms\Components\TextInput::make('unit_area')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('unit_price')
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('living_rooms')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('beadrooms')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('bathrooms')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('kitchen')
                        ->required()
                        ->numeric()
                        ->columnSpan(1),
                    Forms\Components\Select::make('features')
                        ->multiple()
                        ->relationship('features', 'name')
                        ->preload()
                        ->searchable()
                        ->columnSpanFull()
                        ->suffixAction(
                            Action::make('createProject')
                                ->icon('heroicon-m-plus')
                                ->url(FeatureResource::getUrl('create'))
                                ->openUrlInNewTab()
                        )
                        ->label('Unit Feature')
                ])
                ->columns(3),

            // Location Section
            Forms\Components\Section::make('Location')
                ->schema([
                    // Forms\Components\TextInput::make('latitude')
                    //     ->dehydrated()
                    //     ->nullable(),

                    // Forms\Components\TextInput::make('longitude')
                    //     ->dehydrated()
                    //     ->nullable(),

                    // Map::make('location')
                    //     ->label('Location')
                    //     ->columnSpanFull()
                    //     ->defaultLocation(latitude: 24.7136, longitude: 46.6753)
                    //     ->afterStateHydrated(function ($state, $record, Set $set): void {
                    //         if ($record) {
                    //             $set('location', [
                    //                 'lat' => floatval($record->latitude),
                    //                 'lng' => floatval($record->longitude)
                    //             ]);
                    //             $set('latitude', $record->latitude);
                    //             $set('longitude', $record->longitude);
                    //         }
                    //     })
                    //     ->afterStateUpdated(function ($state, callable $set) {
                    //         $set('latitude', $state['lat']);
                    //         $set('longitude', $state['lng']);
                    //     })
                    //     ->extraStyles([
                    //         'min-height: 50vh',
                    //         'border-radius: 7px'
                    //     ])
                    //     ->liveLocation(true, true, 1000)
                    //     ->showMarker()
                    //     ->markerColor("#000000")
                    //     ->showFullscreenControl()
                    //     ->showZoomControl()
                    //     ->draggable()
                    //     ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png/")
                    //     ->zoom(12)
                    //     ->detectRetina()
                    //     ->showMyLocationButton()
                    //     ->extraTileControl([])
                    //     ->extraControl([
                    //         'zoomDelta'           => 1,
                    //         'zoomSnap'            => 2,

                    //     ])
                ])
                ->columns(2),

            // Media Section
            Forms\Components\Section::make('Media')
                ->schema([
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->directory('units/images')
                        ->columnSpan(2),
                    Forms\Components\FileUpload::make('floor_plan')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios([
                            '16:9',
                            '4:3',
                            '1:1',
                        ])
                        ->directory('units/floor_plans')
                        ->columnSpan(2),
                ])
                ->columns(2),

            // Status Section
            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Toggle::make('show_price')
                        ->offColor('danger')
                        ->onColor('success')
                        ->default(true)
                        ->columnSpan(1),
                    Forms\Components\Toggle::make('status')
                        ->offColor('danger')
                        ->onColor('success')
                        ->default(true)
                        ->columnSpan(1),

                    // // user_id
                    // Forms\Components\Select::make('user_id')
                    //     ->relationship('user', 'name')
                    //     ->label('User')
                    //     ->disabled()
                    //     ->nullable()
                    //     ->default(auth()->id())
                    //     ->columnSpan(1),
                ])
                ->columns(2),
        ])
        ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('project.name')->label('المشروع')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_type')->label('نوع الوحدة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\SelectColumn::make('case')->label('حالة الشراء')
                    ->options([
                        0 => 'متاحة',
                        1 => 'محجوزة',
                        2 => 'مباعة',
                    ])
                    ->searchable()
                    ->afterStateUpdated(function ($state) {
                        // Show a success notification after state update
                        Notification::make()
                            ->title('Case Updated')
                            ->body('تم تحديث الحالة ' )
                            ->success()
                            ->send();
                    }),
                Tables\Columns\TextColumn::make('building_number')->label('رقم المبني')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_number')->label('رقم الوحدة')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_price')->label('السعر')
                    ->sortable()
                    ->money('SAR'),
                Tables\Columns\IconColumn::make('status')->label('حالة العرض')
                    ->boolean(),
                Tables\Columns\IconColumn::make('show_price')->label('عرض السعر')
                    ->boolean(),
            ])
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Filter by Project')
                    ->relationship('project', 'name')
                    ->preload()
                    ->searchable(),

                // Filter by unit type
                SelectFilter::make('unit_type')
                    ->label('Filter by Unit Type')
                    ->options([
                        'apartment' => 'Apartment',
                        'villa' => 'Villa',
                        'studio' => 'Studio',
                    ])
                    ->searchable(),

                // Filter by price range
                Filter::make('unit_price')
                    ->label('Filter by Price Range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')->numeric()->label('Min Price'),
                        Forms\Components\TextInput::make('max_price')->numeric()->label('Max Price'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['min_price'], fn($q) => $q->where('unit_price', '>=', $data['min_price']))
                            ->when($data['max_price'], fn($q) => $q->where('unit_price', '<=', $data['max_price']));
                    }),

                // Filter by status
                SelectFilter::make('status')
                    ->label('Filter by Status')
                    ->options([
                        0 => 'Available',
                        1 => 'Reserved',
                        2 => 'Sold',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                TableAction::make('duplicate')
                    ->label('Duplicate')
                    ->url(fn (Unit $record) => route('filament.admin.resources.units.create', ['copy' => $record->id]))
                    ->icon('heroicon-o-document-duplicate'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProjectsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Admin sees all users
        if (auth()->user()->hasRole('Admin')) {
            return $query;
        }
        // Check if the user has the 'sales_manager' role
        if (auth()->user()->hasRole('sales_manager')) {
            // Join the projects table and filter units based on the project sales_manager_id
            $query->whereHas('project', function ($query) {
                $query->where('sales_manager_id', auth()->user()->id);
            });
        }

        return $query;
    }

}
