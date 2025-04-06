<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectTypeResource\Pages;
use App\Filament\Resources\ProjectTypeResource\RelationManagers;
use App\Models\ProjectType;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use phpDocumentor\Reflection\Types\Boolean;

class ProjectTypeResource extends Resource
{
    protected static ?string $model = ProjectType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'المشاريع';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'نوع المشروع';


    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'description',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('المعلومات الأساسية')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('الاسم')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Forms\Components\Toggle::make('status')
                            ->label('الحالة')
                            ->onColor('success')
                            ->required()
                            ->default(true)
                            ->columnSpan(1)
                            ->offColor('danger'),
                        Forms\Components\TextInput::make('slug')
                            ->label('الرابط')
                            ->nullable()
                            ->maxLength(255)
                            ->unique(ProjectType::class, 'slug', ignoreRecord: true)
                            ->columnSpan(2),
                    ])
                    ->columns(2),
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
            'index' => Pages\ListProjectTypes::route('/'),
            'create' => Pages\CreateProjectType::route('/create'),
            'edit' => Pages\EditProjectType::route('/{record}/edit'),
        ];
    }
}
