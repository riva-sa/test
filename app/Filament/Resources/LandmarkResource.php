<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LandmarkResource\Pages;
use App\Filament\Resources\LandmarkResource\RelationManagers;
use App\Models\Landmark;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;

class LandmarkResource extends Resource
{
    protected static ?string $model = Landmark::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'المشاريع';

    protected static ?string $navigationLabel = 'المعالم القريبة';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات المعلم')
                    ->schema([
                        TextInput::make('name')
                            ->label('الاسم')
                            ->required(),
                        TextInput::make('description')
                            ->label('الوصف')
                            ->required(),
                        // TextInput::make('distance')
                        //     ->label('المسافة من المشروع')
                        //     ->numeric()
                        //     ->suffix('كم'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('الاسم'),
                Tables\Columns\TextColumn::make('description')->label('الوصف'),
                Tables\Columns\TextColumn::make('distance')->label('المسافة'),
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
            'index' => Pages\ListLandmarks::route('/'),
            'create' => Pages\CreateLandmark::route('/create'),
            'edit' => Pages\EditLandmark::route('/{record}/edit'),
        ];
    }
}
