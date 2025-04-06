<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GuaranteeResource\Pages;
use App\Filament\Resources\GuaranteeResource\RelationManagers;
use App\Models\Guarantee;
use Doctrine\DBAL\Query\From;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Project;
use Filament\Forms\Components\CheckboxList;

class GuaranteeResource extends Resource
{
    protected static ?string $model = Guarantee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'المشاريع';

    protected static ?string $navigationLabel = 'الضمانات';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات الضمان')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف')
                            ->columnSpanFull(),
                        Forms\Components\FileUpload::make('icon')
                            ->label('الأيقونة')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->disk('public')
                            ->directory('garantee/images'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('مفعل')
                            ->onColor('success')
                            ->offColor('danger')
                            ->default(true),

                        CheckboxList::make('projects')
                            ->label('المشاريع')
                            ->relationship('projects', 'name') // Uses the relationship
                            ->columns(4)
                            ->options(Project::all()->pluck('name', 'id')->toArray()), // Fetch available projects from the database

                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('الحالة')
                    ->onColor('success')
                    ->offColor('danger')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGuarantees::route('/'),
            'create' => Pages\CreateGuarantee::route('/create'),
            'edit' => Pages\EditGuarantee::route('/{record}/edit'),
        ];
    }
}
