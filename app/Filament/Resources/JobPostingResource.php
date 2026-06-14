<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobPostingResource\Pages;
use App\Models\JobPosting;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JobPostingResource extends Resource
{
    protected static ?string $model = JobPosting::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Careers';

    protected static ?string $navigationLabel = 'الوظائف';

    protected static ?string $modelLabel = 'وظيفة';

    protected static ?string $pluralModelLabel = 'الوظائف';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('content')
                    ->tabs([
                        Tabs\Tab::make('المحتوى العربي')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('المسمى الوظيفي')
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get) {
                                        if (blank($get('slug'))) {
                                            $set('slug', Str::slug($state ?? '', '-', 'ar'));
                                        }
                                    }),
                                Forms\Components\RichEditor::make('description')
                                    ->label('الوصف الوظيفي')
                                    ->required(),
                                Forms\Components\RichEditor::make('responsibilities')
                                    ->label('المسؤوليات'),
                                Forms\Components\RichEditor::make('requirements')
                                    ->label('المتطلبات'),
                                Forms\Components\RichEditor::make('benefits')
                                    ->label('المميزات'),
                            ]),
                        Tabs\Tab::make('English Content')
                            ->schema([
                                Forms\Components\TextInput::make('title_en')
                                    ->label('Job Title (English)')
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (?string $state, Forms\Set $set, Forms\Get $get) {
                                        if (filled($state) && blank($get('slug'))) {
                                            $set('slug', Str::slug($state));
                                        }
                                    }),
                                Forms\Components\RichEditor::make('description_en')
                                    ->label('Description (English)'),
                                Forms\Components\RichEditor::make('responsibilities_en')
                                    ->label('Responsibilities (English)'),
                                Forms\Components\RichEditor::make('requirements_en')
                                    ->label('Requirements (English)'),
                                Forms\Components\RichEditor::make('benefits_en')
                                    ->label('Benefits (English)'),
                            ]),
                    ])
                    ->columnSpanFull(),

                Section::make('تفاصيل الوظيفة')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('slug')
                                    ->label('الرابط (Slug)')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(JobPosting::class, 'slug', ignoreRecord: true)
                                    ->helperText('يستخدم في رابط الوظيفة: /careers/{slug}'),
                                Forms\Components\TextInput::make('department')
                                    ->label('القسم')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('department_en')
                                    ->label('القسم (English)')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('location')
                                    ->label('المدينة / الموقع')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('location_en')
                                    ->label('الموقع (English)')
                                    ->maxLength(255),
                                Forms\Components\Select::make('employment_type')
                                    ->label('نوع التوظيف')
                                    ->options(self::employmentTypeOptions())
                                    ->default('full_time')
                                    ->required(),
                                Forms\Components\Select::make('experience_level')
                                    ->label('مستوى الخبرة')
                                    ->options(self::experienceLevelOptions()),
                                Forms\Components\TextInput::make('salary_range')
                                    ->label('نطاق الراتب')
                                    ->maxLength(255)
                                    ->placeholder('مثال: 5000 - 8000 ريال'),
                                Forms\Components\TextInput::make('vacancies')
                                    ->label('عدد الشواغر')
                                    ->numeric()
                                    ->minValue(1)
                                    ->default(1)
                                    ->required(),
                            ]),
                    ]),

                Section::make('النشر')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label('الحالة')
                                    ->options([
                                        JobPosting::STATUS_DRAFT => 'مسودة',
                                        JobPosting::STATUS_PUBLISHED => 'منشورة',
                                        JobPosting::STATUS_CLOSED => 'مغلقة',
                                    ])
                                    ->default(JobPosting::STATUS_DRAFT)
                                    ->required(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label('تاريخ النشر'),
                                Forms\Components\DatePicker::make('expiry_date')
                                    ->label('تاريخ الانتهاء'),
                                Forms\Components\Toggle::make('is_featured')
                                    ->label('وظيفة مميزة')
                                    ->onColor('success'),
                                Forms\Components\TextInput::make('sort_order')
                                    ->label('ترتيب العرض')
                                    ->numeric()
                                    ->default(0),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label('المسمى الوظيفي')
                    ->searchable(['title', 'title_en'])
                    ->description(fn (JobPosting $record): ?string => $record->title_en),
                Tables\Columns\TextColumn::make('department')
                    ->label('القسم')
                    ->searchable(),
                Tables\Columns\TextColumn::make('location')
                    ->label('الموقع')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('employment_type')
                    ->label('نوع التوظيف')
                    ->formatStateUsing(fn (string $state): string => self::employmentTypeOptions()[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        JobPosting::STATUS_DRAFT => 'مسودة',
                        JobPosting::STATUS_PUBLISHED => 'منشورة',
                        JobPosting::STATUS_CLOSED => 'مغلقة',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        JobPosting::STATUS_PUBLISHED => 'success',
                        JobPosting::STATUS_CLOSED => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('مميزة')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('الطلبات')
                    ->counts('applications')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('تاريخ النشر')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('expiry_date')
                    ->label('تاريخ الانتهاء')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        JobPosting::STATUS_DRAFT => 'مسودة',
                        JobPosting::STATUS_PUBLISHED => 'منشورة',
                        JobPosting::STATUS_CLOSED => 'مغلقة',
                    ]),
                Tables\Filters\SelectFilter::make('employment_type')
                    ->label('نوع التوظيف')
                    ->options(self::employmentTypeOptions()),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('مميزة'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('تعديل'),
                Tables\Actions\Action::make('view_public')
                    ->label('عرض في الموقع')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (JobPosting $record): string => route('frontend.careers.single', ['slug' => $record->slug]))
                    ->openUrlInNewTab()
                    ->visible(fn (JobPosting $record): bool => $record->status === JobPosting::STATUS_PUBLISHED),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف')
                        ->requiresConfirmation()
                        ->action(function (Collection $records) {
                            $records->each->delete();
                        }),
                ]),
            ]);
    }

    /**
     * @return array<string, string>
     */
    public static function employmentTypeOptions(): array
    {
        return [
            'full_time' => 'دوام كامل',
            'part_time' => 'دوام جزئي',
            'contract' => 'عقد',
            'internship' => 'تدريب',
            'remote' => 'عن بعد',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function experienceLevelOptions(): array
    {
        return [
            'entry' => 'حديث التخرج',
            'junior' => 'مبتدئ (1-2 سنوات)',
            'mid' => 'متوسط (3-5 سنوات)',
            'senior' => 'خبير (5+ سنوات)',
            'manager' => 'إداري / قيادي',
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobPostings::route('/'),
            'create' => Pages\CreateJobPosting::route('/create'),
            'edit' => Pages\EditJobPosting::route('/{record}/edit'),
        ];
    }
}
