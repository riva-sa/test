<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Toggle;
use Dotswan\MapPicker\Fields\Map;
use Filament\Forms\Components\Group;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Validation\Rule;
use App\Filament\Resources\ProjectResource\RelationManagers\UnitRelationManager;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use App\Models\Feature;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Landmark;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'المشاريع';
    public static function getNavigationSort(): ?int
    {
        return -2;
    }
    protected static ?string $navigationLabel = 'المشاريع';

    protected static ?int $navigationSort = 1;

    public static function getGloballySearchableAttributes(): array
    {
        return
        [
            'name',
            'description',
            'address',
            'city',
            'state',
            'country',
            'bulding_style',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

            grid::make(3)->schema([

                Group::make()
                ->schema([
                    // Section 1: Project Details
                    Section::make('تفاصيل المشروع')
                    ->schema([
                        Grid::make(2) // Use a 2-column grid
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('الاسم')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, Forms\Set $set) {
                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->label('الرابط')
                                    ->nullable()
                                    ->maxLength(255)
                                    ->columnSpan(1)
                                    ->disabled()
                                    ->unique(Project::class, 'slug', ignoreRecord: true)
                                    ->dehydrated(fn ($state) => filled($state)),
                                Forms\Components\TextInput::make('AdLicense')
                                    ->label('رخصة الإعلان')
                                    ->required()
                                    ->columnSpan(1)
                                    ->maxLength(255),

                                TextInput::make('address')
                                    ->label('العنوان')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),

                                TextInput::make('virtualTour')
                                    ->label('رابط الجولة الافتراضية')
                                    ->columnSpan(1),
                                Select::make('developer_id')
                                    ->label('المطور')
                                    ->relationship('developer', 'name')
                                    ->required()
                                    ->columnSpan(1)
                                    ->searchable()
                                    ->native(true)
                                    ->preload()
                                    ->noSearchResultsMessage('لا يوجد مطورين.')
                                    ->suffixAction(
                                        Action::make('createProject')
                                            ->icon('heroicon-m-plus')
                                            ->url(DeveloperResource::getUrl('create'))
                                            ->openUrlInNewTab()
                                    ),
                                Select::make('project_type_id')
                                    ->label('نوع المشروع')
                                    ->relationship('projectType', 'name')
                                    ->required()
                                    ->columnSpan(1)
                                    ->preload()
                                    ->searchable()
                                    ->native(true)
                                    ->suffixAction(
                                        Action::make('createProject')
                                            ->icon('heroicon-m-plus')
                                            ->url(ProjectTypeResource::getUrl('create'))
                                            ->openUrlInNewTab()
                                    ),

                                RichEditor::make('description')
                                    ->label('الوصف')
                                    ->required()
                                    ->columnSpan(2),
                            ]),
                    ]),

                // Section 3: Pricing and Status
                Section::make('التسعير والحالة')
                    ->schema([
                        Grid::make(2) // Use a 2-column grid
                            ->schema([
                                // TextInput::make('price')
                                //     ->required()
                                //     ->maxLength(255)
                                //     ->columnSpan(1),

                                // Toggle::make('show_price')
                                //     ->onColor('success')
                                //     ->required()
                                //     ->default(true)
                                //     ->columnSpanFull()
                                //     ->columnSpan(1)
                                //     ->offColor('danger'),
                                TextInput::make('bulding_style')
                                    ->label('نمط البناء')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                            ]),
                            Select::make('features')
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
                                ->label('مميزات المشروع'),

                            Select::make('guarantees')
                                ->multiple()
                                ->relationship('guarantees', 'name')
                                ->preload()
                                ->searchable()
                                ->columnSpanFull()
                                ->suffixAction(
                                    Action::make('createProject')
                                        ->icon('heroicon-m-plus')
                                        ->url(GuaranteeResource::getUrl('create'))
                                        ->openUrlInNewTab()
                                )
                                ->label('ضمانات المشروع'),

                            // Select::make('landmarks')
                            //     ->multiple()
                            //     ->relationship('landmarks', 'name')
                            //     ->preload()
                            //     ->searchable()
                            //     ->columnSpanFull()
                            //     ->label('المعالم القريبة')
                            //     ->helperText('اختر المعالم القريبة ومسافاتها')
                            //     ->createOptionForm([
                            //         TextInput::make('name')->label('الاسم')->required(),
                            //         TextInput::make('description')->label('الوصف')->required(),
                            //         TextInput::make('distance')
                            //             ->label('المسافة من المشروع')
                            //             ->numeric()
                            //             ->suffix('كم'),
                            //     ]),

                            Select::make('landmarks')
                            ->multiple()
                            ->relationship('landmarks', 'name')
                            ->preload()
                            ->searchable()
                            ->columnSpanFull()
                            ->label('المعالم القريبة')
                            ->helperText('اختر المعالم القريبة ومسافاتها')
                            ->createOptionForm([
                                TextInput::make('name')->label('الاسم')->required(),
                                TextInput::make('description')->label('الوصف')->required(),
                                TextInput::make('distance')
                                    ->label('المسافة من المشروع')
                                    ->numeric()
                                    ->suffix('كم'),
                            ])
                            ->createOptionUsing(function (array $data, $livewire) {
                                // إنشاء معلم جديد
                                $landmark = Landmark::create([
                                    'name' => $data['name'],
                                    'description' => $data['description'],
                                ]);

                                // حفظ المعلومات في الجدول الوسيط يدوياً
                                if (isset($data['distance']) && method_exists($livewire, 'getRecord')) {
                                    $project = $livewire->getRecord();

                                    // Add the relationship with pivot data
                                    $project->landmarks()->attach($landmark->id, [
                                        'distance' => $data['distance']
                                    ]);
                                }

                                return $landmark->id;
                            }),
                            // File Upload for images with repeater to upload multiple images
                            Repeater::make('projectMedia')
                                ->relationship('projectMedia') // Relationship defined in Project model
                                ->schema([
                                    FileUpload::make('media_url')
                                        ->label('تحميل ملف')
                                        ->directory('project-media') // Folder to store the media
                                        ->visibility('public') // Set the visibility (public or private)
                                        ->required()
                                        ->imageEditor()  // This enables the built-in image editor
                                        ->maxSize(1000000)
                                        ->columnSpanFull(),

                                    Select::make('media_type')
                                        ->label('نوع الملف')
                                        ->default('image')
                                        ->options([
                                            'image' => 'Image',
                                            'video' => 'Video',
                                            'pdf' => 'PDF',
                                        ])
                                        ->required(),
                                    // TextInput::make('youtube_url')
                                    //     ->label('YouTube URL')
                                    //     ->placeholder('Optional if media is a video'),

                                    // TextInput::make('vimeo_url')
                                    //     ->label('Vimeo URL')
                                    //     ->placeholder('Optional if media is a video'),

                                    Select::make('status')
                                        ->label('العرض')
                                        ->options([
                                            1 => 'Active',
                                            0 => 'Inactive',
                                        ])
                                        ->default(1),
                                    Select::make('main')
                                        ->label('تعيين ك صورة اساسية')
                                        ->options([
                                            1 => 'Yes',
                                            0 => 'No',
                                        ])
                                        ->default(0),
                                ])
                                ->columns(3)
                                ->label('ملفات المشروع')
                                ->minItems(1)
                                ->maxItems(10),

                                // user_id
                                // Forms\Components\Select::make('sales_manager_id')
                                //     ->relationship('salesManager', 'name')  // Changed from 'user' to 'salesManager'
                                //     ->label('مسؤل المبيعات')  // Updated label to match Arabic UI
                                //     ->nullable()
                                //     ->default(auth()->id())
                                //     ->disabled(function () {
                                //         return auth()->user()->hasRole('sales_manager');
                                //     })
                                //     ->columnSpan(1),

                    ]),
                ])->columnSpan(2),



                Group::make()
                ->schema([
                    Section::make()
                        ->schema([

                            Forms\Components\Toggle::make('status')
                                ->label('الحالة')
                                ->onColor('success')
                                ->offColor('danger')
                                ->default(true)
                                ->required(),
                            Forms\Components\Toggle::make('is_featured')
                                ->label('مميز')
                                ->onColor('success')
                                ->offColor('danger')
                                ->required(),
                            Select::make('city_id')
                                ->label('المدينة')
                                ->native(false)
                                ->relationship('city', 'name')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->columnSpanFull()
                                ->suffixAction(
                                    Action::make('createProject')
                                        ->icon('heroicon-m-plus')
                                        ->url(CityResource::getUrl('create'))
                                        ->openUrlInNewTab()
                                ),
                            Select::make('state_id')
                                ->label('الحي')
                                ->native(false)
                                ->relationship('state', 'name')
                                ->required()
                                ->preload()
                                ->searchable()
                                ->columnSpanFull()
                                ->suffixAction(
                                    Action::make('createProject')
                                        ->icon('heroicon-m-plus')
                                        ->url(StateResource::getUrl('create'))
                                        ->openUrlInNewTab()
                                ),
                            TextInput::make('country')
                                ->label('الدولة')
                                ->required()
                                ->maxLength(255)
                                ->default('المملكة العربية السعودية')
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('latitude')
                                ->label('خط العرض')
                                ->dehydrated()
                                ->required(),

                            Forms\Components\TextInput::make('longitude')
                                ->label('خط الطول')
                                ->dehydrated()
                                ->required(),

                            Map::make('location')
                                ->label('الموقع')
                                ->columnSpanFull()
                                ->defaultLocation(latitude: 24.7136, longitude: 46.6753)
                                ->afterStateHydrated(function ($state, $record, Set $set): void {
                                    if ($record) {
                                        $set('location', [
                                            'lat' => floatval($record->latitude),
                                            'lng' => floatval($record->longitude)
                                        ]);
                                        $set('latitude', $record->latitude);
                                        $set('longitude', $record->longitude);
                                    }
                                })
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $set('latitude', $state['lat']);
                                    $set('longitude', $state['lng']);
                                })
                                ->extraStyles([
                                    'min-height: 50vh',
                                    'border-radius: 7px'
                                ])
                                ->liveLocation(true, true, 1000)
                                ->showMarker()
                                ->markerColor("#000000")
                                ->showFullscreenControl()
                                ->showZoomControl()
                                ->draggable()
                                ->tilesUrl("https://tile.openstreetmap.de/{z}/{x}/{y}.png/")
                                ->zoom(12)
                                ->detectRetina()
                                ->showMyLocationButton()
                                ->extraTileControl([])
                                ->extraControl([
                                    'zoomDelta'           => 1,
                                    'zoomSnap'            => 2,

                                ])

                        ])->columns(2),

                        // user_id
                        // Forms\Components\Select::make('sales_manager_id')
                        //     ->relationship('salesManager', 'name')
                        //     ->label('مسؤل المبيعات')
                        //     ->options(function () {
                        //         return User::role('sales_manager')->pluck('name', 'id');
                        //     })
                        //     ->columnSpan(1)
                        //     ->required(),

                        Forms\Components\Select::make('sales_manager_id')
                            ->relationship('salesManager', 'name')  // Changed from 'user' to 'salesManager'
                            ->label('مسؤل المبيعات')  // Updated label to match Arabic UI
                            ->nullable()
                            ->default(auth()->id())
                            ->disabled(function () {
                                return auth()->user()->hasRole('sales_manager');
                            })
                            ->columnSpan(1),
                        // images


                ]),
            ]),


            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('status')->label('الحالة')
                    ->sortable(),
                Tables\Columns\BooleanColumn::make('is_featured')->label('مميز')
                    ->sortable(),
                Tables\Columns\TextColumn::make('developer.name')->label('المطور')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bulding_style')->label('نوع البناء')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('address')->label('العنوان')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')->label('المدينة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')->label('الحي')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('تعديل'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('حذف'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            UnitRelationManager::class,
            // GuaranteeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Check if the user has the 'sales_manager' role
        if (auth()->user()->hasRole('sales_manager')) {
            // Show only projects assigned to the current sales manager
            $query->where('sales_manager_id', auth()->user()->id);
        }

        return $query;
    }

    protected function applyRoleBasedFilters(Builder $query): Builder
    {
        if (auth()->user()->hasRole('sales_manager')) {
            $query->where('sales_manager_id', auth()->id());
        }

        return $query;
    }

}
